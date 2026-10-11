<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Session</title>
</head>
<body style="padding: 2rem; font-family: sans-serif;">
    <h2>Inspeksi $_SESSION Saat Ini:</h2>
    <pre style="background: #eee; padding: 1rem;"><?php print_r($_SESSION); ?></pre>
    <br>
    <a href="index.php">Kembali ke Beranda</a>
</body>
</html>