<?php 
	include '../../includes/db.php';

	if($_SERVER['REQUEST_METHOD'] === "POST"){
		$folder_id = $_POST['folder_id'];
	    $folderKey = base64_decode($folder_id);

		// Fetch folder details
		$stmt = $connect->prepare("SELECT folder_name FROM lawFirmFolders WHERE id = ?");
		$stmt->execute([$folderKey]);
		$folder = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$folder) {
		    echo "<p>Folder not found.</p>";
		    exit;
		}

		// Fetch files in the specified folder
		$stmt = $connect->prepare("SELECT file_name, uploaded_at, uploaded_by FROM lawFirmFiles WHERE folder_id = ?");
		$stmt->execute([$folderKey]);
		$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($files as $file): ?>
            <tr>
                <td><i class='bi bi-file'></i> <?php echo htmlspecialchars($file['file_name']) ?></td>
                <td><?php echo htmlspecialchars(fetchUserName($file['uploaded_by'])) ?></td>
                <td><?php echo htmlspecialchars(time_ago_check($file['uploaded_at'])) ?></td>
                <td>
                    <div class='dropdown'>
                        <button class='btn btn-secondary btn-sm dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                            <i class='bi bi-three-dots-vertical'></i>
                        </button>
                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                            <li><a class='dropdown-item' href='docs/uploads/<?php echo $file['file_name'] ?>' target='_blank'><i class='bi bi-circle'></i> Open</a></li>
                            <li><button class='dropdown-item' onclick='deleteFolderFile("<?php echo htmlspecialchars($file['file_name']) ?>")'><i class='bi bi-trash2'></i> Delete</button></li>
                            <?php if (!in_array(pathinfo($file['file_name'], PATHINFO_EXTENSION), ['doc', 'docx'])): ?>
                                <li><button class='dropdown-item preview-file' data-file='<?= htmlspecialchars($file['file_name']) ?>'><i class='bi bi-view-list'></i> Preview</button></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach;

	}
?>