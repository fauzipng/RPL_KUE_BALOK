<?php
require 'koneksi.php';

// Logika untuk Mengonfirmasi Pesanan
if (isset($_GET['konfirmasi'])) {
    $id_pesanan = $_GET['konfirmasi'];
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan = 'Selesai' WHERE id = '$id_pesanan'");

    // Refresh halaman agar status langsung berubah dan parameter URL bersih
    header("Location: admin_pesanan.php");
    exit;
}

// Query mengambil data pesanan beserta daftar itemnya (yang terbaru di atas)
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
            <p class="text-gray-500 text-sm mt-1">Data langsung dari Database (Real-time)</p>
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
                            <th class="pb-4 font-medium">Status</th>
                            <th class="pb-4 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-pesanan" class="divide-y divide-gray-50">
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
                                <td class="py-4">
                                    <?php if ($row['status_pesanan'] == 'Pending'): ?>
                                        <span
                                            class="bg-yellow-100 text-yellow-700 border border-yellow-200 px-3 py-1 rounded-full text-[11px] font-medium">Pending</span>
                                    <?php else: ?>
                                        <span
                                            class="bg-green-100 text-green-700 border border-green-200 px-3 py-1 rounded-full text-[11px] font-medium">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4">
                                    <?php if ($row['status_pesanan'] == 'Pending'): ?>
                                        <a href="admin_pesanan.php?konfirmasi=<?= $row['id'] ?>"
                                            class="bg-[#5e3a21] text-white px-3 py-1.5 rounded-lg text-[11px] font-medium hover:bg-[#4a2e1a] transition shadow-sm">
                                            Konfirmasi
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Jalankan fungsi ini setiap 3 detik (3000 milidetik)
        setInterval(function () {
            fetch('admin_pesanan.php')
                .then(response => response.text())
                .then(html => {
                    // Ubah teks HTML yang diambil menjadi dokumen DOM
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');

                    // Ekstrak HANYA bagian tabel (tbody) yang baru
                    let tbodyBaru = doc.getElementById('tabel-pesanan').innerHTML;

                    // Timpa isi tabel lama dengan yang baru tanpa membuat layar berkedip
                    document.getElementById('tabel-pesanan').innerHTML = tbodyBaru;
                })
                .catch(error => console.error('Gagal mengambil data pesanan baru:', error));
        }, 3000);
    </script>
</body>

</html>