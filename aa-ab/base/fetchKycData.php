<?php
	include '../../includes/db.php';
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		try {
			// Fetch data from corporatePart1
		    $stmt1 = $connect->query("SELECT * FROM corporatePart1 WHERE lawFirmId = 'specified_law_firm_id'");
		    $corporatePart1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

		    // Fetch data from corporatePart2
		    $stmt2 = $connect->query("SELECT * FROM corporatePart2 WHERE lawFirmId = 'specified_law_firm_id'");
		    $corporatePart2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

		    // Fetch data from corporatePart3
		    $stmt3 = $connect->query("SELECT * FROM corporatePart3 WHERE lawFirmId = 'specified_law_firm_id'");
		    $corporatePart3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

		    // Fetch data from corporatePart4
		    $stmt4 = $connect->query("SELECT * FROM corporatePart4 WHERE lawFirmId = 'specified_law_firm_id'");
		    $corporatePart4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);

		    // Generate HTML content
		    $html = "<h3>Corporate Part 1</h3><table class='table'><thead><tr><th>ID</th><th>Client Name</th><th>Date of Incorporation</th></tr></thead><tbody>";
		    foreach ($corporatePart1 as $row) {
		        $html .= "<tr><td>{$row['id']}</td><td>{$row['client_name']}</td><td>{$row['date_of_incorporation']}</td></tr>";
		    }
		    $html .= "</tbody></table>";

		    $html .= "<h3>Corporate Part 2</h3><table class='table'><thead><tr><th>ID</th><th>Full Name</th><th>Gender</th></tr></thead><tbody>";
		    foreach ($corporatePart2 as $row) {
		        $html .= "<tr><td>{$row['id']}</td><td>{$row['full_name']}</td><td>{$row['gender']}</td></tr>";
		    }
		    $html .= "</tbody></table>";

		    $html .= "<h3>Corporate Part 3</h3><table class='table'><thead><tr><th>ID</th><th>Name</th><th>Profession</th></tr></thead><tbody>";
		    foreach ($corporatePart3 as $row) {
		        $html .= "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['profession']}</td></tr>";
		    }
		    $html .= "</tbody></table>";

		    $html .= "<h3>Corporate Part 4</h3><table class='table'><thead><tr><th>ID</th><th>Representative Name</th><th>Compliance Officer Name</th></tr></thead><tbody>";
		    foreach ($corporatePart4 as $row) {
		        $html .= "<tr><td>{$row['id']}</td><td>{$row['representative_name']}</td><td>{$row['compliance_officer_name']}</td></tr>";
		    }
		    $html .= "</tbody></table>";

		    echo $html;

		} catch(PDOException $e) {
		    echo "Error: " . $e->getMessage();
		}

		$connect = null;

	}
?>