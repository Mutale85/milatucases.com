<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include '../../includes/db.php';

    if (isset($_POST['docId'])) {
        $docId = $_POST['docId'];
        
        try {
            $sql = $connect->prepare("SELECT generated_document FROM generated_documents WHERE id = ?");
            $sql->execute([$docId]);
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'document' => nl2br(convertMarkdownToHtml(htmlspecialchars($result['generated_document'])))
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Document not found'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request'
        ]);
    }
?>