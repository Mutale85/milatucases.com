<?php
    
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $lawFirmId = $_POST['lawFirmId'];
        $uploaded_by = $_POST['uploaded_by'];
        $files = $_FILES['files'];

        // Directory where files will be saved
        $uploadDir = 'uploads/';
        // Create the directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileCount = count($files['name']);
        $errors = [];

        for ($i = 0; $i < $fileCount; $i++) {
            $filePath = $uploadDir . basename($files['name'][$i]);
            $fileName = basename($files['name'][$i]);

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
                // Save file information in the database
                $stmt = $connect->prepare("INSERT INTO lawFirmFiles (lawFirmId, file_name, file_path, uploaded_by) VALUES (?, ?, ?, ?)");
                $stmt->execute([$lawFirmId, $fileName, $filePath, $uploaded_by]);

                if (!$stmt) {
                    $errors[] = "Error saving file information to the database for file: $fileName.";
                }
            } else {
                $errors[] = "Error uploading the file: $fileName.";
            }
        }

        if (empty($errors)) {
            echo "Files uploaded successfully!";
        } else {
            foreach ($errors as $error) {
                echo $error . "<br>";
            }
        }
    }
    
?>
