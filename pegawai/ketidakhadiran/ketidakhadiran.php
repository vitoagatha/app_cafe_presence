<?php 
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'pegawai') {
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = 'Ketidakhadiran';
include('../layout/header.php');
include_once("../../config.php");

$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id_pegawai = '$id' ORDER BY id DESC");


?>

<div class="page-body">
  <div class="container-xl">
    <a href="<?= base_url('pegawai/ketidakhadiran/pengajuan_ketidakhadiran.php') ?>" class="btn btn-primary">Tambah Data</a>
    <table class="table table-bordered mt-2">
        <tr class="text-center">
            <th>No.</th>
            <th>Tanggal</th>
            <th>keterangan</th>
            <th>Deskripsi</th>
            <th>File</th>
            <th>Status Pengajuan</th>
            <th>Aksi</th>
        </tr>

        <?php if(mysqli_num_rows($result) === 0) { ?>
            <tr>
                <td colspan="7">Data ketidakhadiran kosong.</td>
            </tr>
            <?php } else { ?>
                <?php $no = 1;
                while($data = mysqli_fetch_array($result)) : ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= date('d F Y', strtotime($data['tanggal'])) ?></td>
                    <td class="text-center"><?= $data['keterangan'] ?></td>
                    <td class="text-center"><?= $data['deskripsi'] ?></td>
                    <td class="text-center">
                        <a target="_blank" href="<?= base_url('assets/file_ketidakhadiran/' . $data['file']) ?>" class="badge bg-primary">Lihat Bukti</a>
                    </td>
                    <td><?= $data['status_pengajuan'] ?></td>
                    <td class="text-center">
                        <a href="edit.php?id=<?= $data['id'] ?>" class="badge bg-success">Update</a>
                        <a href="hapus.php?id=<?= $data['id'] ?>" class="badge bg-danger tombol-hapus" >Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php } ?>
    </table>

  </div>
</div>

<?php include('../layout/footer.php') ?>