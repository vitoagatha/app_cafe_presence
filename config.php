<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "cafe_presense";

mysqli_report(MYSQLI_REPORT_OFF); // koneksi gagal -> ditangani manual di bawah, bukan exception

$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$connection) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Aplikasi ini memakai nilai '0000-00-00' untuk "belum presensi keluar".
// MySQL 8 (bawaan Laragon) menolaknya lewat NO_ZERO_DATE, MariaDB/XAMPP tidak.
// Longgarkan hanya untuk sesi koneksi ini.
mysqli_query($connection, "SET SESSION sql_mode = REPLACE(REPLACE(@@sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

function base_url($url = null)
{
    // Hitung folder project relatif terhadap document root server, jadi benar untuk:
    //  - http://localhost/app_cafe_presence/        (document root = C:\laragon\www)
    //  - http://app_cafe_presence.test/             (document root = folder project, Laragon auto virtual host)
    $docroot = rtrim(str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $root    = str_replace('\\', '/', __DIR__);

    if ($docroot !== '' && stripos($root, $docroot) === 0) {
        $path = substr($root, strlen($docroot)); // '' atau '/app_cafe_presence'
    } else {
        $path = '/app_cafe_presence'; // cadangan
    }

    $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base_url = $scheme . '://' . $host . $path;

    if ($url != null) {
        return $base_url . '/' . $url;
    }
    return $base_url;
}
