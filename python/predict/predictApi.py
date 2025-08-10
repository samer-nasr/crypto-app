import sys
import json
import pickle
import numpy as np

# Load model
model = pickle.load(open("../model/xgb_model.pkl", "rb"))
# print("12220022")
# Get input
input_data = json.loads(sys.argv[1])

# Convert to numpy array for prediction
features = np.array(list(input_data.values())).reshape(1, -1)

# Predict
pred = model.predict(features)

# Output result as JSON
print(json.dumps({"label": int(pred[0])}))
