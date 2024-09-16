<?php
// Подключение к базе данных
$dsn = 'mysql:host=localhost;dbname=u2732844_default';
$username = 'u2732844_default';
$password = 'Wf2rPp9b10EwS0l0';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit();
}
?>