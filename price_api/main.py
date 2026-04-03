import joblib
import pandas as pd
import numpy as np
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field
from typing import List, Optional, Dict, Any
from datetime import datetime
import io
import logging
from sklearn.preprocessing import StandardScaler
from sklearn.compose import ColumnTransformer
from sklearn.cluster import AgglomerativeClustering
from sklearn.linear_model import LinearRegression
from sklearn.metrics import silhouette_score, r2_score, mean_absolute_error, mean_absolute_percentage_error
from io import StringIO

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = FastAPI()

# Global variables for model components
model_pipeline = None
cluster_pipeline = None
prediction_pipeline = None
feature_columns = None
model_type = "unknown"

# Maintenance forecast model components
maintenance_model = None
maintenance_scaler = None
maintenance_cluster_labels = None
maintenance_feature_columns = None

# --- 1. Load Price Prediction Model ---
@app.on_event("startup")
async def load_model():
    global model_pipeline, cluster_pipeline, prediction_pipeline, feature_columns, model_type
    
    try:
        model_data = joblib.load("dorm_price_model.joblib")
        
        # Check what type of model we have
        if isinstance(model_data, dict):
            if 'model' in model_data:
                model_pipeline = model_data['model']
                model_type = model_data.get('model_type', 'Unknown')
                feature_columns = model_data.get('feature_columns', [])
                logger.info("Dorm price model loaded successfully (metadata format)")
            elif 'prediction_pipeline' in model_data and 'cluster_pipeline' in model_data:
                cluster_pipeline = model_data['cluster_pipeline']
                prediction_pipeline = model_data['prediction_pipeline']
                feature_columns = model_data['feature_columns']
                model_pipeline = None
                model_type = "separate_pipelines"
                logger.info("Dorm price model loaded successfully (separate pipelines)")
            else:
                for key, value in model_data.items():
                    if hasattr(value, 'predict'):
                        model_pipeline = value
                        model_type = f"extracted_from_{key}"
                        logger.info(f"Dorm price model loaded from key: {key}")
                        break
                else:
                    logger.error("Unknown model dictionary format")
                    return
        else:
            model_pipeline = model_data
            model_type = "direct_pipeline"
            logger.info("Dorm price model loaded successfully (direct pipeline)")
            
        logger.info(f"✅ Model loaded successfully. Type: {model_type}")
        
    except FileNotFoundError:
        logger.error("Error: dorm_price_model.joblib not found!")
    except Exception as e:
        logger.error(f"Error loading model: {e}")

# Columns for price prediction
ALL_COLUMNS = [
    'Living Area (sqft)', 'Floor', 'Bed type', 'Room capacity', 'Furnishing',
    'Free_Wifi', 'Hot_Cold_Shower', 'Electric_Fan', 'Water_Kettle', 
    'Closet_Cabinet', 'Housekeeping', 'Refrigerator', 'Microwave', 
    'Rice_Cooker', 'Dining_Table', 'Utility_Subsidy', 'AC_Unit', 
    'Induction_Cooker', 'Washing_Machine', 'Access_Pool', 'Access_Gym'
]

# --- 2. Define Input Data Models ---
class UnitFeatures(BaseModel):
    Living_Area: float = Field(alias='Living Area (sqft)')
    Floor: int
    Bed_type: str = Field(alias='Bed type')
    Room_capacity: int = Field(alias='Room capacity')
    Furnishing: str
    Free_Wifi: int
    Hot_Cold_Shower: int
    Electric_Fan: int
    Water_Kettle: int
    Closet_Cabinet: int
    Housekeeping: int
    Refrigerator: int
    Microwave: int
    Rice_Cooker: int
    Dining_Table: int
    Utility_Subsidy: int
    AC_Unit: int
    Induction_Cooker: int
    Washing_Machine: int
    Access_Pool: int
    Access_Gym: int

class ForecastRequest(BaseModel):
    csv_data: str
    year: int

class MonthlyForecast(BaseModel):
    year: int
    month: int
    month_name: str
    forecasted_revenue: float

class ForecastResponse(BaseModel):
    success: bool
    forecast_year: int
    monthly_forecasts: List[MonthlyForecast]
    total_annual_revenue: float
    total_remaining_revenue: float
    average_monthly_revenue: float
    data_points_used: int
    
