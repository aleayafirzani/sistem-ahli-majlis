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

$zon_terpilih = isset($_GET['zon']) ? trim($_GET['zon']) : '';

if (empty($zon_terpilih)) {
    header("Location: user_dashboard.php");
    exit();
}

$senarai_ahli_map = [
    "Zon 1" => "TUAN HAJI SAMSOL BIN MOHAMED SETH",
    "Zon 2" => "TUAN HAJI ABDUL RASHID BIN ABDUL HAMID",
    "Zon 3" => "ENCIK LIM TEE SIONG",
    "Zon 4" => "DR. RAVEEN A/L RAMA KRISHNAN",
    "Zon 5" => "PUAN TAN POH CHIN",
    "Zon 6" => "TUAN HAJI ABDULLAH BIN SHARIFF",
    "Zon 7" => "ENCIK MUHAMMAD KHAIRULANNUAR BIN NORDIN",
    "Zon 8" => "CIK NGAW KAI XUAN",
    "Zon 9" => "ENCIK SHAHDEZAN BIN ABDULLAH",
    "Zon 10" => "ENCIK AZMAN BIN AHMAD",
    "Zon 11" => "PUAN SITI AISYAH BINTI RAHMAN",
    "Zon 12" => "ENCIK WONG KAH WAI",
    "Zon 13" => "TUAN HAJI OTHMAN BIN ABU",
    "Zon 14" => "ENCIK DAVID TEOH",
    "Zon 15" => "PUAN NORLIZA BINTI KASSIM",
    "Zon 16" => "ENCIK M. MUTHU A/L RAMALINGAM",
    "Zon 17" => "ENCIK LEE CHUN WAH",
    "Zon 18" => "PUAN NURUL AIN BINTI ISMAIL",
    "Zon 19" => "ENCIK HO TECK SENG",
    "Zon 20" => "PUAN ZAINAH BINTI HASHIM",
    "Zon 21" => "ENCIK MOHD FAIZAL BIN OTHMAN",
    "Zon 22" => "CIK CHONG SIEW LING",
    "Zon 23" => "TUAN HAJI ISMAIL BIN MD. DOM",
    "Zon 24" => "PUAN ROSNAH BINTI ABU BAKAR"
];

$nama_ahli = $senarai_ahli_map[$zon_terpilih] ?? "Maklumat Tidak Dijumpai";

$stmt = $conn->prepare("SELECT * FROM program WHERE zon = ? ORDER BY tarikh_program DESC");
$stmt->bind_param("s", $zon_terpilih);
$stmt->execute();
$result_program = $stmt->get_result();

$stmt_stat = $conn->prepare("SELECT COUNT(*) as total_prog, SUM(belanja) as total_belanja FROM program WHERE zon = ?");
$stmt_stat->bind_param("s", $zon_terpilih);
$stmt_stat->execute();
$stat = $stmt_stat->get_result()->fetch_assoc();

