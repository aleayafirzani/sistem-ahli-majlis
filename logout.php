<?php
session_start();

// Kosongkan dan musnahkan semua sesi
$_SESSION = array();
session_destroy();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Keluar - MyZon MDYP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Meta tag untuk automatik redirect ke login.php selepas 3 saat -->
    <meta http-equiv="refresh" content="3;url=login.php">

    <style>
        :root {
            --body-bg: #f4f7fe;
            --text-main: #1b2559;
            --text-muted: #707eae;
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { 
            background-color: var(--body-bg); 
            color: var(--text-main); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
        }
        .logout-card {
            background: #ffffff;
            border: 1px solid #e0e5f2;
            border-radius: 24px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 450px;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="logout-card">
        <!-- Ikon Rasmi Perisai MDYP -->
        <div class="mb-3 text-primary">
            <i class="fa-solid fa-building-shield fa-3x bg-primary bg-opacity-10 p-3 rounded-circle"></i>
        </div>
        
        <h3 class="fw-bold text-dark mb-2">Anda Telah Log Keluar</h3>
        <p class="text-muted small mb-4">Terima kasih kerana menggunakan MyZon MDYP. Sesi anda telah ditamatkan dengan selamat.</p>
        
        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
        <span class="text-muted small fw-semibold">Mengalihkan ke halaman log masuk...</span>
        
        <div class="mt-4">
            <a href="login.php" class="btn btn-primary btn-sm px-4 py-2 rounded-3 fw-semibold shadow-sm">
                Log Masuk Semula
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>