<?php
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/CommonController.php';
require_once __DIR__ . '/src/controllers/EventController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';
require_once __DIR__ . '/src/controllers/UserController.php';
require_once __DIR__ . '/src/utils/authGaurd.php';

$root = '/event_managment';
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestPath = str_replace($root, '', $requestPath);
$requestPath = '/' . ltrim($requestPath, '/');


if ($requestPath === '/') {
    $auth = new AuthController();
    $auth->index();
    exit();
}

else if ($requestPath === '/api/login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $auth = new AuthController();
        $auth->login();
    }
    exit();
} else if ($requestPath === '/api/registration') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $auth = new AuthController();
        $auth->registration();
        exit();
    }
} else if ($requestPath === '/registration') {
    $auth = new AuthController();
    $auth->getRegistration();
    exit();

} else if ($requestPath === '/logout') {
    $auth = new AuthController();
    $auth->logout();
    exit();
} else if ($requestPath === '/register1') {
    exit();
} else if ($requestPath === '/events') {
    $event = new EventController();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $event->index();
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $event->create();
    }
    exit();
} else if (preg_match('#^/update_events/([0-9]+)$#', $requestPath, $matches)) {
    $eventId = $matches[1];
    $event = new EventController();
    $event->updateEvent($eventId);
    exit();
} else if ($requestPath === '/events/list') {
    $admin = new AdminController();
    $admin->dashboard();
    exit();

} else if (preg_match('#^/events/([0-9]+)$#', $requestPath, $matches)) {
    $eventId = $matches[1];
    $event = new EventController();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $event->viewEvent($eventId);
    } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
        $inputData = json_decode(file_get_contents('php://input'), true);

        if (!$inputData) {
            parse_str(file_get_contents("php://input"), $inputData);
        }
        if ($inputData && is_array($inputData)) {
            $event->update($eventId, $inputData);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input']);
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $event->delete($eventId);

    }
    exit();
} else if (preg_match('#^/api/events/([0-9]+)$#', $requestPath, $matches)) {
    $eventId = $matches[1];
    $event = new EventController();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $event->viewRegistration($eventId);
    }
    exit();
} else if (preg_match('#^/event/([0-9]+)$#', $requestPath, $matches)) {
    $eventId = $matches[1];
    $event = new EventController();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $event->viewEvent($eventId);
    }
    exit();
} else if (preg_match('#^/events/registration/([0-9]+)$#', $requestPath, $matches)) {
    $eventId = $matches[1];
    $event = new EventController();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $event->viewEventRegistration($eventId);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $event->eventRegistration($eventId);
    }
    exit();
} else if ($requestPath === '/dashboard') {
    $admin = new AdminController();
    $admin->index();
    exit();
}
else if (preg_match('#^/download/events/csv/([0-9]+)$#', $requestPath, $matches)) {
    $eventId = $matches[1];

    $event = new EventController();
    $event->generateAttendeeCSV($eventId);
    exit();
} else {
    $route = new CommonController();
    $route->index();
    exit();
}
