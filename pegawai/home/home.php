<?php

session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'pegawai') {
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = 'Home';
include('../layout/header.php');
include_once("../../config.php");

$lokasi_presensi = $_SESSION['lokasi_presensi'];
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

while ($lokasi = mysqli_fetch_array($result)) {
  $latitude_kantor = $lokasi['latitude'];
  $longitude_kantor = $lokasi['longitude'];
  $radius = $lokasi['radius'];
  $zona_waktu = $lokasi['zona_waktu'];
  $jam_pulang = $lokasi['jam_pulang'];
}

if ($zona_waktu == 'WIB') {
  date_default_timezone_set('Asia/jakarta');
} elseif ($zona_waktu == 'WITA') {
  date_default_timezone_set('Asia/Makassar');
} elseif ($zona_waktu == 'WIT') {
  date_default_timezone_set('Asia/Jayapura');
}

?>

<style>
  .parent_date {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    font-size: 20px;
    text-align: center;
    justify-content: center;
  }

  .parent_clock {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    font-size: 20px;
    text-align: center;
    font-weight: bold;
    justify-content: center;
  }
</style>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-4">
        <div class="card text-center h-100">
          <div class="card-header">Presensi Masuk</div>
          <div class="card-body">

            <?php
            $id_pegawai = $_SESSION['id'];
            $tanggaL_hari_ini = date('Y-m-d');

            $cek_presensi_masuk = mysqli_query($connection, "SELECT * FROM presensi WHERE id_pegawai = '$id_pegawai' AND tanggal_masuk = '$tanggaL_hari_ini'");

            ?>
            <?php if (mysqli_num_rows($cek_presensi_masuk) === 0) { ?>
              <div class="parent_date">
                <div id="tanggal_masuk"></div>
                <div class="ms-2"></div>
                <div id="bulan_masuk"></div>
                <div class="ms-2"></div>
                <div id="tahun_masuk"></div>
              </div>
              <div class="parent_clock">
                <div id="jam_masuk"></div>
                <div>:</div>
                <div id="menit_masuk"></div>
                <div>:</div>
                <div id="detik_masuk"></div>
              </div>

              <form method="POST" action="<?= base_url('pegawai/presensi/presensi_masuk.php') ?>">
                <input type="hidden" name="latitude_pegawai" id="latitude_pegawai">
                <input type="hidden" name="longitude_pegawai" id="longitude_pegawai">
                <input type="hidden" value="<?= $latitude_kantor ?>" name="latitude_kantor">
                <input type="hidden" value="<?= $longitude_kantor ?>" name="longitude_kantor">
                <input type="hidden" value="<?= $radius ?>" name="radius">
                <input type="hidden" value="<?= $zona_waktu ?>" name="zona_waktu">
                <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_masuk">
                <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_masuk">

                <button type="submit" name="tombol_masuk" class="btn btn-primary mt-3">Masuk</button>
              </form>

            <?php } else { ?>
              <i class="fa-regular fa-circle-check fa-4x text-success"></i>
              <h4 class="my-3">Anda telah melakukan <br> presensi masuk</h4>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="col-md-2"></div>
        <div class="card text-center h-100">
          <div class="card-header">Presensi Keluar</div>
          <div class="card-body">

            <?php
            $ambil_data_presensi = mysqli_query($connection, "SELECT * FROM  presensi WHERE id_pegawai = '$id_pegawai' AND tanggal_masuk = '$tanggaL_hari_ini'");
            ?>

            <?php $waktu_sekarang = date('H:i:s');

            if (strtotime($waktu_sekarang) <= strtotime($jam_pulang)) { ?>
              <i class="fa-regular fa-circle-xmark fa-4x text-danger"></i>
              <h4 class="my-3">Belum waktunya presensi pulang</h4>

            <?php } elseif (
              strtotime($waktu_sekarang) >= strtotime($jam_pulang) &&
              mysqli_num_rows($ambil_data_presensi) == 0
            ) { ?>
              <i class="fa-regular fa-circle-xmark fa-4x text-danger"></i>
              <h4 class="my-3">Silahkan melakukan presensi masuk <br> terlebih dahulu</h4>

            <?php } else { ?>

              <?php while ($cek_presensi_keluar = mysqli_fetch_array($ambil_data_presensi)) { ?>

                <?php if (($cek_presensi_keluar['tanggal_masuk']) && $cek_presensi_keluar['tanggal_keluar'] == '0000-00-00') { ?>

                  <div class="parent_date">
                    <div id="tanggal_keluar"></div>
                    <div class="ms-2"></div>
                    <div id="bulan_keluar"></div>
                    <div class="ms-2"></div>
                    <div id="tahun_keluar"></div>
                  </div>
                  <div class="parent_clock">
                    <div id="jam_keluar"></div>
                    <div>:</div>
                    <div id="menit_keluar"></div>
                    <div>:</div>
                    <div id="detik_keluar"></div>
                  </div>

                  <form method="POST" action="<?= base_url('pegawai/presensi/presensi_keluar.php') ?>">
                    <input type="hidden" name="id" value="<?= $cek_presensi_keluar['id'] ?>">
                    <input type="hidden" name="latitude_pegawai" id="latitude_pegawai">
                    <input type="hidden" name="longitude_pegawai" id="longitude_pegawai">
                    <input type="hidden" value="<?= $latitude_kantor ?>" name="latitude_kantor">
                    <input type="hidden" value="<?= $longitude_kantor ?>" name="longitude_kantor">
                    <input type="hidden" value="<?= $radius ?>" name="radius">
                    <input type="hidden" value="<?= $zona_waktu ?>" name="zona_waktu">
                    <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_keluar">
                    <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_keluar">
                    <button type="submit" name="tombol-keluar" class="btn btn-danger mt-3">Keluar</button>
                  </form>

                <?php } else { ?>
                  <i class="fa-regular fa-circle-check fa-4x text-success"></i>
                  <h4 class="my-3">Anda telah melakukan <br> presensi keluar</h4>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // waktu masuk
  window.setTimeout("waktuMasuk()", 1000);
  nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

  function waktuMasuk() {
    const waktu = new Date();

    setTimeout("waktuMasuk()", 1000);
    document.getElementById("tanggal_masuk").innerHTML = waktu.getDate();
    document.getElementById("bulan_masuk").innerHTML = nama_bulan[waktu.getMonth()];
    document.getElementById("tahun_masuk").innerHTML = waktu.getFullYear();
    document.getElementById("jam_masuk").innerHTML = waktu.getHours();
    document.getElementById("menit_masuk").innerHTML = waktu.getMinutes();
    document.getElementById("detik_masuk").innerHTML = waktu.getSeconds();
  }

  // waktu keluar
  window.setTimeout("waktuKeluar()", 1000);
  nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

  function waktuKeluar() {
    const waktu = new Date();

    setTimeout("waktuKeluar()", 1000);
    document.getElementById("tanggal_keluar").innerHTML = waktu.getDate();
    document.getElementById("bulan_keluar").innerHTML = nama_bulan[waktu.getMonth()];
    document.getElementById("tahun_keluar").innerHTML = waktu.getFullYear();
    document.getElementById("jam_keluar").innerHTML = waktu.getHours();
    document.getElementById("menit_keluar").innerHTML = waktu.getMinutes();
    document.getElementById("detik_keluar").innerHTML = waktu.getSeconds();
  }

  getLocation();

  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else {
      alert("Browser anda tidak mendukung")
    }
  }

  function showPosition(position) {
    $('#latitude_pegawai').val(position.coords.latitude);
    $('#longitude_pegawai').val(position.coords.longitude);
  }
</script>

<?php include('../layout/footer.php') ?>