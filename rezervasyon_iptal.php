<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["kullanici_id"]) || !isset($_GET['id'])) { 
    header("location: index.php"); 
    exit; 
}

$rez_id = intval($_GET['id']);


$rol = isset($_SESSION["rol"]) ? $_SESSION["rol"] : 'ogrenci';
$iptal_kodu = ($rol == 'admin') ? 3 : 1;

$sorgu = "SELECT masa_id FROM rezervasyonlar WHERE id = $rez_id";
$sonuc = mysqli_query($db, $sorgu);

if ($row = mysqli_fetch_assoc($sonuc)) {
    $masa_id = $row['masa_id'];
    
    
    mysqli_query($db, "UPDATE masalar SET durum = 'bos' WHERE id = $masa_id");
    
    // Rezervasyonu iptal koduyla işaretleme (1: Öğrenci, 2: Sistem, 3: Admin)
    mysqli_query($db, "UPDATE rezervasyonlar SET iptal_edildi = $iptal_kodu WHERE id = $rez_id");
}

// Geldiği sayfaya geri gönder
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: index.php");
}
exit;
?>