<?php
require 'koneksi.php';

// Logika untuk Menghapus Menu
if (isset($_GET['delete'])) {
    $id_hapus = $_GET['delete'];

    // (Opsional) Mengambil nama file gambar sebelum dihapus agar bisa dihapus juga dari folder uploads
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM menu WHERE id = '$id_hapus'");
    $data_gambar = mysqli_fetch_assoc($query_gambar);
    if ($data_gambar['gambar'] && $data_gambar['gambar'] != 'default.jpg') {
        $file_path = 'uploads/' . $data_gambar['gambar'];
        if (file_exists($file_path)) {
            unlink($file_path); // Menghapus file fisik dari folder
        }
    }

    $delete_query = "DELETE FROM menu WHERE id = '$id_hapus'";
    mysqli_query($conn, $delete_query);
    // Refresh halaman setelah dihapus
    header("Location: admin_menu.php");
    exit;
}

$query = "SELECT menu.*, kategori.nama_kategori 
          FROM menu 
          JOIN kategori ON menu.id_kategori = kategori.id 
          ORDER BY menu.id ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Menu - Admin</title>
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
            <h1 class="text-xl font-bold tracking-wide">Kue Balok GenZ</h1>
            <p class="text-xs text-white/70 mt-1">Admin Panel</p>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="admin_pesanan.php"
                class="flex items-center gap-3 text-white/70 hover:bg-white/10 hover:text-white p-3 rounded-xl font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Pesanan
            </a>
            <a href="admin_menu.php"
                class="flex items-center gap-3 bg-white/20 text-white p-3 rounded-xl font-medium transition">
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
        <header class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-3xl font-bold text-[#5e3a21]">Manajemen Menu</h2>
                <p class="text-gray-500 text-sm mt-1">Kelola menu dan produk dari database</p>
            </div>
            <a href="admin_tambah_menu.php"
                class="bg-[#5e3a21] text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-[#4a2e1a] transition flex items-center gap-2">
                <span>+</span> Tambah Menu
            </a>
        </header>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-800 mb-6">Daftar Menu</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="text-gray-500 text-xs border-b border-gray-100">
                            <th class="pb-4 font-medium w-24">Gambar</th>
                            <th class="pb-4 font-medium">Nama</th>
                            <th class="pb-4 font-medium">Kategori</th>
                            <th class="pb-4 font-medium">Harga</th>
                            <th class="pb-4 font-medium">Status</th>
                            <th class="pb-4 font-medium text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-gray-50/50 transition group">

                                <td class="py-3">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center border border-gray-200">
                                        <?php if (!empty($row['gambar']) && $row['gambar'] != 'default.jpg'): ?>
                                            <img src="uploads/<?= $row['gambar'] ?>" alt="<?= $row['nama_menu'] ?>"
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="text-[8px] text-gray-400">No Img</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="py-3 font-medium text-gray-800">
                                    <?= $row['nama_menu'] ?>
                                </td>
                                <td class="py-3"><span class="border px-3 py-1 rounded-full text-[11px] font-medium">
                                        <?= $row['nama_kategori'] ?>
                                    </span></td>
                                <td class="py-3 font-medium text-gray-600">Rp
                                    <?= number_format($row['harga'], 0, ',', '.') ?>
                                </td>
                                <td class="py-3">
                                    <?php $statusColor = ($row['status'] == 'Tersedia') ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'; ?>
                                    <span class="<?= $statusColor ?> px-3 py-1 rounded-full text-[11px] font-medium">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="py-3 text-right pr-4">
                                    <div
                                        class="flex items-center justify-end gap-3 opacity-70 group-hover:opacity-100 transition">
                                        <a href="admin_tambah_menu.php?id=<?= $row['id'] ?>"
                                            class="text-gray-500 hover:text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        <a href="admin_menu.php?delete=<?= $row['id'] ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?');"
                                            class="text-red-400 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </a>
                                    </div>
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