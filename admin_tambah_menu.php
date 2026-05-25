<?php
require 'koneksi.php';

// Cek apakah ini mode EDIT (ada ID di URL) atau mode TAMBAH BARU
$id_menu = isset($_GET['id']) ? $_GET['id'] : '';
$is_edit = $id_menu ? true : false;
$pesan_sukses = "";

// Jika mode Edit, ambil data asli dari database
$data_edit = null;
if ($is_edit) {
    $result_edit = mysqli_query($conn, "SELECT * FROM menu WHERE id='$id_menu'");
    $data_edit = mysqli_fetch_assoc($result_edit);
}

// Jika Form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_menu = mysqli_real_escape_string($conn, $_POST['nama_menu']);
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];

    // Default gambar: Jika edit, gunakan gambar lama. Jika tambah baru, default.jpg
    $gambar_nama = $is_edit ? $data_edit['gambar'] : 'default.jpg';

    // Logika Upload Gambar (Jika ada file yang diunggah)
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_name = $_FILES['gambar']['tmp_name'];

        // Ambil ekstensi file (contoh: jpg, png)
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));

        // Buat nama file unik agar tidak bentrok
        $gambar_baru = uniqid() . '.' . $ekstensi;
        $direktori = 'uploads/'; // Pastikan folder ini sudah Anda buat!

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if (move_uploaded_file($tmp_name, $direktori . $gambar_baru)) {
                $gambar_nama = $gambar_baru; // Gunakan nama file yang baru diupload
            }
        } else {
            $pesan_sukses = "Ekstensi gambar tidak diperbolehkan! Gunakan JPG/PNG.";
        }
    }

    // Eksekusi Database jika tidak ada error ekstensi
    if ($pesan_sukses == "") {
        if ($is_edit) {
            $query = "UPDATE menu SET id_kategori='$id_kategori', nama_menu='$nama_menu', harga='$harga', status='$status', gambar='$gambar_nama' WHERE id='$id_menu'";
            $pesan_sukses = "Menu berhasil diperbarui!";
        } else {
            $query = "INSERT INTO menu (id_kategori, nama_menu, harga, status, gambar) VALUES ('$id_kategori', '$nama_menu', '$harga', '$status', '$gambar_nama')";
            $pesan_sukses = "Menu baru berhasil ditambahkan!";
        }
        mysqli_query($conn, $query);

        // Refresh data edit setelah update
        if ($is_edit) {
            $result_edit = mysqli_query($conn, "SELECT * FROM menu WHERE id='$id_menu'");
            $data_edit = mysqli_fetch_assoc($result_edit);
        }
    }
}

$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $is_edit ? 'Edit Menu' : 'Tambah Menu' ?> - Admin
    </title>
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
            <h1 class="text-xl font-bold">Kue Balok GenZ</h1>
            <p class="text-xs text-white/70 mt-1">Admin Panel</p>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="admin_pesanan.php"
                class="flex items-center gap-3 text-white/70 hover:bg-white/10 p-3 rounded-xl font-medium transition">Pesanan</a>
            <a href="admin_menu.php"
                class="flex items-center gap-3 bg-white/20 text-white p-3 rounded-xl font-medium transition">Menu</a>
        </nav>
        <div class="p-6 border-t border-white/10">
            <a href="index.php"
                class="flex items-center gap-3 text-white/70 hover:text-white text-sm font-medium transition">Keluar</a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-10">
        <header class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="admin_menu.php" class="hover:text-[#5e3a21] transition">Manajemen Menu</a>
                <span>/</span>
                <span class="text-gray-800 font-medium">
                    <?= $is_edit ? 'Edit Menu' : 'Tambah Menu' ?>
                </span>
            </div>
            <h2 class="text-3xl font-bold text-[#5e3a21]">
                <?= $is_edit ? 'Edit Menu' : 'Tambah Menu Baru' ?>
            </h2>
        </header>

        <?php if (!empty($pesan_sukses)): ?>
            <div
                class="mb-6 <?= strpos($pesan_sukses, 'berhasil') !== false ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?> border px-4 py-3 rounded-lg text-sm font-medium">
                <?= $pesan_sukses; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-4xl">
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Nama Produk</label>
                        <input type="text" name="nama_menu" value="<?= $data_edit ? $data_edit['nama_menu'] : '' ?>"
                            required
                            class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-700/20 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Kategori</label>
                        <select name="id_kategori" required
                            class="w-full border border-gray-200 rounded-lg p-3 text-sm bg-white focus:ring-2 focus:ring-amber-700/20 outline-none">
                            <?php while ($kategori = mysqli_fetch_assoc($kategori_query)): ?>
                                <option value="<?= $kategori['id'] ?>" <?= ($data_edit && $data_edit['id_kategori'] == $kategori['id']) ? 'selected' : '' ?>>
                                    <?= $kategori['nama_kategori'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Harga (Rp)</label>
                        <input type="number" name="harga" value="<?= $data_edit ? $data_edit['harga'] : '' ?>" required
                            class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-700/20 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Status</label>
                        <select name="status" required
                            class="w-full border border-gray-200 rounded-lg p-3 text-sm bg-white focus:ring-2 focus:ring-amber-700/20 outline-none">
                            <option value="Tersedia" <?= ($data_edit && $data_edit['status'] == 'Tersedia') ? 'selected' : '' ?>>Tersedia</option>
                            <option value="Habis" <?= ($data_edit && $data_edit['status'] == 'Habis') ? 'selected' : '' ?>>
                                Habis</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Gambar Produk (Opsional)</label>
                        <?php if ($is_edit && $data_edit['gambar'] != 'default.jpg'): ?>
                            <p class="text-[10px] text-gray-400 mb-1">Gambar saat ini:
                                <?= $data_edit['gambar'] ?>
                            </p>
                        <?php endif; ?>
                        <input type="file" name="gambar" accept="image/png, image/jpeg, image/jpg"
                            class="w-full border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-700/20 outline-none file:mr-4 file:py-3 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-[#FAF6F0] file:text-[#5e3a21] hover:file:bg-[#f2e5d2] cursor-pointer">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8 pt-6">
                    <a href="admin_menu.php"
                        class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Batal</a>
                    <button type="submit"
                        class="bg-[#5e3a21] text-white px-8 py-2.5 rounded-lg text-sm font-medium hover:bg-[#4a2e1a] shadow-sm transition">
                        <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Menu' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>