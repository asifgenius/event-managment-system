<?php
function isAuthenticated()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /');
        exit();
    }

}

function authGaurd($role)
{
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        echo 'You are not allowed to perform this action.';
        exit();
    }
}
?>
