<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "libreserve_db";

$db = mysqli_connect($host, $user, $pass, $db_name);
if (!$db) { die("Bağlantı hatası: " . mysqli_connect_error()); }

mysqli_set_charset($db, "utf8");
date_default_timezone_set('Europe/Istanbul');

$simdi_tarih = date('Y-m-d');
$simdi_saat = date('H:i:s');
$on_bes_dk_once = date('H:i:s', strtotime('-15 minutes'));

// 15 dk onaylanmayanları SİLME, İPTAL OLARAK İŞARETLE
$gecikme_sorgu = "SELECT id, masa_id FROM rezervasyonlar WHERE tarih = '$simdi_tarih' AND onaylandi = 0 AND iptal_edildi = 0 AND baslangic_saati < '$on_bes_dk_once'";
$gecikme_sonuc = mysqli_query($db, $gecikme_sorgu);
while ($r = mysqli_fetch_assoc($gecikme_sonuc)) {
    $rid = $r['id']; $mid = $r['masa_id'];
    mysqli_query($db, "UPDATE masalar SET durum = 'bos' WHERE id = $mid");
    mysqli_query($db, "UPDATE rezervasyonlar SET iptal_edildi = 1 WHERE id = $rid");
}

// Süresi bitenleri yeşile çevir
$biten_sorgu = "UPDATE masalar SET durum = 'bos' WHERE durum = 'dolu' AND id NOT IN (SELECT masa_id FROM rezervasyonlar WHERE iptal_edildi = 0 AND (tarih > '$simdi_tarih' OR (tarih = '$simdi_tarih' AND bitis_saati > '$simdi_saat')))";
mysqli_query($db, $biten_sorgu);
?>