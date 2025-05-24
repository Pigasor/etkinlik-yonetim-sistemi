<?php
require 'config.php';
session_start(); // Giriş kontrolü için eklendi

// Duyuruları çekmek için SQL sorgusu
$stmt_announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = $stmt_announcements->fetchAll();

// Kullanıcı giriş yapmış mı? JS tarafına gönderilecek
$is_logged_in = isset($_SESSION['user_id']) ? 'true' : 'false';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ana Sayfa</title>
    <link rel="stylesheet" href="etkinlik.css" />
</head>
<body>

    <div class="container">
        <h2>Etkinlikler</h2>
        <div id="events-container">Yükleniyor...</div>

        <hr />

        <h2>Duyurular</h2>

        <?php foreach ($announcements as $announcement): ?>
            <div class="announcement">
                <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                <p><em><?php echo $announcement['created_at']; ?></em></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        const isLoggedIn = <?php echo $is_logged_in; ?>;

        function formatPrice(price) {
            return Number(price).toFixed(2) + '₺';
        }

        fetch('get-events-api.php')
            .then(response => response.json())
            .then(events => {
                const container = document.getElementById('events-container');
                container.innerHTML = '';

                if (events.length === 0) {
                    container.textContent = 'Etkinlik bulunamadı.';
                    return;
                }

                events.forEach(event => {
                    const div = document.createElement('div');
                    div.classList.add('event');

                    div.innerHTML = `
                        <h3>${event.title}</h3>
                        <p>${event.description.replace(/\n/g, '<br>')}</p>
                        <p><strong>Tarih:</strong> ${event.event_date}</p>
                        <p><strong>Kontenjan:</strong> ${event.quota}</p>
                        <p><strong>Fiyatlar:</strong> Öğrenci ${formatPrice(event.price_student)}, Tam ${formatPrice(event.price_full)}</p>
                        ${isLoggedIn ? `
                            <form action="add-to-cart.php" method="POST" style="margin-top:10px;">
                                <input type="hidden" name="event_id" value="${event.id}">
                                <label>
                                    <input type="radio" name="ticket_type_${event.id}" value="student" checked> Öğrenci
                                </label>
                                <label>
                                    <input type="radio" name="ticket_type_${event.id}" value="full"> Tam
                                </label>
                                <input type="number" name="ticket_count" value="1" min="1" style="width:50px;">
                                <button type="submit">Sepete Ekle</button>
                            </form>
                        ` : '<p><em>Sepete eklemek için giriş yapmalısınız.</em></p>'}
                    `;
                    container.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Etkinlik API hatası:', error);
                document.getElementById('events-container').textContent = 'Etkinlik verileri alınamadı.';
            });
    </script>

</body>
</html>
