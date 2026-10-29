<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_ahli_majlis';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Sambungan gagal: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $user = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($user) || empty($new_password) || empty($confirm_password)) {
        $error = "Sila lengkapkan semua ruangan!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Kata laluan baharu tidak sepadan. Sila cuba lagi.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $update->bind_param("ss", $hashed_password, $user);
            
            if ($update->execute()) {
                header("Location: login.php?reset=success");
                exit();
            } else {
                $error = "Gagal mengemas kini kata laluan.";
            }
            $update->close();
        } else {
            $error = "Nama pengguna tidak wujud di dalam sistem.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemulihan Kata Laluan - Sistem MDYP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { 
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('mdyp.jpg') center/cover no-repeat; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
            padding: 20px;
        }
        .card-box { 
            background: #ffffff; 
            border-radius: 16px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.25); 
            width: 100%; 
            max-width: 440px; 
            padding: 35px; 
        }
        .form-control { 
            font-size: 14px; 
            padding: 12px 16px; 
            border-radius: 8px; 
            border-color: #cbd5e1; 
        }
        .form-control:focus { 
            border-color: #0d6efd; 
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15); 
        }
        .btn-custom {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="text-center mb-4">
            <img src="logo_mydp.png" alt="Logo MDYP" width="55" class="mb-3 object-fit-contain">
            <h4 class="fw-bold text-dark mb-1">Pemulihan Kata Laluan</h4>
            <p class="text-muted small">Sistem Pengurusan Program MDYP</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger py-2 small text-center rounded-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-semibold small text-secondary">Nama Pengguna Akaun</label>
                <input type="text" name="username" class="form-control" placeholder="Cth: admin_mdyp" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small text-secondary">Kata Laluan Baharu</label>
                <input type="password" name="new_password" class="form-control" placeholder="Cipta kata laluan baharu" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small text-secondary">Sahkan Kata Laluan Baharu</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Taip semula kata laluan" required>
            </div>
            <div class="d-grid mb-3 mt-4">
                <button type="submit" name="reset_password" class="btn btn-success btn-custom shadow-sm">TUKAR KATA LALUAN</button>
            </div>
            <div class="text-center">
                <a href="login.php" class="small text-decoration-none fw-semibold text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Log Masuk</a>
            </div>
        </form>
    </div>
</body>
</html>