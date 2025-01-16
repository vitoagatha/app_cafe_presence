<?php 

session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'manajer'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Data Jabatan";

include('../layout/header.php');

require_once('../../config.php');

$result =mysqli_query($connection, "SELECT * FROM jabatan ORDER BY id DESC");
?>

        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
          <a href="<?= base_url('manajer/data_jabatan/tambah.php') ?>" class="btn btn-primary"><span class="text"><i class="fa-solid fa-plus"></i> Tambah Data</span></a>
            <div class="row row-deck row-cards mt-2">

                <table class="table table-bordered">
                    <tr class="text-center">
                        <th>No.</th>
                        <th>Nama Jabatan</th>
                        <th>Aksi</th>
                    </tr>

                    <?php if(mysqli_num_rows($result) === 0) : ?>
                        <tr>
                            <td colspan="3">Data masih kosong, silahkan tambah data baru</td>
                        </tr>
                        <?php else : ?>
                    <?php $no = 1; 
                    while ($jabatan = mysqli_fetch_array($result)) : ?>

                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $jabatan['jabatan'] ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('manajer/data_jabatan/edit.php?id='. $jabatan['id']) ?>" class="badge bg-primary"> Edit</a>
                                <a href="<?= base_url('manajer/data_jabatan/hapus.php?id='. $jabatan['id']) ?>" class="badge bg-danger tombol-hapus"> Hapus</a>
                            </td>
                        </tr>

                    <?php endwhile ?>

                    <?php endif ?>
                </table>
            </div>
          </div>
        </div>

<?php include('../layout/footer.php') ?>