<?php
$host = "localhost";
$user = "root";       
$pass = "";           
$db   = "db_spk_ekskul";

// 1. KONEKSI DATABASE
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil data dari database
$nama = $_POST['nama'] ?? 'Siswa';
$kelas = $_POST['kelas'] ?? '-';
$gender = $_POST['gender'] ?? '-';
$bidang = $_POST['bidang'] ?? '-';                 
$mapel = $_POST['mapel'] ?? '-';                   
$cara_belajar = $_POST['cara_belajar'] ?? '-';     
$aktif_fisik = $_POST['aktif_fisik'] ?? '-';       
$bersedia_lomba = $_POST['bersedia_lomba'] ?? '-'; 
$jenis_kegiatan = $_POST['jenis_kegiatan'] ?? '-'; 

// daftar ekskul dan bobot SAW
$ekskul = [
    'Jurnalis', 'Pramuka', 'PMR', 'Peduli Lingkungan', 
    'Basket', 'Badminton', 'Futsal', 'Paskibra', 
    'Rohis', 'Karate & Taekwondo', 'Bahasa'
];

$bobot = [
    'C1_Minat' => 0.25,
    'C2_Mapel' => 0.20,
    'C3_Belajar' => 0.15,
    'C4_Fisik' => 0.15,
    'C5_Lomba' => 0.15,
    'C6_Kegiatan' => 0.10
];

// PEMBENTUKAN MATRIKS X (SAW)
$matriks_X = [];
foreach ($ekskul as $e) {
    $matriks_X[$e] = ['C1' => 25, 'C2' => 25, 'C3' => 25, 'C4' => 25, 'C5' => 25, 'C6' => 25]; 
    
    if ($e == 'Jurnalis') {
        if($bidang == 'Literasi & Bahasa') $matriks_X[$e]['C1'] = 100;
        if($mapel == 'Bahasa Indonesia' || $mapel == 'Informatika') $matriks_X[$e]['C2'] = 100;
        if($cara_belajar == 'Auditory' || $cara_belajar == 'Visual') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Kurang aktif' || $aktif_fisik == 'Cukup aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Ya siap' || $bersedia_lomba == 'Mungkin') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Mandiri' || $jenis_kegiatan == 'Keduanya') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'Pramuka') {
        if($bidang == 'Pramuka & Alam') $matriks_X[$e]['C1'] = 100;
        if($mapel == 'IPA' || $mapel == 'IPS') $matriks_X[$e]['C2'] = 75;
        if($cara_belajar == 'Kinestetik') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Sangat aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Ya siap') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Tim' || $jenis_kegiatan == 'Keduanya') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'PMR') {
        if($bidang == 'Sosial & Lingkungan') $matriks_X[$e]['C1'] = 100;
        if($mapel == 'IPA') $matriks_X[$e]['C2'] = 100;
        if($cara_belajar == 'Kinestetik' || $cara_belajar == 'Visual') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Cukup aktif' || $aktif_fisik == 'Sangat aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Mungkin' || $bersedia_lomba == 'Ya siap') $matriks_X[$e]['C5'] = 75;
        if($jenis_kegiatan == 'Tim' || $jenis_kegiatan == 'Keduanya') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'Peduli Lingkungan') {
        if($bidang == 'Sosial & Lingkungan' || $bidang == 'Pramuka & Alam') $matriks_X[$e]['C1'] = 100;
        if($mapel == 'IPA' || $mapel == 'IPS') $matriks_X[$e]['C2'] = 100;
        if($cara_belajar == 'Kinestetik') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Cukup aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Tidak' || $bersedia_lomba == 'Mungkin') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Tim' || $jenis_kegiatan == 'Keduanya') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'Basket' || $e == 'Futsal') {
        if($bidang == 'Olahraga') $matriks_X[$e]['C1'] = 100;
        if($mapel == 'Seni Budaya') $matriks_X[$e]['C2'] = 75; 
        if($cara_belajar == 'Kinestetik') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Sangat aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Ya siap') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Tim') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'Badminton') {
        if($bidang == 'Olahraga') $matriks_X[$e]['C1'] = 100;
        if($cara_belajar == 'Kinestetik') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Sangat aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Ya siap') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Mandiri' || $jenis_kegiatan == 'Keduanya') $matriks_X[$e]['C6'] = 100; 
    }
    elseif ($e == 'Paskibra') {
        if($bidang == 'Pramuka & Alam' || $bidang == 'Olahraga') $matriks_X[$e]['C1'] = 75;
        if($cara_belajar == 'Kinestetik') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Sangat aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Ya siap' || $bersedia_lomba == 'Mungkin') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Tim') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'Rohis') {
        if($bidang == 'Keagamaan') $matriks_X[$e]['C1'] = 100;
        if($mapel == 'IPS') $matriks_X[$e]['C2'] = 75; 
        if($cara_belajar == 'Auditory' || $cara_belajar == 'Visual') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Kurang aktif' || $aktif_fisik == 'Cukup aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Mungkin' || $bersedia_lomba == 'Ya siap') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Tim' || $jenis_kegiatan == 'Keduanya') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'Karate & Taekwondo') {
        if($bidang == 'Olahraga') $matriks_X[$e]['C1'] = 100;
        if($cara_belajar == 'Kinestetik') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Sangat aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Ya siap') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Mandiri') $matriks_X[$e]['C6'] = 100;
    }
    elseif ($e == 'Bahasa') {
        if($bidang == 'Literasi & Bahasa') $matriks_X[$e]['C1'] = 100;
        if($mapel == 'Bahasa Inggris' || $mapel == 'Bahasa Indonesia') $matriks_X[$e]['C2'] = 100;
        if($cara_belajar == 'Auditory' || $cara_belajar == 'Visual') $matriks_X[$e]['C3'] = 100;
        if($aktif_fisik == 'Kurang aktif' || $aktif_fisik == 'Cukup aktif') $matriks_X[$e]['C4'] = 100;
        if($bersedia_lomba == 'Ya siap' || $bersedia_lomba == 'Mungkin') $matriks_X[$e]['C5'] = 100;
        if($jenis_kegiatan == 'Mandiri' || $jenis_kegiatan == 'Keduanya') $matriks_X[$e]['C6'] = 100;
    }
}

