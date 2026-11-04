# IMPORT LIBRARY
import pandas as pd
import numpy as np
import os

from prophet import Prophet
from sklearn.metrics import mean_absolute_error
from sqlalchemy import create_engine
from sqlalchemy import text
from datetime import datetime

# KONEKSI DATABASE LARAVEL
engine = create_engine(
    "mysql+pymysql://root:@localhost/pos_ta"
)
print("Database terkoneksi")

# DATASET HISTORIS
BASE_DIR = os.path.dirname(
    os.path.abspath(__file__)
)

FILE_EXCEL = os.path.join(
    BASE_DIR,
    "dataset",
    "data_clean.xlsx"
)
print(FILE_EXCEL)

# mapping sheet excel ke product_id laravel
mapping_produk = {
    "surya_kaleng": 11,
    "sampoerna_16": 12,
    "aqua_600ml": 10,
    "aqua_1500ml": 9,
    "indomie": 13,
    "teh_botol_450ml": 16,
    "golda": 17,
    "surya_16": 18
}

# ============================================================
# MAPPING KONFIGURASI PROPHET TERBAIK PER PRODUK
# HASIL EKSPERIMEN PEMBAGIAN DATA 80% TRAINING : 20% TESTING
# ============================================================

mapping_config = {

    "surya_kaleng": {
        "changepoint_prior_scale": 0.1,
        "yearly_seasonality": True,
        "weekly_seasonality": True,
        "daily_seasonality": False,
        "seasonality_mode": "multiplicative",
        "n_changepoints": 25,
        "changepoint_range": 0.9,
        "holidays": None
    },

    "sampoerna_16": {
        "changepoint_prior_scale": 0.01,
        "yearly_seasonality": True,
        "weekly_seasonality": False,
        "daily_seasonality": False,
        "seasonality_mode": "multiplicative",
        "n_changepoints": 10,
        "changepoint_range": 1.0,
        "holidays": None
    },

    "aqua_600ml": {
        "changepoint_prior_scale": 0.1,
        "yearly_seasonality": False,
        "weekly_seasonality": True,
        "daily_seasonality": False,
        "seasonality_mode": "multiplicative",
        "n_changepoints": 25,
        "changepoint_range": 0.9,
        "holidays": None
    },

    "aqua_1500ml": {
        "changepoint_prior_scale": 0.05,
        "yearly_seasonality": True,
        "weekly_seasonality": True,
        "daily_seasonality": False,
        "seasonality_mode": "additive",
        "n_changepoints": 25,
        "changepoint_range": 0.9,
        "holidays": None
    },

    "indomie": {
        "changepoint_prior_scale": 0.05,
        "yearly_seasonality": True,
        "weekly_seasonality": False,
        "daily_seasonality": False,
        "seasonality_mode": "additive",
        "n_changepoints": 10,
        "changepoint_range": 1.0,
        "holidays": None
    },

    "teh_botol_450ml": {
        "changepoint_prior_scale": 0.05,
        "yearly_seasonality": True,
        "weekly_seasonality": True,
        "daily_seasonality": False,
        "seasonality_mode": "additive",
        "n_changepoints": 40,
        "changepoint_range": 0.8,
        "holidays": None
    },

    "golda": {
        "changepoint_prior_scale": 0.001,
        "yearly_seasonality": False,
        "weekly_seasonality": True,
        "daily_seasonality": False,
        "seasonality_mode": "multiplicative",
        "n_changepoints": 40,
        "changepoint_range": 0.9,
        "holidays": None
    },

    "surya_16": {
        "changepoint_prior_scale": 0.05,
        "yearly_seasonality": True,
        "weekly_seasonality": False,
        "daily_seasonality": False,
        "seasonality_mode": "additive",
        "n_changepoints": 10,
        "changepoint_range": 0.9,
        "holidays": None
    }
}

# DAFTAR SHEET YANG AKAN DIPROSES
sheet_produk = list(mapping_produk.keys())

# MENAMPUNG HASIL FORECAST
hasil_semua = []
semua_detail_forecast = []

