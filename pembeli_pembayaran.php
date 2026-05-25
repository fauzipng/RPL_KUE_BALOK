<?php
session_start();
// Redirect ke halaman sebelumnya jika keranjang tiba-tiba kosong
if (empty($_SESSION['keranjang'])) {
    header("Location: pembeli_pesanan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Kue Balok Lumer GenZ</title>
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
                    class="bg-[#f2e5d2] text-[#5e3a21] hover:bg-[#e6d6be] py-2 px-4 rounded-lg font-medium text-sm text-center block">Minuman</a>
                <a href="pembeli_pesanan.php"
                    class="bg-[#5e3a21] text-white py-2 px-4 rounded-lg font-medium text-sm text-center block shadow-sm">Pesanan</a>
            </ul>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto flex justify-center">
            <div class="w-full max-w-2xl">
                <a href="pembeli_pesanan.php"
                    class="inline-flex items-center text-sm font-medium text-[#5e3a21] hover:underline mb-6"><- Kembali
                        ke Pesanan</a>
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Pilih Metode Pembayaran</h2>

                        <form action="pembeli_proses.php" method="POST" class="space-y-6">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-3">Ringkasan Transaksi</h3>

                                <div class="flex justify-between text-sm mb-2 text-gray-600">
                                    <span>Subtotal
                                        (
                                        <?= $_SESSION['total_item'] ?> Item)
                                    </span>
                                    <span>Rp
                                        <?= number_format($_SESSION['subtotal'], 0, ',', '.') ?>
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm mb-4 text-gray-600">
                                    <span>Pajak (10%)</span>
                                    <span>Rp
                                        <?= number_format($_SESSION['pajak'], 0, ',', '.') ?>
                                    </span>
                                </div>
                                <div
                                    class="flex justify-between font-bold text-lg pt-4 border-t border-gray-100 text-black">
                                    <span>Total Pembayaran</span>
                                    <span class="text-[#5e3a21]">Rp
                                        <?= number_format($_SESSION['total_bayar'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-800 mb-4">Metode Pembayaran</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="metode_bayar" value="Tunai" class="peer hidden"
                                            required>
                                        <div
                                            class="bg-white border-2 border-gray-200 rounded-xl p-5 hover:bg-gray-50 peer-checked:border-[#5e3a21] peer-checked:bg-[#FAF6F0] flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                                Rp</div>
                                            <div>
                                                <h4 class="font-bold">Tunai / Cash</h4>
                                                <p class="text-xs">Bayar di kasir</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="metode_bayar" value="QRIS" class="peer hidden"
                                            required>
                                        <div
                                            class="bg-white border-2 border-gray-200 rounded-xl p-5 hover:bg-gray-50 peer-checked:border-[#5e3a21] peer-checked:bg-[#FAF6F0] flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                                QR</div>
                                            <div>
                                                <h4 class="font-bold">QRIS</h4>
                                                <p class="text-xs">Scan via M-Banking</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit"
                                    class="bg-[#5e3a21] text-white font-bold py-3 px-12 rounded-lg hover:bg-[#4a2e1a] shadow-lg">Konfirmasi
                                    Pembayaran</button>
                            </div>
                        </form>
            </div>
        </main>
    </div>
</body>

</html>