class MaintenanceForecastRequest(BaseModel):
    csv_data: str
    year: int

class MaintenanceMonthlyForecast(BaseModel):
    year: int
    month: int
    month_name: str
    forecasted_cost: float
    maintenance_count_estimate: int
    urgency_estimate: float
    seasonal_factor: float

class MaintenanceScheduleItem(BaseModel):
    month_name: str
    category: str
    priority: str
    estimated_cost: float
    reason: str
    recommended_action: str

class MaintenanceForecastResponse(BaseModel):
    success: bool
    forecast_year: int
    monthly_forecasts: List[MaintenanceMonthlyForecast]
    maintenance_schedule: List[MaintenanceScheduleItem]
    total_annual_cost: float
    total_remaining_cost: float
    average_monthly_cost: float
    data_points_used: int
    model_performance: Dict[str, float]

def preprocess_input_data(input_data: Dict[str, Any]) -> pd.DataFrame:
    processed_data = {}
    for key, value in input_data.items():
        if isinstance(value, bool):
            processed_data[key] = 1 if value else 0
        else:
            processed_data[key] = value
    input_df = pd.DataFrame([processed_data])
    column_mapping = {'Living_Area': 'Living Area (sqft)', 'Bed_type': 'Bed type'}
    return input_df.rename(columns=column_mapping)

def predict_with_cluster_pipeline(input_df: pd.DataFrame) -> float:
    try:
        input_data_without_cluster = {k: v for k, v in input_df.iloc[0].items() if k != 'Cluster'}
        input_df_for_cluster = pd.DataFrame([input_data_without_cluster], columns=feature_columns)
        cluster_labels = cluster_pipeline.predict(input_df_for_cluster)
        input_df_with_cluster = input_df_for_cluster.copy()
        input_df_with_cluster['Cluster'] = cluster_labels.astype(str)
        prediction = prediction_pipeline.predict(input_df_with_cluster)
        return float(prediction[0])
    except Exception as e:
        logger.error(f"Cluster pipeline prediction error: {e}")
        raise

def predict_with_single_pipeline(input_df: pd.DataFrame) -> float:
    try:
        try:
            prediction = model_pipeline.predict(input_df)
            return float(prediction[0])
        except Exception:
            input_df_with_cluster = input_df.copy()
            input_df_with_cluster['Cluster'] = '0'
            prediction = model_pipeline.predict(input_df_with_cluster)
            return float(prediction[0])
    except Exception as e:
        logger.error(f"Single pipeline prediction error: {e}")
        raise

@app.post("/predict")
def predict_price(features: UnitFeatures):
    try:
        if model_pipeline is None and cluster_pipeline is None:
            raise HTTPException(status_code=503, detail="Model not loaded")
        input_data = features.dict(by_alias=True)
        input_df = preprocess_input_data(input_data)
        if cluster_pipeline is not None and prediction_pipeline is not None:
            prediction_value = predict_with_cluster_pipeline(input_df)
        else:
            prediction_value = predict_with_single_pipeline(input_df)
        return {"predicted_price": round(prediction_value, 2)}
    except Exception as e:
        logger.error(f"Price prediction error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/")
def health_check():
    model_status = "loaded" if (model_pipeline is not None or cluster_pipeline is not None) else "not loaded"
    return {"status": "healthy", "service": "price_api", "model_status": model_status, "model_type": model_type}

# --- 3. REVENUE FORECASTING (FIXED fillna) ---
def create_features(df):
    df = df.copy()
    date_col = next((col for col in df.columns if 'date' in col.lower() or col == 'date_column'), None)
    if date_col is None:
        raise KeyError(f"No date column found. Columns: {list(df.columns)}")

    df['month'] = df[date_col].dt.month
    df['quarter'] = df[date_col].dt.quarter
    df['month_sin'] = np.sin(2 * np.pi * df['month'] / 12)
    df['month_cos'] = np.cos(2 * np.pi * df['month'] / 12)

    df['revenue_lag1'] = df['monthly_net_revenue'].shift(1)
    df['revenue_lag2'] = df['monthly_net_revenue'].shift(2)
    df['revenue_lag3'] = df['monthly_net_revenue'].shift(3)

    df['revenue_rolling_mean_3'] = df['monthly_net_revenue'].rolling(window=3, min_periods=1).mean()
    df['revenue_rolling_std_3'] = df['monthly_net_revenue'].rolling(window=3, min_periods=1).std().fillna(0)

    # MODERN PANDAS FILLNA FIX
    df['revenue_lag1'] = df['revenue_lag1'].bfill().ffill()
    df['revenue_lag2'] = df['revenue_lag2'].bfill().ffill()
    df['revenue_lag3'] = df['revenue_lag3'].bfill().ffill()

    if 'transaction_count' not in df.columns:
        df['transaction_count'] = df.get('maintenance_count', 0)
    return df

