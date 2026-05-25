<?php
session_start();
require 'koneksi.php';

$pesan_sukses = "";

// 1. Logika untuk Menangkap Klik Tombol Tambah di halaman ini
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Tambah kuantitas jika sudah ada, atau set 1 jika menu baru
    if (isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id]++;
    } else {
        $_SESSION['keranjang'][$id] = 1;
    }

    // Redirect (kembali) ke halaman ini sendiri dengan membawa sinyal "sukses"
    header("Location: pembeli_minuman.php?success=true");
    exit;
}

// 2. Menangkap sinyal sukses untuk memunculkan notifikasi
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $pesan_sukses = "Minuman berhasil ditambahkan ke Pesanan!";
}

$query_minuman = mysqli_query($conn, "SELECT * FROM menu WHERE id_kategori = 2");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Minuman - Kue Balok Lumer GenZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAF6F0;
        }
    </style>
</head>

<body class="text-gray-800 h-screen flex flex-col overflow-hidden">
    <header class="bg-[#5e3a21] text-white py-4 px-6 flex items-center shadow-md w-full z-10 shrink-0">
        <h1 class="text-lg font-semibold tracking-wide leading-tight ml-4">Kue Balok Lumer GenZ</h1>
    </header>

    <div class="flex-1 flex w-full overflow-hidden">
        <aside class="w-[220px] bg-white px-5 py-6 shadow-sm border-r border-gray-200 shrink-0 flex flex-col">
            <h3 class="font-semibold text-gray-700 mb-4 text-sm">Kategori</h3>
            <ul class="space-y-3">
                <a href="pembeli_kue.php"
                    class="bg-[#f2e5d2] text-[#5e3a21] hover:bg-[#e6d6be] py-2 px-4 rounded-lg font-medium text-sm text-center block">Kue</a>
                <a href="pembeli_minuman.php"
                    class="bg-[#5e3a21] text-white py-2 px-4 rounded-lg font-medium text-sm text-center block shadow-sm">Minuman</a>
                <a href="pembeli_pesanan.php"
                    class="bg-[#f2e5d2] text-[#5e3a21] hover:bg-[#e6d6be] py-2 px-4 rounded-lg font-medium text-sm text-center block">Pesanan
                    <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                        <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1">
                            <?= array_sum($_SESSION['keranjang']) ?>
                        </span>
                    <?php endif; ?>
                </a>
            </ul>
            <a href="index.php"
                class="block text-center mt-auto text-red-500 text-sm font-medium hover:underline pb-4">Keluar</a>
        </aside>

        <main class="flex-1 p-6 overflow-y-auto">

            <?php if ($pesan_sukses): ?>
                <div
                    class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center justify-between shadow-sm transition-all">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <?= $pesan_sukses ?>
                    </div>
                    <button onclick="this.parentElement.style.display='none'"
                        class="text-green-700 hover:text-green-900 font-bold text-lg leading-none">&times;</button>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php while ($minuman = mysqli_fetch_assoc($query_minuman)): ?>
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col relative">
                        <?php if ($minuman['status'] == 'Habis'): ?>
                            <div class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center">
                                <span class="bg-red-500 text-white font-bold px-3 py-1 rounded text-xs">STOK HABIS</span>
                            </div>
                        <?php endif; ?>

                        <div class="bg-gray-100 h-40 w-full overflow-hidden flex items-center justify-center">
                            <?php if (!empty($minuman['gambar']) && $minuman['gambar'] != 'default.jpg'): ?>
                                <img src="uploads/<?= $minuman['gambar'] ?>" alt="<?= $minuman['nama_menu'] ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">Tidak ada gambar</span>
                            <?php endif; ?>
                        </div>

                        <div class="p-4 flex flex-col flex-1">
                            <h4 class="font-semibold text-sm mb-1 text-gray-800">
                                <?= $minuman['nama_menu'] ?>
                            </h4>
                            <div class="mt-auto flex justify-between items-center">
                                <span class="font-semibold text-[#5e3a21] text-sm">Rp
                                    <?= number_format($minuman['harga'], 0, ',', '.') ?>
                                </span>
                                <?php if ($minuman['status'] != 'Habis'): ?>
                                    <a href="pembeli_minuman.php?action=add&id=<?= $minuman['id'] ?>"
                                        class="bg-[#5e3a21] text-white w-6 h-6 rounded flex items-center justify-center text-lg hover:bg-[#4a2e1a] transition leading-none pb-0.5">+</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>
</body>

</html>