// NORMALISASI MATRIKS R (SAW)
$matriks_R = [];
$max_C1 = max(array_column($matriks_X, 'C1'));
$max_C2 = max(array_column($matriks_X, 'C2'));
$max_C3 = max(array_column($matriks_X, 'C3'));
$max_C4 = max(array_column($matriks_X, 'C4'));
$max_C5 = max(array_column($matriks_X, 'C5'));
$max_C6 = max(array_column($matriks_X, 'C6'));

foreach ($ekskul as $e) {
    $matriks_R[$e]['C1'] = ($max_C1 != 0) ? $matriks_X[$e]['C1'] / $max_C1 : 0;
    $matriks_R[$e]['C2'] = ($max_C2 != 0) ? $matriks_X[$e]['C2'] / $max_C2 : 0;
    $matriks_R[$e]['C3'] = ($max_C3 != 0) ? $matriks_X[$e]['C3'] / $max_C3 : 0;
    $matriks_R[$e]['C4'] = ($max_C4 != 0) ? $matriks_X[$e]['C4'] / $max_C4 : 0;
    $matriks_R[$e]['C5'] = ($max_C5 != 0) ? $matriks_X[$e]['C5'] / $max_C5 : 0;
    $matriks_R[$e]['C6'] = ($max_C6 != 0) ? $matriks_X[$e]['C6'] / $max_C6 : 0;
}

