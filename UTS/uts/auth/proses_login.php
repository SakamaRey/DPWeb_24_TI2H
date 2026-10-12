<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Inisialisasi counter di session jika belum ada
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Cek jika sudah mencapai batas maksimal (misal: 3 kali)
if ($_SESSION['login_attempts'] >= 3) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terlalu banyak percobaan gagal. Akun dikunci sementara.'];
    header('Location: login.php');
    exit;
}

if ($user && password_verify($password, $user['password'])) {
    unset($_SESSION['login_attempts']);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    // Jika checkbox Remember Me dicentang
if (isset($_POST['remember'])) {
    // Simpan cookie user_id yang aktif selama 30 hari (30 * 24 * 60 * 60 detik)
    setcookie('simpus_user', $user['id'], time() + (86400 * 30), "/");
}
} else {
    // Tambah counter jika gagal
    $_SESSION['login_attempts']++;
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah. (Percobaan ke-' . $_SESSION['login_attempts'] . ')'];
    header('Location: login.php');
    exit;
}