$(document).ready(function() {
    $('#incomeForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'finance/createIncome',
            data: formData,
            beforeSend: function() {
                $("#submitBtn").prop('disabled', true).html('Processing...');
            },
            success: function(response) {
                $('#incomeModal').modal('hide');
                alert(response);
                $("#income_id").val("");
                displayIncomes();
                $('#incomeForm')[0].reset();
                $("#submitBtn").prop('disabled', false).html('Record Income');
            },
            error: function(xhr, status, error) {
                alert('An error occurred while processing your request.');
                $("#submitBtn").prop('disabled', false).html('Record Income');
            }
        });
    });

    function displayIncomes() {
        $.ajax({
            type: 'GET',
            url: 'finance/fetchIncomes',
            success: function(response) {
                $('#incomesTable').html(response);
            }
        });

        $.ajax({
            type: 'GET',
            url: 'finance/fetchTotalIncomes',
            success: function(response) {
                $('#incomesTableTotal').html(response);
            }
        });
        displayIncomesData();
    }

    displayIncomes();
});

$(document).on('click', '.editIncome', function() {
    var incomeId = $(this).data('id');
    
    $.ajax({
        type: 'POST',
        url: 'finance/fetchSelectedIncome',
        data: {incomeId: incomeId},
        dataType: 'json',
        success: function(response) {
            $('#description').val(response.description);
            $('#amount').val(response.amount);
            $('#date').val(response.income_date);
            $('#income_id').val(incomeId); // Set the income_id in a hidden field for later use
            $('#incomeModal').modal('show');
        },
        error: function() {
            alert('An error occurred while fetching the income data.');
        }
    });
});

$(document).on('click', '.deleteIncome', function() {
    var incomeId = $(this).data('id');
    
    if (confirm("Are you sure you want to delete this income?")) {
        $.ajax({
            type: 'POST',
            url: 'finance/deleteIncome',
            data: {incomeId: incomeId},
            success: function(response) {
                alert(response);
                displayIncomes();
            },
            error: function() {
                alert('An error occurred while deleting the income.');
            }
        });
    }
});


var categoriesBarChart = null;
var categoriesLineChart = null;

function displayIncomesData() {
    fetch('finance/incomeData')
        .then(response => response.json())
        .then(data => {
            if (!data || !data.monthlyIncomes) {
                throw new Error("Invalid data format");
            }

            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyIncomes = monthLabels.map(month => data.monthlyIncomes[month] || 0);

            var optionsCategoriesBar = {
                series: [{
                    name: 'Incomes',
                    data: monthlyIncomes
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        endingShape: 'rounded',
                    },
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -10,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    },
                    background: {
                        enabled: true,
                        foreColor: '#fff',
                        padding: 4,
                        borderRadius: 2,
                        borderWidth: 1,
                        borderColor: '#304758',
                        opacity: 0.9,
                    },
                },
                xaxis: {
                    categories: monthLabels,
                    title: {
                        text: 'Month'
                    },
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Amount'
                    },
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return " " + val + "k"
                        }
                    }
                }
            };

            var optionsCategoriesLine = {
                series: [{
                    name: 'Incomes',
                    data: monthlyIncomes
                }],
                chart: {
                    type: 'line',
                    height: 350
                },
                xaxis: {
                    categories: monthLabels,
                    title: {
                        text: 'Month'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Amount'
                    }
                },
                colors: ['#FF5733'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 5,
                    colors: ['#FF5733'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                }
            };

            // Destroy existing charts if they exist
            if (categoriesBarChart) {
                categoriesBarChart.destroy();
            }
            if (categoriesLineChart) {
                categoriesLineChart.destroy();
            }

            categoriesBarChart = new ApexCharts(document.querySelector("#incomesBarChart"), optionsCategoriesBar);
            categoriesLineChart = new ApexCharts(document.querySelector("#incomesLineChart"), optionsCategoriesLine);

            categoriesBarChart.render();
            categoriesLineChart.render();
        })
        .catch(error => {
            console.error('Error fetching or processing data:', error);
        });
}
