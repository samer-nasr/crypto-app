import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report, confusion_matrix
import joblib

# Load your data
df = pd.read_csv("../data/btc_usdt.csv")

# Drop rows where 'label' is NaN (target must not have missing values)
df = df.dropna(subset=['label'])

# Drop rows with any missing values in features
df = df.dropna(subset=[
    "avg_price",
    "percentage_change",
    "previous_avg_price",
    "previous_price_change",
    "price_range"
])

# Select features and target
features = [
    "avg_price",
    "percentage_change",
    "previous_avg_price",
    "previous_price_change",
    "price_range"
]
X = df[features]
y = df["label"]

# Train/test split
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Model training
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Save the model
joblib.dump(model, '../model/model2.pkl')

# Evaluation
y_pred = model.predict(X_test)
print("Classification Report:")
print(classification_report(y_test, y_pred))

print("Confusion Matrix:")
print(confusion_matrix(y_test, y_pred))
