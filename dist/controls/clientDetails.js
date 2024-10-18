$(document).ready(function() {
    $('#viewKYC').on('click', function() {
        $.ajax({
            url: 'base/fetch_kyc_data', // PHP script to fetch KYC data
            method: 'GET',
            success: function(response) {
                $('#kycData').html(response);
                // $('#printOrCreatePDF').show();
            }
        });
    });
})


window.jsPDF = window.jspdf.jsPDF;

function generatePdf() {
    let jsPdf = new jsPDF();
    let tables = document.querySelectorAll('.kycData');
    let index = 0;

    function captureNextTable() {
        if (index < tables.length) {
            let table = tables[index];
            if (index !== 0) {
                jsPdf.addPage();
            }

            html2canvas(table, {
                backgroundColor: null, // Set background color to null for transparency
                logging: false, // Disable logging
                scale: 2, // Increase scale for better quality (optional)
                x: 20, // Add padding left
                y: 20, // Add padding top
                width: table.offsetWidth - 40, // Subtract padding from width
                height: table.offsetHeight - 40 // Subtract padding from height
            }).then(canvas => {
                let imgData = canvas.toDataURL('image/png');
                let imgProps = jsPdf.getImageProperties(imgData);
                let pdfWidth = jsPdf.internal.pageSize.getWidth();
                let pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                jsPdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);

                index++;
                captureNextTable();
            });
        } else {
            jsPdf.save('tables.pdf');
        }
    }

    captureNextTable();
}

document.getElementById('downloadPdf').addEventListener('click', generatePdf);


$('#caseDocuments').on('change', function() {
    var files = this.files;
    var filesList = $('#uploadedFilesList');
    filesList.empty();

    for (var i = 0; i < files.length; i++) {
        filesList.append('<p>' + files[i].name + '</p>');
    }
});


$(document).ready(function() {
    var tpin = document.getElementById("clientId").value;
    fetchCases(tpin);
    $('#addCaseForm').on('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: 'base/createCase',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend:function(){
                $("#submitCase").prop("disabled", true).html("Processing...");
            },
            success: function(response) {
                // Handle the successful response
                alert(response);
                $('#addCaseForm')[0].reset();
                $("#submitCase").prop("disabled", false).html("Submit Case");
                // fetchCases(tpin);
            },
            error: function(xhr, status, error) {
                // Handle the error response
                alert(error);
                $("#submitCase").prop("disabled", false).html("Submit Case");
            }
        });
    });
});


function printDiv(el){
    // $(".editable").css("border-bottom", "none");
    var restorepage = $('body').html();
    var printcontent = $('#' + el).clone();
    $('body').empty().html(printcontent);
    window.print();
    $('body').html(restorepage);

}