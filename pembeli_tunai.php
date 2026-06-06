<?php
session_start();
if (!isset($_SESSION['id_pesanan_aktif'])) {
    header("Location: pembeli_kue.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tunai - Kue Balok Lumer GenZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAF6F0;
        }

        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #5e3a21;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="h-screen flex items-center justify-center text-gray-800">
    <div
        class="bg-white p-10 rounded-3xl shadow-xl w-[400px] text-center border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-3 bg-[#5e3a21]"></div>

        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#5e3a21]" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Tunai</h2>
        <p class="text-sm text-gray-500 mb-8">Silakan menuju meja kasir untuk melakukan pembayaran secara tunai.</p>

        <div
            class="bg-orange-50 text-[#5e3a21] font-semibold py-3 px-4 rounded-xl mb-8 text-sm border border-orange-100">
            Total Tagihan: <span class="text-lg ml-1">Rp
                <?= number_format($_SESSION['total_bayar'], 0, ',', '.') ?>
            </span>
        </div>

        <div class="text-sm text-gray-500 font-medium py-2">
            <div class="loader"></div> Menunggu konfirmasi kasir...
        </div>
    </div>

    <script>
        setInterval(function () {
            fetch('cek_status_pesanan.php')
                .then(response => response.text())
                .then(status => {
                    // Jika admin sudah klik konfirmasi
                    if (status.trim() === 'Selesai') {
                        window.location.href = 'pembeli_sukses.php?metode=Tunai';
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 3000); // Mengecek ke database setiap 3 detik
    </script>
</body>

</html>