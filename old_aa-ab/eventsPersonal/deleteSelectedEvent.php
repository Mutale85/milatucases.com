<?php
    include("../../includes/db.php");
    $eventId = $_POST['eventId'];

    try {
        // Start a transaction
        $connect->beginTransaction();
        // Delete event
        $query = "DELETE FROM events_personal WHERE id = :eventId";
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();

        // Commit the transaction
        $connect->commit();

        // Return success message if needed
        echo "Event deleted successfully";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $connect->rollBack();

        // Return error message
        echo "Error deleting event";
    }
?>