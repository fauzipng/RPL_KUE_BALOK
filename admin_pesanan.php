<?php
require 'koneksi.php';

// Query diubah menjadi ORDER BY p.waktu_pesan DESC agar yang terbaru di atas
$query = "SELECT p.*, 
            (SELECT GROUP_CONCAT(CONCAT(m.nama_menu, ' x', dp.qty) SEPARATOR ', ') 
             FROM detail_pesanan dp 
             JOIN menu m ON dp.id_menu = m.id 
             WHERE dp.id_pesanan = p.id) AS daftar_item 
          FROM pesanan p 
          ORDER BY p.waktu_pesan DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <p class="text-xs text-white/70 mt-1">Admin Panel</p>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="admin_pesanan.php"
                class="flex items-center gap-3 bg-white/20 text-white p-3 rounded-xl font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Pesanan
            </a>
            <a href="admin_menu.php"
                class="flex items-center gap-3 text-white/70 hover:bg-white/10 hover:text-white p-3 rounded-xl font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Menu
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

    <main class="flex-1 overflow-y-auto p-10">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-[#5e3a21]">Pesanan</h2>
            <p class="text-gray-500 text-sm mt-1">Data langsung dari Database</p>
        </header>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-800 mb-6">Daftar Pesanan</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="text-gray-500 text-xs border-b border-gray-100">
                            <th class="pb-4 font-medium">ID Pesanan</th>
                            <th class="pb-4 font-medium">Pelanggan</th>
                            <th class="pb-4 font-medium">Item</th>
                            <th class="pb-4 font-medium">Total</th>
                            <th class="pb-4 font-medium">Pembayaran</th>
                            <th class="pb-4 font-medium">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            $waktuFormat = date('d M Y, H:i', strtotime($row['waktu_pesan']));
                            ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 text-gray-600">
                                    <?= $row['kode_pesanan'] ?>
                                </td>

                                <td class="py-4 font-medium text-gray-800">
                                    <?= str_replace('Tamu', 'Pelanggan', $row['nama_pelanggan']) ?>
                                </td>

                                <td class="py-4 text-gray-500">
                                    <?= $row['daftar_item'] ?: 'Belum ada item' ?>
                                </td>
                                <td class="py-4 font-medium">Rp
                                    <?= number_format($row['total_harga'], 0, ',', '.') ?>
                                </td>
                                <td class="py-4">
                                    <span
                                        class="border border-gray-200 text-gray-500 px-3 py-1 rounded-full text-[11px] font-medium">
                                        <?= $row['metode_pembayaran'] ?>
                                    </span>
                                </td>
                                <td class="py-4 text-gray-500 text-xs">
                                    <?= $waktuFormat ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>