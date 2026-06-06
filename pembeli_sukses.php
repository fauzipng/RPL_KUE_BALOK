<?php
session_start();

// Validasi Keamanan: Pastikan pelanggan berasal dari alur pembayaran yang benar
if (!isset($_SESSION['id_pesanan_aktif'])) {
    header("Location: pembeli_kue.php");
    exit;
}

$metode = isset($_GET['metode']) ? $_GET['metode'] : 'Tunai';
$kode_pesanan = $_SESSION['kode_pesanan_aktif'];
$total_harga = $_SESSION['total_bayar'];

// Bersihkan memori keranjang karena pesanan sudah tuntas masuk ke database
unset($_SESSION['keranjang']);
unset($_SESSION['subtotal']);
unset($_SESSION['pajak']);
unset($_SESSION['total_bayar']);
unset($_SESSION['total_item']);
unset($_SESSION['id_pesanan_aktif']);
unset($_SESSION['kode_pesanan_aktif']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAF6F0;
        }
    </style>
</head>

<body class="h-screen flex items-center justify-center text-gray-800">
    <div class="bg-white p-10 rounded-3xl shadow-xl w-[450px] text-center border border-gray-100">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
            <svg class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="text-3xl font-bold mb-3">Pembayaran Berhasil!</h2>
        <p class="text-gray-500 text-sm mb-8">Terima kasih! Pesanan Anda telah tercatat ke dalam sistem kami dan akan
            segera disiapkan.</p>

        <div class="bg-[#FAF6F0] p-5 rounded-2xl text-left border border-gray-200 mb-8">
            <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-200">
                <span class="text-xs text-gray-500">ID Pesanan</span><span class="font-bold">
                    <?= $kode_pesanan ?>
                </span>
            </div>
            <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-200">
                <span class="text-xs text-gray-500">Metode</span><span class="font-semibold">
                    <?= $metode ?>
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Total Dibayar</span><span
                    class="font-bold text-[#5e3a21] text-lg">Rp
                    <?= number_format($total_harga, 0, ',', '.') ?>
                </span>
            </div>
        </div>

        <a href="pembeli_kue.php"
            class="block w-full border-2 border-[#5e3a21] text-[#5e3a21] font-bold py-3.5 rounded-xl hover:bg-[#FAF6F0] transition">Kembali
            ke Menu Utama</a>
    </div>
</body>

</html>