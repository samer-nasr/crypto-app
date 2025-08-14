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

# Input for training
class TrainingRecord(BaseModel):
    avg_price: float
    percentage_change: float
    previous_avg_price: float
    previous_price_change: float
    price_range: float
    label: int  # target

class TrainingData(BaseModel):
    records: List[TrainingRecord]


# Load model if exists
# if os.path.exists(MODEL_PATH):
#     model = joblib.load(MODEL_PATH)
# else:
#     model = None


@app.post("/predict")
def predict(data: PredictionInput , model_path: str):
    if model_path is None:
        raise HTTPException(status_code=400, detail="missing model path")

    model = joblib.load(model_path)

    features = [[
        data.avg_price,
        data.percentage_change,
        data.previous_avg_price,
        data.previous_price_change,
        data.price_range
    ]]

    prediction = model.predict(features)[0]
    return {"prediction": float(prediction)}


@app.post("/train")
def train(data: TrainingData , symbol: str , test: bool):
    global model

    # Convert incoming JSON to DataFrame
    df = pd.DataFrame([record.dict() for record in data.records])

    # Separate features and target
    X = df.drop(columns=["label"])
    y = df["label"]

    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # Train model
    model = RandomForestClassifier(n_estimators=100, random_state=42)

    if test:
        model.fit(X_train, y_train)
    else:
        model.fit(X, y)
        
    # model.fit(X_train, y_train)

    # Evaluation
    y_pred = model.predict(X_test)
    # Convert classification report to dict
    classification_report_result = classification_report(
        y_test, y_pred, output_dict=True
    )

    # Convert confusion matrix to list for JSON serialization
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
        "model_name" :f"xgb_model_{timestamp}.pkl",
        "classification_report": classification_report_result,
        "confusion_matrix": confusion_matrix_result
        }


if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8001)