def load_and_preprocess_data(csv_data: str):
    df = pd.read_csv(StringIO(csv_data))
    date_col = None
    for col in df.columns:
        if any(k in col.lower() for k in ['date', 'time', 'period', 'month', 'year']):
            date_col = col
            break
    if not date_col: raise Exception("No date column found")
    
    df[date_col] = pd.to_datetime(df[date_col], errors='coerce')
    df = df.dropna(subset=[date_col])

    amount_col = next((col for col in df.columns if any(k in col.lower() for k in ['amount', 'revenue', 'cost', 'price']) and col != date_col), None)
    if not amount_col: raise Exception("No numeric revenue column found")

    df['year'], df['month'] = df[date_col].dt.year, df[date_col].dt.month
    
    if 'transaction_type' in df.columns:
        inflow_df = df[df['transaction_type'].astype(str).str.strip().str.upper().isin(['CREDIT', 'INFLOW'])].copy()
        count_col = 'transaction_id' if 'transaction_id' in inflow_df.columns else inflow_df.columns[0]
        monthly_data = inflow_df.groupby(['year', 'month']).agg({amount_col: 'sum', count_col: 'count'}).reset_index()
    else:
        inflow_df = df[df[amount_col] > 0].copy()
        monthly_data = inflow_df.groupby(['year', 'month']).agg({amount_col: 'sum', inflow_df.columns[0]: 'count'}).reset_index()

    monthly_data.columns = ['year', 'month', 'monthly_net_revenue', 'transaction_count']
    monthly_data['date_column'] = pd.to_datetime(monthly_data['year'].astype(str) + '-' + monthly_data['month'].astype(str) + '-01')
    return monthly_data.sort_values('date_column').reset_index(drop=True)

def train_forecast_model(X, y, cluster_range=(2, 6)):
    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)
    best_score, best_n, best_labels = -1, cluster_range[0], None
    for n in range(cluster_range[0], cluster_range[1] + 1):
        try:
            clustering = AgglomerativeClustering(n_clusters=n, linkage='ward')
            labels = clustering.fit_predict(X_scaled)
            if len(np.unique(labels)) > 1:
                score = silhouette_score(X_scaled, labels)
                if score > best_score:
                    best_score, best_n, best_labels = score, n, labels
        except: continue
    
    final_clustering = AgglomerativeClustering(n_clusters=best_n, linkage='ward')
    cluster_labels = final_clustering.fit_predict(X_scaled)
    cluster_models = {i: LinearRegression().fit(X.iloc[cluster_labels == i], y.iloc[cluster_labels == i]) for i in range(best_n) if sum(cluster_labels == i) >= 2}
    return {"models": cluster_models, "scaler": scaler, "cluster_labels": cluster_labels, "best_n_clusters": best_n}

