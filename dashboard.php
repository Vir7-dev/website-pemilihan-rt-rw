<?php
session_start();

// cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data user
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        h1 {
            color: #333;
        }

        .logout {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Selamat Datang, <?php echo htmlspecialchars($nama); ?> 🎉</h1>
        <p>Ini adalah halaman dashboard. Hanya bisa diakses kalau sudah login.</p>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>

</html>