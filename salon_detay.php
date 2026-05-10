<?php
session_start();
require_once 'config.php';
date_default_timezone_set('Europe/Istanbul');

// --- AJAX 1: BELİRLİ BİR SAAT ARALIĞI SEÇİLİNCE HARİTAYI GÜNCELLEME ---
if (isset($_GET['ajax_tarih'])) {
    header('Content-Type: application/json');
    $tarih = $_GET['ajax_tarih'];
    $baslangic = $_GET['ajax_baslangic'];
    $bitis = $_GET['ajax_bitis'];
    $salon_ismi = $_GET['salon_ismi'];

    $dolu_masalar = [];
    $sorgu = "SELECT m.id FROM rezervasyonlar r 
              JOIN masalar m ON r.masa_id = m.id 
              WHERE m.salon_adi = ? AND r.tarih = ? AND r.iptal_edildi = 0 
              AND (? < r.bitis_saati AND ? > r.baslangic_saati)";
              
    if ($stmt = mysqli_prepare($db, $sorgu)) {
        mysqli_stmt_bind_param($stmt, "ssss", $salon_ismi, $tarih, $baslangic, $bitis);
        mysqli_stmt_execute($stmt);
        $sonuc = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($sonuc)) {
            $dolu_masalar[] = (int)$row['id'];
        }
        mysqli_stmt_close($stmt);
    }
    echo json_encode($dolu_masalar);
    exit;
}

// --- AJAX 2: GÜN BOYUNCA HİÇ BOŞ YERİ KALMAYAN MASALARI BULMA ---
if (isset($_GET['ajax_tam_dolu_tarih'])) {
    header('Content-Type: application/json');
    $tarih = $_GET['ajax_tam_dolu_tarih'];
    $salon_ismi = $_GET['salon_ismi'];
    
    $tam_dolu_masalar = [];
    $saatler = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
    
    $simdi_tarih = date('Y-m-d');
    $suAnkiSaat = date('H:i');
    
    $m_sorgu = "SELECT id FROM masalar WHERE salon_adi = ?";
    if ($m_stmt = mysqli_prepare($db, $m_sorgu)) {
        mysqli_stmt_bind_param($m_stmt, "s", $salon_ismi);
        mysqli_stmt_execute($m_stmt);
        $m_res = mysqli_stmt_get_result($m_stmt);
        
        while($m_row = mysqli_fetch_assoc($m_res)) {
            $m_id = $m_row['id'];
            
            $r_sorgu = "SELECT baslangic_saati, bitis_saati FROM rezervasyonlar WHERE masa_id = ? AND tarih = ? AND iptal_edildi = 0";
            $r_stmt = mysqli_prepare($db, $r_sorgu);
            mysqli_stmt_bind_param($r_stmt, "is", $m_id, $tarih);
            mysqli_stmt_execute($r_stmt);
            $r_res = mysqli_stmt_get_result($r_stmt);
            
            $dolu_araliklar = [];
            while($r_row = mysqli_fetch_assoc($r_res)) {
                $dolu_araliklar[] = [
                    'bas' => substr($r_row['baslangic_saati'], 0, 5),
                    'bit' => substr($r_row['bitis_saati'], 0, 5)
                ];
            }
            mysqli_stmt_close($r_stmt);
            
            $tamamen_dolu = true;
            for($i=0; $i < count($saatler) - 1; $i++) {
                $slotBas = $saatler[$i];
                $slotBit = $saatler[$i+1];
                $isBusy = false;
                
                if ($tarih == $simdi_tarih && $slotBas <= $suAnkiSaat) {
                    $isBusy = true; 
                }
                
                foreach($dolu_araliklar as $aralik) {
                    if ($slotBas >= $aralik['bas'] && $slotBas < $aralik['bit']) {
                        $isBusy = true;
                    }
                }
                
                if (!$isBusy) {
                    $tamamen_dolu = false; 
                    break; 
                }
            }
            
            if ($tamamen_dolu) {
                $tam_dolu_masalar[] = (int)$m_id;
            }
        }
        mysqli_stmt_close($m_stmt);
    }
    echo json_encode($tam_dolu_masalar);
    exit;
}

