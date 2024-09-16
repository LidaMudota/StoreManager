<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $quantitySold = $_POST['quantity'];

    $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product['quantity'] >= $quantitySold) {
        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt->execute([$quantitySold, $productId]);

        $stmt = $pdo->prepare("INSERT INTO sales (product_id, quantity, sale_date) VALUES (?, ?, NOW())");
        $stmt->execute([$productId, $quantitySold]);

        echo "Товар продан!";
    } else {
        echo "Недостаточно товара!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Продажа товара</title>
</head>
<body>
<h1>Продать товар</h1>

<form method="POST">
    Товар: <select name="product_id">
        <?php
        $stmt = $pdo->query("SELECT * FROM products");
        while ($row = $stmt->fetch()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select><br>
    Количество: <input type="number" name="quantity"><br>
    <button type="submit">Продать</button>
</form>

<a href="index.php">Назад</a>
</body>
</html>
