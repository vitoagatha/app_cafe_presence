<?php

session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'manajer'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Data Lokasi Presensi";

include('../layout/header.php');

require_once('../../config.php');

$result =mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY id DESC");

?>

 <!-- Page body -->
 <div class="page-body">
    <div class="container-xl">
    <a href="<?= base_url('manajer/data_lokasi_presensi/tambah.php') ?>" class="btn btn-primary"><span class="text"><i class="fa-solid fa-plus"></i> Tambah Data</span></a>

    <table class="table table-bordered mt-3">
        <tr class="text-center">
            <th>No</th>
            <th>Nama Lokasi</th>
            <th>Tipe Lokasi</th>
            <th>Latitude/Longitude</th>
            <th>Radius</th>
            <th>Aksi</th>
        </tr>

        <?php if(mysqli_num_rows($result) === 0){ ?>
            <tr>
                <td colspan="6">Data Kosong, silahkan tambahkan data baru</td>
            </tr>
        <?php } else { ?>
            <?php $no = 1;
            while ($lokasi = mysqli_fetch_array($result)) : ?>

            <tr class="text-center">
                <td><?= $no++ ?></td>
                <td><?= $lokasi['nama_lokasi']?></td>
                <td><?= $lokasi['alamat_lokasi']?></td>
                <td><?= $lokasi['latitude'] . '/' . $lokasi['longitude']?></td>
                <td><?= $lokasi['radius']?></td>
                <td>
                    <a href="<?= base_url('manajer/data_lokasi_presensi/detail.php?id=' . $lokasi
                    ['id'])?>" class="badge bg-primary">Detail</a>

                    <a href="<?= base_url('manajer/data_lokasi_presensi/edit.php?id=' . $lokasi
                    ['id'])?>" class="badge bg-primary">Edit</a>

                    <a href="<?= base_url('manajer/data_lokasi_presensi/hapus.php?id=' . $lokasi
                    ['id'])?>" class="badge bg-danger tombol-hapus">Hapus</a>
                </td>
            </tr>
            <?php endwhile ?>
        <?php } ?>
    </table>

    </div>
</div>

<?php include('../layout/footer.php') ?>