<?php
session_start();
require_once 'config.php';
date_default_timezone_set('Europe/Istanbul');

if (!isset($_SESSION["kullanici_id"]) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: salonlar.php");
    exit;
}

$kullanici_id = $_SESSION["kullanici_id"];
$masa_id = trim($_POST['masa_id']);
$tarih = trim($_POST['tarih']);
$baslangic = trim($_POST['baslangic']);
$bitis = trim($_POST['bitis']);

//GEÇMİŞ VE GELECEK SAAT/TARİH KONTROLÜ
$simdi_tarih = date('Y-m-d');
$simdi_saat = date('H:i:s');
$max_tarih = date('Y-m-d', strtotime('+10 days'));

if ($tarih < $simdi_tarih || ($tarih == $simdi_tarih && $baslangic < $simdi_saat)) {
    echo "<script>alert('HATA: Geçmiş günlere veya saatlere rezervasyon yapamazsınız. Lütfen güncel bir saat seçin.'); window.location.href='salonlar.php';</script>";
    exit;
}

// 10 GÜN SINIRINI HERKESE UYGULAMA
if ($tarih > $max_tarih) {
    echo "<script>alert('HATA: En fazla 10 gün sonrası için rezervasyon yapabilirsiniz.'); window.location.href='salonlar.php';</script>";
    exit;
}

//AYNI KULLANICININ ÇAKIŞAN RANDEVUSU VAR MI KONTROLÜ
$kullanici_kontrol_sorgu = "SELECT id FROM rezervasyonlar 
                            WHERE kullanici_id = ? AND tarih = ? AND iptal_edildi = 0 
                            AND (? < bitis_saati AND ? > baslangic_saati)";

if ($stmt_kullanici = mysqli_prepare($db, $kullanici_kontrol_sorgu)) {
    mysqli_stmt_bind_param($stmt_kullanici, "isss", $kullanici_id, $tarih, $baslangic, $bitis);
    mysqli_stmt_execute($stmt_kullanici);
    mysqli_stmt_store_result($stmt_kullanici);

    if (mysqli_stmt_num_rows($stmt_kullanici) > 0) {
        echo "<script>alert('HATA: Bu saat aralığında zaten başka bir masada aktif rezervasyonunuz bulunuyor! Aynı anda iki yerde olamazsınız.'); window.location.href='salonlar.php';</script>";
        exit;
    }
    mysqli_stmt_close($stmt_kullanici);
}

//MASA ÇAKIŞMASINI ENGELLEME
$kontrol_sorgu = "SELECT id FROM rezervasyonlar 
                  WHERE masa_id = ? AND tarih = ? AND iptal_edildi = 0 
                  AND (? < bitis_saati AND ? > baslangic_saati)";

if ($stmt_kontrol = mysqli_prepare($db, $kontrol_sorgu)) {
    mysqli_stmt_bind_param($stmt_kontrol, "isss", $masa_id, $tarih, $baslangic, $bitis);
    mysqli_stmt_execute($stmt_kontrol);
    mysqli_stmt_store_result($stmt_kontrol);

    if (mysqli_stmt_num_rows($stmt_kontrol) > 0) {
        echo "<script>alert('HATA: Bu masa seçtiğiniz saatler arasında başka birine aittir!'); window.location.href='salonlar.php';</script>";
        exit;
    }
    mysqli_stmt_close($stmt_kontrol);
}

//HER ŞEY TAMAMSA REZERVASYONU KAYDET
$rez_sorgu = "INSERT INTO rezervasyonlar (kullanici_id, masa_id, tarih, baslangic_saati, bitis_saati, onaylandi, iptal_edildi) VALUES (?, ?, ?, ?, ?, 0, 0)";

if ($stmt = mysqli_prepare($db, $rez_sorgu)) {
    mysqli_stmt_bind_param($stmt, "iisss", $kullanici_id, $masa_id, $tarih, $baslangic, $bitis);
    if (mysqli_stmt_execute($stmt)) {
        header("location: rezervasyonlarim.php?durum=basarili");
        exit;
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($db);
?>