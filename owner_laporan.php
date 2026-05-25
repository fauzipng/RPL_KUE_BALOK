<?php
require 'koneksi.php';

// 1. Mengambil Total Penjualan & Total Transaksi
$query_stats = mysqli_query($conn, "SELECT SUM(total_harga) AS total_penjualan, COUNT(id) AS total_transaksi FROM pesanan");
$stats = mysqli_fetch_assoc($query_stats);

$total_penjualan = $stats['total_penjualan'] ? $stats['total_penjualan'] : 0;
$total_transaksi = $stats['total_transaksi'] ? $stats['total_transaksi'] : 0;

// 2. Mengambil Total Item Terjual
$query_item = mysqli_query($conn, "SELECT SUM(qty) AS total_item FROM detail_pesanan");
$item = mysqli_fetch_assoc($query_item);
$item_terjual = $item['total_item'] ? $item['total_item'] : 0;

// 3. Menghitung Rata-rata Transaksi
$rata_transaksi = ($total_transaksi > 0) ? ($total_penjualan / $total_transaksi) : 0;

// 4. Mengambil Data Produk Terlaris
$query_terlaris = mysqli_query($conn, "
    SELECT m.nama_menu, SUM(dp.qty) as total_qty, SUM(dp.subtotal) as total_pendapatan
    FROM detail_pesanan dp
    JOIN menu m ON dp.id_menu = m.id
    GROUP BY dp.id_menu
    ORDER BY total_qty DESC
    LIMIT 5
");

// 5. DATA UNTUK GRAFIK TREN PENJUALAN
$query_tren = mysqli_query($conn, "
    SELECT DATE(waktu_pesan) as tanggal, SUM(total_harga) as total_harian
    FROM pesanan
    GROUP BY DATE(waktu_pesan)
    ORDER BY DATE(waktu_pesan) ASC
    LIMIT 7
");

$label_tanggal = [];
$data_pendapatan = [];

while ($row = mysqli_fetch_assoc($query_tren)) {
    $label_tanggal[] = date('d M', strtotime($row['tanggal']));
    $data_pendapatan[] = $row['total_harian'];
}

$json_tanggal = json_encode($label_tanggal);
$json_pendapatan = json_encode($data_pendapatan);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Owner Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAF6F0;
        }

        .bg-sidebar {
            background-color: #5e3a21;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden text-gray-800">

    <aside class="w-[260px] bg-sidebar text-white flex flex-col shrink-0 transition-all">
        <div class="p-6 pb-8 border-b border-white/10">
            <h1 class="text-xl font-bold tracking-wide">Kue Balok Lumer GenZ</h1>
            <p class="text-xs text-white/70 mt-1">Owner Panel</p>
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="owner_laporan.php"
                class="flex items-center gap-3 bg-white/20 text-white p-3 rounded-xl font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Laporan
            </a>
        </nav>

        <div class="p-6 border-t border-white/10">
            <a href="index.php"
                class="flex items-center gap-3 text-white/70 hover:text-white text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-10 bg-[#FAF6F0]">

        <header class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-3xl font-bold text-[#5e3a21]">Laporan Penjualan</h2>
                <p class="text-gray-500 text-sm mt-1">Analisis dan laporan penjualan bisnis</p>
            </div>
            <button id="btn-export"
                class="bg-[#b58c67] text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-[#a07954] transition flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </button>
        </header>

        <div id="area-laporan" class="bg-[#FAF6F0] pb-8 pt-2">

            <div id="judul-pdf" class="hidden mb-6 text-center">
                <h1 class="text-2xl font-bold text-[#5e3a21]">Laporan Penjualan - Kue Balok Lumer GenZ</h1>
                <p class="text-sm text-gray-500">Dicetak pada: <?= date('d M Y, H:i') ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[12px] text-gray-500 mb-2">Total Penjualan</p>
                    <h3 class="text-xl font-bold text-[#5e3a21]">Rp <?= number_format($total_penjualan, 0, ',', '.') ?>
                    </h3>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[12px] text-gray-500 mb-2">Total Transaksi</p>
                    <h3 class="text-xl font-bold text-[#5e3a21]"><?= number_format($total_transaksi, 0, ',', '.') ?>
                    </h3>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[12px] text-gray-500 mb-2">Rata-rata Transaksi</p>
                    <h3 class="text-xl font-bold text-[#5e3a21]">Rp <?= number_format($rata_transaksi, 0, ',', '.') ?>
                    </h3>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[12px] text-gray-500 mb-2">Item Terjual</p>
                    <h3 class="text-xl font-bold text-[#5e3a21]"><?= number_format($item_terjual, 0, ',', '.') ?></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800 mb-6">Tren Penjualan</h3>
                    <div class="h-56 relative w-full bg-white">
                        <canvas id="grafikTren"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">Produk Terlaris</h3>
                    <div class="flex-1 overflow-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead>
                                <tr class="text-gray-500 text-xs border-b border-gray-100">
                                    <th class="pb-3 font-medium">Produk</th>
                                    <th class="pb-3 font-medium text-center">Terjual</th>
                                    <th class="pb-3 font-medium text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php while ($produk = mysqli_fetch_assoc($query_terlaris)): ?>
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3 font-medium text-gray-800"><?= $produk['nama_menu'] ?></td>
                                        <td class="py-3 text-center text-gray-600"><?= $produk['total_qty'] ?></td>
                                        <td class="py-3 text-right font-medium text-gray-800">Rp
                                            <?= number_format($produk['total_pendapatan'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <?php if (mysqli_num_rows($query_terlaris) == 0): ?>
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-gray-500 text-xs">Belum ada data
                                            penjualan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        const labelTanggal = <?= $json_tanggal ?>;
        const dataPendapatan = <?= $json_pendapatan ?>;
        const ctx = document.getElementById('grafikTren').getContext('2d');

        const grafikTren = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelTanggal,
                datasets: [{
                    label: 'Pendapatan',
                    data: dataPendapatan,
                    borderColor: '#5e3a21',
                    backgroundColor: 'rgba(94, 58, 33, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#5e3a21',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

    <script>
        document.getElementById('btn-export').addEventListener('click', function () {
            // Ubah tombol menjadi teks "Memproses..." agar terlihat keren
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Memproses PDF...';
            btn.disabled = true;

            // Munculkan kop judul khusus PDF sementara
            document.getElementById('judul-pdf').style.display = 'block';

            // Area yang akan di-capture menjadi PDF
            const element = document.getElementById('area-laporan');

            // Pengaturan PDF
            const opt = {
                margin: [0.5, 0.5, 0.5, 0.5], // margin top, left, bottom, right (dalam inci)
                filename: 'Laporan_KueBalokGenZ.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true }, // scale 2 agar gambar resolusinya tajam
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' } // landscape agar muat kolom kiri dan kanan
            };

            // Jalankan proses HTML ke PDF
            html2pdf().set(opt).from(element).save().then(function () {
                // Kembalikan keadaan tombol dan sembunyikan judul PDF setelah selesai
                btn.innerHTML = originalText;
                btn.disabled = false;
                document.getElementById('judul-pdf').style.display = 'none';
            });
        });
    </script>

</body>

</html>