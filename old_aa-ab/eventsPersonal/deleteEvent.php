<?php 
    include("../../includes/db.php");
    include("../../includes/conf.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../../vendor/autoload.php';  // Ensure you have PHPMailer loaded via Composer or autoload file

    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get the event ID from the POST data
        $eventId = $_POST['eventId'];
        
        try {
            // Get the event details
            $stmt = $connect->prepare("DELETE FROM events_personal WHERE event_id = ?");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
    
                echo json_encode(['message' => 'Event deleted successfully']);
            } else {
                // Respond with error message if the event is not found
                echo json_encode(['error' => 'Event not found']);
            }
        } catch (PDOException $e) {
            // Respond with error message if there's a PDO exception
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Respond with error message if request method is not POST
        echo json_encode(['error' => 'Invalid request method']);
    }
?>
