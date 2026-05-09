<?php
session_start();
require_once 'config.php';

date_default_timezone_set('Europe/Istanbul');
$suan_tarih = date('Y-m-d');
$suan_saat = date('H:i:s');

$temizle_sorgu = "UPDATE masalar SET durum = 'bos' 
                  WHERE durum = 'dolu' AND id NOT IN (
                      SELECT masa_id FROM rezervasyonlar 
                      WHERE tarih > '$suan_tarih' OR (tarih = '$suan_tarih' AND bitis_saati > '$suan_saat')
                  )";
mysqli_query($db, $temizle_sorgu);
// -----------------------------------------------------------------------------

date_default_timezone_set('Europe/Istanbul');

if (!isset($_SESSION["kullanici_id"])) {
    header("location: login.php");
    exit;
}

$salon_ismi = isset($_GET['isim']) ? $_GET['isim'] : 'Ana Salon';

$sorgu = "SELECT * FROM masalar WHERE salon_adi = ? ORDER BY id ASC";
$stmt = mysqli_prepare($db, $sorgu);
mysqli_stmt_bind_param($stmt, "s", $salon_ismi);
mysqli_stmt_execute($stmt);
$sonuc = mysqli_stmt_get_result($stmt);

$masalar = [];
while($row = mysqli_fetch_assoc($sonuc)) {
    $masalar[] = $row;
}
$toplam_masa = count($masalar);

$layout_type = 'dortlu'; 
if ($salon_ismi == 'Grup Çalışma Salonu') $layout_type = 'uzun';
elseif ($salon_ismi == 'Bilgisayar Salonu') $layout_type = 'kenar';

