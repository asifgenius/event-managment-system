<?php
session_start();

require_once __DIR__ . '/../../src/config/db.php';
require_once __DIR__ . '/../../src/services/userService.php';

class AuthController
{
    public function index()
    {
        require_once __DIR__ . '/../views/login.html';
    }
    public function login()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $email = htmlspecialchars(trim($_POST['email']));
        $password = $_POST['password'];

        try {
            $userService = new UserService();
            $user = $userService->login($email, $password);

            if ($user) {
                echo json_encode([
                    'success' => true,
                    'role' => $_SESSION['role'],
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid email or password.',
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
        exit();
    }

    public function getRegistration()
    {
        require_once __DIR__ . '/../views/registration.html';
    }

    public function registration()
    {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $phone = htmlspecialchars(trim($_POST['phone']));
        $password = trim($_POST['password']);
        $password_confirm = trim($_POST['password_confirm']);
        $role = 'user';

        if (!$email) {
            $_SESSION['error'] = "Invalid email format.";
            header('Location: /event_managment/registration');
            exit();
        }

        if ($password !== $password_confirm) {
            $_SESSION['error'] = "Passwords do not match.";
            echo json_encode([
                'success' => false,
                'message' => 'Passwords do not match.',
            ]);
        
            exit();
        }

        
        try {
            $userService = new UserService();
            $result = $userService->registration($name, $email, $phone, $password_confirm, $role);

            if (headers_sent()) {
                die("Headers already sent! Fix output before header() calls.");
            }

            if (isset($result['success'])) {
                $_SESSION['success'] = "Registration successful! You can now login.";

                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful! You can now login.',
                ]);
            
                exit();
            } else {
                $_SESSION['error'] = $result['error'];
                echo json_encode([
                    'success' => false,
                    'message' => $result['error'],
                ]);
                exit();
            }

        } catch (PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
            echo json_encode([
                'success' => false,
                'message' => "Database Error: " . $e->getMessage(),
            ]);
            exit();
        }

    }
    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /event_managment/');
        exit();

    }
}
