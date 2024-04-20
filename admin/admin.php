<?php
session_start();

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../user/auth/signin.php');
    exit();
}

require_once '../database/user.php';
$user = new User();

// Обработка изменения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['new_status'])) {
    if ($user->changeReportStatus($_POST['report_id'], $_POST['new_status'])) {
        $message = "Статус заявления успешно обновлен.";
    } else {
        $error = "Ошибка обновления статуса: " . $user->getError();
    }
}

// Получение всех заявлений
$reports = $user->getAllReports();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Административная панель</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Админ-панель</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger" href="../user/auth/logout.php">Выход</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Панель администратора</h1>
        <?php if (!empty($message)) echo "<p class='alert alert-success'>$message</p>"; ?>
        <?php if (!empty($error)) echo "<p class='alert alert-danger'>$error</p>"; ?>

        <h2>Все заявления</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Номер автомобиля</th>
                    <th>Описание</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['name']) ?></td>
                        <td><?= htmlspecialchars($report['car_number']) ?></td>
                        <td><?= htmlspecialchars($report['description']) ?></td>
                        <td><?= htmlspecialchars($report['status']) ?></td>
                        <td>
                            <form action="admin.php" method="post">
                                <input type="hidden" name="report_id" value="<?= htmlspecialchars($report['report_id']) ?>">
                                <select name="new_status" class="form-control">
                                    <option value="новое" <?= $report['status'] == 'новое' ? 'selected' : '' ?>>Новое</option>
                                    <option value="подтверждено" <?= $report['status'] == 'подтверждено' ? 'selected' : '' ?>>Подтверждено</option>
                                    <option value="отклонено" <?= $report['status'] == 'отклонено' ? 'selected' : '' ?>>Отклонено</option>
                                </select>
                                <button type="submit" class="btn btn-primary mt-2">Изменить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