// Datalist için saatler
$saatler = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - <?php echo htmlspecialchars($salon_ismi); ?></title>
    
    <script>
        if (performance.getEntriesByType("navigation")[0].type === "reload") {
            window.location.href = "index.php";
        }
    </script>

    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        .nav-links a { color: #a0a0b0; text-decoration: none; margin: 0 15px; font-size: 14px; font-weight: bold; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { color: #ffffff; text-shadow: 0 0 10px rgba(255, 255, 255, 0.3); }

        .container { display: flex; padding: 40px; gap: 30px; flex-wrap: wrap; max-width: 1400px; margin: 0 auto; }
        
        .map-section { flex: 2; min-width: 600px; background-color: #1a1a24; border-radius: 12px; padding: 30px; border: 1px solid #2d2d3a; display: flex; flex-direction: column;}
        .map-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #2d2d3a; padding-bottom: 15px; }
        .map-header h2 { margin: 0; font-size: 24px; color: #ffffff; }
        .btn-back { color: #a0a0b0; text-decoration: none; font-size: 14px; font-weight: bold; }

        .seating-area { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 20px; background-color: #0d0d12; border-radius: 8px; border: 1px dashed #2d2d3a; margin-bottom: 30px;}
        
        .layout-dortlu { display: flex; flex-wrap: wrap; gap: 40px; justify-content: center; max-width: 800px; }
        .group-4 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background-color: rgba(255,255,255,0.02); padding: 15px; border-radius: 12px; }
        .layout-uzun { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .layout-kenar { display: grid; grid-template-columns: repeat(10, 1fr); gap: 12px; max-width: 900px; }

        .seat { width: 45px; height: 45px; border-radius: 8px; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 13px; transition: all 0.2s; cursor: pointer; }
        .seat.bos { background-color: #10ac84; color: #ffffff; }
        .seat.dolu { background-color: #ee5253; color: #ffffff; cursor: not-allowed; opacity: 0.8; }
        .seat.secili { background-color: #0abde3 !important; transform: scale(1.15); box-shadow: 0 0 15px rgba(10, 189, 227, 0.6); border: 2px solid #ffffff; }

        .reservation-section { flex: 1; min-width: 350px; background-color: #1a1a24; border-radius: 12px; padding: 30px; border: 1px solid #2d2d3a; height: fit-content; position: sticky; top: 100px;}
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 12px; color: #a0a0b0; text-transform: uppercase; }
        
        /* INPUT STİLLERİ */
        .form-group input { width: 100%; padding: 12px; background-color: #0d0d12; border: 1px solid #2d2d3a; border-radius: 6px; color: #fff; box-sizing: border-box; outline: none; font-family: inherit;}
        /* Zaman alanında çıkan varsayılan saat ikonunu biraz uyumlu yapalım */
        ::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }

        .btn-reserve { width: 100%; padding: 15px; background-color: #58493e; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; transition: all 0.3s; margin-top: 10px;}
        .btn-reserve:hover:not(:disabled) { background-color: #b89c88; color: #0d0d12; }
        .btn-reserve:disabled { background-color: #2d2d3a; color: #a0a0b0; cursor: not-allowed; }

        .legend { display: flex; gap: 20px; margin-top: 20px; font-size: 13px; color: #a0a0b0; justify-content: center; }
        .dot { width: 14px; height: 14px; border-radius: 4px; display: inline-block; margin-right: 5px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php">Ana Sayfa</a>
            <a href="salonlar.php" class="active">Salonlar</a>
            <a href="rezervasyonlarim.php">Rezervasyonlarım</a>
        </div>
        <div class="user-menu">
            <span style="color:#a0a0b0; font-size:14px; margin-right:15px;">Merhaba, <?php echo htmlspecialchars($_SESSION["ad_soyad"]); ?></span>
            <a href="cikis.php" class="logout-btn" style="color:#ff4d4d; text-decoration:none; font-weight:bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;">Çıkış Yap</a>
        </div>
    </nav>

    <div class="container">
        <div class="map-section">
            <div class="map-header">
                <a href="salonlar.php" class="btn-back">← Geri Dön</a>
                <h2><?php echo htmlspecialchars($salon_ismi); ?></h2>
                <span style="color: #10ac84; font-weight: bold;"><?php echo $toplam_masa; ?> Masa</span>
            </div>
            
            <div class="seating-area">
                <?php
                if ($layout_type == 'dortlu') {
                    echo '<div class="layout-dortlu">';
                    $index = 0;
                    while ($index < count($masalar)) {
                        echo '<div class="group-4">';
                        for ($j = 0; $j < 4 && $index < count($masalar); $j++) {
                            $m = $masalar[$index++];
                            echo '<div class="seat '.$m['durum'].'" data-kodu="'.$m['masa_kodu'].'" data-id="'.$m['id'].'">'.$m['masa_kodu'].'</div>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
                elseif ($layout_type == 'uzun') {
                    echo '<div class="layout-uzun">';
                    foreach ($masalar as $m) {
                        echo '<div class="seat '.$m['durum'].'" data-kodu="'.$m['masa_kodu'].'" data-id="'.$m['id'].'">'.$m['masa_kodu'].'</div>';
                    }
                    echo '</div>';
                }
                elseif ($layout_type == 'kenar') {
                    echo '<div class="layout-kenar">';
                    $index = 0;
                    for ($row = 1; $row <= 6; $row++) {
                        for ($col = 1; $col <= 10; $col++) {
                            if (($row == 1 || $row == 6 || $col == 1 || $col == 10) && $index < count($masalar)) {
                                $m = $masalar[$index++];
                                echo '<div class="seat '.$m['durum'].'" data-kodu="'.$m['masa_kodu'].'" data-id="'.$m['id'].'">'.$m['masa_kodu'].'</div>';
                            } else { echo '<div></div>'; }
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>

            <div class="legend">
                <span><span class="dot" style="background:#10ac84"></span> Boş</span>
                <span><span class="dot" style="background:#0abde3"></span> Seçili</span>
                <span><span class="dot" style="background:#ee5253"></span> Dolu</span>
            </div>
        </div>

        <div class="reservation-section">
            <h3>Rezervasyon Yap</h3>
            <form action="rezervasyon_islem.php" method="POST" id="rezervasyonForm">
                <div class="form-group">
                    <label>Tarih</label>
                    <input type="date" name="tarih" id="tarih" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <datalist id="saatSecenekleri">
                    <?php foreach($saatler as $saat): ?>
                        <option value="<?php echo $saat; ?>"></option>
                    <?php endforeach; ?>
                </datalist>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Başlangıç</label>
                        <input type="time" name="baslangic" id="baslangic" list="saatSecenekleri" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Bitiş</label>
                        <input type="time" name="bitis" id="bitis" list="saatSecenekleri" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Masa</label>
                    <input type="text" id="secili_masa_gosterim" placeholder="Masa seçin" readonly required style="text-align:center; font-weight:bold; color:#0abde3;">
                    <input type="hidden" name="masa_id" id="secili_masa_id" required>
                </div>
                <button type="submit" class="btn-reserve" id="submitBtn" disabled>REZERVASYON YAP</button>
            </form>
        </div>
    </div>

    <script>
        const bosMasalar = document.querySelectorAll('.seat.bos');
        const gosterimInput = document.getElementById('secili_masa_gosterim');
        const idInput = document.getElementById('secili_masa_id');
        const submitBtn = document.getElementById('submitBtn');

        bosMasalar.forEach(masa => {
            masa.addEventListener('click', function() {
                bosMasalar.forEach(m => m.classList.remove('secili'));
                this.classList.add('secili');
                gosterimInput.value = this.getAttribute('data-kodu');
                idInput.value = this.getAttribute('data-id');
                submitBtn.disabled = false;
            });
        });

        document.getElementById('rezervasyonForm').addEventListener('submit', function(e) {
            const bugun = new Date();
            const seciliTarih = document.getElementById('tarih').value;
            const baslangic = document.getElementById('baslangic').value;
            const bitis = document.getElementById('bitis').value;
            const bugunStr = bugun.toISOString().split('T')[0];
            const suanSaat = bugun.getHours() + ':' + (bugun.getMinutes() < 10 ? '0' : '') + bugun.getMinutes();

            if (bitis <= baslangic) {
                alert("Bitiş saati başlangıçtan sonra olmalı!");
                e.preventDefault();
            } else if (seciliTarih === bugunStr && baslangic < suanSaat) {
                alert("Geçmiş bir saate rezervasyon yapılamaz!");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>