// --- AJAX 3: MASAYA TIKLAYINCA GÖRSEL SAAT KUTUCUKLARI İÇİN VERİ GETİRME ---
if (isset($_GET['masa_id_saat_getir'])) {
    header('Content-Type: application/json');
    $m_id = $_GET['masa_id_saat_getir'];
    $tarih = $_GET['tarih'];
    
    $dolu_araliklar = [];
    $sorgu = "SELECT baslangic_saati, bitis_saati FROM rezervasyonlar WHERE masa_id = ? AND tarih = ? AND iptal_edildi = 0";
    if ($stmt = mysqli_prepare($db, $sorgu)) {
        mysqli_stmt_bind_param($stmt, "is", $m_id, $tarih);
        mysqli_stmt_execute($stmt);
        $sonuc = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($sonuc)) {
            $dolu_araliklar[] = [
                'bas' => substr($row['baslangic_saati'], 0, 5),
                'bit' => substr($row['bitis_saati'], 0, 5)
            ];
        }
        mysqli_stmt_close($stmt);
    }
    echo json_encode($dolu_araliklar);
    exit;
}
// -------------------------------------------------------------------------

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
while($row = mysqli_fetch_assoc($sonuc)) { $masalar[] = $row; }
$toplam_masa = count($masalar);

$layout_type = 'dortlu'; 
if ($salon_ismi == 'Grup Çalışma Salonu') $layout_type = 'uzun';
elseif ($salon_ismi == 'Bilgisayar Salonu') $layout_type = 'kenar';

