<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS - Kue Balok Lumer GenZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAF6F0;
        }
    </style>
</head>

<body class="h-screen flex items-center justify-center text-gray-800">
    <div
        class="bg-white p-10 rounded-3xl shadow-xl w-[400px] text-center border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-3 bg-blue-500"></div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2 mt-2">Pembayaran QRIS</h2>
        <p class="text-sm text-gray-500 mb-8">Scan QR Code di bawah ini menggunakan aplikasi M-Banking atau E-Wallet
            Anda.</p>

        <div class="bg-gray-50 p-4 rounded-2xl border-2 border-dashed border-gray-200 inline-block mb-8">
            <img src="uploads/qris_fauzi.jpeg" alt="QRIS Fauzi" class="w-48 h-auto rounded-lg object-cover">
        </div>

        <div class="bg-blue-50 text-blue-700 font-semibold py-3 px-4 rounded-xl mb-8 text-sm border border-blue-100">
            Total Tagihan: <span class="text-lg ml-1">Rp
                <?= number_format($_SESSION['total_bayar'], 0, ',', '.') ?>
            </span>
        </div>

        <a href="pembeli_sukses.php?metode=QRIS"
            class="block w-full bg-[#5e3a21] text-white font-bold py-3.5 rounded-xl hover:bg-[#4a2e1a] shadow-md">
            Saya Sudah Bayar (Selesai)
        </a>
    </div>
</body>

</html>