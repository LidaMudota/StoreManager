<?php
session_start();
include 'database.php';

// Проверяем, что пользователь авторизован
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Проверяем, что пользователь - администратор
if ($_SESSION['role'] !== 'admin') {
    echo 'У вас нет прав доступа к этой странице!';
    exit();
}

// Показ уведомлений
if (isset($_SESSION['message'])) {
    echo '<p>' . $_SESSION['message'] . '</p>';
    unset($_SESSION['message']);
}

// Определение сортировки
$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'name'; 
$validColumns = ['name', 'price', 'quantity'];
if (!in_array($orderBy, $validColumns)) {
    $orderBy = 'name';
}

// Фильтрация товаров
$minPrice = isset($_GET['minPrice']) ? (float) $_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) ? (float) $_GET['maxPrice'] : PHP_INT_MAX;
$minQty = isset($_GET['minQty']) ? (int) $_GET['minQty'] : 0;
$maxQty = isset($_GET['maxQty']) ? (int) $_GET['maxQty'] : PHP_INT_MAX;

// SQL-запрос с фильтрацией
$stmt = $pdo->prepare("SELECT * FROM products WHERE price BETWEEN ? AND ? AND quantity BETWEEN ? AND ? ORDER BY $orderBy");
$stmt->execute([$minPrice, $maxPrice, $minQty, $maxQty]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Система учета товаров</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar">
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="add.php">Добавить товар</a></li>
            <li><a href="export.php">Экспорт товаров в CSV</a></li>
        </ul>
        <span>Ваша система учета товаров</span>
        <ul>
            <li><a href="#"><?php echo $_SESSION['username']; ?></a></li>
            <li><a href="logout.php">Выйти</a></li>
        </ul>
    </nav>
</header>
<div class="table-container">
    <h1>Список товаров</h1>
    <form method="GET" action="" class="form-inline">
        <label for="minPrice">Цена от:</label>
        <input type="number" step="0.01" name="minPrice" value="<?php echo htmlspecialchars($minPrice); ?>">
        <label for="maxPrice">до:</label>
        <input type="number" step="0.01" name="maxPrice" value="<?php echo htmlspecialchars($maxPrice); ?>">
        <label for="minQty">Количество от:</label>
        <input type="number" name="minQty" value="<?php echo htmlspecialchars($minQty); ?>">
        <label for="maxQty">до:</label>
        <input type="number" name="maxQty" value="<?php echo htmlspecialchars($maxQty); ?>">
        <button type="submit" class="btn btn-primary">Применить фильтр</button>
    </form>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th><a href="?sort=name">Название</a></th>
                <th><a href="?sort=price">Цена</a></th>
                <th><a href="?sort=quantity">Количество</a></th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Редактировать</a>
                    <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот товар?');">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
