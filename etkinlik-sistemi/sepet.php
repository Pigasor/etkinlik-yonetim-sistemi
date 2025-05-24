<?php
session_start();
require 'config.php'; // Veritabanı bağlantı bilgileri

// Kullanıcı giriş yaptı mı kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Sepeti al
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Sepet boşsa mesaj ver
if (empty($cart)) {
    echo "Sepetiniz boş.";
    exit();
}

// Etkinlikleri listele
$total = 0;
foreach ($cart as $event_id => $ticket_count) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if ($event) {
        $fiyat = 100; // sabit fiyat
        $event_price = $fiyat * $ticket_count;
        $total += $event_price;

        echo "<div class='event'>
                <p><strong>Etkinlik:</strong> " . htmlspecialchars($event['title']) . "</p>
                <p><strong>Tarih:</strong> " . htmlspecialchars($event['event_date']) . "</p>
                <p><strong>Fiyat:</strong> " . number_format($fiyat, 2) . " TL</p>
                <p><strong>Adet:</strong> $ticket_count</p>
                <p><strong>Toplam Fiyat:</strong> " . number_format($event_price, 2) . " TL</p>
              </div>";
    }
}

// Ödeme ve sepet özeti
echo "<div class='cart-summary'>
        <p><strong>Toplam Tutar:</strong> " . number_format($total, 2) . " TL</p>
        <form action='odeme.php' method='POST'>
            <button type='submit' class='btn btn-success'>Ödeme Yap</button>
        </form>
      </div>";
?>
