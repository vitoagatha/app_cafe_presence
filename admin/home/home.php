<?php

session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'admin') {
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Home";
include('../layout/header.php');

$pegawai = mysqli_query($connection, "SELECT pegawai.*, users.status FROM pegawai JOIN users ON pegawai.id = users.id_pegawai WHERE status = 'Aktif'");
$total_pegawai_aktif = mysqli_num_rows($pegawai);

$pegawai_hadir = mysqli_query($connection, "SELECT DISTINCT presensi.id_pegawai 
    FROM presensi JOIN pegawai ON presensi.id_pegawai = pegawai.id");
$total_pegawai_hadir = mysqli_num_rows($pegawai_hadir);

$pegawai_sakit_izin_cuti = mysqli_query($connection, "SELECT DISTINCT ketidakhadiran.id_pegawai 
    FROM ketidakhadiran WHERE ketidakhadiran.keterangan IN ('Sakit', 'Izin', 'Cuti') AND ketidakhadiran.status_pengajuan = 'APPROVED'");
$total_pegawai_sakit_izin_cuti = mysqli_num_rows($pegawai_sakit_izin_cuti);

$pegawai_alpa = mysqli_query($connection, "SELECT pegawai.id FROM pegawai
    LEFT JOIN presensi ON pegawai.id = presensi.id_pegawai LEFT JOIN ketidakhadiran ON pegawai.id = ketidakhadiran.id_pegawai 
    AND ketidakhadiran.status_pengajuan = 'APPROVED' WHERE presensi.id_pegawai IS NULL AND (ketidakhadiran.id_pegawai IS NULL 
    OR ketidakhadiran.keterangan NOT IN ('Sakit', 'Izin', 'Cuti'))");
$total_pegawai_alpa = mysqli_num_rows($pegawai_alpa);

?>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="row row-deck row-cards">

      <div class="col-12">
        <div class="row row-cards">
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Total Pegawai Aktif
                    </div>
                    <div class="text-secondary">
                      <?= $total_pegawai_aktif . ' Pegawai' ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-green text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-check">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                        <path d="M15 19l2 2l4 -4" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Jumlah Hadir
                    </div>
                    <div class="text-secondary">
                      <?= $total_pegawai_hadir . ' Pegawai' ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-twitter text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/brand-twitter -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-x">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                        <path d="M22 22l-5 -5" />
                        <path d="M17 22l5 -5" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Jumlah Alpa
                    </div>
                    <div class="text-secondary">
                      <?= $total_pegawai_alpa . ' Pegawai' ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-facebook text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/brand-facebook -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-question">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                        <path d="M19 22v.01" />
                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                      </svg>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      Jumlah Sakit, Izin dan Cuti
                    </div>
                    <div class="text-secondary">
                      <?= $total_pegawai_sakit_izin_cuti . ' Pegawai' ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<?php include('../layout/footer.php') ?>