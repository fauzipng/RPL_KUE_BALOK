<?php
// 1. Hubungkan ke database melalui file koneksi
require 'koneksi.php';

$error_message = "";

// 2. Jalankan logika pemeriksaan saat tombol "Masuk" ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form input dan amankan dari SQL Injection
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    // Query untuk mencari user yang cocok di database
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    // Jika user ditemukan
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Cek role user dan alihkan ke halaman masing-masing
        if ($user['role'] === 'admin') {
            header("Location: admin_pesanan.php");
            exit;
        } elseif ($user['role'] === 'owner') {
            header("Location: owner_laporan.php"); // Diarahkan langsung ke laporan
            exit;
        } elseif ($user['role'] === 'pembeli') {
            header("Location: pembeli_kue.php");
            exit;
        }
    } else {
        // Jika tidak ada data yang cocok, siapkan pesan error
        $error_message = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kue Balok Lumer GenZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Gradasi background krem lembut sesuai dengan gambar */
            background: linear-gradient(135deg, #FAF6F0 0%, #F0E5D6 100%);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center text-gray-800">

    <div class="text-center mb-8 flex flex-col items-center">
        <div class="w-14 h-14 bg-[#5e3a21] rounded-full flex items-center justify-center mb-4 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M12 2c-.4 0-.8.3-.9.7l-1.5 5.3H5c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-2c0-1.1-.9-2-2-2h-4.6l-1.5-5.3c-.1-.4-.5-.7-.9-.7zm-7 14v4c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2v-4H5z" />
            </svg>
        </div>
        <h1 class="text-3xl text-[#5e3a21] font-medium tracking-wide">Kue Balok Lumer GenZ</h1>
        <p class="text-sm text-gray-500 mt-2">Sistem Pemesanan & Kasir</p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-lg w-[400px]">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">Login</h2>
            <p class="text-xs text-gray-500">Masuk untuk mengakses sistem</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-2.5 rounded-lg text-xs font-medium">
                <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4 text-left">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required
                    class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required
                    class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-700/20 focus:border-amber-700 outline-none transition-all">
            </div>

            <button type="submit"
                class="w-full bg-[#5e3a21] hover:bg-[#4a2e1a] text-white font-medium py-3 rounded-lg transition-colors duration-300 mt-2">
                Masuk
            </button>
        </form>

        <div class="flex items-center my-5">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="px-3 text-[10px] font-medium text-gray-400 tracking-wider">ATAU</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <a href="pembeli_kue.php"
            class="block w-full text-center border-2 border-[#5e3a21] text-[#5e3a21] font-medium py-2.5 rounded-lg hover:bg-[#FAF6F0] transition-colors duration-300">
            Masuk sebagai Pembeli
        </a>

        <div class="mt-6 text-center">
            <p class="text-[11px] text-gray-400">Admin & Owner wajib login menggunakan akun</p>
        </div>
    </div>

</body>

</html>