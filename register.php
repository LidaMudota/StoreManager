<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = isset($_POST['role']) ? $_POST['role'] : 'user';  // По умолчанию роль "user"

    $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
    if ($stmt->execute([$username, $password, $role])) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка при регистрации!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Регистрация</h1>
    <form method="POST">
        <label for="username">Логин:</label>
        <input type="text" name="username" required>
        <label for="password">Пароль:</label>
        <input type="password" name="password" required>
        <label for="role">Роль:</label> <!-- Только если админ будет создавать пользователей -->
        <select name="role">
            <option value="user">Пользователь</option>
            <option value="admin">Администратор</option>
        </select>
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>
