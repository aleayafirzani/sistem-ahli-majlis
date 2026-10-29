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

// Ambil data zon daripada URL
$zon_dipilih = isset($_GET['zon']) ? trim($_GET['zon']) : '';

if (empty($zon_dipilih)) {
    header("Location: ahli.php");
    exit();
}

// Senarai penuh nama ahli majlis untuk padanan mengikut zon
$senarai_ahli = [
    'Zon 1' => 'TUAN HAJI SAMSOL BIN MOHAMED SETH',
    'Zon 2' => 'TUAN HAJI ABDUL RASHID BIN ABDUL HAMID',
    'Zon 3' => 'ENCIK LIM TEE SIONG',
    'Zon 4' => 'DR. RAVEEN A/L RAMA KRISHNAN',
    'Zon 5' => 'PUAN TAN POH CHIN',
    'Zon 6' => 'TUAN HAJI ABDULLAH BIN SHARIFF',
    'Zon 7' => 'ENCIK MUHAMMAD KHAIRULANNUAR BIN NORDIN',
    'Zon 8' => 'CIK NGAW KAI XUAN',
    'Zon 9' => 'ENCIK SHAHDEZAN BIN ABDULLAH',
    'Zon 10' => 'ENCIK AZMAN BIN AHMAD',
    'Zon 11' => 'PUAN SITI AISYAH BINTI RAHMAN',
    'Zon 12' => 'ENCIK WONG KAH WAI',
    'Zon 13' => 'TUAN HAJI OTHMAN BIN ABU',
    'Zon 14' => 'ENCIK DAVID TEOH',
    'Zon 15' => 'PUAN NORLIZA BINTI KASSIM',
    'Zon 16' => 'ENCIK M. MUTHU A/L RAMALINGAM',
    'Zon 17' => 'ENCIK LEE CHUN WAH',
    'Zon 18' => 'PUAN NURUL AIN BINTI ISMAIL',
    'Zon 19' => 'ENCIK HO TECK SENG',
    'Zon 20' => 'PUAN ZAINAH BINTI HASHIM',
    'Zon 21' => 'ENCIK MOHD FAIZAL BIN OTHMAN',
    'Zon 22' => 'CIK CHONG SIEW LING',
    'Zon 23' => 'TUAN HAJI ISMAIL BIN MD. DOM',
    'Zon 24' => 'PUAN ROSNAH BINTI ABU BAKAR'
];

$nama_ahli = $senarai_ahli[$zon_dipilih] ?? 'Tidak Diketahui';

// Ambil rekod program mengikut zon daripada pangkalan data
$stmt = $conn->prepare("SELECT * FROM program WHERE zon = ? ORDER BY tarikh_program DESC");
$stmt->bind_param("s", $zon_dipilih);
$stmt->execute();
$result_program = $stmt->get_result();

// Kira jumlah peruntukan dan perbelanjaan bagi zon ini
$stat_stmt = $conn->prepare("SELECT COUNT(*) as jumlah_prog, SUM(belanja) as total_belanja FROM program WHERE zon = ?");
$stat_stmt->bind_param("s", $zon_dipilih);
$stat_stmt->execute();
$stat = $stat_stmt->get_result()->fetch_assoc();

