<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include '../../includes/db.php';
    require_once '../../includes/conf.php';

    $parentId = $_SESSION['parent_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $documentType = $_POST['documentType'] ?? '';
        $prompt = $_POST['prompt'] ?? '';
        
        if (empty($documentType) || empty($prompt)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        
        if (!$parentId || !$userId) {
            http_response_code(403);
            echo json_encode(['error' => 'User is not authenticated']);
            exit;
        }
        
        $generatedDocument = generateDocument($documentType, $prompt);
        
        $formattedDocument = convertMarkdownToHtml($generatedDocument);
        
        try {
            $sql = $connect->prepare("INSERT INTO generated_documents (parent_id, user_id, document_type, prompt, generated_document) 
                    VALUES (?, ?, ?, ?, ?)");
            
            if ($sql->execute([$parentId, $userId, $documentType, $prompt, $generatedDocument])) {
                echo json_encode([
                    'message' => 'Document generated and saved successfully',
                    'generatedText' => $formattedDocument
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save document']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
?>