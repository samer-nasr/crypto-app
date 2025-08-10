from fastapi import FastAPI
from pydantic import BaseModel
import joblib
import uvicorn

# Load your trained model (once at startup)
model = joblib.load("../model/xgb_model.pkl")  # change path if needed

app = FastAPI()

# Define the expected input structure
class PredictionInput(BaseModel):
    avg_price: float
    percentage_change: float
    previous_avg_price: float
    previous_price_change: float
    price_range: float

@app.post("/predict")
def predict(data: PredictionInput):
    # Convert input to list for model prediction
    features = [[
        data.avg_price,
        data.percentage_change,
        data.previous_avg_price,
        data.previous_price_change,
        data.price_range
    ]]

    prediction = model.predict(features)[0]  # Single prediction
    return {"prediction": float(prediction)}


if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8001)
