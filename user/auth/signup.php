<!-- user\reg\signup.php -->

<?php
require_once '../../database/user.php';

$user = new User();
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $login = $_POST['login'];

    if ($user->validate($name, $email, $password, $phone, $login)) {
        if ($user->signup($name, $email, $password, $phone, $login)) {
            header("Location: signin.php"); // Redirect to login page after successful registration
            exit();
        } else {
            $error_messages = $user->getValidationErrors();
        }
    } else {
        $error_messages = $user->getValidationErrors();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-utilities.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-grid.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-3">Регистрация нового пользователя</h2>
        <?php if (!empty($error_messages)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($error_messages as $msg) echo "<div>$msg</div>"; ?>
            </div>
        <?php endif; ?>
        <form action="signup.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">ФИО</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                <a href="signin.php" class="btn btn-outline-secondary">Вход</a>
            </div>
        </form>
    </div>
</body>
</html>
