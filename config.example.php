<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = ""; // isi sesuai lingkungan lokal; salin file ini menjadi config.php
$db_name = "cafe_presense";

mysqli_report(MYSQLI_REPORT_OFF);

$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$connection) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

mysqli_query($connection, "SET SESSION sql_mode = REPLACE(REPLACE(@@sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

function base_url($url = null)
{
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