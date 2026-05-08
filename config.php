<?php
// 1. Veritabanı Kimlik Bilgileri
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'libreserve_db';

// 2. MySQLi ile Bağlantıyı Kurma
$db = mysqli_connect($host, $username, $password, $dbname);

// 3. Hata Kontrolü (Bağlantı başarısızsa işlemi durdur)
if (!$db) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

// 4. Türkçe karakter sorunu yaşamamak için karakter setini ayarlama
mysqli_set_charset($db, "utf8mb4");

// echo "Veritabanı bağlantısı başarılı!";
?>