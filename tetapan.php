<?php
session_start();

// Semakan sesi log masuk
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

// Logik jika borang profil dihantar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        // Proses kemaskini profil boleh ditambah di sini
        $mesej = "Maklumat profil berjaya dikemaskini!";
        $status_type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tetapan Sistem - MyZon MDYP</title>
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

        /* SIDEBAR STYLING */
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

        /* MAIN WRAPPER */
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
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1px solid #e0e5f2;
            border-radius: 10px;
            padding: 11px 15px;
            font-size: 0.9rem;
            color: var(--text-main);
            background-color: #fcfdfe;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--sidebar-bg);
            box-shadow: 0 0 0 3px rgba(13, 92, 190, 0.1);
            background-color: #ffffff;
        }
        .profile-avatar-box {
            border: 1px solid #e0e5f2;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            background: #fcfdfe;
        }
        .avatar-img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 12px;
        }
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e5f2;
        }
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
                <li><a href="ahli.php"><i class="fa-solid fa-users"></i> Senarai Ahli Majlis</a></li>
                <li><a href="tetapan.php" class="active"><i class="fa-solid fa-gear"></i> Tetapan Sistem</a></li>
                <!-- Pautan Log Keluar dikemas kini memanggil Modal -->
                <li class="mt-4">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" class="text-warning">
                        <i class="fa-solid fa-right-from-bracket"></i> Log Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Tetapan Sistem</h4>
            <div class="text-muted small">
                <i class="fa-regular fa-bell me-3"></i>
                <span class="fw-semibold text-dark">Jabatan Pembangunan Masyarakat</span>
            </div>
        </div>

        <?php if (!empty($mesej)): ?>
            <div class="alert alert-<?php echo $status_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $mesej; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- KAD UTAMA TETAPAN -->
        <div class="content-card">
            
            <form action="" method="POST">
                <div class="section-title">Profil Pengguna</div>
                
                <div class="row">
                    <!-- RUANGAN KIRI: INPUT MAKLUMAT -->
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Peranan Sistem</label>
                                <select name="role" class="form-select">
                                    <option value="Administrator" selected>Pentadbir Sistem (Administrator)</option>
                                    <option value="Urus Setia">Urus Setia Zon</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emel Rasmi</label>
                                <input type="email" name="email" class="form-control" value="pembangunan@mdyp.gov.my">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Jabatan / Bahagian</label>
                                <input type="text" name="nama_jabatan" class="form-control" value="Jabatan Pembangunan Masyarakat">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telefon</label>
                                <input type="text" name="phone" class="form-control" value="+607 491 1200">
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" name="update_profile" class="btn btn-primary px-4 py-2 fw-semibold rounded-3 shadow-sm">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- RUANGAN KANAN: GAMBAR PROFIL -->
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="profile-avatar-box">
                            <img src="logo_mydp.png" alt="Avatar" class="avatar-img bg-light p-2">
                            <h6 class="fw-bold mb-1">MDYP Admin</h6>
                            <p class="text-muted small mb-2">Pentadbir Sistem</p>
                            <a href="#" class="text-primary small fw-semibold text-decoration-none">Kemas Kini Gambar</a>
                        </div>
                    </div>
                </div>
            </form>

        </div>

        <!-- FOOTER -->
        <div class="text-center text-muted small pb-3">
            MyZon MDYP &bull; Versi 1.0.0 &copy; 2026 Majlis Daerah Yong Peng (MDYP)
        </div>

    </div>

    <!-- MODAL LOG KELUAR MODEN DI TENGAH SKRIN -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-body text-center p-4 p-lg-5">
                    <div class="mb-3 text-primary">
                        <i class="fa-solid fa-building-shield fa-3x bg-primary bg-opacity-10 p-3 rounded-circle"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Log Keluar Sistem</h4>
                    <p class="text-muted mb-4" style="font-size: 0.9rem;">Adakah anda pasti mahu keluar dari sesi aktif sistem pengurusan MyZon MDYP ini?</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light border px-4 py-2 rounded-3 fw-semibold text-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <a href="logout.php" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold shadow-sm">
                            Ya, Log Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>