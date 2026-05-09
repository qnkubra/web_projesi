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

// --- KRİTİK DÜZELTME: ÇAKIŞMA KONTROLÜNDE İPTAL EDİLENLERİ DEVRE DIŞI BIRAK ---
$kontrol_sorgu = "SELECT id FROM rezervasyonlar WHERE kullanici_id = ? AND tarih = ? AND iptal_edildi = 0 AND (
    (baslangic_saati <= ? AND bitis_saati > ?) OR 
    (baslangic_saati < ? AND bitis_saati >= ?) OR
    (? <= baslangic_saati AND ? >= bitis_saati)
)";

if ($stmt_kontrol = mysqli_prepare($db, $kontrol_sorgu)) {
    // iptal_edildi = 0 olanları kontrol ederek, iptal edilen saatlerin boşa çıkmasını sağladık
    mysqli_stmt_bind_param($stmt_kontrol, "isssssss", $kullanici_id, $tarih, $baslangic, $baslangic, $bitis, $bitis, $baslangic, $bitis);
    mysqli_stmt_execute($stmt_kontrol);
    mysqli_stmt_store_result($stmt_kontrol);

    if (mysqli_stmt_num_rows($stmt_kontrol) > 0) {
        echo "<script>alert('HATA: Bu saat aralığında zaten AKTİF bir rezervasyonunuz bulunuyor!'); window.location.href='salonlar.php';</script>";
        exit;
    }
    mysqli_stmt_close($stmt_kontrol);
}

// 2. AŞAMA: REZERVASYONU KAYDET
$rez_sorgu = "INSERT INTO rezervasyonlar (kullanici_id, masa_id, tarih, baslangic_saati, bitis_saati, onaylandi, iptal_edildi) VALUES (?, ?, ?, ?, ?, 0, 0)";

if ($stmt = mysqli_prepare($db, $rez_sorgu)) {
    mysqli_stmt_bind_param($stmt, "iisss", $kullanici_id, $masa_id, $tarih, $baslangic, $bitis);
    
    if (mysqli_stmt_execute($stmt)) {
        // Masayı dolu yap
        $masa_guncelle = "UPDATE masalar SET durum = 'dolu' WHERE id = ?";
        if ($guncelle_stmt = mysqli_prepare($db, $masa_guncelle)) {
            mysqli_stmt_bind_param($guncelle_stmt, "i", $masa_id);
            mysqli_stmt_execute($guncelle_stmt);
            mysqli_stmt_close($guncelle_stmt);
        }
        header("location: rezervasyonlarim.php?durum=basarili");
        exit;
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($db);
?>