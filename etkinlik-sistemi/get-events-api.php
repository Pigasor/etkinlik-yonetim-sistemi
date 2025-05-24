<?php
require 'config.php';

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($events);
