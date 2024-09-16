<?php
session_start();
include 'database.php';

// Генерация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ошибка валидации CSRF!');
    }

    // Получение данных из формы с проверкой на существование
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';

    // Валидация данных
    if (empty($name)) {
        $errors[] = 'Название товара не может быть пустым.';
    }
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = 'Цена должна быть числом больше 0.';
    }
    if (!is_numeric($quantity) || $quantity < 0) {
        $errors[] = 'Количество должно быть числом больше или равно 0.';
    }

    // Если ошибок нет, добавляем товар в базу данных
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$name, $price, $quantity]);

        $_SESSION['message'] = 'Товар успешно добавлен!';
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить товар</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Добавить новый товар</h1>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="add.php">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    
    Название: <input type="text" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"><br>
    Цена: <input type="number" step="0.01" name="price" value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>"><br>
    Количество: <input type="number" name="quantity" value="<?php echo isset($quantity) ? htmlspecialchars($quantity) : ''; ?>"><br>
    
    <button type="submit">Добавить</button>
</form>

<a href="index.php">Назад</a>
</body>
</html>