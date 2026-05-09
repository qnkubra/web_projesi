<?php
session_start();
require_once 'config.php';
date_default_timezone_set('Europe/Istanbul');

// Güvenlik: Sadece admin girebilir!
if (!isset($_SESSION["kullanici_id"]) || $_SESSION["rol"] != 'admin') {
    header("location: index.php");
    exit;
}

$bugun = date('Y-m-d');
$suan_saat = date('H:i:s');

// Tüm randevuları kullanıcı ve masa bilgileriyle çekiyoruz
$sorgu = "SELECT r.*, m.masa_kodu, m.salon_adi, k.ad_soyad, k.email 
          FROM rezervasyonlar r 
          JOIN masalar m ON r.masa_id = m.id 
          JOIN kullanicilar k ON r.kullanici_id = k.id 
          ORDER BY r.tarih DESC, r.baslangic_saati DESC";

$sonuc = mysqli_query($db, $sorgu);

$aktif_rez = [];
$gecmis_rez = [];
$iptal_rez = [];

while ($row = mysqli_fetch_assoc($sonuc)) {
    $bitis_zamani = strtotime($row['tarih'] . ' ' . $row['bitis_saati']);
    
    if ($row['iptal_edildi'] == 1) {
        $iptal_rez[] = $row;
    } elseif ($bitis_zamani > time()) {
        $aktif_rez[] = $row;
    } else {
        $gecmis_rez[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>LibReserve - Admin Yönetim Paneli</title>
    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        
        /* NAVBAR */
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        h1 { color: #b89c88; margin-bottom: 40px; }
        
        .section-title { border-left: 4px solid #58493e; padding-left: 15px; margin: 50px 0 20px; font-size: 22px; font-weight: bold; }
        
        /* TABLO TASARIMI */
        table { width: 100%; border-collapse: collapse; background-color: #1a1a24; border-radius: 12px; overflow: hidden; margin-bottom: 40px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #2d2d3a; font-size: 14px; }
        th { background-color: #2d2d3a; color: #a0a0b0; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        tr:hover { background-color: rgba(255, 255, 255, 0.02); }
        
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-aktif { background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid #2ed573; }
        .badge-gecmis { background: rgba(160, 160, 176, 0.1); color: #a0a0b0; border: 1px solid #a0a0b0; }
        .badge-iptal { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; }
        
        .btn-cancel { color: #ff4d4d; text-decoration: none; font-weight: bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 4px; transition: 0.3s; font-size: 12px; }
        .btn-cancel:hover { background: #ff4d4d; color: white; }
        
        .empty-msg { color: #555; font-style: italic; padding: 20px; text-align: center; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span> - Admin Panel</div>
        <div style="display: flex; gap: 20px; align-items: center;">
            <a href="index.php" style="color: #a0a0b0; text-decoration: none; font-weight: bold;">Siteye Dön</a>
            <a href="cikis.php" style="color:#ff4d4d; text-decoration:none; font-weight:bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">Çıkış Yap</a>
        </div>
    </nav>

    <div class="container">
        <h1>Rezervasyon Yönetimi</h1>

        <div class="section-title" style="color: #2ed573;">Aktif Rezervasyonlar</div>
        <table>
            <thead>
                <tr>
                    <th>Öğrenci</th>
                    <th>Salon</th>
                    <th>Masa</th>
                    <th>Tarih</th>
                    <th>Saat Aralığı</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($aktif_rez as $rez): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($rez['ad_soyad']); ?></strong><br><small style="color:#666;"><?php echo $rez['email']; ?></small></td>
                    <td><?php echo $rez['salon_adi']; ?></td>
                    <td><span style="color:#b89c88; font-weight:bold;"><?php echo $rez['masa_kodu']; ?></span></td>
                    <td><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?></td>
                    <td><?php echo substr($rez['baslangic_saati'], 0, 5) . " - " . substr($rez['bitis_saati'], 0, 5); ?></td>
                    <td>
                        <?php if($rez['onaylandi'] == 1): ?>
                            <span class="badge badge-aktif">MASADA</span>
                        <?php else: ?>
                            <span class="badge" style="color:#ffa502; border: 1px solid #ffa502;">BEKLİYOR</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="rezervasyon_iptal.php?id=<?php echo $rez['id']; ?>" class="btn-cancel" onclick="return confirm('Bu öğrencinin rezervasyonunu iptal etmek istediğinizden emin misiniz?');">İPTAL ET</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($aktif_rez)) echo "<tr><td colspan='7' class='empty-msg'>Aktif randevu bulunmuyor.</td></tr>"; ?>
            </tbody>
        </table>

        <div class="section-title" style="color: #a0a0b0;">Geçmiş Rezervasyonlar</div>
        <table>
            <thead>
                <tr>
                    <th>Öğrenci</th>
                    <th>Salon</th>
                    <th>Masa</th>
                    <th>Tarih</th>
                    <th>Saat Aralığı</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gecmis_rez as $rez): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rez['ad_soyad']); ?></td>
                    <td><?php echo $rez['salon_adi']; ?></td>
                    <td><?php echo $rez['masa_kodu']; ?></td>
                    <td><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?></td>
                    <td><?php echo substr($rez['baslangic_saati'], 0, 5) . " - " . substr($rez['bitis_saati'], 0, 5); ?></td>
                    <td><span class="badge badge-gecmis">TAMAMLANDI</span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($gecmis_rez)) echo "<tr><td colspan='6' class='empty-msg'>Geçmiş randevu bulunmuyor.</td></tr>"; ?>
            </tbody>
        </table>

        <div class="section-title" style="color: #ff4d4d;">İptal Edilen Rezervasyonlar</div>
        <table>
            <thead>
                <tr>
                    <th>Öğrenci</th>
                    <th>Salon</th>
                    <th>Masa</th>
                    <th>Tarih</th>
                    <th>Saat Aralığı</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($iptal_rez as $rez): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rez['ad_soyad']); ?></td>
                    <td><?php echo $rez['salon_adi']; ?></td>
                    <td><?php echo $rez['masa_kodu']; ?></td>
                    <td><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?></td>
                    <td><?php echo substr($rez['baslangic_saati'], 0, 5) . " - " . substr($rez['bitis_saati'], 0, 5); ?></td>
                    <td><span class="badge badge-iptal">İPTAL EDİLDİ</span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($iptal_rez)) echo "<tr><td colspan='6' class='empty-msg'>İptal edilen randevu bulunmuyor.</td></tr>"; ?>
            </tbody>
        </table>
    </div>

</body>
</html>