$saatler = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - <?php echo htmlspecialchars($salon_ismi); ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect x='15' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='35' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='55' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><polygon points='75,20 87,20 97,80 85,80' fill='%23b89c88'/></svg>">
    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        .nav-links a { color: #a0a0b0; text-decoration: none; margin: 0 15px; font-size: 14px; font-weight: bold; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { color: #ffffff; }

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

        .legend { display: flex; gap: 20px; margin-top: 20px; font-size: 13px; color: #a0a0b0; justify-content: center; }
        .dot { width: 14px; height: 14px; border-radius: 4px; display: inline-block; margin-right: 5px; }

        .reservation-section { flex: 1; min-width: 350px; background-color: #1a1a24; border-radius: 12px; padding: 30px; border: 1px solid #2d2d3a; height: fit-content; position: sticky; top: 100px;}
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 12px; color: #a0a0b0; text-transform: uppercase; }
        
        .form-group input, .form-group select { width: 100%; padding: 12px; background-color: #0d0d12; border: 1px solid #2d2d3a; border-radius: 6px; color: #fff; box-sizing: border-box; outline: none; font-family: inherit; appearance: none; }
        ::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }

        .btn-reserve { width: 100%; padding: 15px; background-color: #58493e; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; transition: all 0.3s; margin-top: 10px;}
        .btn-reserve:hover:not(:disabled) { background-color: #b89c88; color: #0d0d12; }
        .btn-reserve:disabled { background-color: #2d2d3a; color: #a0a0b0; cursor: not-allowed; }

        /* GÖRSEL SAAT KUTUCUKLARI TASARIMI (HOVER VE TIKLAMA İPTAL EDİLDİ) */
        .visual-time-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 15px; margin-bottom: 25px; }
        .time-box { background-color: #0d0d12; border: 1px solid #2d2d3a; padding: 12px 5px; text-align: center; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: default; } /* cursor: default yapıldı */
        .time-box.free { border-color: #10ac84; color: #10ac84; background-color: rgba(16, 172, 132, 0.05); }
        .time-box.busy { border-color: #ee5253; color: #ee5253; background-color: rgba(238, 82, 83, 0.05); text-decoration: line-through; opacity: 0.6; }
        .grid-title { text-align: center; color: #b89c88; font-size: 14px; text-transform: uppercase; margin-bottom: 15px; border-bottom: 1px dashed #2d2d3a; padding-bottom: 10px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php">Ana Sayfa</a>
            <a href="salonlar.php" class="active">Salonlar</a>
            <a href="rezervasyonlarim.php">Rezervasyonlarım</a>
            
            <?php if(isset($_SESSION["rol"]) && $_SESSION["rol"] == 'admin'): ?>
                <a href="admin_panel.php" style="color: #ff4d4d !important; border: 1px solid #ff4d4d; padding: 5px 12px; border-radius: 6px;">Yönetim Paneli</a>
            <?php endif; ?>
        </div>
        <div class="user-menu">
            <span style="color:#a0a0b0; font-size:14px; margin-right:15px;">Merhaba, <?php echo htmlspecialchars($_SESSION["ad_soyad"]); ?></span>
            <a href="cikis.php" style="color:#ff4d4d; text-decoration:none; font-weight:bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">Çıkış Yap</a>
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
                            echo '<div class="seat bos" data-kodu="'.$m['masa_kodu'].'" data-id="'.$m['id'].'">'.$m['masa_kodu'].'</div>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
                elseif ($layout_type == 'uzun') {
                    echo '<div class="layout-uzun">';
                    foreach ($masalar as $m) {
                        echo '<div class="seat bos" data-kodu="'.$m['masa_kodu'].'" data-id="'.$m['id'].'">'.$m['masa_kodu'].'</div>';
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
                                echo '<div class="seat bos" data-kodu="'.$m['masa_kodu'].'" data-id="'.$m['id'].'">'.$m['masa_kodu'].'</div>';
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
                <span><span class="dot" style="background:#ee5253"></span> Dolu/Geçmiş</span>
            </div>
        </div>

        <div class="reservation-section">
            <h3 style="margin-top:0; color:#b89c88; text-align:center;">Rezervasyon Yap</h3>
            
            <div id="saat_kutucuklari_alani" style="display: none;">
                <div class="grid-title" id="grid_baslik">Masa Saatleri</div>
                <div class="visual-time-grid" id="gorsel_saatler"></div>
            </div>

            <form action="rezervasyon_islem.php" method="POST" id="rezervasyonForm">
                <div class="form-group">
                    <label>Tarih</label>
                    <input type="date" name="tarih" id="tarih" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" onchange="haritaVeKutulariGuncelle()" required>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Başlangıç</label>
                        <select name="baslangic" id="baslangic" onchange="haritayiGuncelle()" required>
                            <option value="">Seçiniz</option>
                            <?php foreach($saatler as $s): ?>
                                <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Bitiş</label>
                        <select name="bitis" id="bitis" onchange="haritayiGuncelle()" required>
                            <option value="">Seçiniz</option>
                            <?php foreach($saatler as $s): ?>
                                <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Seçili Masa</label>
                    <input type="text" id="secili_masa_gosterim" placeholder="Haritadan masa seçin" readonly required style="text-align:center; font-weight:bold; color:#0abde3; cursor:not-allowed;">
                    <input type="hidden" name="masa_id" id="secili_masa_id" required>
                </div>
                
                <button type="submit" class="btn-reserve" id="submitBtn" disabled>REZERVASYON YAP</button>
            </form>
        </div>
    </div>

    <script>
        const tumSaatler = ["08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00"];

        window.onload = function() {
            saatSecenekleriniGuncelle();
            haritayiGuncelle();
        };

        function saatSecenekleriniGuncelle() {
            let tarih = document.getElementById('tarih').value;
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            let bugunTarih = `${yyyy}-${mm}-${dd}`;
            
            let suAnkiSaat = String(today.getHours()).padStart(2, '0') + ":" + String(today.getMinutes()).padStart(2, '0');

            ['baslangic', 'bitis'].forEach(id => {
                let select = document.getElementById(id);
                Array.from(select.options).forEach(opt => {
                    if (opt.value && tarih === bugunTarih && opt.value <= suAnkiSaat) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });
            });

            let bas = document.getElementById('baslangic');
            if(bas.options[bas.selectedIndex] && bas.options[bas.selectedIndex].disabled) bas.value = "";
            let bit = document.getElementById('bitis');
            if(bit.options[bit.selectedIndex] && bit.options[bit.selectedIndex].disabled) bit.value = "";
        }

        function haritayiGuncelle() {
            let tarih = document.getElementById('tarih').value;
            let baslangic = document.getElementById('baslangic').value;
            let bitis = document.getElementById('bitis').value;
            let salon = "<?php echo $salon_ismi; ?>";

            if(!tarih) return;

            if (baslangic && bitis) {
                if(baslangic >= bitis) {
                    alert("Bitiş saati, başlangıç saatinden ileride olmalıdır!");
                    document.getElementById('submitBtn').disabled = true;
                    return;
                }
                
                fetch(`salon_detay.php?ajax_tarih=${tarih}&ajax_baslangic=${baslangic}&ajax_bitis=${bitis}&salon_ismi=${salon}`)
                .then(res => res.json())
                .then(doluMasaIdleri => {
                    haritaRenkleriniUygula(doluMasaIdleri, true);
                });
            } 
            else {
                fetch(`salon_detay.php?ajax_tam_dolu_tarih=${tarih}&salon_ismi=${salon}`)
                .then(res => res.json())
                .then(tamDoluIdler => {
                    haritaRenkleriniUygula(tamDoluIdler, false);
                });
            }
        }

        function haritaRenkleriniUygula(kizartilacakIDler, saatSecildiMi) {
            let mevcutSeciliId = parseInt(document.getElementById('secili_masa_id').value);
            let secimIptal = false;

            document.querySelectorAll('.seat').forEach(masa => {
                let m_id = parseInt(masa.getAttribute('data-id'));
                
                if (kizartilacakIDler.includes(m_id)) {
                    masa.classList.remove('bos', 'secili');
                    masa.classList.add('dolu');
                    if (m_id === mevcutSeciliId) secimIptal = true;
                } else {
                    masa.classList.remove('dolu');
                    masa.classList.add('bos');
                    if (m_id === mevcutSeciliId) {
                        masa.classList.add('secili');
                    } else {
                        masa.classList.remove('secili');
                    }
                }
            });
            
            if (secimIptal) {
                document.getElementById('secili_masa_gosterim').value = '';
                document.getElementById('secili_masa_id').value = '';
                document.getElementById('submitBtn').disabled = true;
                
                if(saatSecildiMi) {
                    alert("Seçtiğiniz masa bu yeni saat aralığında maalesef dolu.");
                } else {
                    alert("Bu masa bugün için tamamen doludur veya boş saati kalmamıştır. Lütfen başka bir masa seçin.");
                    document.getElementById('saat_kutucuklari_alani').style.display = 'none';
                }
            } else if (mevcutSeciliId && document.getElementById('baslangic').value && document.getElementById('bitis').value) {
                document.getElementById('submitBtn').disabled = false;
            } else {
                document.getElementById('submitBtn').disabled = true;
            }
        }

        function haritaVeKutulariGuncelle() {
            saatSecenekleriniGuncelle();
            haritayiGuncelle();
            let seciliMasaId = document.getElementById('secili_masa_id').value;
            let seciliMasaKodu = document.getElementById('secili_masa_gosterim').value;
            if(seciliMasaId) {
                gorselSaatleriGetir(seciliMasaId, seciliMasaKodu);
            }
        }

        // 2. KUTUCUKLU SAATLERİ ÇİZME (TIKLAMA VE HOVER İPTAL EDİLDİ)
        function gorselSaatleriGetir(m_id, kodu) {
            let tarih = document.getElementById('tarih').value;
            if(!tarih) return;

            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            let bugunTarih = `${yyyy}-${mm}-${dd}`;
            let suAnkiSaat = String(today.getHours()).padStart(2, '0') + ":" + String(today.getMinutes()).padStart(2, '0');

            fetch(`salon_detay.php?masa_id_saat_getir=${m_id}&tarih=${tarih}`)
            .then(res => res.json())
            .then(doluAraliklar => {
                let grid = document.getElementById('gorsel_saatler');
                grid.innerHTML = ""; 
                document.getElementById('grid_baslik').innerHTML = `<span style="color:#0abde3;">${kodu}</span> Masası Müsaitlik Durumu`;
                document.getElementById('saat_kutucuklari_alani').style.display = 'block';

                for(let i=0; i < tumSaatler.length - 1; i++) {
                    let slotBas = tumSaatler[i];
                    let slotBit = tumSaatler[i+1];
                    let isBusy = false;

                    if (tarih === bugunTarih && slotBas <= suAnkiSaat) {
                        isBusy = true; 
                    }

                    doluAraliklar.forEach(aralik => {
                        if (slotBas >= aralik.bas && slotBas < aralik.bit) {
                            isBusy = true;
                        }
                    });

                    let div = document.createElement('div');
                    div.className = "time-box " + (isBusy ? "busy" : "free");
                    div.innerHTML = slotBas + " - " + slotBit;
                    
                    // SADECE GÖRSEL OLARAK EKLENİYOR, TIKLAMA (ONCLICK) SİLİNDİ
                    grid.appendChild(div);
                }
            });
        }

        // 3. HARİTADAN MASAYA TIKLAMA OLAYI
        document.querySelector('.seating-area').addEventListener('click', function(e) {
            if (e.target.classList.contains('seat')) {
                let masa = e.target;
                let kodu = masa.getAttribute('data-kodu');
                let m_id = masa.getAttribute('data-id');

                gorselSaatleriGetir(m_id, kodu);

                if (masa.classList.contains('bos')) {
                    document.querySelectorAll('.seat').forEach(m => m.classList.remove('secili'));
                    masa.classList.add('secili');
                    document.getElementById('secili_masa_gosterim').value = kodu;
                    document.getElementById('secili_masa_id').value = m_id;
                    
                    let baslangic = document.getElementById('baslangic').value;
                    let bitis = document.getElementById('bitis').value;
                    
                    if(baslangic && bitis && baslangic < bitis) {
                        document.getElementById('submitBtn').disabled = false;
                    }
                } else if (masa.classList.contains('dolu')) {
                    document.getElementById('secili_masa_gosterim').value = '';
                    document.getElementById('secili_masa_id').value = '';
                    document.getElementById('submitBtn').disabled = true;
                }
            }
        });
    </script>
</body>
</html>