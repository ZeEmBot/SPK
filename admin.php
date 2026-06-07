<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login_admin.php");
    exit;
}

$host = "localhost";
$user = "root";       
$pass = "";           
$db   = "db_spk_ekskul";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT * FROM hasil_rekomendasi ORDER BY waktu_input DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SPK Ekskul</title>
    <link rel="stylesheet" href="styleAdmin.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            SPK Panel Guru
        </div>
        <a href="logout.php" class="btn-logout">Logout</a>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1>Data Rekomendasi Siswa</h1>
                <p>Menampilkan seluruh hasil perhitungan kuisioner ekstrakurikuler siswa (Hybrid SAW + ML).</p>
            </div>
            <div style="background: #e2e8f0; padding: 8px 15px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                Total Data: <?php echo $result ? $result->num_rows : 0; ?>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>L/P</th>
                        <th>Hasil Akhir (Hybrid)</th>
                        <th>Rekomendasi Lainnya</th>
                        <th>Skor SAW</th>
                        <th>Skor ML</th>
                        <th>Skor Hybrid</th>
                        <th>Waktu Submit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result && $result->num_rows > 0) {
                        $no = 1;
                        while($row = $result->fetch_assoc()) {
                            $tanggal = date('d M Y, H:i', strtotime($row['waktu_input']));
                            
                            // Persiapan Data Skor
                            $skor_saw = isset($row['skor_saw']) ? $row['skor_saw'] : 0;
                            $persentase_saw = round($skor_saw * 100, 2) . "%";
                            
                            $skor_ml = isset($row['skor_ml']) ? $row['skor_ml'] : 0;
                            $persentase_ml = round($skor_ml * 100, 2) . "%";
                            
                            $skor_hybrid = isset($row['skor_hybrid']) ? $row['skor_hybrid'] : 0;
                            $persentase_hybrid = round($skor_hybrid * 100, 2) . "%";
                            
                            $alternatif = isset($row['rekomendasi_lainnya']) ? $row['rekomendasi_lainnya'] : '-';
                            
                            echo "<tr>";
                            echo "<td style='text-align:center;'>".$no++."</td>";
                            echo "<td style='font-weight:500; color:#0f172a;'>".htmlspecialchars($row['nama'] ?? '-')."</td>";
                            echo "<td>".htmlspecialchars($row['kelas'] ?? '-')."</td>";
                            echo "<td>".htmlspecialchars($row['jenis_kelamin'] ?? '-')."</td>";
                            echo "<td><span style='background:#10b981; color:white; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:12px;'>".htmlspecialchars($row['rekomendasi_ekskul'] ?? '-')."</span></td>";
                            echo "<td style='color:#64748b; font-size: 13px;'>".htmlspecialchars($alternatif ?? '-')."</td>";
                            
                            // Menampilkan Skor
                            echo "<td style='color:#059669;'>".$persentase_saw."</td>";
                            echo "<td><span style='color:#8b5cf6; font-weight:600;'>".$persentase_ml."</span></td>";
                            echo "<td style='font-weight:700; color:#1e40af;'>".$persentase_hybrid."</td>";
                            
                            echo "<td style='color:#64748b; font-size: 13px;'>".$tanggal."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align:center; padding: 40px; color:#64748b;'>Belum ada data yang masuk dari siswa.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>