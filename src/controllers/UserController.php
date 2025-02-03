<?php
require_once __DIR__ . '/../../src/config/db.php';
require_once __DIR__ . '/../../src/services/eventService.php';
require_once __DIR__ . '/../../src/utils/authGaurd.php';

class UserController
{

    public function __construct() {
        isAuthenticated();
    }

    public function index(){
        require_once __DIR__ . '/../views/dashboard.html';
    }
    public function dashboard()
    {
        $limit = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 5;
        $offset = (isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0) * $limit;
        $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'title';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

        $eventService = new EventService();
        $events = $eventService->getEvents($limit, $offset, $sortBy, $order, $filter);

        $totalEvents = $eventService->count();
        $totalPages = ceil($totalEvents / $limit);

        echo json_encode([
            'events' => $events,
            'totalPages' => $totalPages
        ]);
    }
}
