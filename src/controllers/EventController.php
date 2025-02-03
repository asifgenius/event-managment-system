<?php
require_once __DIR__ . '/../../src/config/db.php';
require_once __DIR__ . '/../../src/services/eventService.php';
require_once __DIR__ . '/../../src/utils/authGaurd.php';

class EventController
{

    public function __construct()
    {
        isAuthenticated();
    }

    public function index()
    {
        authGaurd('admin');
        require_once __DIR__ . '/../views/form/event.html';
    }

    public function viewById($id)
    {
        if (! is_numeric($id)) {
            echo json_encode(['error' => 'Invalid event ID']);
            return;
        }

        $eventService = new EventService();
        $eventDetails = $eventService->getEventById($id);

        if ($eventDetails) {
            echo json_encode(['event' => $eventDetails]);
        } else {
            echo json_encode(['error' => 'Event not found']);
        }
    }

    public function create()
    {
        authGaurd('admin');
        $eventService = new EventService();

        $data = [
            'title'        => htmlspecialchars($_POST['title']),
            'description'  => htmlspecialchars($_POST['description']),
            'start_date'   => $_POST['start_date'],
            'end_date'     => $_POST['end_date'],
            'location'     => htmlspecialchars($_POST['location']),
            'price'        => htmlspecialchars($_POST['price']),
            'duration'     => intval($_POST['duration']),
            'max_capacity' => intval($_POST['max_capacity']),
            'created_by'   => $_SESSION['user_id'],
        ];

        if ($eventService->createEvent($data)) {
            echo json_encode(['success' => true, 'message' => "Event created successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Failed to create event."]);
        }

    }

    public function update($eventId, $data)
    {
        authGaurd('admin');

        if (! is_numeric($eventId)) {
            echo json_encode(['error' => 'Invalid event ID']);
            return;
        }

        $eventService = new EventService();
        $result       = $eventService->update($eventId, $data);

        if ($result) {
            echo json_encode(['success' => 'Event updated successfully']);
        } else {
            echo json_encode(['error' => 'Failed to update event']);
        }
    }
    public function delete($eventId)
    {
        authGaurd('admin');
        if (! is_numeric($eventId)) {
            echo json_encode(['error' => 'Invalid event ID']);
            return;
        }
        $eventService = new EventService();

        if ($eventService->delete($eventId)) {
            echo json_encode(['success' => 'Event deleted successfully']);
        } else {
            echo json_encode(['error' => 'Failed to delete event']);
        }
    }
    public function updateEvent($eventId)
    {
        authGaurd('admin');
        require_once __DIR__ . '/../views/form/update_event.html';
    }
    public function viewEvent($eventId)
    {
        require_once __DIR__ . '/../views/view_event.html';
    }

    public function viewEventRegistration($eventId)
    {
        require_once __DIR__ . '/../views/register_event.html';

    }
    public function viewRegistration($eventId)
    {
        $eventService = new EventService();
        $eventDetails = $eventService->getEventById($eventId);

        $attendeesCount = $eventService->getAttendeesCount($eventId);
        echo json_encode(['event' => $eventDetails, "attendees_count" => $attendeesCount]);
    }

    public function eventRegistration($eventId)
    {
        try {

            $userId       = $_SESSION['user_id'];
            $eventService = new EventService();
            $event        = $eventService->getAttendeeById($eventId);

            if (! $event) {
                echo json_encode(['error' => 'Event not found.']);
            }

            $attendeesCount = $eventService->getAttendeesCount($eventId);

            if ($attendeesCount >= $event['max_capacity']) {
                echo json_encode(['error' => 'Event has reached maximum capacity.']);
                return;
            }

            if ($eventService->registerUser($eventId, $userId)) {
                echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
            } else {
                echo json_encode(['error' => 'You are already registered for this event.']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }

    }

    public function generateAttendeeCSV($eventId)
    {
        authGaurd('admin');
        $eventService = new EventService();
        $attendees    = $eventService->getEventAttendees($eventId);
        $filename     = "attendees_event_{$eventId}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        ob_clean();
        flush();

        $output = fopen('php://output', 'w');

        fputcsv($output, ['Attendee ID', 'Name', 'Email', 'Phone', 'Registered At']);

        if (! empty($attendees)) {
            foreach ($attendees as $attendee) {
                fputcsv($output, [
                    $attendee['attendee_id'],
                    $attendee['user_name'],
                    $attendee['email'],
                    $attendee['phone'],
                    $attendee['registration_date'],
                ]);
            }
        } else {
            fputcsv($output, ['No attendees found for this event.', '', '', '', '']);
        }

        fclose($output);
        exit();
    }

}
