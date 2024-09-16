<?php
session_start();
include 'database.php';
include 'auth.php';

$stmt = $pdo->query("SELECT products.name, SUM(sales.quantity) AS total_sold FROM sales
                     JOIN products ON sales.product_id = products.id
                     GROUP BY products.name");
$sales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчет о продажах</title>
</head>
<body>
<h1>Отчет о продажах</h1>

<table>
    <thead>
        <tr>
            <th>Товар</th>
            <th>Продано</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($sales as $sale): ?>
        <tr>
            <td><?php echo htmlspecialchars($sale['name']); ?></td>
            <td><?php echo htmlspecialchars($sale['total_sold']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<a href="index.php">Назад</a>
</body>
</html>
