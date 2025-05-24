<?php
session_start();
require 'config.php'; // PDO bağlantı burada

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['event_id'], $_POST['ticket_count'], $_POST['ticket_type'])) {
        $event_id = (int)$_POST['event_id'];
        $ticket_count = (int)$_POST['ticket_count'];
        $ticket_type = $_POST['ticket_type']; // 'student' veya 'full'
        $user_id = $_SESSION['user_id'];

        // Ticket type doğrulaması
        if (!in_array($ticket_type, ['student', 'full'])) {
            // Geçersiz ticket_type ise ana sayfaya yönlendir
            header("Location: index.php");
            exit();
        }

        if ($event_id > 0 && $ticket_count > 0) {
            try {
                // Etkinliğin fiyatını veritabanından al
                $price_column = ($ticket_type === 'student') ? 'price_student' : 'price_full';
                $stmt_price = $pdo->prepare("SELECT $price_column, quota FROM events WHERE id = ?");
                $stmt_price->execute([$event_id]);
                $event = $stmt_price->fetch();

                if (!$event) {
                    // Etkinlik bulunamadıysa ana sayfaya yönlendir
                    header("Location: index.php");
                    exit();
                }

                // Kontenjan kontrolü: sepetteki ve istenen bilet sayısını topla
                $stmt_cart = $pdo->prepare("SELECT SUM(ticket_count) AS total_tickets FROM sepet WHERE event_id = ?");
                $stmt_cart->execute([$event_id]);
                $cart_data = $stmt_cart->fetch();
                $total_tickets_in_cart = (int)$cart_data['total_tickets'];

                if (($total_tickets_in_cart + $ticket_count) > (int)$event['quota']) {
                    // Kontenjan dolmuş veya yetersizse hata mesajı ver veya yönlendir
                    die("Bu etkinlik için yeterli kontenjan bulunmamaktadır.");
                }

                $ticket_price = $event[$price_column];

                // Sepet tablosunda aynı event_id ve ticket_type için kontrol
                $stmt = $pdo->prepare("SELECT * FROM sepet WHERE user_id = ? AND event_id = ? AND ticket_type = ?");
                $stmt->execute([$user_id, $event_id, $ticket_type]);
                $cart_item = $stmt->fetch();

                if ($cart_item) {
                    $new_count = $cart_item['ticket_count'] + $ticket_count;
                    $update_stmt = $pdo->prepare("UPDATE sepet SET ticket_count = ?, updated_at = NOW() WHERE id = ?");
                    $update_stmt->execute([$new_count, $cart_item['id']]);
                } else {
                    $insert_stmt = $pdo->prepare("INSERT INTO sepet (user_id, event_id, ticket_type, ticket_count, price, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $insert_stmt->execute([$user_id, $event_id, $ticket_type, $ticket_count, $ticket_price]);
                }

                header("Location: sepet.php");
                exit();

            } catch (PDOException $e) {
                die("Veritabanı hatası: " . $e->getMessage());
            }
        }
    }
}

// Geçersiz isteklerde ana sayfaya yönlendir
header("Location: index.php");
exit();
?>
