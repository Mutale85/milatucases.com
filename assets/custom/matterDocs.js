$(document).on("click", ".displayDocument", function(e){
	e.preventDefault();
    var caseId = $(this).data("case-id");
    var caseNo = $(this).data('caseNo');
    $.ajax({
        url:"cases/fetchCaseDocuments",
        method:"POST",
        data:{caseId:caseId, caseNo:caseNo},
        success:function(response){
            $("#documentModal").modal("show");
            $("#showDocuments").html(response);
        }
    })
})
$(document).on("submit", "#uploadDocumentsForm", function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: "cases/uploadCaseDocuments", // A separate PHP file to handle the upload
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend:function(){
            $("#uploadMoreBtn").html("Processing...").prop("disabled", true);
        },
        success: function(response) {
            // Reload the documents
            var caseId = $("input[name='newcaseId']").val();
            var caseNo = $("input[name='newcaseNo']").val();
            $.ajax({
                url: "cases/fetchCaseDocuments",
                method: "POST",
                data: { caseId: caseId, caseNo: caseNo },
                success: function(response) {
                    $("#showDocuments").html(response);
                }
            });
            $("#uploadMoreBtn").html("Upload").prop("disabled", false);
        }
    });
});

function callDocument(caseId, caseNo){
    $.ajax({
        url: "cases/fetchCaseDocuments",
        method: "POST",
        data: { caseId: caseId, caseNo: caseNo },
        success: function(response) {
            $("#showDocuments").html(response);
        }
    });
}

$(document).on("click", ".remove-file", function() {
    var docId = $(this).data("id");
    if (confirm("You wish to remove this document")) {
        $.ajax({
            url: "cases/removeCaseDocument", // A separate PHP file to handle the removal
            method: "POST",
            data: { docId: docId },
            success: function(response) {
                // Reload the documents
                var caseId = $("input[name='newcaseId']").val();
                var caseNo = $("input[name='newcaseNo']").val();
                $.ajax({
                    url: "cases/fetchCaseDocuments",
                    method: "POST",
                    data: { caseId: caseId, caseNo: caseNo },
                    success: function(response) {
                        $("#showDocuments").html(response);
                    }
                });
            }
        });
    }else{
        return false;
    }
});