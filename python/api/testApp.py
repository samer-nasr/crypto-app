from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List
import joblib
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
import uvicorn
from datetime import datetime
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, confusion_matrix
import os

app = FastAPI()

# MODEL_PATH = "../model/BTCUSDT/xgb_model_20250809_233116.pkl"
MODEL_DIR = "../model"

# Input for prediction
class PredictionInput(BaseModel):
    avg_price: float
    percentage_change: float
    previous_avg_price: float
    previous_price_change: float
    price_range: float
    ema_5: float
    ema_10: float
    ema_20: float
    ema_50: float
    sma_5: float
    sma_10: float
    sma_20: float
    sma_50: float
    rsi_14: float

# Input for training
# class TrainingRecord(BaseModel):
#     avg_price: float
#     percentage_change: float
#     previous_avg_price: float
#     previous_price_change: float
#     price_range: float
#     ema_5: float
#     ema_10: float
#     ema_20: float
#     ema_50: float
#     sma_5: float
#     sma_10: float
#     sma_20: float
#     sma_50: float
#     rsi_14: float
#     label: int  # target

class TrainingData(BaseModel):
    records: List[dict]


@app.post("/predict")
def predict(data: PredictionInput , model_path: str):
    if model_path is None:
        raise HTTPException(status_code=400, detail="missing model path")

    model = joblib.load(model_path)

    # features = [[
    #     data.avg_price,
    #     data.percentage_change,
    #     data.previous_avg_price,
    #     data.previous_price_change,
    #     data.price_range,
    #     data.ema_5,
    #     data.ema_10,
    #     data.ema_20,
    #     data.ema_50,
    #     data.sma_5,
    #     data.sma_10,
    #     data.sma_20,
    #     data.sma_50,
    #     data.rsi_14
    # ]]

    #  # Make sure features are in the same order as training
    # feature_dict = {
    #     "avg_price": data.avg_price,
    #     "percentage_change": data.percentage_change,
    #     "previous_avg_price": data.previous_avg_price,
    #     "previous_price_change": data.previous_price_change,
    #     "price_range": data.price_range,
    #     "ema_5": data.ema_5,
    #     "ema_10": data.ema_10,
    #     "ema_20": data.ema_20,
    #     "ema_50": data.ema_50,
    #     "sma_5": data.sma_5,
    #     "sma_10": data.sma_10,
    #     "sma_20": data.sma_20,
    #     "sma_50": data.sma_50,
    #     "rsi_14": data.rsi_14,
    # }

    # features_df = pd.DataFrame([features])

      # Define feature columns (same as training order)
    feature_names = [
        "avg_price",
        "percentage_change",
        "previous_avg_price",
        "previous_price_change",
        "price_range",
        "sma_10",
        "sma_20",
        "sma_5",
        "ema_10",
        "ema_20",
        "ema_5",
        "ema_50",
        "sma_50",
        "rsi_14"
    ]

    # Create DataFrame with column names
    features = pd.DataFrame([[
        data.avg_price,
        data.percentage_change,
        data.previous_avg_price,
        data.previous_price_change,
        data.price_range,
        data.sma_10,
        data.sma_20,
        data.sma_5,
        data.ema_10,
        data.ema_20,
        data.ema_5,
        data.ema_50,
        data.sma_50,
        data.rsi_14
    ]], columns=feature_names)

    prediction = model.predict(features)[0]
     # Probability (confidence for each class)
    probabilities = model.predict_proba(features)[0]  
    # Map class -> probability
    class_probabilities = {
        str(cls): float(prob) for cls, prob in zip(model.classes_, probabilities)
    }

    return {
        "prediction": float(prediction),
        "probabilities": class_probabilities
    }
@app.post("/train")
def train(data: TrainingData, symbol: str, test: bool):
    global model

    # Convert incoming JSON to DataFrame
    df = pd.DataFrame([record for record in data.records])

    # Separate features and target
    X = df.drop(columns=["label"])
    y = df["label"]

    # return y.tolist()
    # return X

    # Chronological split (first 80% train, last 20% test)
    train_size = int(len(df) * 0.8)
    X_train, X_test = X[:train_size], X[train_size:]
    y_train, y_test = y[:train_size], y[train_size:]

    # Train model
    model = RandomForestClassifier(n_estimators=100, random_state=42)

    if test:
        # Only train on first 80%
        model.fit(X_train, y_train)
    else:
        # Train on full dataset
        model.fit(X, y)

    # Evaluation (always on last 20% to simulate "future")
    y_pred = model.predict(X_test)
    classification_report_result = classification_report(
        y_test, y_pred, output_dict=True
    )
    confusion_matrix_result = confusion_matrix(y_test, y_pred).tolist()

    # Save model
    MODEL_DIR = os.path.join("../model", symbol)
    os.makedirs(MODEL_DIR, exist_ok=True)

    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    NEW_MODEL_PATH = os.path.join(MODEL_DIR, f"xgb_model_{timestamp}.pkl")

    joblib.dump(model, NEW_MODEL_PATH)

    return {
        "message": "Model trained successfully",
        "records_used": len(df),
        "model_name": f"xgb_model_{timestamp}.pkl",
        "classification_report": classification_report_result,
        "confusion_matrix": confusion_matrix_result
    }

if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8001)
