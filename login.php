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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($pass, $row['password'])) {
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; // Menetapkan peranan ('admin' atau 'user')
            
            // Halakan mengikut peranan masing-masing
            if ($row['role'] == 'admin') {
                header("Location: index.php");
                exit();
            } else {
                header("Location: user_dashboard.php");
                exit();
            }
        } else {
            $error = "Nama pengguna atau kata laluan salah!";
        }
    } else {
        $error = "Nama pengguna atau kata laluan salah!";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk - MyZon MDYP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { 
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), url('mdyp.jpg') center/cover no-repeat; 
            height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; 
        }
        .login-card { 
            background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
            width: 100%; max-width: 440px; padding: 40px; 
        }
        .form-control { font-size: 14px; padding: 12px 16px; border-radius: 8px; border-color: #cbd5e1; }
        .form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <img src="logo_mydp.png" alt="Logo MDYP" width="55" class="mb-3 object-fit-contain">
            <h4 class="fw-bold text-dark mb-1">MyZon MDYP</h4>
            <p class="text-muted small mb-0">Sistem Pengurusan Program MDYP</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
            <div class="alert alert-success py-2 small text-center">Kata laluan berjaya dikemaskini! Sila log masuk.</div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-semibold small text-secondary">Nama Pengguna</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold small text-secondary">Kata Laluan</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordField" class="form-control" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <button class="btn btn-outline-secondary border" type="button" id="togglePassword" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
                </div>
                <div>
                    <a href="forgot_password.php" class="small text-decoration-none text-primary">Lupa kata laluan?</a>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" name="login" class="btn btn-primary py-2 fw-semibold">LOG MASUK</button>
            </div>
            <div class="text-center">
                <p class="small text-muted">Belum ada akaun? <a href="register.php" class="text-decoration-none fw-semibold">Daftar</a></p>
            </div>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>