# LOOP SEMUA PRODUK
for sheet in sheet_produk:
    print(f"\nMemproses : {sheet}")
    product_id = mapping_produk[sheet]

    # BACA DATA EXCEL
    df = pd.read_excel(FILE_EXCEL, sheet_name=sheet)

    # 1. PREPROCESSING BARU
    df['ds'] = pd.to_datetime(df['ds'])
    df = df.sort_values('ds').dropna()
    df.loc[df['y'] == 0, 'y'] = np.nan 

    cutoff_date = None
    #cutoff_date = "2025-08-31"

    if cutoff_date:
        cutoff_date = pd.to_datetime(cutoff_date)
        df = df[df['ds'] <= cutoff_date]

    print("MIN DATE :", df['ds'].min().date())
    print("MAX DATE :", df['ds'].max().date())

    # 2. AMBIL LEAD TIME DAN MOQ
    query_supplier = f"""
    SELECT s.lead_time, s.MOQ
    FROM product_suppliers ps
    JOIN suppliers s ON ps.supplier_id = s.id
    WHERE ps.product_id = {product_id}
    """
    supplier = pd.read_sql(query_supplier, engine)

    if len(supplier) == 0:
        lead_time = 14
        moq = 1
    else:
        lead_time = int(supplier.iloc[0]['lead_time'])
        moq = int(supplier.iloc[0]['MOQ'])

    # ============================================================
    # KONFIGURASI MODEL PRODUK
    # ============================================================

    config = mapping_config[sheet]

    print("Konfigurasi Prophet:")
    print("  changepoint_prior_scale :", config["changepoint_prior_scale"])
    print("  yearly_seasonality      :", config["yearly_seasonality"])
    print("  weekly_seasonality      :", config["weekly_seasonality"])
    print("  daily_seasonality       :", config["daily_seasonality"])
    print("  seasonality_mode        :", config["seasonality_mode"])
    print("  n_changepoints          :", config["n_changepoints"])
    print("  changepoint_range       :", config["changepoint_range"])


    # ============================================================
    # TAHAP 1: EVALUASI MODEL
    # 80% TRAINING : 20% TESTING
    # ============================================================

    train_size = int(len(df) * 0.80)

    train_data = df.iloc[:train_size].copy()
    test_data = df.iloc[train_size:].copy()

    print("Jumlah training :", len(train_data))
    print("Jumlah testing  :", len(test_data))


    model_eval = Prophet(
        changepoint_prior_scale=config["changepoint_prior_scale"],
        yearly_seasonality=config["yearly_seasonality"],
        weekly_seasonality=config["weekly_seasonality"],
        daily_seasonality=config["daily_seasonality"],
        seasonality_mode=config["seasonality_mode"],
        n_changepoints=config["n_changepoints"],
        changepoint_range=config["changepoint_range"],
        holidays=config["holidays"]
    )

    model_eval.fit(train_data)

    future_eval = test_data[["ds"]].copy()

    forecast_eval = model_eval.predict(
        future_eval
    )

    y_true = test_data["y"].values
    y_pred = forecast_eval["yhat"].values

    mask = ~np.isnan(y_true) & (y_true != 0)

    if mask.sum() > 0:
        mae = mean_absolute_error(
            y_true[mask],
            y_pred[mask]
        )
    else:
        mae = 0

    print("MAE evaluasi :", round(mae, 2))

    # ============================================================
    # MODEL FINAL SESUAI KONFIGURASI TERBAIK PRODUK
    # ============================================================

    config = mapping_config[sheet]

    model_final = Prophet(
        changepoint_prior_scale=config["changepoint_prior_scale"],
        yearly_seasonality=config["yearly_seasonality"],
        weekly_seasonality=config["weekly_seasonality"],
        daily_seasonality=config["daily_seasonality"],
        seasonality_mode=config["seasonality_mode"],
        n_changepoints=config["n_changepoints"],
        changepoint_range=config["changepoint_range"],
        holidays=config["holidays"]
    )

    model_final.fit(df)

    future_final = model_final.make_future_dataframe(periods=lead_time)
    forecast_final = model_final.predict(future_final)
    forecast_lead_time = forecast_final.tail(lead_time)

    # AMBIL CURRENT STOCK
    query_stock = f"""
    SELECT current_stock
    FROM stocks
    WHERE product_id = {product_id}
    """
    stok = pd.read_sql(query_stock, engine)
    current_stock = 0 if len(stok) == 0 else int(stok.iloc[0]['current_stock'])

    # TAHAP 3: PERHITUNGAN INVENTARIS BARU
    forecast_qty = int(forecast_lead_time["yhat"].clip(lower=0).round().sum())
    safety_stock = int(round(mae * lead_time))
    daily_demand = forecast_qty / lead_time
    rop = int(round((daily_demand * lead_time) + safety_stock))

    rekomendasi = max(0, rop - current_stock)

    # SESUAIKAN MOQ
    if rekomendasi > 0 and rekomendasi < moq:
        rekomendasi = moq

    print(f"ROP={rop} | STOCK={current_stock} | REKOMENDASI={rekomendasi}")

    # SIMPAN DETAIL FORECAST HARIAN
    for _, row in forecast_lead_time.iterrows():
        semua_detail_forecast.append({
            "product_id": product_id,
            "forecast_date": row['ds'].date(),
            "forecast_qty": int(round(max(0, row['yhat'])))
        })

    # SIMPAN KE LIST
    hasil_semua.append({
        "product_id": product_id,
        "forecast_period": forecast_lead_time.iloc[0]['ds'].date(),
        "forecast_qty": forecast_qty,
        "current_stock": current_stock,
        "lead_time": lead_time,
        "moq": moq,
        "daily_demand": round(daily_demand, 2),
        "safety_stock": safety_stock,
        "rop": rop,
        "rekomendasi": rekomendasi
    })

