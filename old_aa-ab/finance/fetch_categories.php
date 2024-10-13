<?php
    require '../../includes/db.php';

    // Validate and sanitize the input
    $church_id = filter_input(INPUT_POST, 'church_id', FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$church_id) {
        // Invalid input, handle the error (e.g., log, return an error message)
        exit('Invalid church ID');
    }

    $stmt = $connect->prepare("SELECT * FROM church_budgets WHERE church_id = ?");
    if (!$stmt) {
        // Error preparing the statement, handle the error
        exit('Error preparing statement');
    }

    if (!$stmt->execute([$church_id])) {
        // Error executing the statement, handle the error
        exit('Error executing statement');
    }

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = '<option value="">Select Category</option>';
    foreach ($categories as $category) {
        // Encode the output
        $output .= '<option value="' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['category']) . '</option>';
    }

    echo $output;
?>
