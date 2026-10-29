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

// Proses Padam Rekod
if (isset($_GET['padam'])) {
    $id_padam = intval($_GET['padam']);
    $stmt_del = $conn->prepare("DELETE FROM program WHERE id = ?");
    $stmt_del->bind_param("i", $id_padam);
    $stmt_del->execute();
    $stmt_del->close();
    header("Location: index.php");
    exit();
}

// Proses Carian
$carian = isset($_GET['carian']) ? trim($_GET['carian']) : '';
if ($carian !== '') {
    $stmt = $conn->prepare("SELECT * FROM program WHERE tajuk_program LIKE ? OR nama_ahli_majlis LIKE ? OR zon LIKE ? ORDER BY tarikh_program DESC");
    $like_carian = "%" . $carian . "%";
    $stmt->bind_param("sss", $like_carian, $like_carian, $like_carian);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM program ORDER BY tarikh_program DESC");
}

// Ringkasan Kewangan Keseluruhan
$stat_query = $conn->query("SELECT COUNT(*) as jumlah_prog, SUM(belanja) as total_belanja FROM program");
$stat = $stat_query->fetch_assoc();

$jumlah_program = $stat['jumlah_prog'] ?? 0;
$jumlah_terkumpul = 137200.00; // Mengikut peruntukan terkumpul (RM6,000.00 x 24 Ahli)
$jumlah_telah_diguna = $stat['total_belanja'] ?? 0;
$baki_keseluruhan = $jumlah_terkumpul - $jumlah_telah_diguna;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utama (Overview) - MyZon MDYP</title>
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
        .stat-card {
            background: var(--card-bg);
            border: 1px solid #e0e5f2;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .content-card {
            background: var(--card-bg);
            border: 1px solid #e0e5f2;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
        }
        .table { font-size: 0.85rem; vertical-align: middle; }
        .table th { font-weight: 600; color: var(--text-muted); background-color: #f8fafc; border-bottom: 2px solid #e0e5f2; padding: 12px 10px; }
        .table td { padding: 12px 10px; }
        .badge-doc { font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; display: inline-block; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div>
            <!-- LOGO MYZON MDYP -->
            <a href="index.php" class="sidebar-brand">
                <img src="logo_mydp.png" alt="Logo MDYP" style="width: 35px; height: 35px; object-fit: contain;">
                <span>MyZon MDYP</span>
            </a>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active"><i class="fa-solid fa-house"></i> Utama (Overview)</a></li>
                <li><a href="tambah.php"><i class="fa-solid fa-circle-plus"></i> Tambah Program</a></li>
                <li><a href="ahli.php"><i class="fa-solid fa-users"></i> Senarai Ahli Majlis</a></li>
                <li><a href="tetapan.php"><i class="fa-solid fa-gear"></i> Tetapan Sistem</a></li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
        
        <!-- RINGKASAN KEWANGAN KESELURUHAN -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-folder-open"></i></div>
                    <div>
                        <span class="text-muted d-block small fw-semibold text-uppercase">Jumlah Program</span>
                        <h4 class="fw-bold mb-0 text-dark"><?php echo number_format($jumlah_program); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fa-solid fa-wallet"></i></div>
                    <div>
                        <span class="text-muted d-block small fw-semibold text-uppercase">Jumlah Terkumpul</span>
                        <h6 class="fw-bold mb-0 text-dark">RM <?php echo number_format($jumlah_terkumpul, 2); ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div>
                        <span class="text-muted d-block small fw-semibold text-uppercase">Telah Digunakan</span>
                        <h6 class="fw-bold mb-0 text-dark">RM <?php echo number_format($jumlah_telah_diguna, 2); ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fa-solid fa-scale-balanced"></i></div>
                    <div>
                        <span class="text-muted d-block small fw-semibold text-uppercase">Baki Keseluruhan</span>
                        <h6 class="fw-bold mb-0 text-info">RM <?php echo number_format($baki_keseluruhan, 2); ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- JADUAL UTAMA -->
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Senarai Semak & Rekod Program 2026</h5>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Ringkasan pelaksanaan program, rekod kehadiran, kewangan, dan status dokumentasi.</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="text" name="carian" class="form-control form-control-sm" placeholder="Cari program / ahli..." value="<?php echo htmlspecialchars($carian); ?>">
                        <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa-solid fa-search"></i></button>
                    </form>
                    <a href="tambah.php" class="btn btn-success btn-sm text-nowrap"><i class="fa-solid fa-plus me-1"></i> Tambah</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead>
                        <tr class="text-center">
                            <th style="width: 70px;">Zon</th>
                            <th style="width: 180px;" class="text-start">Ahli Majlis</th>
                            <th class="text-start">Tajuk Program</th>
                            <th style="width: 100px;">Tarikh</th>
                            <th style="width: 80px;">Hadir</th>
                            <th style="width: 110px;">Belanja (RM)</th>
                            <th style="width: 140px;">Dokumentasi</th>
                            <th style="width: 130px;" class="text-start">Catatan</th>
                            <th style="width: 90px;" class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?php echo htmlspecialchars($row['zon']); ?></span>
                                    </td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_ahli_majlis']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tajuk_program']); ?></td>
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
                                    <td class="small text-muted text-truncate" style="max-width: 130px;" title="<?php echo htmlspecialchars($row['catatan'] ?? ''); ?>">
                                        <?php echo !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-'; ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Kemaskini"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="index.php?padam=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Adakah anda pasti mahu memadam rekod ini?');" title="Padam"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">Tiada rekod program ditemui.</td>
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