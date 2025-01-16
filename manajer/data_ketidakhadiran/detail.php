<?php
ob_start();
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION["role"] != 'manajer') {
    header("Location: ../../auth/login.php?pesan=akses_ditolak");
    exit;
}

$judul = "Detail ketidakhadiran";
include('../layout/header.php');
require_once('../../config.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak ditemukan atau tidak valid.");
}

$id = intval($_GET['id']);

if (isset($_POST['update'])) {
    if (!isset($_POST['status_pengajuan']) || empty($_POST['status_pengajuan'])) {
        die("Status pengajuan harus dipilih.");
    }

    $status_pengajuan = mysqli_real_escape_string($connection, $_POST['status_pengajuan']);
    $query = "UPDATE ketidakhadiran SET status_pengajuan = '$status_pengajuan' WHERE id = $id";

    if (mysqli_query($connection, $query)) {
        $_SESSION['berhasil'] = 'Status Pengajuan berhasil diupdate';
        header("Location: ketidakhadiran.php");
        exit;
    } else {
        die("Error saat memperbarui data: " . mysqli_error($connection));
    }
}

$query = "SELECT * FROM ketidakhadiran WHERE id = $id";
$result = mysqli_query($connection, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Data dengan ID tersebut tidak ditemukan.");
}

$data = mysqli_fetch_assoc($result);
$keterangan = $data['keterangan'];
$status_pengajuan = $data['status_pengajuan'];
$tanggal = $data['tanggal'];
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= htmlspecialchars($tanggal); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" value="<?= htmlspecialchars($keterangan); ?>" readonly>
                    </div>
                    <label for="">Status Pengajuan</label>
                    <select name="status_pengajuan" class="form-control">
                        <option value="">Pilih Status</option>
                        <option <?= $status_pengajuan == 'PENDING' ? 'selected' : ''; ?> value="PENDING">PENDING</option>
                        <option <?= $status_pengajuan == 'REJECTED' ? 'selected' : ''; ?> value="REJECTED">REJECTED</option>
                        <option <?= $status_pengajuan == 'APPROVED' ? 'selected' : ''; ?> value="APPROVED">APPROVED</option>
                    </select>

                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>
