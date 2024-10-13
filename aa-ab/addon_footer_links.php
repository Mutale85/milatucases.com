<!-- build:js assets/vendor/js/core.js -->
<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="../assets/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
<!-- Main JS -->
<script src="../assets/js/main.js"></script>
<!-- Page JS -->
<script src="../assets/js/dashboards-analytics.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Trumbowyg JavaScript -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/trumbowyg.min.js"></script> -->
<!-- Data Tables -->
<script type="text/javascript" src="../assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../assets/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../assets/js/jszip.min.js"></script>
<script type="text/javascript" src="../assets/js/pdfmake.min.js"></script>
<script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
<script type="text/javascript" src="../assets/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="../assets/js/buttons.print.min.js"></script>

<script src="../assets/js/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="../assets/custom/timeTrack.js"></script>
<script type="text/javascript" src="../assets/custom/timeme2.js"></script>

<!-- New Data Tables -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<!-- DataTables Core -->
<script src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script>

<!-- DataTables SearchBuilder -->
<script src="https://cdn.datatables.net/searchbuilder/1.8.0/js/dataTables.searchBuilder.js"></script>
<script src="https://cdn.datatables.net/searchbuilder/1.8.0/js/searchBuilder.dataTables.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>

<!-- DataTables DateTime -->
<script src="https://cdn.datatables.net/datetime/1.5.3/js/dataTables.dateTime.min.js"></script>
<!-- Toaster -->

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>    

<script>
    $(document).ready(function() {
        $('.datatable-class').each(function() {
            new DataTable(this, {
                layout: {
                    top1: 'searchBuilder'
                },
                
                // Enable individual column searching
                initComplete: function () {
                    var api = this.api();
                    api.columns().every(function () {
                        var column = this;
                        var select = $('<select><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
         
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
         
                        column.data().unique().sort().each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>')
                        });
                    });
                }
            });
        });
    });
</script>

<script>
    function fetchCurrencyData(){
        const key = "41574dba141438223442dee975e0606d0cd00731";
        const url = `https://api.getgeoapi.com/v2/ip/check?api_key=${key}&format=json`;

        fetch(url)
          .then(response => response.json())
          .then(data => {
            // Check if the status is success
            if (data.status === "success") {
              // Prepare data to send to PHP
              const country = data.country.name; // Accessing the country name
              const currency = data.currency.code; // Accessing the currency code

              // Send data to PHP
              fetch('settings/processCurrencyData', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify({ country, currency }),
              })
              .then(response => response.json())
              .then(result => console.log(result))
              .catch(error => console.error('Error sending data to PHP:', error));
            } else {
              console.error('Error fetching data from GeoAPI:', data);
            }
        })
        .catch(error => console.error('Error fetching data from GeoAPI:', error));
    }
</script>