<?php
    include "../../includes/db.php";

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $query = $connect->prepare("SELECT * FROM case_milestones WHERE id = ?");
        $query->execute([$id]);
        
        $milestone = $query->fetch(PDO::FETCH_ASSOC);
        if ($milestone) {
            $milestone['milestoneTitle'] = htmlspecialchars(decrypt($milestone['milestoneTitle']), ENT_QUOTES, 'UTF-8');
            $milestone['milestoneDescription'] = htmlspecialchars_decode(decrypt($milestone['milestoneDescription']), ENT_QUOTES);

            echo json_encode($milestone);
        } else {
            echo json_encode([]);
        }
    }
?>
