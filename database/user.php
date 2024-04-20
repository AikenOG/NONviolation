<?php 
require_once 'connectdb.php';

class User extends Connect {
    private $error_valid = false;
    private $error_valid_text = [];

    public function signup($name, $email, $password, $phone, $login) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare("INSERT INTO users (name, email, login, password, role, phone) VALUES (?, ?, ?, ?, 'user', ?)");
        $stmt->bind_param("sssss", $name, $email, $login, $password_hash, $phone);
        if ($stmt->execute()) {
            return true;
        } else {
            $this->error_valid = true;
            $this->error_valid_text["database"] = $this->connection->error;
            return false;
        }
    }

    public function validate($name, $email, $password, $phone, $login) {
        $this->checkEmpty($name, 'name', 'Введите ФИО');
        $this->checkEmpty($email, 'email', 'Введите email');
        $this->checkEmpty($password, 'password', 'Введите пароль');
        $this->checkEmpty($phone, 'phone', 'Введите телефон');
        $this->checkEmpty($login, 'login', 'Введите логин');
        
        if (!empty($phone) && strlen($phone) != 11) {
            $this->error_valid = true;
            $this->error_valid_text["phone"] = 'Введите корректный телефон';
        }

        return !$this->error_valid;
    }

    private function checkEmpty($value, $field, $message) {
        if (empty($value)) {
            $this->error_valid = true;
            $this->error_valid_text[$field] = $message;
        }
    }

    public function getValidationErrors() {
        return $this->error_valid_text;
    }

    public function login($login, $password) {
        $stmt = $this->connection->prepare("SELECT user_id, password, role FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];  // Сохраняем роль в сессии
                return true;
            }
        }
        return false;
    }
    
    public function addReport($user_id, $car_number, $description) {
        // Обновление значения статуса по умолчанию с 'new' на 'новое'
        $stmt = $this->connection->prepare("INSERT INTO reports (user_id, car_number, description, status) VALUES (?, ?, ?, 'Новое')");
        $stmt->bind_param("iss", $user_id, $car_number, $description);
        if ($stmt->execute()) {
            return true;
        } else {
            $this->error_valid = true;
            $this->error_valid_text["database"] = $this->connection->error;
            return false;
        }
    }
    

    public function getReports($user_id) {
        $stmt = $this->connection->prepare("SELECT car_number, description, status FROM reports WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        return $reports;
    }

    public function getError() {
        return $this->error_valid_text["database"] ?? "Unknown error";
    }

    public function logout() {
        // Уничтожаем сессию пользователя
        $_SESSION = array();  // Очистка массива сессии
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();  // Уничтожение сессии
    }

    public function getAllReports() {
        $stmt = $this->connection->prepare("SELECT reports.report_id, users.name, reports.car_number, reports.description, reports.status FROM reports JOIN users ON reports.user_id = users.user_id");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }    
    
    public function changeReportStatus($report_id, $new_status) {
        // Изменение SQL запроса: использование `report_id` вместо `id`
        $stmt = $this->connection->prepare("UPDATE reports SET status = ? WHERE report_id = ?");
        $stmt->bind_param("si", $new_status, $report_id);
        if ($stmt->execute()) {
            return true;
        } else {
            $this->error_valid = true;
            $this->error_valid_text["database"] = $this->connection->error;
            return false;
        }
    }
    

}
?>
