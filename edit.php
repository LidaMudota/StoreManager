<?php
session_start();
include 'database.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Проверка существующего токена, создание нового только если его нет
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token = $_SESSION['csrf_token'];

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ошибка валидации CSRF!');
    }

    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (empty($name)) {
        $errors[] = 'Название товара не может быть пустым.';
    }
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = 'Цена должна быть числом больше 0.';
    }
    if (!is_numeric($quantity) || $quantity < 0) {
        $errors[] = 'Количество должно быть числом больше или равно 0.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, quantity = ? WHERE id = ?");
        $stmt->execute([$name, $price, $quantity, $id]);
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать товар</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Редактировать товар</h1>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
    Название: <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>"><br>
    Цена: <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>"><br>
    Количество: <input type="number" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>"><br>
    <button type="submit">Сохранить</button>
</form>
<a href="index.php">Назад</a>
</body>
</html>