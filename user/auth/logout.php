<?php
session_start();

require_once '../../database/user.php';

$user = new User();

// Деавторизация пользователя
$user->logout();

// Переадресация на страницу входа
header('Location: signin.php');
exit();
