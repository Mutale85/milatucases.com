<?php
    include '../../includes/db.php'; // Adjust the path to your database connection script

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
        $companyName = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $address = $_POST['address'];
        $postalCode = $_POST['postal_code'];
        $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        // Handle the logo upload
        $logoUrl = null;
        // if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        //     $logo = $_FILES['logo'];
        //     $logoName = time() . '_' . basename($logo['name']);
        //     $logoPath = 'uploads/' . $logoName; // Adjust the upload path as needed

        //     // Move the uploaded file to the target directory
        //     if (move_uploaded_file($logo['tmp_name'], $logoPath)) {
        //         $logoUrl = $logoPath;
        //     } else {
        //         echo json_encode(['success' => false, 'message' => 'Error uploading the logo.']);
        //         exit;
        //     }
        // }

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo = $_FILES['logo'];
            $logoName = time() . '_' . basename($logo['name']);
            $logoPath = 'uploads/' . $logoName; // Adjust the upload path as needed

            // Move the uploaded file to the target directory
            if (move_uploaded_file($logo['tmp_name'], $logoPath)) {
                // Resize the image
                resizeImage($logoPath, 218, 103); // Width and height

                $logoUrl = $logoPath;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error uploading the logo.']);
                exit;
            }
        }

        try {
            // Check if the record already exists
            $stmt = $connect->prepare("SELECT id FROM company_info WHERE lawFirmId = ?");
            $stmt->execute([$lawFirmId]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                // Update the existing record
                $stmt = $connect->prepare("
                    UPDATE company_info SET
                    company_name = ?, 
                    address = ?,
                    postal_code = ?, 
                    telephone = ?, 
                    email = ?, 
                    logo = IFNULL(?, logo)
                    WHERE lawFirmId = ?
                ");

                $stmt->execute([
                    $companyName,
                    $address,
                    $postalCode,
                    $telephone,
                    $email,
                    $logoUrl,
                    $lawFirmId
                ]);
            } else {
                // Insert a new record
                $stmt = $connect->prepare("
                    INSERT INTO company_info (lawFirmId, company_name, address, postal_code, telephone, email, logo)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $lawFirmId,
                    $companyName,
                    $address,
                    $postalCode,
                    $telephone,
                    $email,
                    $logoUrl
                ]);
            }

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
