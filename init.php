<?php
require_once __DIR__ . '/src/config/db.php';
require_once __DIR__ . '/src/services/userService.php';
require_once __DIR__ . '/src/services/eventService.php';

try {
    $conn    = Database::getConnection();
    $sqlFile = __DIR__ . '/src/config/database.sql';

    if (! file_exists($sqlFile)) {
        die("Error: SQL file does not exist at $sqlFile");
    }

    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        die("Error reading SQL file.");
    }

    $sqlStatements = explode(";", $sql);

    foreach ($sqlStatements as $statement) {
        $statement = trim($statement);
        if (! empty($statement)) {
            $conn->exec($statement);
        }
    }

    $user = new UserService();
    $user->registration('asifislam', 'admin@gmail.com', '1234567890', '1234', 'admin');
    $user->registration('asifuser', 'user@gmail.com', '12330987654', '1234', 'user');

    $event = new EventService();
    $event->createEvent([
        "title" => "Technology Conference 2025",
        "description" => "A technology conference for developers",
        "start_date" => date('Y-m-d H:i:s'), 
        "end_date" => date('Y-m-d H:i:s', strtotime('+1 day')),
        "location" => "Dhaka",
        "price" => 100.00,
        "duration" => 24,
        "max_capacity" => 200,
        "current_capacity" => 1,
        "created_by" => 1
    ]);

    $event->createEvent([
        "title" => "Web Development Bootcamp",
        "description" => "Bootcamp to learn web development",
        "start_date" => date('Y-m-d H:i:s'), 
        "end_date" => date('Y-m-d H:i:s', strtotime('+5 days')),
        "location" => "Dinajpur",
        "price" => 500.00,
        "duration" => 3,
        "max_capacity" => 400,
        "current_capacity" => 4, 
        "created_by" => 1 
    ]);
    
    echo "Database seed executed successfully!";

} catch (PDOException $e) {
    echo "Error executing SQL file: " . $e->getMessage();
}
