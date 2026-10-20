<?php
ob_start();
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'pegawai') {
  http_response_code(401);
  echo json_encode(['status' => 'error', 'pesan' => 'Sesi habis, silakan login ulang']);
  exit;
}

include_once("../../config.php");

$file_foto     = $_POST['photo'] ?? '';
$id_pegawai    = $_SESSION['id']; // jangan percaya id dari browser
$tanggal_masuk = $_POST['tanggal_masuk'] ?? date('Y-m-d');
$jam_masuk     = $_POST['jam_masuk'] ?? date('H:i:s');

// Cegah presensi ganda di hari yang sama
try {
$cek = mysqli_prepare($connection, "SELECT id FROM presensi WHERE id_pegawai = ? AND tanggal_masuk = ?");
mysqli_stmt_bind_param($cek, "ss", $id_pegawai, $tanggal_masuk);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);
if (mysqli_stmt_num_rows($cek) > 0) {
  echo json_encode(['status' => 'error', 'pesan' => 'Anda sudah presensi masuk hari ini']);
  exit;
}

// Decode foto (prefix aslinya "data:image/jpeg;base64," -> pakai titik koma)
$foto = preg_replace('#^data:image/\w+;base64,#i', '', $file_foto);
$foto = str_replace(' ', '+', $foto);
$data = base64_decode($foto, true);

if ($data === false || $data === '') {
  echo json_encode(['status' => 'error', 'pesan' => 'Data foto tidak valid']);
  exit;
}

$file      = 'masuk_' . $id_pegawai . '_' . date('Y-m-d') . '.jpg';
$nama_file = __DIR__ . '/foto/' . $file;

if (!is_dir(__DIR__ . '/foto')) {
  mkdir(__DIR__ . '/foto', 0775, true);
}
if (file_put_contents($nama_file, $data) === false) {
  echo json_encode(['status' => 'error', 'pesan' => 'Gagal menyimpan foto (cek izin folder foto)']);
  exit;
}

// kolom keluar NOT NULL tanpa default di tabel -> isi eksplisit (anti error strict mode)
$stmt = mysqli_prepare($connection, "INSERT INTO presensi (id_pegawai, tanggal_masuk, jam_masuk, foto_masuk, tanggal_keluar, jam_keluar, foto_keluar) VALUES (?, ?, ?, ?, '0000-00-00', '00:00:00', '')");
mysqli_stmt_bind_param($stmt, "ssss", $id_pegawai, $tanggal_masuk, $jam_masuk, $file);

if (mysqli_stmt_execute($stmt)) {
  $_SESSION['berhasil'] = "Presensi masuk berhasil";
  echo json_encode(['status' => 'ok']);
} else {
  // pesan error DB ditampilkan agar mudah dilacak saat development
  echo json_encode(['status' => 'error', 'pesan' => 'Gagal menyimpan ke database: ' . mysqli_error($connection)]);
}
} catch (Throwable $e) {
  // PHP 8.1+ melempar exception saat query gagal; tangkap supaya browser dapat pesan jelas, bukan HTTP 500
  http_response_code(500);
  echo json_encode(['status' => 'error', 'pesan' => 'Error server: ' . $e->getMessage()]);
}
