<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    ob_start();
    header('Content-Type: application/json');

    try {
        require_once '../../../includes/db.php';
        require_once '../../../includes/conf.php';
        $openai_api_key = SECRET_KEY;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $docType = $_POST['docType'] ?? '';
            $customDocType = $_POST['customDocType'] ?? '';
            $clientId = $_POST['clientId'] ?? '';
            $startDate = $_POST['startDate'] ?? '';
            $description = $_POST['description'] ?? '';
            $lawFirmId = $_SESSION['parent_id'] ?? '';
            $userId = $_SESSION['user_id'] ?? '';

            if (empty($docType) || empty($clientId) || empty($startDate) || empty($description)) {
                throw new Exception('Missing required form data');
            }

            $clientNames = getClientNameById($clientId, $lawFirmId);
            $prompt = "Generate a " . ($docType === 'custom' ? $customDocType : $docType) . " for " . $clientNames . " starting from " . $startDate . ". Include the following description: " . $description;
            
            $content = generateDocumentContent($prompt);
            $chunks = preg_split('/(\\n)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Insert into database
            $stmt = $connect->prepare("INSERT INTO ai_generated_documents (clientId, lawFirmId, userId, doc_type, start_date, description, content, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$clientId, $lawFirmId, $userId, ($docType === 'custom' ? $customDocType : $docType), $startDate, $description, $content]);
            $generationId = $connect->lastInsertId();

            echo json_encode(['success' => true, 'chunks' => $chunks, 'generationId' => $generationId]);
        } else {
            throw new Exception('Invalid request method');
        }
    } catch (Exception $e) {
        error_log('Error in AI document generation: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    ob_end_flush();
