import joblib
import pandas as pd

# Load the model
model = joblib.load("../model/xgb_model.pkl")

# Example new data
new_data = pd.DataFrame([{
  "avg_price": 114548.2875,
  "percentage_change": 0.007557363439418767,
  "previous_avg_price": 114240.64749999999,
  "previous_price_change": -0.008042064740672344,
  "price_range": 2360.8699999999953
}])

# Predict
prediction = model.predict(new_data)
print("Prediction:", prediction)