def forecast_all_months(cluster_models, scaler, cluster_labels, monthly_data, target_year, feature_columns):
    from sklearn.neighbors import NearestNeighbors
    forecasts = []
    historical_mean = monthly_data['monthly_net_revenue'].mean()
    monthly_patterns = monthly_data.groupby('month').agg({'monthly_net_revenue': 'mean'}).to_dict()['monthly_net_revenue']
    
    current_lags = {'l1': monthly_data.iloc[-1]['monthly_net_revenue'], 'l2': historical_mean, 'l3': historical_mean}
    X_hist_scaled = scaler.transform(monthly_data[feature_columns])
    nn = NearestNeighbors(n_neighbors=1).fit(X_hist_scaled)

    for month in range(1, 13):
        feat = {
            'month': month, 'quarter': (month-1)//3+1, 'month_sin': np.sin(2*np.pi*month/12), 'month_cos': np.cos(2*np.pi*month/12),
            'revenue_lag1': current_lags['l1'], 'revenue_lag2': current_lags['l2'], 'revenue_lag3': current_lags['l3'],
            'revenue_rolling_mean_3': np.mean([current_lags['l1'], current_lags['l2'], current_lags['l3']]),
            'revenue_rolling_std_3': np.std([current_lags['l1'], current_lags['l2'], current_lags['l3']]),
            'transaction_count': monthly_data['transaction_count'].mean()
        }
        cdf = pd.DataFrame([feat])[feature_columns]
        dist, idx = nn.kneighbors(scaler.transform(cdf))
        model = cluster_models.get(cluster_labels[idx[0][0]], list(cluster_models.values())[0])
        res = model.predict(cdf)[0]
        
        final_res = max(0, (0.7 * res + 0.3 * monthly_patterns.get(month, historical_mean)))
        forecasts.append({'year': target_year, 'month': month, 'month_name': datetime(target_year, month, 1).strftime('%B'), 'forecasted_revenue': float(final_res)})
        current_lags['l3'], current_lags['l2'], current_lags['l1'] = current_lags['l2'], current_lags['l1'], final_res
    return forecasts

@app.post("/api/forecast/revenue")
def forecast_revenue(request: ForecastRequest):
    try:
        monthly_data = load_and_preprocess_data(request.csv_data)
        cutoff = datetime(datetime.now().year, datetime.now().month, 1)
        monthly_data = monthly_data[monthly_data['date_column'] < cutoff].copy()
        if len(monthly_data) < 4: raise Exception("Insufficient data")

        clean_data = create_features(monthly_data)
        cols = ['month', 'quarter', 'month_sin', 'month_cos', 'revenue_lag1', 'revenue_lag2', 'revenue_lag3', 'revenue_rolling_mean_3', 'revenue_rolling_std_3', 'transaction_count']
        trained = train_forecast_model(clean_data[cols], clean_data['monthly_net_revenue'], (2, min(6, len(clean_data)-1)))
        
        forecasts = forecast_all_months(trained["models"], trained["scaler"], trained["cluster_labels"], clean_data, request.year, cols)
        total = sum(f['forecasted_revenue'] for f in forecasts)
        return {'success': True, 'forecast_year': request.year, 'monthly_forecasts': forecasts, 'total_annual_revenue': total, 'average_monthly_revenue': total/12, 'data_points_used': len(clean_data)}
    except Exception as e:
        logger.error(f"Revenue Forecast Error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

# --- 4. MAINTENANCE FORECASTING (FIXED fillna) ---
def load_and_preprocess_maintenance_data(csv_data: str):
    df = pd.read_csv(StringIO(csv_data))
    date_col = next((c for c in df.columns if any(k in c.lower() for k in ['date', 'log_date', 'completion_date'])), None)
    cost_col = next((c for c in df.columns if any(k in c.lower() for k in ['cost', 'amount'])), None)
    
    df[date_col] = pd.to_datetime(df[date_col], errors='coerce')
    df[cost_col] = pd.to_numeric(df[cost_col], errors='coerce')
    df = df.dropna(subset=[date_col, cost_col])
    
    df['year'], df['month'] = df[date_col].dt.year, df[date_col].dt.month
    urgency_map = {'Level 1': 4, 'Level 2': 3, 'Level 3': 2, 'Level 4': 1}
    df['urgency_score'] = df['urgency'].apply(lambda x: urgency_map.get(x, 2)) if 'urgency' in df.columns else 0
    
    agg = {'monthly_maintenance_cost': (cost_col, 'sum'), 'maintenance_count': (df.columns[0], 'count'), 'urgency_score': ('urgency_score', 'sum'), 'category': ('category', lambda x: x.mode().iloc[0] if not x.mode().empty else 'Unknown')}
    monthly = df.groupby(['year', 'month'], as_index=False).agg(**agg)
    monthly['date_column'] = pd.to_datetime(monthly['year'].astype(str) + '-' + monthly['month'].astype(str) + '-01')
    return monthly.sort_values('date_column').reset_index(drop=True)

def train_maintenance_model(df, year, cluster_range):
    df = df.copy()
    df['cost_lag_1'] = df['monthly_maintenance_cost'].shift(1)
    df['cost_rolling_3'] = df['monthly_maintenance_cost'].rolling(3).mean()
    df['month_sin'] = np.sin(2*np.pi*df['month']/12)