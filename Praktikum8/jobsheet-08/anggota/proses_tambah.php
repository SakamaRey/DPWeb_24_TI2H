<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

try {
    // Siapkan query
    $stmt = $pdo->prepare(
        "INSERT INTO anggota (nama, no_anggota, alamat, no_hp) 
         VALUES (:nama, :no_anggota, :alamat, :no_hp) 
         RETURNING id"
    );

    // Jalankan eksekusi
    $stmt->execute([
        'nama' => $nama,
        'no_anggota' => $noAnggota,
        'alamat' => $alamat,
        'no_hp' => $noHp
    ]);

    // Jika sukses, beri flash message hijau lalu redirect
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
    header('Location: list.php');
    exit;

} catch (PDOException $e) {
    // Kode '23505' adalah kode spesifik dari PostgreSQL untuk error "unique_violation"
    if ($e->getCode() == '23505') {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'No. Anggota sudah terdaftar, gunakan nomor lain!'];
    } else {
        // Jika errornya dari hal lain
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal menyimpan ke database: ' . $e->getMessage()];
    }
    
    // Kembalikan ke halaman form tambah
    header('Location: tambah.php');
    exit;
}