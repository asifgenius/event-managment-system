<?php
require_once __DIR__ . '/./baseService.php';

class UserService extends BaseService
{
    protected $tableName = 'users';

    public function emailExists($email)
    {
        $stmt = $this->connect->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function registration($name, $email, $phone, $password, $role)
    {
        if ($this->emailExists($email)) {
            return ['error' => "Email is already registered."];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->connect->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");

        if ($stmt->execute([$name, $email, $phone, $hashedPassword, $role])) {
            return ['success' => "Registration successful!"];
        } else {
            return ['error' => "Failed to register user."];
        }
    }

    public function login($email, $password)
    {
        $stmt = $this->connect->prepare("SELECT id, name, role, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return $user;
        } else {
            return ['error' => "Invalid email or password."];
        }
    }

    public function getUsers()
    {
        $sql = "SELECT e.*, u.name AS creator 
                FROM events e 
                JOIN users u ON e.created_by = u.id 
                ORDER BY e.start_date ASC";
        $stmt = $this->connect->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
