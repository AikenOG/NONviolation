<?php
session_start();

// Подключение класса User
require_once '../../database/user.php';

$user = new User();

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'], $_POST['password'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if ($user->login($login, $password)) {
        header('Location: ../../index.php'); // Переадресация на главную страницу после успешного входа
        exit();
    } else {
        $error_message = "Неверный логин или пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-utilities.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-grid.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-3">Вход в систему</h2>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="signin.php" method="post">
            <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button type="submit" class="btn btn-primary">Войти</button>
                <a href="signup.php" class="btn btn-outline-secondary">Регистрация</a>
            </div>
        </form>
    </div>
</body>
</html>
