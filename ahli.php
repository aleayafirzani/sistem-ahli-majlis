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

// Senarai Penuh 24 Ahli Majlis MDYP (Zon & Nama Sahaja)
$senarai_ahli = [
    ['zon' => 'Zon 1', 'nama' => 'TUAN HAJI SAMSOL BIN MOHAMED SETH'],
    ['zon' => 'Zon 2', 'nama' => 'TUAN HAJI ABDUL RASHID BIN ABDUL HAMID'],
    ['zon' => 'Zon 3', 'nama' => 'ENCIK LIM TEE SIONG'],
    ['zon' => 'Zon 4', 'nama' => 'DR. RAVEEN A/L RAMA KRISHNAN'],
    ['zon' => 'Zon 5', 'nama' => 'PUAN TAN POH CHIN'],
    ['zon' => 'Zon 6', 'nama' => 'TUAN HAJI ABDULLAH BIN SHARIFF'],
    ['zon' => 'Zon 7', 'nama' => 'ENCIK MUHAMMAD KHAIRULANNUAR BIN NORDIN'],
    ['zon' => 'Zon 8', 'nama' => 'CIK NGAW KAI XUAN'],
    ['zon' => 'Zon 9', 'nama' => 'ENCIK SHAHDEZAN BIN ABDULLAH'],
    ['zon' => 'Zon 10', 'nama' => 'ENCIK AZMAN BIN AHMAD'],
    ['zon' => 'Zon 11', 'nama' => 'PUAN SITI AISYAH BINTI RAHMAN'],
    ['zon' => 'Zon 12', 'nama' => 'ENCIK WONG KAH WAI'],
    ['zon' => 'Zon 13', 'nama' => 'TUAN HAJI OTHMAN BIN ABU'],
    ['zon' => 'Zon 14', 'nama' => 'ENCIK DAVID TEOH'],
    ['zon' => 'Zon 15', 'nama' => 'PUAN NORLIZA BINTI KASSIM'],
    ['zon' => 'Zon 16', 'nama' => 'ENCIK M. MUTHU A/L RAMALINGAM'],
    ['zon' => 'Zon 17', 'nama' => 'ENCIK LEE CHUN WAH'],
    ['zon' => 'Zon 18', 'nama' => 'PUAN NURUL AIN BINTI ISMAIL'],
    ['zon' => 'Zon 19', 'nama' => 'ENCIK HO TECK SENG'],
    ['zon' => 'Zon 20', 'nama' => 'PUAN ZAINAH BINTI HASHIM'],
    ['zon' => 'Zon 21', 'nama' => 'ENCIK MOHD FAIZAL BIN OTHMAN'],
    ['zon' => 'Zon 22', 'nama' => 'CIK CHONG SIEW LING'],
    ['zon' => 'Zon 23', 'nama' => 'TUAN HAJI ISMAIL BIN MD. DOM'],
    ['zon' => 'Zon 24', 'nama' => 'PUAN ROSNAH BINTI ABU BAKAR']
];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senarai Ahli Majlis - MyZon MDYP</title> <!-- Dikemaskini kepada MyZon MDYP -->
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
        }
        .member-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .member-item {
            border: 1px solid #e0e5f2;
            border-radius: 12px;
            padding: 14px 20px;
            background: #fcfdfe;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
        }
        .member-link:hover .member-item {
            border-color: var(--sidebar-bg);
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(13, 92, 190, 0.06);
            transform: translateX(4px);
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div>
            <a href="index.php" class="sidebar-brand">
                <img src="logo_mydp.png" alt="Logo MDYP" style="width: 35px; height: 35px; object-fit: contain;">
                <span>MyZon MDYP</span> <!-- Dikemaskini kepada MyZon MDYP -->
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
        <div class="content-card">
            <div class="mb-4">
                <h5 class="fw-bold mb-1"><i class="fa-solid fa-users text-primary me-2"></i> Direktori Rasmi Ahli Majlis (Zon 1 - 24)</h5>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Klik pada mana-mana baris ahli majlis untuk melihat profil dan senarai program terperinci mengikut zon.</p>
            </div>

            <!-- SENARAI SEBARIS DENGAN PAUTAN KE PROFIL -->
            <div class="row">
                <?php foreach ($senarai_ahli as $ahli): ?>
                    <div class="col-12">
                        <a href="profil_ahli.php?zon=<?php echo urlencode($ahli['zon']); ?>" class="member-link">
                            <div class="member-item">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-3 fw-bold text-nowrap" style="min-width: 80px; text-align: center;">
                                    <?php echo $ahli['zon']; ?>
                                </span>
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <i class="fa-solid fa-user-tie text-secondary opacity-50 me-1"></i>
                                    <span class="fw-semibold text-dark" style="font-size: 0.95rem;"><?php echo $ahli['nama']; ?></span>
                                </div>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>