<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["kullanici_id"]) || !isset($_GET['id'])) { header("location: index.php"); exit; }

$rez_id = intval($_GET['id']);
$sorgu = "SELECT masa_id FROM rezervasyonlar WHERE id = $rez_id";
$sonuc = mysqli_query($db, $sorgu);

if ($row = mysqli_fetch_assoc($sonuc)) {
    $masa_id = $row['masa_id'];
    mysqli_query($db, "UPDATE masalar SET durum = 'bos' WHERE id = $masa_id");
    mysqli_query($db, "UPDATE rezervasyonlar SET iptal_edildi = 1 WHERE id = $rez_id");
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>