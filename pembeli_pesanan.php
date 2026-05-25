<?php
session_start();
require 'koneksi.php';

// Logika untuk Menambah, Mengurangi, dan Menghapus Item di Keranjang
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    if ($action == 'add') {
        if (isset($_SESSION['keranjang'][$id])) {
            $_SESSION['keranjang'][$id]++; // Tambah kuantitas
        } else {
            $_SESSION['keranjang'][$id] = 1; // Masukkan barang baru
        }
    } elseif ($action == 'min') {
        if (isset($_SESSION['keranjang'][$id])) {
            $_SESSION['keranjang'][$id]--; // Kurangi kuantitas
            if ($_SESSION['keranjang'][$id] <= 0) {
                unset($_SESSION['keranjang'][$id]); // Hapus jika 0
            }
        }
    } elseif ($action == 'del') {
        unset($_SESSION['keranjang'][$id]); // Hapus item langsung
    }

    // Redirect agar URL bersih kembali
    header("Location: pembeli_pesanan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Kue Balok Lumer GenZ</title>
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
                    class="bg-[#f2e5d2] text-[#5e3a21] hover:bg-[#e6d6be] py-2 px-4 rounded-lg font-medium text-sm transition text-center block">Kue</a>
                <a href="pembeli_minuman.php"
                    class="bg-[#f2e5d2] text-[#5e3a21] hover:bg-[#e6d6be] py-2 px-4 rounded-lg font-medium text-sm transition text-center block">Minuman</a>
                <a href="pembeli_pesanan.php"
                    class="bg-[#5e3a21] text-white py-2 px-4 rounded-lg font-medium text-sm transition text-center block shadow-sm">Pesanan</a>
            </ul>
            <a href="index.php"
                class="block text-center mt-auto text-red-500 text-sm font-medium hover:underline pb-4">Keluar</a>
        </aside>

        <main class="flex-1 flex flex-col relative overflow-hidden">
            <div class="flex-1 p-8 overflow-y-auto relative">

                <?php
                $subtotal = 0;
                $total_item = 0;

                // Cek apakah keranjang kosong
                if (empty($_SESSION['keranjang'])) {
                    echo '<div class="text-center text-gray-400 mt-20"><p>Belum ada pesanan yang dipilih.</p></div>';
                } else {
                    // Looping data keranjang secara dinamis
                    foreach ($_SESSION['keranjang'] as $id_menu => $qty):
                        $query = mysqli_query($conn, "SELECT * FROM menu WHERE id = '$id_menu'");
                        $menu = mysqli_fetch_assoc($query);
                        $subtotal_item = $menu['harga'] * $qty;
                        $subtotal += $subtotal_item;
                        $total_item += $qty;
                        ?>
                        <div
                            class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-4 flex justify-between items-center relative z-10">
                            <div>
                                <h4 class="font-semibold text-sm text-gray-800">
                                    <?= $menu['nama_menu'] ?>
                                </h4>
                                <p class="text-xs text-gray-400 mb-2">Rp
                                    <?= number_format($menu['harga'], 0, ',', '.') ?>
                                </p>
                                <div class="flex items-center space-x-3 bg-gray-100 w-fit rounded px-2 py-1">
                                    <a href="pembeli_pesanan.php?action=min&id=<?= $id_menu ?>"
                                        class="text-gray-500 hover:text-black font-bold text-xs px-1">-</a>
                                    <span class="text-xs font-medium w-4 text-center">
                                        <?= $qty ?>
                                    </span>
                                    <a href="pembeli_pesanan.php?action=add&id=<?= $id_menu ?>"
                                        class="text-gray-500 hover:text-black font-bold text-xs px-1">+</a>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="pembeli_pesanan.php?action=del&id=<?= $id_menu ?>"
                                    class="text-red-400 mb-6 block ml-auto hover:text-red-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                                <span class="font-semibold text-[#5e3a21] text-sm">Rp
                                    <?= number_format($subtotal_item, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                        <?php
                    endforeach;
                }

                // Perhitungan Pajak dan Grand Total
                $pajak = $subtotal * 0.1; // Pajak 10%
                $total_bayar = $subtotal + $pajak;

                // Simpan total ke Session untuk digunakan di halaman Pembayaran
                $_SESSION['subtotal'] = $subtotal;
                $_SESSION['pajak'] = $pajak;
                $_SESSION['total_bayar'] = $total_bayar;
                $_SESSION['total_item'] = $total_item;
                ?>
            </div>

            <div class="bg-white px-10 py-6 border-t border-gray-200 shrink-0">
                <div class="flex justify-between text-sm mb-1 text-gray-600">
                    <span>Subtotal (
                        <?= $total_item ?> Item)
                    </span>
                    <span>Rp
                        <?= number_format($subtotal, 0, ',', '.') ?>
                    </span>
                </div>
                <div class="flex justify-between text-sm mb-4 text-gray-600">
                    <span>Pajak (10%)</span>
                    <span>Rp
                        <?= number_format($pajak, 0, ',', '.') ?>
                    </span>
                </div>
                <div class="flex justify-between font-bold text-lg mb-6 text-black">
                    <span>Total</span>
                    <span>Rp
                        <?= number_format($total_bayar, 0, ',', '.') ?>
                    </span>
                </div>

                <div class="flex justify-center">
                    <?php if ($total_item > 0): ?>
                        <a href="pembeli_pembayaran.php"
                            class="inline-block bg-[#5e3a21] text-white font-semibold py-3 px-12 rounded-lg hover:bg-[#4a2e1a] transition shadow-lg text-center w-full md:w-auto">Lanjut
                            ke Pembayaran</a>
                    <?php else: ?>
                        <button disabled
                            class="inline-block bg-gray-300 text-gray-500 font-semibold py-3 px-12 rounded-lg cursor-not-allowed text-center w-full md:w-auto">Keranjang
                            Kosong</button>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>
</body>

</html>