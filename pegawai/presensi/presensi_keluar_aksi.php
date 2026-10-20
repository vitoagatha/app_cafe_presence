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

$file_foto      = $_POST['photo'] ?? '';
$id_presensi    = $_POST['id'] ?? '';
$tanggal_keluar = $_POST['tanggal_keluar'] ?? date('Y-m-d');
$jam_keluar     = $_POST['jam_keluar'] ?? date('H:i:s');

$foto = preg_replace('#^data:image/\w+;base64,#i', '', $file_foto);
$foto = str_replace(' ', '+', $foto);
$data = base64_decode($foto, true);

if ($data === false || $data === '') {
  echo json_encode(['status' => 'error', 'pesan' => 'Data foto tidak valid']);
  exit;
}

$file      = 'keluar_' . $_SESSION['id'] . '_' . date('Y-m-d') . '.jpg';
$nama_file = __DIR__ . '/foto/' . $file;

if (!is_dir(__DIR__ . '/foto')) {
  mkdir(__DIR__ . '/foto', 0775, true);
}
if (file_put_contents($nama_file, $data) === false) {
  echo json_encode(['status' => 'error', 'pesan' => 'Gagal menyimpan foto (cek izin folder foto)']);
  exit;
}

// id_pegawai ikut di WHERE supaya pegawai tidak bisa mengubah presensi orang lain
try {
$stmt = mysqli_prepare($connection, "UPDATE presensi SET tanggal_keluar = ?, jam_keluar = ?, foto_keluar = ? WHERE id = ? AND id_pegawai = ?");
mysqli_stmt_bind_param($stmt, "sssss", $tanggal_keluar, $jam_keluar, $file, $id_presensi, $_SESSION['id']);

if (mysqli_stmt_execute($stmt)) {
  $_SESSION['berhasil'] = "Presensi keluar berhasil";
  echo json_encode(['status' => 'ok']);
} else {
  echo json_encode(['status' => 'error', 'pesan' => 'Gagal menyimpan ke database: ' . mysqli_error($connection)]);
}
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['status' => 'error', 'pesan' => 'Error server: ' . $e->getMessage()]);
}
