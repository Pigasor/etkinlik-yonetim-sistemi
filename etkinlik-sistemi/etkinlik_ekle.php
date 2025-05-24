<?php
include 'config.php'; // Veritabanı bağlantısını dahil et

// Form gönderildiğinde işlemleri yap
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen veriler
    $ad = $_POST['ad'];
    $tarih = $_POST['tarih'];
    $konum = $_POST['konum'];
    $aciklama = $_POST['aciklama'];
    $fiyat = $_POST['fiyat'];
    $kontenjan = $_POST['kontenjan'];
    $etkinlik_tipi = $_POST['etkinlik_tipi'];

    // Etkinliği veritabanına ekle
    $stmt = $pdo->prepare("INSERT INTO etkinlikler (ad, tarih, konum, aciklama, fiyat, kontenjan, etkinlik_tipi) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$ad, $tarih, $konum, $aciklama, $fiyat, $kontenjan, $etkinlik_tipi]);

    echo "Etkinlik başarıyla eklendi!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etkinlik Ekle</title>
    <link rel="stylesheet" href="etkinlik.css">
</head>
<body>
    <div class="container">
        <h1>Yeni Etkinlik Ekle</h1>
        <form method="POST" action="etkinlik-ekle.php">
            <label for="ad">Etkinlik Adı:</label>
            <input type="text" id="ad" name="ad" required>

            <label for="tarih">Tarih:</label>
            <input type="datetime-local" id="tarih" name="tarih" required>

            <label for="konum">Konum:</label>
            <input type="text" id="konum" name="konum" required>

            <label for="aciklama">Açıklama:</label>
            <textarea id="aciklama" name="aciklama"></textarea>

            <label for="fiyat">Fiyat (₺):</label>
            <input type="number" id="fiyat" name="fiyat" step="0.01">

            <label for="kontenjan">Kontenjan:</label>
            <input type="number" id="kontenjan" name="kontenjan">

            <label for="etkinlik_tipi">Etkinlik Tipi:</label>
            <input type="text" id="etkinlik_tipi" name="etkinlik_tipi">

            <button type="submit">Etkinlik Ekle</button>
        </form>
    </div>
</body>
</html>
