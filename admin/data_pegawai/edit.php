<?php

session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login"); exit;
  exit;
}else if($_SESSION["role"] != 'admin'){
  header("Location: ../../auth/login.php?pesan=akses_ditolak"); exit;
  exit;
}

$judul = "Edit Data Pegawai";

include('../layout/header.php');

require_once('../../config.php');

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// ---------------------------------------------------------------
// 1. Data awal selalu diambil dari database (sekali saja)
// ---------------------------------------------------------------
$stmt = mysqli_prepare($connection, "SELECT users.username, users.password, users.status, users.role, pegawai.*
    FROM users JOIN pegawai ON users.id_pegawai = pegawai.id WHERE pegawai.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(!$data){
    $_SESSION['gagal'] = 'Data pegawai tidak ditemukan';
    header("Location: pegawai.php");
    exit;
}

$nama = $data['nama'];
$jenis_kelamin = $data['jenis_kelamin'];
$alamat = $data['alamat'];
$no_handphone = $data['no_handphone'];
$jabatan = $data['jabatan'];
$username = $data['username'];
$status = $data['status'];
$role = $data['role'];
$lokasi_presensi = $data['lokasi_presensi'];
$foto = $data['foto'];
$password_lama = $data['password']; // hash dari DB, tidak lagi dikirim lewat form

// ---------------------------------------------------------------
// 2. Saat form dikirim: nilai dari form MENIMPA data DB, jadi jika
//    validasi gagal isian tidak hilang / kembali ke nilai lama.
// ---------------------------------------------------------------
if(isset($_POST['edit'])){

    $nama = htmlspecialchars(trim($_POST['nama'] ?? ''));
    $jenis_kelamin = htmlspecialchars(trim($_POST['jenis_kelamin'] ?? ''));
    $alamat = htmlspecialchars(trim($_POST['alamat'] ?? ''));
    $no_handphone = htmlspecialchars(trim($_POST['no_handphone'] ?? ''));
    $jabatan = htmlspecialchars(trim($_POST['jabatan'] ?? ''));
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $role = htmlspecialchars(trim($_POST['role'] ?? ''));
    $status = htmlspecialchars(trim($_POST['status'] ?? ''));
    $lokasi_presensi = htmlspecialchars(trim($_POST['lokasi_presensi'] ?? ''));
    $password_baru = $_POST['password'] ?? '';
    $ulangi_password = $_POST['ulangi_password'] ?? '';

    $pesan_kesalahan = [];

    if($nama === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Nama wajib diisi"; }
    if($jenis_kelamin === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Jenis Kelamin wajib diisi"; }
    if($alamat === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Alamat wajib diisi"; }
    if($no_handphone === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>No Handphone wajib diisi"; }
    if($jabatan === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Jabatan wajib diisi"; }
    if($username === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Username wajib diisi"; }
    if($role === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Role wajib diisi"; }
    if($status === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Status wajib diisi"; }
    if($lokasi_presensi === ''){ $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Lokasi presensi wajib diisi"; }

    if($role !== '' && !in_array($role, ['admin', 'manajer', 'pegawai'], true)){
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Role tidak valid";
    }
    if($status !== '' && !in_array($status, ['Aktif', 'Tidak Aktif'], true)){
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Status tidak valid";
    }
    if($jenis_kelamin !== '' && !in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'], true)){
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Jenis kelamin tidak valid";
    }

    // Password dikosongkan = tidak diubah
    if($password_baru !== '' && $password_baru !== $ulangi_password){
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Password tidak cocok";
    }

    // Username harus unik (login.php mensyaratkan tepat 1 baris per username)
    if($username !== ''){
        $cek = mysqli_prepare($connection, "SELECT id FROM users WHERE username = ? AND id_pegawai <> ?");
        mysqli_stmt_bind_param($cek, "si", $username, $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        if(mysqli_stmt_num_rows($cek) > 0){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Username sudah dipakai pegawai lain";
        }
    }

    // Validasi foto SEBELUM file dipindahkan ke folder
    $nama_file = $foto;
    $upload_baru = null;
    if(isset($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] !== UPLOAD_ERR_NO_FILE){
        $f = $_FILES['foto_baru'];
        $ekstensi = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

        if($f['error'] !== UPLOAD_ERR_OK){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Upload foto gagal (kode " . (int) $f['error'] . ")";
        }elseif(!in_array($ekstensi, ['jpg', 'jpeg', 'png'], true)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Hanya file JPG, JPEG, dan PNG yang diizinkan";
        }elseif($f['size'] > 10 * 1024 * 1024){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Ukuran file melebihi: 10Mb";
        }elseif(@getimagesize($f['tmp_name']) === false){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>File bukan gambar yang valid";
        }else{
            $upload_baru = ['tmp' => $f['tmp_name'], 'ext' => $ekstensi];
        }
    }

    if(empty($pesan_kesalahan) && $upload_baru){
        $nama_file = 'pegawai_' . $id . '_' . time() . '.' . $upload_baru['ext'];
        if(!move_uploaded_file($upload_baru['tmp'], "../../assets/img/foto_pegawai/" . $nama_file)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Gagal menyimpan foto (cek izin folder assets/img/foto_pegawai)";
        }
    }

    if(empty($pesan_kesalahan)){
        $password_simpan = ($password_baru === '') ? $password_lama : password_hash($password_baru, PASSWORD_DEFAULT);

        try {
            mysqli_begin_transaction($connection);

            $up1 = mysqli_prepare($connection, "UPDATE pegawai SET nama = ?, jenis_kelamin = ?, alamat = ?,
                no_handphone = ?, jabatan = ?, lokasi_presensi = ?, foto = ? WHERE id = ?");
            mysqli_stmt_bind_param($up1, "sssssssi", $nama, $jenis_kelamin, $alamat, $no_handphone, $jabatan, $lokasi_presensi, $nama_file, $id);
            mysqli_stmt_execute($up1);

            // WHERE pakai id_pegawai (bukan users.id), karena keduanya belum tentu sama
            $up2 = mysqli_prepare($connection, "UPDATE users SET username = ?, password = ?, status = ?, role = ? WHERE id_pegawai = ?");
            mysqli_stmt_bind_param($up2, "ssssi", $username, $password_simpan, $status, $role, $id);
            mysqli_stmt_execute($up2);

            mysqli_commit($connection);

            $_SESSION['berhasil'] = 'Data berhasil diupdate';
            header("Location: pegawai.php");
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($connection);
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i>Gagal menyimpan: " . htmlspecialchars($e->getMessage());
        }
    }

    $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
}

// Opsi select: jika nilai tersimpan tidak ada di tabel referensi, tetap tampil (terpilih) agar tidak hilang
function opsi_select($daftar, $terpilih, $label_tidak_ada)
{
    $html = '';
    $ketemu = false;
    foreach($daftar as $item){
        $sel = ($terpilih == $item) ? ' selected="selected"' : '';
        if($sel){ $ketemu = true; }
        $html .= '<option value="' . $item . '"' . $sel . '>' . $item . '</option>';
    }
    if(!$ketemu && $terpilih !== ''){
        $html .= '<option value="' . $terpilih . '" selected="selected">' . $terpilih . ' (' . $label_tidak_ada . ')</option>';
    }
    return $html;
}

$daftar_jabatan = [];
$q = mysqli_query($connection, "SELECT jabatan FROM jabatan ORDER BY jabatan ASC");
while($r = mysqli_fetch_assoc($q)){ $daftar_jabatan[] = $r['jabatan']; }

$daftar_lokasi = [];
$q = mysqli_query($connection, "SELECT nama_lokasi FROM lokasi_presensi ORDER BY nama_lokasi ASC");
while($r = mysqli_fetch_assoc($q)){ $daftar_lokasi[] = $r['nama_lokasi']; }
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?= base_url('admin/data_pegawai/edit.php') ?>" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="text">Nama</label>
                        <input type="text" class="form-control" name="nama" value="<?= $nama ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" <?= $jenis_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $jenis_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Alamat</label>
                        <input type="text" class="form-control" name="alamat" value="<?= $alamat ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">No Handphone</label>
                        <input type="text" class="form-control" name="no_handphone" value="<?= $no_handphone ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">Jabatan</label>
                        <select name="jabatan" class="form-control">
                            <option value="">Pilih Jabatan</option>
                            <?= opsi_select($daftar_jabatan, $jabatan, 'tidak ada di Data Jabatan') ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Pilih Status</option>
                            <option value="Aktif" <?= $status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Tidak Aktif" <?= $status == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= $username ?>">
                    </div>

                    <div class="mb-3">
                        <label for="text">Password</label>
                        <input type="password" class="form-control" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah">
                    </div>

                    <div class="mb-3">
                        <label for="text">Ulangi Password</label>
                        <input type="password" class="form-control" name="ulangi_password" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label for="text">Role</label>
                        <select name="role" class="form-control">
                            <option value="">Pilih Role</option>
                            <option value="admin" <?= strtolower($role) == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="manajer" <?= strtolower($role) == 'manajer' ? 'selected' : '' ?>>Manajer</option>
                            <option value="pegawai" <?= strtolower($role) == 'pegawai' ? 'selected' : '' ?>>Pegawai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Lokasi Presensi</label>
                        <select name="lokasi_presensi" class="form-control">
                            <option value="">Pilih Lokasi Presensi</option>
                            <?= opsi_select($daftar_lokasi, $lokasi_presensi, 'tidak ada di Lokasi Presensi') ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="text">Foto</label>
                        <?php if(!empty($foto)): ?>
                            <div class="form-hint mb-1">Foto saat ini: <?= $foto ?> (kosongkan jika tidak diganti)</div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="foto_baru" accept=".jpg,.jpeg,.png">
                    </div>

                    <input type="hidden" value="<?= $id ?>" name="id">

                    <button type="submit" class="btn btn-primary" name="edit">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>
