<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    ob_start();
    header('Content-Type: application/json');

    try {
        require_once '../../../includes/db.php';
        require_once '../../../includes/conf.php';

        // OpenAI API configuration
        $openai_api_key = SECRET_KEY;

        // Function to generate document content using OpenAI
        function generateDocumentContent($prompt) {
            global $openai_api_key;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that generates legal documents.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $openai_api_key
            ]);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception('cURL Error: ' . curl_error($ch));
            }
            curl_close($ch);

            $decoded = json_decode($response, true);
            if (isset($decoded['choices'][0]['message']['content'])) {
                return $decoded['choices'][0]['message']['content'];
            } else {
                throw new Exception('Failed to generate content: ' . print_r($decoded, true));
            }
        }

        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $docType = $_POST['docType'] ?? '';
            $customDocType = $_POST['customDocType'] ?? '';
            $clientId = $_POST['clientId'] ?? '';
            $startDate = $_POST['startDate'] ?? '';
            $description = $_POST['description'] ?? '';
            $lawFirmId = $_SESSION['parentId'] ?? '';
            $userId = $_SESSION['user_id'] ?? '';

            if (empty($docType) || empty($clientId) || empty($startDate) || empty($description)) {
                throw new Exception('Missing required form data');
            }

            $clientNames = getClientNameById($clientId, $lawFirmId);

            // Generate document content
            $prompt = "Generate a " . ($docType === 'custom' ? $customDocType : $docType) . " for " . $clientNames . " starting from " . $startDate . ". Include the following description: " . $description;
            
            $content = generateDocumentContent($prompt);

            // Preserve line breaks and break content into chunks
            $chunks = preg_split('/(\\n)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Return chunks as JSON
            echo json_encode(['success' => true, 'chunks' => $chunks]);
        } else {
            throw new Exception('Invalid request method');
        }
    } catch (Exception $e) {
        error_log('Error in AI document generation: ' . $e->getMessage());        
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    ob_end_flush();
//SHA256:xa806XRDk5whSuG5G6o8GuuL7kjPFHi4LkzYDLy1XFw mutamuls@gmail.com
//ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMKGimG0LGvhUO+vr0O9AdtwLVqNng0PJyTPAPNgYEtV mutamuls@gmail.com