// PERANGKINGAN SAW (NILAI MURNI)
$hasil_akhir_saw = [];
foreach ($ekskul as $e) {
    $v = ($matriks_R[$e]['C1'] * $bobot['C1_Minat']) +
         ($matriks_R[$e]['C2'] * $bobot['C2_Mapel']) +
         ($matriks_R[$e]['C3'] * $bobot['C3_Belajar']) +
         ($matriks_R[$e]['C4'] * $bobot['C4_Fisik']) +
         ($matriks_R[$e]['C5'] * $bobot['C5_Lomba']) +
         ($matriks_R[$e]['C6'] * $bobot['C6_Kegiatan']);
         
    $hasil_akhir_saw[$e] = $v;
}

// Ambil Juara 1 dari perhitungan SAW murni beserta Skor Tertingginya
$dummy_saw = $hasil_akhir_saw; 
arsort($dummy_saw);
$rekomendasi_utama_saw = array_key_first($dummy_saw);
$nilai_tertinggi_saw = $dummy_saw[$rekomendasi_utama_saw];

// API MACHINE LEARNING (FASTAPI)
$data_siswa = [
    "bidang_minat"    => $bidang,
    "mata_pelajaran"  => $mapel,
    "cara_belajar"    => $cara_belajar,
    "aktif_fisik"     => $aktif_fisik,
    "kesediaan_lomba" => $bersedia_lomba,
    "jenis_kegiatan"  => $jenis_kegiatan
];

$ch = curl_init('http://127.0.0.1:8000/predict');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_siswa));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

// Tangkap JSON dari Machine Learning
$hasil_api = json_decode($response, true);
$ml_rekomendasi = $hasil_api['rekomendasi_ekskul'] ?? 'Tidak Ditemukan';
$ml_probabilitas = isset($hasil_api['probabilitas']) ? (float)$hasil_api['probabilitas'] : 0.0;

// PERHITUNGAN SKOR HYBRID
$hasil_hybrid = [];
foreach ($ekskul as $e) {
    $skor_saw = $hasil_akhir_saw[$e];
    
    // Jika nama ekskul sama dengan tebakan AI, masukkan probabilitasnya. Jika beda, beri base score 0.0
    $proba = ($e == $ml_rekomendasi) ? $ml_probabilitas : 0.0; 
    
    // RUMUS HYBRID (80% SAW, 20% ML)
    $hybrid = (0.8 * $skor_saw) + (0.2 * $proba);
    $hasil_hybrid[$e] = $hybrid;
}

// PENENTUAN REKOMENDASI HYBRID & ALTERNATIF
arsort($hasil_hybrid); // Urutkan dari yang terbesar

$urutan_ekskul = array_keys($hasil_hybrid);
$rekomendasi_utama = $urutan_ekskul[0]; // Juara 1 Hybrid
$rekomendasi_ke_2  = $urutan_ekskul[1]; // Juara 2 Hybrid
$rekomendasi_ke_3  = $urutan_ekskul[2]; // Juara 3 Hybrid

$rekomendasi_lainnya = "$rekomendasi_ke_2, $rekomendasi_ke_3"; 

$skor_hybrid_tertinggi = $hasil_hybrid[$rekomendasi_utama]; 
$skor_saw_utama = $hasil_akhir_saw[$rekomendasi_utama]; 

/// 10. SIMPAN KE DATABASE
$nama_db = $conn->real_escape_string($nama);
$mapel_db = $conn->real_escape_string($mapel);
$cara_belajar_db = $conn->real_escape_string($cara_belajar);
$rekomendasi_db = $conn->real_escape_string($rekomendasi_utama);
$lainnya_db = $conn->real_escape_string($rekomendasi_lainnya);
$skor_ml_db = $ml_probabilitas;

$sql = "INSERT INTO hasil_rekomendasi 
        (nama, kelas, jenis_kelamin, bidang_minat, mapel, cara_belajar, aktif_fisik, bersedia_lomba, rekomendasi_ekskul, rekomendasi_lainnya, skor_saw, skor_ml, skor_hybrid) 
        VALUES ('$nama_db', '$kelas', '$gender', '$bidang', '$mapel_db', '$cara_belajar_db', '$aktif_fisik', '$bersedia_lomba', '$rekomendasi_db', '$lainnya_db', '$skor_saw_utama', '$skor_ml_db', '$skor_hybrid_tertinggi')";

