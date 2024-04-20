<?php
session_start();

// Проверка авторизации пользователя
if (!isset($_SESSION['user_id'])) {
    header('Location: user/auth/signin.php');
    exit();
}

// Проверка роли пользователя, редирект администраторов
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header('Location: admin/admin.php');
    exit();
}

require_once 'database/user.php';
$user = new User();

// Отправка нового заявления
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_number'], $_POST['description'])) {
    $car_number = $_POST['car_number'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    if ($user->addReport($user_id, $car_number, $description)) {
        $_SESSION['message'] = "Заявление успешно добавлено!";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = "Ошибка: " . $user->getError();
    }
}

$reports = $user->getReports($_SESSION['user_id']);
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Страница заявлений</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Страница заявлений</h1>
        <?php if (!empty($message)) echo $message; ?>
        <h2>Оставить новое заявление</h2>
        <form action="index.php" method="post">
            <div class="mb-3">
                <label for="car_number" class="form-label">Гос. номер автомобиля</label>
                <input type="text" class="form-control" id="car_number" name="car_number" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Описание нарушения</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Отправить заявление</button>
        </form>
        <h2 class="mt-5">Мои заявления</h2>
        <?php
        if ($reports) {
            echo "<ul class='list-group'>";
            foreach ($reports as $report) {
                echo "<li class='list-group-item'>Номер автомобиля: " . htmlspecialchars($report['car_number']) . " - Описание: " . htmlspecialchars($report['description']) . " - Статус: " . htmlspecialchars($report['status']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>У вас пока нет заявлений.</p>";
        }
        ?>
        <p class="mt-4"><a href="user/auth/logout.php" class="btn btn-danger">Выход</a></p>
    </div>
</body>
</html>
