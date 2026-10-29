<?php
session_start();

// Pastikan hanya admin yang log masuk boleh akses fail padam ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Menggunakan prepared statement untuk keselamatan pangkalan data yang lebih kukuh
    $stmt = $conn->prepare("DELETE FROM program WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit();
?>