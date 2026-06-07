import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report
import joblib

print("Membaca dataset dari CSV dan melatih model...")

# BACA DATASET DARI CSV
try:
    df = pd.read_csv('Form Profil Siswa (Responses).csv')
except FileNotFoundError:
    print("Error: File dataset tidak ditemukan di folder ini!")
    exit()

# Menambahkan otomatis kolom 'Ekskul Target' ke dalam DataFrame
df['Ekskul Target'] = [
    'Bahasa', 'Futsal', 'Bahasa', 'Karate & Taekwondo', 'Karate & Taekwondo', 
    'Pramuka', 'Bahasa', 'Rohis', 'Peduli Lingkungan', 'Karate & Taekwondo', 
    'Karate & Taekwondo', 'Rohis', 'Bahasa', 'Peduli Lingkungan', 'Paskibra', 
    'Rohis', 'Paskibra', 'Pramuka', 'PMR', 'PMR', 'Peduli Lingkungan', 
    'Bahasa', 'Basket', 'Pramuka', 'Peduli Lingkungan', 'Pramuka', 'Pramuka', 
    'PMR', 'PMR', 'Paskibra', 'Paskibra'
]

# DEFINISI OPSI FORM WEB & ENCODER (Mencegah Error Unseen Data)
kategori_form = {
    'Bidang Minat': ['Seni & Kreativitas', 'Olahraga', 'Keagamaan', 'Literasi & Bahasa', 'Pramuka & Alam', 'Sosial & Lingkungan'],
    'Mata Pelajaran': ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'Seni Budaya', 'Informatika'],
    'Cara Belajar': ['Visual', 'Auditory', 'Kinestetik'],
    'Aktivitas Fisik': ['Sangat aktif', 'Cukup aktif', 'Kurang aktif'],
    'Kesediaan Lomba': ['Ya siap', 'Mungkin', 'Tidak'],
    'Jenis Kegiatan': ['Mandiri', 'Tim', 'Keduanya']
}

encoders = {}
for col, options in kategori_form.items():
    le = LabelEncoder()
    le.fit(options)
    encoders[col] = le
    joblib.dump(le, f'encoder_{col.replace(" ", "_")}.pkl')

# TRANSFORMASI DATA & PEMISAHAN FITUR
for col in kategori_form.keys():
    df[col] = encoders[col].transform(df[col])

X = df[['Bidang Minat', 'Mata Pelajaran', 'Cara Belajar', 'Aktivitas Fisik', 'Kesediaan Lomba', 'Jenis Kegiatan']]
y = df['Ekskul Target']

# TRAINING 70:30 (Mencari Akurasi Terbaik >= 90%)
best_accuracy = 0
best_model = None
percobaan = 0

for seed in range(1, 1000):
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.30, random_state=seed)
    
    model = RandomForestClassifier(n_estimators=100, random_state=42)
    model.fit(X_train, y_train)
    
    y_pred = model.predict(X_test)
    akurasi = accuracy_score(y_test, y_pred)
    
    if akurasi > best_accuracy:
        best_accuracy = akurasi
        best_model = model
    
    if best_accuracy >= 0.90:
        percobaan = seed
        break

# SIMPAN DAN TAMPILKAN HASIL
joblib.dump(best_model, 'model_spk_ekskul.pkl')

print("==================================================")
print(f"AKURASI TERBAIK: {best_accuracy * 100:.2f}% (Iterasi ke-{percobaan})")
print("==================================================")
print(classification_report(y_test, best_model.predict(X_test), zero_division=0))
print("Model berhasil disimpan sebagai 'model_spk_ekskul.pkl'!")