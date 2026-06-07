from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import joblib
import numpy as np

# Inisialisasi Aplikasi FastAPI
app = FastAPI(
    title="API SPK Ekskul",
    description="REST API untuk memprediksi rekomendasi ekstrakurikuler menggunakan Machine Learning",
    version="1.0.0"
)

# Load Model dan Encoders saat server pertama kali menyala
try:
    model = joblib.load('model_spk_ekskul.pkl')
    le_bidang = joblib.load('encoder_Bidang_Minat.pkl')
    le_mapel = joblib.load('encoder_Mata_Pelajaran.pkl')
    le_belajar = joblib.load('encoder_Cara_Belajar.pkl')
    le_fisik = joblib.load('encoder_Aktivitas_Fisik.pkl')
    le_lomba = joblib.load('encoder_Kesediaan_Lomba.pkl')
    le_kegiatan = joblib.load('encoder_Jenis_Kegiatan.pkl')
except Exception as e:
    print(f"Error memuat model/encoder: {e}")

# Desain Struktur Data JSON yang diterima menggunakan Pydantic
class DataSiswa(BaseModel):
    bidang_minat: str
    mata_pelajaran: str
    cara_belajar: str
    aktif_fisik: str
    kesediaan_lomba: str
    jenis_kegiatan: str

@app.post("/predict")
def prediksi_ekskul(data: DataSiswa):
    try:
        # Konversi teks ke angka menggunakan encoder
        bidang_enc = le_bidang.transform([data.bidang_minat])[0]
        mapel_enc = le_mapel.transform([data.mata_pelajaran])[0]
        belajar_enc = le_belajar.transform([data.cara_belajar])[0]
        fisik_enc = le_fisik.transform([data.aktif_fisik])[0]
        lomba_enc = le_lomba.transform([data.kesediaan_lomba])[0]
        kegiatan_enc = le_kegiatan.transform([data.jenis_kegiatan])[0]
        
        # Eksekusi model
        fitur = np.array([[bidang_enc, mapel_enc, belajar_enc, fisik_enc, lomba_enc, kegiatan_enc]])
        hasil_prediksi = model.predict(fitur)
        
        # Hitung tingkat probabilitas dalam skala desimal (0.0 - 1.0)
        probabilitas = model.predict_proba(fitur)
        nilai_prob = float(np.max(probabilitas))
        
        return {
            "status": "success",
            "rekomendasi_ekskul": str(hasil_prediksi[0]),
            "probabilitas": nilai_prob,
            "input_data": data
        }
        
    except ValueError as e:
        raise HTTPException(status_code=400, detail=f"Data input tidak sesuai: {str(e)}")
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Terjadi kesalahan server: {str(e)}")

# Endpoint dasar untuk mengecek server hidup atau tidak
@app.get("/")
def read_root():
    return {"message": "API SPK Ekskul berjalan dengan baik. Akses /docs untuk melihat dokumentasi API."}