# PROSES UPDATE/INSERT KE DATABASE LARAVEL
hasil_df = pd.DataFrame(hasil_semua)

# UPDATE STOCKS
with engine.begin() as conn:
    for _, row in hasil_df.iterrows():
        conn.execute(
            text("""
                UPDATE stocks
                SET safety_stock = :safety_stock, reorder_point = :rop, updated_at = NOW()
                WHERE product_id = :product_id
            """),
            {
                "safety_stock": int(row['safety_stock']),
                "rop": int(row['rop']),
                "product_id": int(row['product_id'])
            }
        )

# HAPUS FORECAST LAMA
with engine.begin() as conn:
    conn.execute(text("DELETE FROM forecast_results"))

# SIMPAN FORECAST BARU
with engine.begin() as conn:
    for _, row in hasil_df.iterrows():
        conn.execute(
            text("""
                INSERT INTO forecast_results
                ( product_id, forecast_period, predicted_qty, created_at, updated_at)
                VALUES
                ( :product_id, :forecast_period, :qty, NOW(), NOW())
            """),
            {
                "product_id": int(row['product_id']),
                "forecast_period": row["forecast_period"],
                "qty": int(row['forecast_qty'])
            }
        )

# HAPUS FORECAST DETAIL LAMA
with engine.begin() as conn:
    conn.execute(text("DELETE FROM forecast_details"))

# SIMPAN FORECAST DETAIL BARU
with engine.begin() as conn:
    for item in semua_detail_forecast:
        conn.execute(
            text("""
                INSERT INTO forecast_details (product_id, forecast_date, forecast_qty, created_at, updated_at)
                VALUES (:product_id, :forecast_date, :forecast_qty, NOW(), NOW())
            """),
            item
        )

# HAPUS REKOMENDASI LAMA
with engine.begin() as conn:
    conn.execute(text("DELETE FROM purchase_recommendations"))

# SIMPAN REKOMENDASI BARU
with engine.begin() as conn:
    for _, row in hasil_df.iterrows():
        conn.execute(
            text("""
                INSERT INTO purchase_recommendations (product_id, recommendation_qty, created_at, updated_at)
                VALUES (:product_id, :qty, NOW(), NOW())
            """),
            {"product_id": int(row['product_id']), "qty": int(row['rekomendasi'])}
        )

print("=" * 50)
print("FORECAST SELESAI & DATABASE TERUPDATE")
print(f"Produk diproses : {len(hasil_df)}")
print("=" * 50)
