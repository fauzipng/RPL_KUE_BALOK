<?php
session_start();
require 'koneksi.php';

// Jika keranjang kosong, kembalikan ke halaman kue
if (empty($_SESSION['keranjang'])) {
    header("Location: pembeli_kue.php");
    exit;
}

// Pastikan nilai bersih dari spasi tersembunyi
$metode = trim($_POST['metode_bayar']);

// Generate Data Pesanan (Format Unik Panjang)
date_default_timezone_set('Asia/Makassar');
$kode_pesanan = '#KB' . date('ymdHis') . rand(10, 99);
$nama_pelanggan = "Pelanggan " . rand(10, 99);
$waktu_pesan = date('Y-m-d H:i:s');
$total_harga = $_SESSION['total_bayar'];

// SEMUA pesanan awalnya berstatus 'Pending', baik QRIS maupun Tunai
$status_pesanan = 'Pending';

// 1. Simpan Transaksi Utama ke Database
$query_pesanan = "INSERT INTO pesanan (kode_pesanan, nama_pelanggan, total_harga, metode_pembayaran, status_pesanan, waktu_pesan) 
                  VALUES ('$kode_pesanan', '$nama_pelanggan', '$total_harga', '$metode', '$status_pesanan', '$waktu_pesan')";

if (!mysqli_query($conn, $query_pesanan)) {
    die("Error menyimpan pesanan utama: " . mysqli_error($conn));
}

$id_pesanan_baru = mysqli_insert_id($conn);

// Simpan ID pesanan ke session
$_SESSION['id_pesanan_aktif'] = $id_pesanan_baru;
$_SESSION['kode_pesanan_aktif'] = $kode_pesanan;

// 2. Simpan Detail Item
foreach ($_SESSION['keranjang'] as $id_menu => $qty) {
    $query_menu = mysqli_query($conn, "SELECT harga FROM menu WHERE id='$id_menu'");
    if ($menu = mysqli_fetch_assoc($query_menu)) {
        $subtotal_item = $menu['harga'] * $qty;

        $query_detail = "INSERT INTO detail_pesanan (id_pesanan, id_menu, qty, subtotal) 
                        VALUES ('$id_pesanan_baru', '$id_menu', '$qty', '$subtotal_item')";
        mysqli_query($conn, $query_detail);
    }
}

// 3. Arahkan sesuai metode ke halaman tunggu masing-masing
if (strpos(strtoupper($metode), 'QRIS') !== false) {
    header("Location: pembeli_qris.php");
    exit;
} else {
    // Jika Tunai, arahkan ke halaman tunggu khusus Tunai
    header("Location: pembeli_tunai.php");
    exit;
}
?>