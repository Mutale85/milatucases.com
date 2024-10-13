document.addEventListener("DOMContentLoaded", function () {
	    const filesInput = document.getElementById("files");
	    const fileList = document.getElementById("fileList");

	    filesInput.addEventListener("change", updateFileList);

	    function updateFileList() {
	        fileList.innerHTML = "";
	        const files = Array.from(filesInput.files);
	        
	        files.forEach((file, index) => {
	            const listItem = document.createElement("li");
	            listItem.className = "list-group-item d-flex justify-content-between align-items-center";

	            const fileName = document.createElement("span");
	            fileName.textContent = `${file.name} (${(file.size / 1024).toFixed(2)} KB)`;

	            const removeButton = document.createElement("button");
	            removeButton.className = "btn btn-danger btn-sm";
	            removeButton.textContent = "Remove";
	            removeButton.addEventListener("click", function () {
	                files.splice(index, 1);
	                updateInputFiles(files);
	                updateFileList();
	            });

	            listItem.appendChild(fileName);
	            listItem.appendChild(removeButton);
	            fileList.appendChild(listItem);
	        });
	    }

	    function updateInputFiles(files) {
	        const dataTransfer = new DataTransfer();
	        files.forEach(file => dataTransfer.items.add(file));
	        filesInput.files = dataTransfer.files;
	    }
	});

	$('#folderForm').on('submit', function(event) {
	    event.preventDefault(); 
	    let formData = new FormData(this);
	    $.ajax({
	        url: 'cases/process/addFilesToFolder', 
	        type: 'POST',
	        data: formData,
	        contentType: false,
	        processData: false,
	        beforeSend:function () {
	            $('#fileBtn').prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
	        },
	        success: function(response) {
	          sweetSuccess('Files uploaded successfully!');
	          folderListedFiles();
	          $('#fileBtn').prop("disabled", false).html("Save Files");
	        },
	        error: function(xhr, status, error) {
	          sweetError('An error occurred while uploading the files.');
	        }
	    });
	});


	folderListedFiles();

	function folderListedFiles() {
	    const folderId = $('#folderId').val();
	    
	    $.ajax({
	        url: 'cases/process/folderListedFiles',
	        type: 'POST',
	        data: { folder_id: folderId },
	        success: function(response) {
	            $('#folderListedFiles').html(response);
	        },
	        error: function() {
	            sweetError('Error fetching files.');
	        }
	    });

	}

	$(document).on("click",".deleteCaseFolderFile", function(e){
		e.preventDefault();
			const folderId = $('#folderId').val();
	    const documentId = $(this).data('id');
	    const file_name = $(this).attr('id');
	    
	    if (confirm('Are you sure you want to delete this file?')) {
	        $.ajax({
	            url: 'cases/process/deleteFolderFile',
	            type: 'POST',
	            data: { file_name: file_name, folder_id: folderId, documentId:documentId },
	            success: function(response) {
	              folderListedFiles(); 
	            },
	            error: function() {
	              sweetError('Error deleting file.');
	            }
	        });
	    }
	})

	$(document).on('click', '.previewFile', function(e) {
	    e.preventDefault();
	    var fileName = $(this).data('file');
	    if (!fileName) {
	        console.error('File name is undefined');
	        return;
	    }

	    var fileExtension = fileName.split('.').pop().toLowerCase();
	    var filePath = 'cases/' + fileName;
	    var fileContent = '';

	    if (fileExtension === 'pdf') {
	        fileContent = '<embed src="' + filePath + '" type="application/pdf" width="100%" height="600px" />';
	    } else if (['jpg', 'jpeg', 'png', 'gif'].indexOf(fileExtension) !== -1) {
	        fileContent = '<img src="' + filePath + '" class="img-fluid" />';
	    } else if (['doc', 'docx'].indexOf(fileExtension) !== -1) {
	        var encodedUrl = encodeURIComponent(window.location.origin + '/' + filePath);
	        fileContent = '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' + encodedUrl + '" width="100%" height="600px"></iframe>';
	    } else {
	        fileContent = '<p>File type not supported for preview.</p>';
	    }

	    $('#filePreviewContent').html(fileContent);
	    $('#filePreviewModal').modal('show');
	});