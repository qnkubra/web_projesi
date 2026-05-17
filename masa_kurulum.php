<?php//öğrenci siteyi kullanırken çalışmıyor. masa yukleme sıfırlama
require_once 'config.php';

// Önceki denemelerden kalan masaları tamamen temizler
mysqli_query($db, "TRUNCATE TABLE masalar");
//truncate : fabrika ayarlarına döndürme.
//ınsert ınto: kayıt etme, veritabanına satır ekleme
$eklenen_masa = 0;

// 1. Ana Salon (80 Masa)
for($i = 1; $i <= 80; $i++) {
    mysqli_query($db, "INSERT INTO masalar (masa_kodu, salon_adi, durum) VALUES ('A$i', 'Ana Salon', 'bos')");
    $eklenen_masa++;
}

// 2. Sessiz Salon (48 Masa)
for($i = 1; $i <= 48; $i++) {
    mysqli_query($db, "INSERT INTO masalar (masa_kodu, salon_adi, durum) VALUES ('S$i', 'Sessiz Salon', 'bos')");
    $eklenen_masa++;
}

// 3. Grup Çalışma Salonu (6 Masa)
for($i = 1; $i <= 6; $i++) {
    mysqli_query($db, "INSERT INTO masalar (masa_kodu, salon_adi, durum) VALUES ('G$i', 'Grup Çalışma Salonu', 'bos')");
    $eklenen_masa++;
}

// 4. Bilgisayar Salonu (28 Masa)
for($i = 1; $i <= 28; $i++) {
    mysqli_query($db, "INSERT INTO masalar (masa_kodu, salon_adi, durum) VALUES ('B$i', 'Bilgisayar Salonu', 'bos')");
    $eklenen_masa++;
}

echo "<h1>Harika! Toplam $eklenen_masa adet masa veritabanına başarıyla eklendi!</h1>";
echo "<a href='salonlar.php'>Salonlara Dön</a>";
?>