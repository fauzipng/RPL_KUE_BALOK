<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['id_pesanan_aktif'])) {
    $id_pesanan = $_SESSION['id_pesanan_aktif'];

    $query = mysqli_query($conn, "SELECT status_pesanan FROM pesanan WHERE id = '$id_pesanan'");
    if ($row = mysqli_fetch_assoc($query)) {
        // Akan mencetak "Menunggu" atau "Selesai" ke JavaScript
        echo $row['status_pesanan'];
    }
}
?>