// Menampilkan hasil
echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Hasil Rekomendasi Ekskul</title>
    <link rel='stylesheet' href='style.css'> 
</head>
<body>
    <div class='container' style='text-align: center; max-width: 800px;'>";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Halo, $nama!</h2>";
    echo "<p class='subtitle'>Berikut adalah hasil analisismu menggunakan 2 metode perhitungan yang digabungkan:</p>";
    
    echo "<div style='display:flex; flex-wrap:wrap; justify-content:center; gap:20px; margin:30px 0;'>";
    
    // Kotak Hasil SAW
    $persen_saw_tampil = round($nilai_tertinggi_saw * 100, 2);
    
    echo "<div style='background:#f8fafc; padding:25px; border-radius:12px; width:45%; min-width:250px; border:2px solid #e2e8f0; box-shadow:0 4px 6px rgba(0,0,0,0.05);'>";
    echo "<h3 style='color:#475569; margin-top:0; font-size:16px;'>Hasil Rumus Logika (SAW)</h3>";
    echo "<h2 style='color:#27ae60; margin:10px 0;'>$rekomendasi_utama_saw</h2>";
    echo "<p style='font-size:13px; color:#64748b; margin:0;'>Skor Kecocokan SAW: <b style='color:#0f172a;'>$persen_saw_tampil%</b></p>";
    echo "</div>";

    // Kotak Hasil ML
    $persen_ml_tampil = round($ml_probabilitas * 100, 2);
    
    echo "<div style='background:#f8fafc; padding:25px; border-radius:12px; width:45%; min-width:250px; border:2px solid #e2e8f0; box-shadow:0 4px 6px rgba(0,0,0,0.05);'>";
    echo "<h3 style='color:#475569; margin-top:0; font-size:16px;'>Prediksi Kecerdasan Buatan (AI)</h3>";
    echo "<h2 style='color:#8b5cf6; margin:10px 0;'>$ml_rekomendasi</h2>";
    echo "<p style='font-size:13px; color:#64748b; margin:0;'>Tingkat Keyakinan AI: <b style='color:#0f172a;'>$persen_ml_tampil%</b></p>";
    echo "</div>";
    
    echo "</div>";

    // KESIMPULAN HYBRID
    echo "<div style='margin: 40px 0; border-top: 1px dashed #cbd5e1; padding-top: 30px;'>";
    echo "<h3 style='color:#334155;'>KESIMPULAN AKHIR (HYBRID) </h3>";
    echo "<h1 style='color:#1e40af; font-size:36px; margin:10px 0;'>$rekomendasi_utama</h1>";
    echo "<p style='color:#64748b; font-size:15px;'>Alternatif cadangan yang disarankan: <b>$rekomendasi_lainnya</b></p>";
    echo "</div>";
    
    echo "<p style='margin-top:30px; font-weight:600; text-align:left; color:#34495e;'>Detail Peringkat Persentase Keseluruhan (Hybrid):</p>";
    echo "<ul>";
    
    // Tampilkan list persentase
    foreach ($hasil_hybrid as $eks => $nilai) {
        $persentase = round($nilai * 100, 2);
        $highlight = ($eks == $rekomendasi_utama) ? "style='border-left: 4px solid #27ae60; background-color: #e8f8f5;'" : "";
        echo "<li $highlight>";
        echo "<span>$eks</span>";
        echo "<span style='font-weight:700;'>$persentase%</span>";
        echo "</li>";
    }
    echo "</ul>";
    
    echo "<button class='btn' onclick='window.location.href=\"index.html\"' style='margin-top:30px;'>Isi Form Kembali</button>";
} else {
    echo "<h2 style='color:red;'>Terjadi Kesalahan!</h2>";
    echo "<p>Gagal menyimpan data ke database: " . $conn->error . "</p>";
    echo "<button class='btn' onclick='window.history.back()'>Kembali</button>";
}

echo "    </div>
</body>
</html>";

$conn->close();
?>