function displayExpenses() {
    $.ajax({
        type: 'GET',
        url: 'finance/fetchExpenses',
        success: function(response) {
            $('#expensesTable').html(response);
        }
    });

    $.ajax({
        type: 'GET',
        url: 'finance/fetchTotalExpenses',
        success: function(response) {
            $('#expensesTableTotal').html(response);
        }
    });
    displayExpensesData();
}

displayExpenses();
$(document).ready(function() {
    $('#expenditureForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'finance/createExpenditure',
            data: formData,
            beforeSend: function() {
                $("#submitBtn").prop('disabled', true).html('Processing...');
            },
            success: function(response) {
                $('#expenditureModal').modal('hide');
                alert(response);
                displayExpenses(); // Assuming this function displays the updated list of expenses
                $('#expenditureForm')[0].reset();
                $("#expense_id").val("");
                $("#submitBtn").prop('disabled', false).html('Record Expense');
            },
            error: function(xhr, status, error) {
                alert('An error occurred while processing your request.');
                $("#submitBtn").prop('disabled', false).html('Record Expense');
            }
        });
    });

});

$(document).on('click', '.editExpense', function() {
    var expenseId = $(this).data('id');
    
    $.ajax({
        type: 'POST',
        url: 'finance/fetchSelectedExpense',
        data: {expenseId: expenseId},
        dataType: 'json',
        success: function(response) {
            $('#currency').val(response.currency);

            $('#description').val(response.description);
            $('#amount').val(response.amount);
            $('#date').val(response.date_added);
            $('#expense_id').val(expenseId); // Set the expense_id in a hidden field for later use
            $('#expenseModal').modal('show');
        },
        error: function() {
            alert('An error occurred while fetching the expense data.');
        }
    });
});

$(document).on('click', '.deleteExpense', function() {
    var expenseId = $(this).data('id');
    
    if (confirm("Are you sure you want to delete this expense?")) {
        $.ajax({
            type: 'POST',
            url: 'finance/deleteExpense',
            data: {expenseId: expenseId},
            success: function(response) {
                alert(response);
                displayExpenses();
            },
            error: function() {
                alert('An error occurred while deleting the expense.');
            }
        });
    }
});


var categoriesBarChart = null;
var categoriesLineChart = null;

function displayExpensesData() {
    fetch('finance/expenseData')
        .then(response => response.json())
        .then(data => {
            if (!data || !data.monthlyExpenses) {
                throw new Error("Invalid data format");
            }

            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyExpenses = monthLabels.map(month => data.monthlyExpenses[month] || 0);

            var optionsCategoriesBar = {
                series: [{
                    name: 'Expenses',
                    data: monthlyExpenses
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
                    name: 'Expenses',
                    data: monthlyExpenses
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

            categoriesBarChart = new ApexCharts(document.querySelector("#expensesBarChart"), optionsCategoriesBar);
            categoriesLineChart = new ApexCharts(document.querySelector("#expensesLineChart"), optionsCategoriesLine);

            categoriesBarChart.render();
            categoriesLineChart.render();
        })
        .catch(error => {
            console.error('Error fetching or processing data:', error);
        });
}






















