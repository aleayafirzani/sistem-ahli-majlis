<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_ahli_majlis';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Sambungan gagal: " . $conn->connect_error);
}

$mesej = "";
$status_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $zon = $_POST['zon'] ?? '';
    $tajuk_program = $_POST['tajuk_program'] ?? '';
    $tarikh_program = $_POST['tarikh_program'] ?? '';
    $jumlah_kehadiran = $_POST['jumlah_kehadiran'] ?? 0;
    $belanja = $_POST['belanja'] ?? 0;
    $catatan = $_POST['catatan'] ?? '';
    
    $minit_mesyuarat = isset($_POST['minit_mesyuarat']) ? 1 : 0;
    $kertas_kerja = isset($_POST['kertas_kerja']) ? 1 : 0;
    $laporan = isset($_POST['laporan']) ? 1 : 0;

    if (!empty($zon) && !empty($tajuk_program)) {
        $stmt = $conn->prepare("INSERT INTO program (zon, tajuk_program, tarikh_program, jumlah_kehadiran, belanja, minit_mesyuarat, kertas_kerja, laporan, catatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssidiiis", $zon, $tajuk_program, $tarikh_program, $jumlah_kehadiran, $belanja, $minit_mesyuarat, $kertas_kerja, $laporan, $catatan);
        
        if ($stmt->execute()) {
            $mesej = "Program berjaya direkodkan ke dalam sistem!";
            $status_type = "success";
        } else {
            $mesej = "Ralat: " . $conn->error;
            $status_type = "danger";
        }
    } else {
        $mesej = "Sila lengkapkan ruangan Zon dan Tajuk Program.";
        $status_type = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Program - MyZon MDYP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar-bg: #0d5cbe; --body-bg: #f4f7fe; --card-bg: #ffffff; --text-main: #1b2559; --text-muted: #707eae; }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background-color: var(--body-bg); color: var(--text-main); margin: 0; padding: 0; }
        .sidebar { width: 270px; height: 100vh; background-color: var(--sidebar-bg); position: fixed; top: 0; left: 0; z-index: 1000; display: flex; flex-direction: column; padding: 24px 20px; box-shadow: 4px 0 24px rgba(0,0,0,0.04); }
        .sidebar-brand { color: #ffffff; font-weight: 700; font-size: 1.15rem; display: flex; align-items: center; gap: 12px; text-decoration: none; margin-bottom: 35px; padding-left: 5px; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; width: 100%; }
        .sidebar-menu li { margin-bottom: 8px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 14px; color: rgba(255,255,255,0.75); text-decoration: none; padding: 13px 18px; border-radius: 12px; font-weight: 500; font-size: 0.92rem; transition: all 0.2s ease; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background-color: rgba(255, 255, 255, 0.15); color: #ffffff; }
        .sidebar-menu a i { width: 22px; text-align: center; }
        .main-wrapper { margin-left: 270px; padding: 30px; }
        .content-card { background: var(--card-bg); border: 1px solid #e0e5f2; border-radius: 20px; padding: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.02); }
        .form-label-custom { font-size: 0.8rem; font-weight: 700; color: var(--sidebar-bg); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .form-control, .form-select { border: 1px solid #e0e5f2; border-radius: 10px; padding: 11px 15px; font-size: 0.9rem; background-color: #fcfdfe; }
        .section-box { background: #f8fafc; border: 1px solid #e0e5f2; border-radius: 14px; padding: 20px; margin-bottom: 22px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <a href="index.php" class="sidebar-brand">
                <img src="logo_mydp.png" alt="Logo MDYP" style="width: 35px; height: 35px; object-fit: contain;">
                <span>MyZon MDYP</span>
            </a>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fa-solid fa-house"></i> Utama (Overview)</a></li>
                <li><a href="tambah.php" class="active"><i class="fa-solid fa-circle-plus"></i> Tambah Program</a></li>
                <li><a href="ahli.php"><i class="fa-solid fa-users"></i> Senarai Ahli Majlis</a></li>
                <li><a href="tetapan.php"><i class="fa-solid fa-gear"></i> Tetapan Sistem</a></li>
            </ul>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <a href="index.php" class="btn btn-light border text-secondary fw-semibold px-3 py-2 rounded-3 shadow-sm" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                    <div>
                        <h4 class="fw-bold mb-1"><i class="fa-solid fa-circle-plus text-primary me-2"></i> Borang Tambah Program Baru</h4>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Isikan maklumat program, bilangan kehadiran, perbelanjaan dan status dokumentasi rasmi.</p>
                    </div>
                </div>
            </div>

            <?php if (!empty($mesej)): ?>
                <div class="alert alert-<?php echo $status_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mesej; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="section-box">
                    <label class="form-label-custom"><i class="fa-solid fa-location-dot"></i> Pilih Zon & Ahli Majlis</label>
                    <select name="zon" class="form-select" required>
                        <option value="" disabled selected>-- Sila Pilih Zon / Ahli Majlis --</option>
                        <?php 
                        $zon_list = [
                            "Zon 1 - Tuan Haji Samsol Bin Mohamed Seth", "Zon 2 - Tuan Haji Abdul Rashid Bin Abdul Hamid",
                            "Zon 3 - Encik Lim Tee Siong", "Zon 4 - Dr. Raveen A/L Rama Krishnan",
                            "Zon 5 - Puan Tan Poh Chin", "Zon 6 - Tuan Haji Abdullah Bin Shariff",
                            "Zon 7 - Encik Muhammad Khairulannuar Bin Nordin", "Zon 8 - Cik Ngaw Kai Xuan",
                            "Zon 9 - Encik Shahdezan Bin Abdullah", "Zon 10 - Encik Azman Bin Ahmad",
                            "Zon 11 - Puan Siti Aisyah Binti Rahman", "Zon 12 - Encik Wong Kah Wai",
                            "Zon 13 - Tuan Haji Othman Bin Abu", "Zon 14 - Encik David Teoh",
                            "Zon 15 - Puan Norliza Binti Kassim", "Zon 16 - Encik M. Muthu A/L Ramalingam",
                            "Zon 17 - Encik Lee Chun Wah", "Zon 18 - Puan Nurul Ain Binti Ismail",
                            "Zon 19 - Encik Ho Teck Seng", "Zon 20 - Puan Zainah Binti Hashim",
                            "Zon 21 - Encik Mohd Faizal Bin Othman", "Zon 22 - Cik Chong Siew Ling",
                            "Zon 23 - Tuan Haji Ismail Bin Md. Dom", "Zon 24 - Puan Rosnah Binti Abu Bakar"
                        ];
                        foreach($zon_list as $z) {
                            echo '<option value="'.substr($z, 0, 5).'">'.$z.'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="section-box">
                    <label class="form-label-custom"><i class="fa-solid fa-pen-nib"></i> Tajuk Program Rasmi</label>
                    <input type="text" name="tajuk_program" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="section-box">
                            <label class="form-label-custom"><i class="fa-regular fa-calendar"></i> Tarikh Program</label>
                            <input type="date" name="tarikh_program" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-box">
                            <label class="form-label-custom"><i class="fa-solid fa-users"></i> Kehadiran Peserta</label>
                            <input type="number" name="jumlah_kehadiran" class="form-control" placeholder="0" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-box">
                            <label class="form-label-custom"><i class="fa-solid fa-wallet"></i> Belanja Semasa (RM)</label>
                            <input type="number" step="0.01" name="belanja" class="form-control" placeholder="0.00" min="0">
                        </div>
                    </div>
                </div>

                <div class="section-box">
                    <label class="form-label-custom mb-3"><i class="fa-solid fa-folder-open"></i> Semakan Dokumentasi</label>
                    <div class="row g-3">
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="minit_mesyuarat" value="1" id="minit"><label class="form-check-label small fw-semibold" for="minit">Minit Mesyuarat</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="kertas_kerja" value="1" id="kertas"><label class="form-check-label small fw-semibold" for="kertas">Kertas Kerja</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="laporan" value="1" id="laporan"><label class="form-check-label small fw-semibold" for="laporan">Laporan / Gambar</label></div></div>
                    </div>
                </div>

                <div class="section-box">
                    <label class="form-label-custom"><i class="fa-solid fa-comment-dots"></i> Catatan / Keterangan Tambahan</label>
                    <textarea name="catatan" class="form-control" rows="3"></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-5 py-2.5 fw-semibold rounded-3 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Rekod Program
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>