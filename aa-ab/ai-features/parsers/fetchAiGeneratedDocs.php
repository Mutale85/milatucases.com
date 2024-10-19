<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include '../../../includes/db.php';

    if (isset($_POST['fetchGeneratedDocs'])) {
        // Check if session variables are set
        if (!isset($_SESSION['parent_id']) || !isset($_SESSION['user_id'])) {
            echo "Error: Session variables not set.";
            exit;
        }
        
        $parentId = $_SESSION['parent_id'];
        $userId = $_SESSION['user_id'];
        
        try {
            $sql = $connect->prepare("SELECT * FROM ai_generated_documents WHERE lawFirmId = ? ORDER BY created_at DESC");
            $sql->execute([$parentId]);
            $documents = $sql->fetchAll(PDO::FETCH_ASSOC);
            if (count($documents) > 0) {
                echo "<ul class='list-group'>";
                foreach ($documents as $doc) {
                    $title = createTitle($doc['description'], $doc['doc_type']);
                    $createdAt = date('M d, Y', strtotime($doc['created_at']));
                    echo "
                    <li class='list-group-item d-flex justify-content-between align-items-center'>
                        <div>
                            <a href='#' class='showDocument' data-doc-id='{$doc['id']}'>{$title}</a>
                            <small class='text-muted ml-2'>{$createdAt}</small>
                        </div>
                        <button class='btn btn-sm btn-outline-danger deleteDocument' data-doc-id='{$doc['id']}'>
                            <i class='bi bi-trash'></i>
                        </button>
                    </li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No documents generated yet.</p>";
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Fetch request not received.<br>";
    }
?>