import pandas as pd
from sklearn.model_selection import train_test_split
from xgboost import XGBClassifier
from sklearn.metrics import classification_report, confusion_matrix
import joblib

# Load your data
df = pd.read_csv("btc_usdt.csv")

# Drop rows where target or any feature is missing
df = df.dropna(subset=[
    "avg_price",
    "percentage_change",
    "previous_avg_price",
    "previous_price_change",
    "price_range",
    "label"
])

# Select features and label
features = [
    "avg_price",
    "percentage_change",
    "previous_avg_price",
    "previous_price_change",
    "price_range"
]
X = df[features]
y = df["label"]

# Split the data
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train with XGBoost
model = XGBClassifier(use_label_encoder=False, eval_metric='logloss')
model.fit(X_train, y_train)

# Save model
joblib.dump(model, 'xgb_model.pkl')

# Evaluate
y_pred = model.predict(X_test)
print("Classification Report:")
print(classification_report(y_test, y_pred))

print("Confusion Matrix:")
print(confusion_matrix(y_test, y_pred))
