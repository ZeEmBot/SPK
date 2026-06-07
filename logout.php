<?php
session_start();
session_unset();
session_destroy();

header("Location: login_admin.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Keluar...</title>
    <script>
        window.location.href = "login_admin.php";
    </script>
</head>
<body style="background-color: #f4f6f9; text-align: center; font-family: sans-serif; padding-top: 50px;">
    <p>Sedang mengeluarkan akun... Jika halaman tidak berpindah, <a href="login_admin.php">klik di sini</a>.</p>
</body>
</html>