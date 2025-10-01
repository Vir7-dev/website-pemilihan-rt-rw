<?php

session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nik = trim($_POST['nik'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nik === '' || $password === '') {
        echo "⚠️ Isi NIK dan password.";
        exit;
    }

    $stmt = mysqli_prepare($koneksi, "SELECT user_id, nama, nik, password FROM users WHERE nik = ? LIMIT 1");
    if (!$stmt) {
        echo "Terjadi kesalahan database.";
        exit;
    }
    mysqli_stmt_bind_param($stmt, "s", $nik);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $db_nama, $db_nik, $db_password);

    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        echo "⚠️ NIK tidak ditemukan!";
        exit;
    }
    mysqli_stmt_close($stmt);

    $authenticated = false;
    $needs_rehash_and_update = false;

    if (preg_match('/^\$(2y|2a)\$|^\$argon2/i', $db_password)) {
        if (password_verify($password, $db_password)) {
            $authenticated = true;
            if (password_needs_rehash($db_password, PASSWORD_DEFAULT)) {
                $needs_rehash_and_update = true;
            }
        }
    } else {
        if ($password === $db_password) {
            $authenticated = true;
            $needs_rehash_and_update = true;
        }
    }

    if ($authenticated) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['nik'] = $db_nik;
        $_SESSION['nama'] = $db_nama;

        if ($needs_rehash_and_update) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $upd = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE user_id = ?");
            if ($upd) {
                mysqli_stmt_bind_param($upd, "si", $newHash, $user_id);
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);
            }
        }

        header("Location: dashboard.php");
        exit;
    } else {
        echo "⚠️ Password salah!";
        exit;
    }
}
