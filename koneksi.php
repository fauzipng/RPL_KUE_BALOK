<?php
$host = "localhost";
$user = "root"; // Username bawaan XAMPP
$password = "";     // Password bawaan XAMPP kosong
$db = "db_kue_balok";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $password, $db);

// Mengecek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>