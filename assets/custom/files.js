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

$('#churchForm').on('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting the default way

    let formData = new FormData(this);

    $.ajax({
        url: 'files/createFiles', // Replace with your PHP script URL
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend:function () {
            $('#fileBtn').prop("disabled", true).html("Processing...");
        },
        success: function(response) {
            alert('Files uploaded successfully!');
            fetchFiles();
            // Handle success response
            $('#fileBtn').prop("disabled", false).html("Save Files");
        },
        error: function(xhr, status, error) {
            alert('An error occurred while uploading the files.');
            // Handle error response
        }
    });
});


fetchFiles();

function fetchFiles() {
    $.ajax({
        url: 'files/fetchFiles',
        type: 'GET',
        success: function(response) {
            $('#listedFiles').html(response);
        },
        error: function() {
            console.log('Error fetching files.');
        }
    });

    fetchandLoadFolder();
}


function deleteFile(fileName) {
    if (confirm('Are you sure you want to delete this file?')) {
        $.ajax({
            url: 'files/deleteFile',
            type: 'POST',
            data: { file_name: fileName },
            success: function(response) {
                fetchFiles(); // Refresh the file list
            },
            error: function() {
                console.log('Error deleting file.');
            }
        });
    }
}

// Submit form to create folder
$('#createFolderForm').submit(function(e) {
    e.preventDefault();
    var folderName = $('#folderName').val();
    $.ajax({
        url: 'files/createFolder',
        type: 'POST',
        data: { folderName: folderName },
        beforeSend:function () {
            $('#folderBtn').prop("disabled", true).html("Processing...");
        },
        success: function(response) {
            // Update folder list or show success message
            alert(response);
         
            fetchFiles();

            $('#createFolderModal').modal('hide');
            $('#folderName').val('');
            $('#folderBtn').prop("disabled", false).html("Create");
        },
        error: function() {
            console.log('Error creating folder.');
        }
    });
});


function deleteFolder(folderId) {
    if (confirm('Are you sure you want to delete this folder?')) {
        $.ajax({
            url: 'files/deleteFolder',
            type: 'POST',
            data: { folder_id: folderId },
            success: function(response) {
                alert(response);
                // Reload the page or update the folder list
                fetchFiles();
            },
            error: function(xhr, status, error) {
                console.error('Error deleting folder:', error);
            }
        });
    }else{
        return false;
    }
}


$(document).on('click', '.preview-file', function(e) {
    e.preventDefault();
    var fileName = $(this).data('file');
    if (!fileName) {
        console.error('File name is undefined');
        return;
    }

    var fileExtension = fileName.split('.').pop().toLowerCase();
    var filePath = 'uploads/files/' + fileName;
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


$(document).ready(function() {
    const $checkboxes = $('.file-checkbox');
    const $moveButton = $('#moveToFolderButton');
    const $selectedFilesInput = $('#selectedFiles');
    const $folderSelect = $('#folderSelect');
    const $moveFilesForm = $('#moveFilesForm');

    $(document).on('change', '.file-checkbox', function() {
        const $checkedCheckboxes = $('.file-checkbox:checked');
        if ($checkedCheckboxes.length > 0) {
            $moveButton.show();
        } else {
            $moveButton.hide();
        }
    });

    $moveButton.on('click', function(event) {
        event.preventDefault();
        const selectedFiles = [];
        const $checkedCheckboxes = $('.file-checkbox:checked');
        $checkedCheckboxes.each(function() {
            selectedFiles.push($(this).val());
        });
        $selectedFilesInput.val(JSON.stringify(selectedFiles));
        const selectFolderModal = new bootstrap.Modal($('#selectFolderModal')[0]);
        selectFolderModal.show();
    });

    $("#moveFilesForm").on('submit', function(event) {
        event.preventDefault();
        const folderId = $folderSelect.val();
        const selectedFiles = $selectedFilesInput.val();

        $.ajax({
            url: 'files/moveFiles',
            type: 'POST',
            data: JSON.stringify({
                folderId: folderId,
                files: JSON.parse(selectedFiles)
            }),
            contentType: 'application/json',
            success: function(data) {
                alert(data);
                fetchFiles();
                
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });
});

$("#moveToFolderButton").click(function(){
    fetchandLoadFolder();
})

function fetchandLoadFolder(){
    var getFolders = 'getFolders';
    $.ajax({
        url:'files/fetchFolders',
        method:"POST",
        data:{getFolders:getFolders},
        success:function(response){
            $("#folderSelect").html(response);
        }
    })
}









