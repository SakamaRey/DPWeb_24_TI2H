<?php
session_start();
session_destroy(); // Mengosongkan seluruh data session
header('Location: index.php'); // Kembali ke halaman awal
exit;
?>