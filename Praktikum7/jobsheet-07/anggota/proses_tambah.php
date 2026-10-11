<?php
session_start();

// Menerima Data Form
$no_anggota = trim($_POST['no_anggota'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');

$errors = [];

// Validasi
if ($no_anggota === '') {
    $errors[] = "Nomor Anggota wajib diisi.";
}
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($no_hp !== '' && !is_numeric($no_hp)) {
    $errors[] = "Nomor HP harus berupa angka.";
}

// Redirect dengan Flash Message jika Error
if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

// Menyiapkan Session
if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

// Menyimpan ke Session
$_SESSION['anggota'][] = [
    'no_anggota' => $no_anggota,
    'nama' => $nama,
    'alamat' => $alamat,
    'no_hp' => $no_hp
];

// Redirect dengan Flash Message Sukses
$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;
?>