$jumlah_program = $stat['total_prog'] ?? 0;
$jumlah_belanja = $stat['total_belanja'] ?? 0;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?php echo htmlspecialchars($zon_terpilih); ?> - MyZon MDYP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .profile-header-card {
            background: var(--card-bg);
            border: 1px solid #e0e5f2;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
            margin-bottom: 25px;
        }
        .content-card {
            background: var(--card-bg);
            border: 1px solid #e0e5f2;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
        }
        .stat-box {
            background: #f8fafc;
            border: 1px solid #e0e5f2;
            border-radius: 14px;
            padding: 15px 20px;
        }
        .table { font-size: 0.85rem; vertical-align: middle; }
        .table th { font-weight: 600; color: var(--text-muted); background-color: #f8fafc; border-bottom: 2px solid #e0e5f2; padding: 12px 10px; }
        .table td { padding: 12px 10px; }
    </style>
</head>
<body>

    <!-- SIDEBAR KHAS USER -->
    <div class="sidebar">
        <div class="d-flex flex-column justify-content-between h-100">
            <div>
                <a href="user_dashboard.php" class="sidebar-brand">
                    <img src="logo_mydp.png" alt="Logo MDYP" style="width: 35px; height: 35px; object-fit: contain;">
                    <span>MyZon MDYP</span>
                </a>
                <ul class="sidebar-menu">
                    <li><a href="user_dashboard.php"><i class="fa-solid fa-house"></i> Utama (Rujukan)</a></li>
                    <li><a href="user_ahli.php" class="active"><i class="fa-solid fa-users"></i> Senarai Ahli Majlis</a></li>
                </ul>
            </div>
            
            <div class="mb-3">
                <ul class="sidebar-menu">
                    <li><a href="#" onclick="sahkanLogKeluar(event)" class="text-warning"><i class="fa-solid fa-right-from-bracket"></i> Log Keluar</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
        
        <!-- BREADCRUMBS NAVIGASI JEJAK -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0 py-2 px-3 bg-white border rounded-3 shadow-sm" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="user_dashboard.php" class="text-decoration-none">Utama</a></li>
                <li class="breadcrumb-item"><a href="user_ahli.php" class="text-decoration-none">Direktori Ahli Majlis</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page"><?php echo htmlspecialchars($zon_terpilih); ?></li>
            </ol>
        </nav>

        <!-- KAD PROFIL AHLI MAJLIS -->
        <div class="profile-header-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="user_ahli.php" class="btn btn-light border text-secondary fw-semibold px-3 py-2 rounded-3 shadow-sm" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-2 fw-bold mb-2"><?php echo htmlspecialchars($zon_terpilih); ?></span>
                        <h4 class="fw-bold mb-1"><i class="fa-solid fa-user-tie text-primary me-2"></i> <?php echo htmlspecialchars($nama_ahli); ?></h4>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Wakil Rasmi Ahli Majlis Majlis Daerah Yong Peng</p>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="stat-box d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4"><i class="fa-solid fa-folder-open"></i></div>
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block">Jumlah Program</span>
                            <h5 class="fw-bold mb-0 text-dark"><?php echo number_format($jumlah_program); ?> Program</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-box d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4"><i class="fa-solid fa-wallet"></i></div>
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block">Jumlah Perbelanjaan</span>
                            <h5 class="fw-bold mb-0 text-danger">RM <?php echo number_format($jumlah_belanja, 2); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SENARAI PROGRAM MENGIKUT ZON -->
        <div class="content-card">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Rekod Program & Pelaksanaan</h5>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Senarai aktiviti rasmi yang dilaksanakan di bawah <?php echo htmlspecialchars($zon_terpilih); ?>.</p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead>
                        <tr class="text-center">
                            <th style="width: 70px;">Bil</th>
                            <th class="text-start">Tajuk Program</th>
                            <th style="width: 120px;">Tarikh</th>
                            <th style="width: 90px;">Hadir</th>
                            <th style="width: 120px;">Belanja (RM)</th>
                            <th style="width: 140px;">Dokumentasi</th>
                            <th style="width: 150px;" class="text-start">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_program && $result_program->num_rows > 0): ?>
                            <?php $bil = 1; while($row = $result_program->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center fw-semibold text-muted"><?php echo $bil++; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['tajuk_program']); ?></td>
                                    <td class="text-center text-nowrap"><i class="fa-regular fa-calendar me-1 text-muted"></i><?php echo htmlspecialchars($row['tarikh_program']); ?></td>
                                    <td class="text-center fw-semibold"><?php echo number_format($row['jumlah_kehadiran'] ?? 0); ?></td>
                                    <td class="text-end fw-semibold text-danger">RM <?php echo number_format($row['belanja'], 2); ?></td>
                                    <td class="text-center">
                                        <?php if(($row['minit_mesyuarat'] ?? 0) && ($row['kertas_kerja'] ?? 0) && ($row['laporan'] ?? 0)): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="fa-solid fa-check-circle me-1"></i>Lengkap</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning text-dark px-2 py-1"><i class="fa-solid fa-clock me-1"></i>Proses</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($row['catatan'] ?? ''); ?>">
                                        <?php echo !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Tiada sebarang rekod program ditemui bagi zon ini buat masa ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function sahkanLogKeluar(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Log Keluar?',
                text: "Adakah anda pasti mahu keluar dari portal sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d5cbe',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            })
        }
    </script>
</body>
</html>