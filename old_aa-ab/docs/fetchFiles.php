<?php
	include '../../includes/db.php';

	$lawFirmId = $_SESSION['parent_id'];

	// Fetch folders
	$stmt = $connect->prepare("SELECT * FROM lawFirmFolders WHERE lawFirmId = ? ORDER BY folder_name ASC ");
	$stmt->execute([$lawFirmId]);
	$folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$stmt = $connect->prepare("SELECT * FROM lawFirmFiles WHERE lawFirmId = ? AND (folder_id IS NULL OR folder_id = 0) ORDER BY file_name ASC");
	$stmt->execute([$lawFirmId]);
	$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Fetch the count of files in each folder
	$stmt = $connect->prepare("
	    SELECT folder_id, COUNT(*) as file_count 
	    FROM lawFirmFiles 
	    WHERE lawFirmId = ? AND (folder_id IS NOT NULL AND folder_id != 0)
	    GROUP BY folder_id
	");
	$stmt->execute([$lawFirmId]);
	$fileCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Convert the file counts to an associative array with folder_id as the key
	$fileCountsAssoc = [];
	foreach ($fileCounts as $fileCount) {
	    $fileCountsAssoc[$fileCount['folder_id']] = $fileCount['file_count'];
	}

	/*/ Sort folders by creation time
	usort($folders, function($a, $b) {
	    return strtotime($b['created_at']) - strtotime($a['created_at']);
	});
	*/
	usort($folders, function($a, $b) {
	    return strcmp($a['folder_name'], $b['folder_name']);
	});

	/*/ Sort files by uploaded time
	usort($files, function($a, $b) {
	    return strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']);
	});
	*/

	usort($files, function($a, $b) {
	    return strcmp($a['file_name'], $b['file_name']);
	});

	// Display folders and files
	if (!empty($folders) || !empty($files)) {
	    // Display folders first
	    foreach ($folders as $folder) {
	        $username = fetchUserName($folder['uploaded_by']);
	        $encrypted_id = base64_encode($folder['id']);
	        // $fileCount = isset($fileCountsAssoc[$folder['id']]) ? $fileCountsAssoc[$folder['id']] : 0;
	        $fileCount = countFilesInFolder($folder['id'], $lawFirmId);
	        echo "<tr>
	                <td><i class='bi bi-folder2'></i> </td>
	                <td><a href='docs/file?doc={$encrypted_id}'><i class='bi bi-folder'></i> {$folder['folder_name']} ({$fileCount} files)</a></td>
	                <td>{$username} <small><em>(" . time_ago_check($folder['created_at']) . ")</em></small></td>
	                <td>
	                    <div class='dropdown'>
	                        <button class='btn btn-secondary btn-sm dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
	                            <i class='bi bi-three-dots-vertical'></i>
	                        </button>
	                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
	                            <li><button class='dropdown-item' onclick='deleteFolder(\"{$folder['id']}\")'><i class='bi bi-trash2'></i> Delete</button></li>
	                            <li><button class='dropdown-item' onclick='editFolder(\"{$folder['id']}\")'><i class='bi bi-pen'></i> Edit</button></li>
	                            <li><a class='dropdown-item' href='docs/file?doc={$encrypted_id}'><i class='bi bi-folder2-open'></i> Open</a></li>
	                        </ul>
	                    </div>
	                </td>
	              </tr>";
	    }

	    // Display files
	    foreach ($files as $file) {
	        $username = fetchUserName($file['uploaded_by']);
	        $time_at = time_ago_check($file['uploaded_at']);
	        $filename = ucwords($file['file_name']);
	        $hidePreview = in_array(pathinfo($filename, PATHINFO_EXTENSION), ['doc', 'docx']);
	        $downloadLink = $file['file_name'];
	        echo "<tr>
	                <td><input type='checkbox' class='file-checkbox' value='{$file['file_name']}'></td>
	                <td>{$filename}</td>
	                <td>{$username} <small><em>({$time_at})</em></small></td>
	                <td>
	                    <div class='dropdown'>
	                        <button class='btn btn-secondary btn-sm dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
	                            <i class='bi bi-three-dots-vertical'></i>
	                        </button>
	                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
	                            <li><a class='dropdown-item' href='docs/uploads/".$downloadLink."' target='_blank'><i class='bi bi-circle'></i>  Open</a></li>
	                            <li><button class='dropdown-item' onclick='deleteFile(\"{$file['file_name']}\")'><i class='bi bi-trash2'></i> Delete</button></li>";

						        if (!$hidePreview) {
						            echo "<li><button class='dropdown-item preview-file' data-file='{$file['file_name']}'><i class='bi bi-view-list'></i> Preview</button></li>";
						        }

						        echo "</ul>
	                	</div>
	            	</td>
	            </tr>";
	    }
	} else {
	    echo "<tr><td colspan='4'>No items found.</td></tr>";
	}
?>