$jumlah_program = $stat['jumlah_prog'] ?? 0;
$peruntukan_zon = 6000.00;
$telah_guna = $stat['total_belanja'] ?? 0;
$baki_zon = $peruntukan_zon - $telah_guna;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Program <?php echo htmlspecialchars($zon_dipilih); ?> - MyZon MDYP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0d5cbe;
            --body-bg: #f4f7fe;
            --card-bg: #ffffff;
            --text-main: #1b2559;
            --text-muted: #707eae;
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background-color: var(--body-bg); color: var(--text-main); margin: 0; padding: 0; }

        .sidebar {
            width: 270px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            padding: 24px 20px;
            box-shadow: 4px 0 24px rgba(0,0,0,0.04);
        }
        .sidebar-brand {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            margin-bottom: 35px;
            padding-left: 5px;
        }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; width: 100%; }
        .sidebar-menu li { margin-bottom: 8px; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            padding: 13px 18px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .sidebar-menu a i { width: 22px; text-align: center; }

        .main-wrapper {
            margin-left: 270px;
            padding: 30px;
        }
        .content-card {
            background: var(--card-bg);
            border: 1px solid #e0e5f2;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
            margin-bottom: 25px;
        }
        .stat-box {
            background: #f8fafc;
            border: 1px solid #e0e5f2;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
        }
        .table { font-size: 0.85rem; vertical-align: middle; }
        .table th { font-weight: 600; color: var(--text-muted); background-color: #f8fafc; border-bottom: 2px solid #e0e5f2; padding: 12px 10px; }
        .table td { padding: 12px 10px; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div>
            <a href="index.php" class="sidebar-brand">
                <img src="logo_mydp.png" alt="Logo MDYP" style="width: 35px; height: 35px; object-fit: contain;">
                <span>MyZon MDYP</span>
            </a>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fa-solid fa-house"></i> Utama (Overview)</a></li>
                <li><a href="tambah.php"><i class="fa-solid fa-circle-plus"></i> Tambah Program</a></li>
                <li><a href="ahli.php" class="active"><i class="fa-solid fa-users"></i> Senarai Ahli Majlis</a></li>
                <li><a href="tetapan.php"><i class="fa-solid fa-gear"></i> Tetapan Sistem</a></li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
        
        <!-- HEADER KAD PROFIL DENGAN BUTANG KEMBALI DI KIRI -->
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <!-- Butang Kembali di Sebelah Kiri -->
                    <a href="ahli.php" class="btn btn-light border text-secondary fw-semibold px-3 py-2 rounded-3 shadow-sm" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Direktori
                    </a>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold mb-1" style="font-size: 0.8rem;">
                            <?php echo htmlspecialchars($zon_dipilih); ?>
                        </span>
                        <h3 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($nama_ahli); ?></h3>
                        <p class="text-muted small mb-0"><i class="fa-solid fa-location-dot me-1"></i> Wakil Rasmi Ahli Majlis Majlis Daerah Yong Peng</p>
                    </div>
                </div>
            </div>

            <!-- RINGKASAN KEWANGAN KHAS ZON -->
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Jumlah Program</span>
                        <h4 class="fw-bold text-primary mb-0"><?php echo number_format($jumlah_program); ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Peruntukan Diluluskan</span>
                        <h5 class="fw-bold text-dark mb-0">RM <?php echo number_format($peruntukan_zon, 2); ?></h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Baki Peruntukan</span>
                        <h5 class="fw-bold text-success mb-0">RM <?php echo number_format($baki_zon, 2); ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- JADUAL PROGRAM BAGI ZON INI -->
        <div class="content-card">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Rekod Program & Pelaksanaan</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead>
                        <tr class="text-center">
                            <th style="width: 50px;">Bil</th>
                            <th class="text-start">Tajuk Program</th>
                            <th style="width: 110px;">Tarikh</th>
                            <th style="width: 80px;">Hadir</th>
                            <th style="width: 120px;">Belanja (RM)</th>
                            <th style="width: 140px;">Dokumentasi</th>
                            <th class="text-start">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_program && $result_program->num_rows > 0): ?>
                            <?php $bil = 1; while($prog = $result_program->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center text-muted"><?php echo $bil++; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($prog['tajuk_program']); ?></td>
                                    <td class="text-center text-nowrap"><i class="fa-regular fa-calendar me-1 text-muted"></i><?php echo htmlspecialchars($prog['tarikh_program']); ?></td>
                                    <td class="text-center"><?php echo number_format($prog['jumlah_kehadiran'] ?? 0); ?></td>
                                    <td class="text-end fw-semibold text-danger">RM <?php echo number_format($prog['belanja'], 2); ?></td>
                                    <td class="text-center">
                                        <?php if(($prog['minit_mesyuarat'] ?? 0) && ($prog['kertas_kerja'] ?? 0) && ($prog['laporan'] ?? 0)): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="fa-solid fa-check-circle me-1"></i>Lengkap</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning text-dark px-2 py-1"><i class="fa-solid fa-clock me-1"></i>Proses</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted"><?php echo !empty($prog['catatan']) ? htmlspecialchars($prog['catatan']) : '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tiada sebarang rekod program didaftarkan untuk zon ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>