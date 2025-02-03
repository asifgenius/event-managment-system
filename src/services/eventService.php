<?php
require_once __DIR__ . '/./baseService.php';

class EventService extends BaseService
{
    protected $tableName = 'events';


    public function createEvent(array $data)
    {

        return $this->create($data);
    }

    public function getEvents($limit = 10, $offset = 0, $sortBy = 'title', $order = 'ASC', $filter = '')
    {
        $query = "SELECT e.*, u.name AS creator
              FROM events e
              JOIN users u ON e.created_by = u.id";
        $params = [];

        if (!empty($filter)) {
            $query .= " WHERE e.title LIKE :filter OR e.location LIKE :filter";
            $params[':filter'] = '%' . $filter . '%';
        }

        $query .= " ORDER BY $sortBy $order LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int) $limit;
        $params[':offset'] = (int) $offset;

        $stmt = $this->connect->prepare($query);
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventById($id)
    {
        $sql = "SELECT e.*, u.name AS creator 
                FROM events e 
                JOIN users u ON e.created_by = u.id 
                WHERE e.id = :id";
        $stmt = $this->connect->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEvent($id, array $data)
    {
        $fields = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
        $sql = "UPDATE {$this->tableName} SET $fields WHERE id = :id";
        $stmt = $this->connect->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function getAttendeeById($eventId)
    {
        $stmt = $this->connect->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAttendeesCount($eventId)
    {
        try {
            $this->connect->beginTransaction();
            $stmt = $this->connect->prepare("SELECT COUNT(*) AS user_count FROM attendees WHERE event_id = ? FOR UPDATE");
            $stmt->execute([$eventId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->connect->commit();
            return $result['user_count'] ?? 0;
        } catch (PDOException $e) {
            $this->connect->rollBack();
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function registerUser($eventId, $userId)
    {
        try {
            $stmt = $this->connect->prepare("INSERT INTO attendees (event_id, user_id) VALUES (?, ?)");
            $stmt->execute([$eventId, $userId]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function isUserRegistered($eventId, $userId)
    {
        $stmt = $this->connect->prepare("SELECT * FROM attendees WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$eventId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAttendees($eventId)
    {
        try {
            $stmt = $this->connect->prepare("SELECT users.name, users.email, users.phone, attendees.registration_date
                                                FROM attendees
                                                JOIN users ON attendees.user_id = users.id
                                                WHERE attendees.event_id = ?");
            $stmt->execute([$eventId]);

            $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return !empty($attendees) ? $attendees : ['error' => 'No attendees found for this event.'];
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getEventAttendees($eventId)
    {
        $stmt = $this->connect->prepare("
        SELECT
            attendees.id AS attendee_id,
            users.name AS user_name,
            users.email,
            users.phone,
            attendees.registration_date
        FROM attendees
        JOIN users ON attendees.user_id = users.id
        WHERE attendees.event_id = ?
    ");

        $stmt->execute([$eventId]);

        $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($attendees)) {
            error_log("No attendees found for event ID: " . $eventId);
        }

        return $attendees;
    }


    public function generateAttendeeCSV($eventId)
    {
        $attendees = $this->getEventAttendees($eventId);

        if (empty($attendees)) {
            echo "No attendees found for this event.";
            exit();
        }

        $filename = "attendees_event_{$eventId}.csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['Attendee ID', 'Name', 'Email', 'Phone', 'Registered At']);

        foreach ($attendees as $attendee) {
            fputcsv($output, [
                $attendee['attendee_id'],
                $attendee['user_name'],
                $attendee['email'],
                $attendee['phone'],
                $attendee['registration_date']
            ]);
        }

        fclose($output);
        exit();
    }
}
?>
