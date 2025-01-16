<?php 

session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'manajer'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Data ketidakhadiran";

include('../layout/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran ORDER BY id DESC");


?>

<div class="page-body">
    <div class="container-xl">

    <table class="table table-bordered mt-2">
        <tr class="text-center">
            <th>No.</th>
            <th>Tanggal</th>
            <th>keterangan</th>
            <th>Deskripsi</th>
            <th>File</th>
            <th>Status Pengajuan</th>
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
                    <td class="text-center">
                        <?php if($data['status_pengajuan'] == 'PENDING') : ?>
                            <a class="badge bg-warning" href="<?= base_url('manajer/data_ketidakhadiran/detail.php?id=' . $data['id']) ?>">PENDING</a>
                            <?php elseif($data['status_pengajuan'] == 'REJECTED') : ?> 
                                <a class="badge bg-danger" href="<?= base_url('manajer/data_ketidakhadiran/detail.php?id=' . $data['id']) ?>">REJECTED</a>
                            <?PHP else : ?>
                                <a class="badge bg-success" href="<?= base_url('manajer/data_ketidakhadiran/detail.php?id=' . $data['id']) ?>">APPROVED</a>
                            <?PHP endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php } ?>
    </table>
    </div>
</div>

<?php include('../layout/footer.php') ?>