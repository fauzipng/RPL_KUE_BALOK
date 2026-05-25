<?php
// Mengambil metode pembayaran dari form
$metode = $_POST['metode_bayar'];

if ($metode == 'QRIS') {
    // Jika QRIS, arahkan ke halaman Scan QR Code
    header("Location: pembeli_qris.php");
    exit;
} else {
    // Jika Tunai, langsung arahkan ke halaman sukses
    header("Location: pembeli_sukses.php?metode=Tunai");
    exit;
}
?>