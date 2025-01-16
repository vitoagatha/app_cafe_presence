<?php

session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'manajer'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak");
}

$judul = "Edit Data Pegawai";

include('../layout/header.php');

require_once('../../config.php');

if(isset($_POST['edit'])){

    $id = $_POST['id'];
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_handphone = htmlspecialchars($_POST['no_handphone']);
    $jabatan = htmlspecialchars($_POST['jabatan']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);

    if(empty($_POST['password'])){
        $password = $_POST['password_lama'];
    }else{
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if($_FILES['foto_baru']['error'] === 4){
        $nama_file = $_POST['foto_lama'];
    }else{
        if(isset($_FILES['foto_baru'])){
            $file = $_FILES['foto_baru'];
            $nama_file = $file['name'];
            $file_tmp = $file['tmp_name'];
            $ukuran_file = $file['size'];
            $file_direktori = "../../assets/img/foto_pegawai/" . $nama_file;
    
            $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
            $ektensi_diizinkan = ["jpg", "png", "jpeg"];
            $max_ukuran_file = 10 * 1024 * 1024;
    
            move_uploaded_file($file_tmp, $file_direktori);
        }
    }
    

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($nama)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Nama wajib diisi";
        }
        if(empty($jenis_kelamin)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Jenis Kelamin wajib diisi";
        }
        if(empty($alamat)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Alamat wajib diisi";
        }
        if(empty($no_handphone)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>No Handphone wajib diisi";
        }
        if(empty($jabatan)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Jabatan wajib diisi";
        }
        if(empty($username)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Username wajib diisi";
        }
        if(empty($role)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Role wajib diisi";
        }
        if(empty($status)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Status wajib diisi";
        }
        if(empty($lokasi_presensi)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Lokasi presensi wajib diisi";
        }

        if(empty($password)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Password wajib diisi";
        }
        if($_POST['password'] != $_POST['ulangi_password']){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Password tidak cocok";
        }

        if($_FILES['foto_baru']['error'] != 4){

            if(!in_array(strtolower($ambil_ekstensi), $ektensi_diizinkan)){
                $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Hanya file JPG, JPEG, dan PNG yang diizinkan";
            }

            if($ukuran_file > $max_ukuran_file){
                $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Ukuran file melebihi: 10Mb";
            }
        }

        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = implode("<br>" , $pesan_kesalahan);
        }else{
            $pegawai = mysqli_query($connection, "UPDATE pegawai SET
                nama = '$nama',
                jenis_kelamin = '$jenis_kelamin',
                alamat = '$alamat',
                no_handphone = '$no_handphone',
                jabatan = '$jabatan',
                lokasi_presensi = '$lokasi_presensi',
                foto = '$nama_file'
                WHERE id = '$id'
            ");

            $user = mysqli_query($connection, "UPDATE users SET 
                username = '$username',
                password = '$password',
                status = '$status',
                role = '$role'
                WHERE id = '$id'
            ");

            $_SESSION['berhasil'] = 'Data berhasil diupdate';
            header("Location: pegawai.php");
            exit;
        }
    }

}
$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result =mysqli_query($connection, "SELECT users.id_pegawai, users.username, users.password, users.status,
    users.role, pegawai.* FROM users JOIN pegawai ON users.id_pegawai = pegawai.id WHERE pegawai.id = $id");

while($pegawai = mysqli_fetch_array($result)){
    $nama = $pegawai['nama'];
    $jenis_kelamin = $pegawai['jenis_kelamin'];
    $alamat = $pegawai['alamat'];
    $no_handphone = $pegawai['no_handphone'];
    $jabatan = $pegawai['jabatan'];
    $username = $pegawai['username'];
    $password = $pegawai['password'];
    $status = $pegawai['status'];
    $role = $pegawai['role'];
    $lokasi_presensi = $pegawai['lokasi_presensi'];
    $foto = $pegawai['foto'];
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?= base_url('manajer/data_pegawai/edit.php') ?>" method="POST" enctype="multipart/form-data">

                   

                    <div class="mb-3">
                        <label for="text">Nama</label>
                        <input type="text" class="form-control" name="nama" value="<?= $nama ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option <?php if($jenis_kelamin == 'Laki-laki'){
                                echo 'Selected';
                            } ?> value="Laki-laki">Laki-laki</option>
                            <option <?php if($jenis_kelamin == 'Perempuan'){
                                echo 'Selected';
                            } ?>value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Alamat</label>
                        <input type="text" class="form-control" name="alamat" value="<?= $alamat ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">No Handhphone</label>
                        <input type="text" class="form-control" name="no_handphone" value="<?= $no_handphone ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">Jabatan</label>
                        <select name="jabatan" class="form-control">
                            <option value="">Pilih Jabatan</option>

                            <?php 
                            $ambil_jabatan = mysqli_query($connection, "SELECT * FROM jabatan ORDER BY jabatan ASC");

                            while($row = mysqli_fetch_assoc($ambil_jabatan)){
                                $nama_jabatan = $row['jabatan'];

                                if($jabatan == $nama_jabatan){
                                    echo '<option value="' . $nama_jabatan . '" selected="selected">' . $nama_jabatan . '</option>';
                                } else {
                                    echo '<option value="' .$nama_jabatan . '">' .$nama_jabatan . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Pilih Status</option>
                            <option <?php if($status == 'Aktif'){
                                echo 'Selected';
                            } ?> value="Aktif">Aktif</option>
                            <option <?php if($status == 'Tidak Aktif'){
                                echo 'Selected';
                            } ?>value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                        
                    <div class="mb-3">
                        <label for="text">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= $username ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">Password</label>
                        <input type="hidden" value="<?= $password; ?>" name="password_lama">
                        <input type="text" class="form-control" name="password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="text">Ulangi Password</label>
                        <input type="text" class="form-control" name="ulangi_password">
                    </div>

                    <div class="mb-3">
                        <label for="text">Role</label>
                        <select name="role" class="form-control">
                            <option value="">Pilih Role</option>
                            <option <?php if($role == 'admin'){
                                echo 'Selected';
                            } ?> value="admin">Admin</option>
                            <option <?php if($role == 'manajer'){
                                echo 'Selected';
                            } ?>value="manajer">Manajer</option>
                            <option <?php if($role == 'pegawai'){
                                echo 'Selected';
                            } ?>value="pegawai">Pegawai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Lokasi Presensi</label>
                        <select name="lokasi_presensi" class="form-control">
                            <option value="">Pilih Lokasi Presensi</option>

                            <?php 
                            $ambil_lok_presensi = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY nama_lokasi ASC");

                            while($lokasi = mysqli_fetch_assoc($ambil_lok_presensi)){
                                $nama_lokasi = $lokasi['nama_lokasi'];

                                if($lokasi_presensi == $nama_lokasi){
                                    echo '<option value="' . $nama_lokasi . '" selected="selected">' . $nama_lokasi . '</option>';
                                } else {
                                    echo '<option value="' .$nama_lokasi . '">' .$nama_lokasi . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Foto</label>
                        <input type="hidden" value="<?= $foto ?>" name="foto_lama">
                        <input type="file" class="form-control" name="foto_baru">
                    </div>

                    <input type="hidden" value="<?= $id ?>" name="id">

                    <button type="submit" class="btn btn-primary" name="edit">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>