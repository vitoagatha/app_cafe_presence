<?php 

session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'manajer'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Tambah Data Jabatan";

include('../layout/header.php');

require_once('../../config.php');

if(isset($_POST['submit'])){
    $jabatan = htmlspecialchars($_POST['jabatan']);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($jabatan)){
            $pesan_kesalahan =  "Jabatan wajib diisi";
        }

        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = $pesan_kesalahan;
        }else{
            $result = mysqli_query($connection, "INSERT INTO jabatan(jabatan) VALUES('$jabatan')");

            $_SESSION['berhasil'] = "Data berhasil disimpan";
            header("Location: jabatan.php");
            exit;
        }
    }

}
?>

        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">

            <div class="card col-md-6">
                <div class="card-body">
                    <form action="<?= base_url('manajer/data_jabatan/tambah.php') ?>" method="POST">
                        <div class="mb-3">
                            <label for="">Nama jabatan</label>
                            <input type="text" class="form-control" name="jabatan">
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>

          </div>
        </div>

<?php include('../layout/footer.php') ?>