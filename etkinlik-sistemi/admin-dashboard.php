<?php
session_start();
require 'config.php';

// Admin kontrolü
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header('Location: login.php');
    exit;
}

// Etkinlik ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['description'], $_POST['event_date'], $_POST['price_student'], $_POST['price_full'], $_POST['quota'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $price_student = $_POST['price_student'];
    $price_full = $_POST['price_full'];
    $quota = $_POST['quota'];

    // Basit validasyon (isteğe göre geliştirilebilir)
    if (!is_numeric($price_student) || $price_student < 0) {
        echo "Öğrenci fiyatı geçersiz.";
        exit;
    }
    if (!is_numeric($price_full) || $price_full < 0) {
        echo "Tam bilet fiyatı geçersiz.";
        exit;
    }
    if (!is_numeric($quota) || $quota < 1) {
        echo "Kontenjan geçersiz.";
        exit;
    }

    // Etkinlik verisini veritabanına ekleyelim
    $sql = "INSERT INTO events (title, description, event_date, price_student, price_full, quota) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $event_date, $price_student, $price_full, $quota]);

    echo "Etkinlik başarıyla eklendi!";
}

// Duyuru ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['announcement_title']) && isset($_POST['announcement_content'])) {
    $announcement_title = $_POST['announcement_title'];
    $announcement_content = $_POST['announcement_content'];

    // Duyuru verisini veritabanına ekleyelim
    $sql = "INSERT INTO announcements (title, content) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$announcement_title, $announcement_content]);

    echo "Duyuru başarıyla eklendi!";
}

// Kullanıcı onaylama işlemi
if (isset($_GET['approve_user_id'])) {
    $user_id = $_GET['approve_user_id'];
    // Kullanıcıyı onayla
    $sql = "UPDATE users SET is_approved = 1 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    echo "Kullanıcı başarıyla onaylandı!";
}

// Etkinlikleri listele
$stmt_events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt_events->fetchAll();

// Duyuruları listele
$stmt_announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = $stmt_announcements->fetchAll();

// Onaylanmamış kullanıcıları listele
$stmt_users = $pdo->query("SELECT * FROM users WHERE is_approved = 0");
$users = $stmt_users->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="etkinlik.css" />
</head>
<body>
    <div class="admin-container">
        <h2>Admin Paneli</h2>

        <!-- Etkinlik Ekleme Formu -->
        <h3>Etkinlik Ekle</h3>
        <form method="POST" action="admin-dashboard.php">
            <label for="title">Etkinlik Başlığı:</label><br>
            <input type="text" name="title" id="title" required><br><br>

            <label for="description">Açıklama:</label><br>
            <textarea name="description" id="description" rows="5" required></textarea><br><br>

            <label for="event_date">Etkinlik Tarihi:</label><br>
            <input type="datetime-local" name="event_date" id="event_date" required><br><br>

            <label for="price_student">Öğrenci Bileti Fiyatı (TL):</label><br>
            <input type="number" name="price_student" id="price_student" min="0" step="0.01" required><br><br>

            <label for="price_full">Tam Bilet Fiyatı (TL):</label><br>
            <input type="number" name="price_full" id="price_full" min="0" step="0.01" required><br><br>

            <label for="quota">Kontenjan:</label><br>
            <input type="number" name="quota" id="quota" min="1" required><br><br>

            <button type="submit">Etkinlik Ekle</button>
        </form>

        <hr />

        <!-- Duyuru Ekleme Formu -->
        <h3>Duyuru Ekle</h3>
        <form method="POST" action="admin-dashboard.php">
            <label for="announcement_title">Duyuru Başlığı:</label><br>
            <input type="text" name="announcement_title" id="announcement_title" required><br><br>

            <label for="announcement_content">Duyuru İçeriği:</label><br>
            <textarea name="announcement_content" id="announcement_content" rows="5" required></textarea><br><br>

            <button type="submit">Duyuru Ekle</button>
        </form>

        <hr />

        <!-- Etkinlikleri Listeleme -->
        <h3>Etkinlikler</h3>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Açıklama</th>
                    <th>Tarih</th>
                    <th>Öğrenci Fiyatı (TL)</th>
                    <th>Tam Bilet Fiyatı (TL)</th>
                    <th>Kontenjan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($event['description'])) ?></td>
                    <td><?= $event['event_date'] ?></td>
                    <td><?= number_format($event['price_student'], 2) ?></td>
                    <td><?= number_format($event['price_full'], 2) ?></td>
                    <td><?= htmlspecialchars($event['quota']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr />

        <!-- Duyuruları Listeleme -->
        <h3>Duyurular</h3>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>İçerik</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($announcements as $announcement): ?>
                <tr>
                    <td><?= htmlspecialchars($announcement['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($announcement['content'])) ?></td>
                    <td><?= $announcement['created_at'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr />

        <!-- Onaylanmamış Kullanıcılar -->
        <h3>Onay Bekleyen Kullanıcılar</h3>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Onayla</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><a href="admin-dashboard.php?approve_user_id=<?= $user['id'] ?>">Onayla</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
