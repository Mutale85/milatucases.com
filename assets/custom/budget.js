$(document).ready(function() {
    // Global variables to store the chart instances
    var categoriesBarChart = null;
    var categoriesDonutChart = null;

    $('#budgetForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'finances/createBudget',
            data: formData,
            beforeSend: function() {
                $("#submitBtn").html('Processing...');
            },
            success: function(response) {
                $('#budgetsModal').modal('hide');
                alert(response);
                displayBudgets();
                $('#budgetForm')[0].reset();
                $("#submitBtn").html("Create Budget");
            },
            error: function() {
                alert('An error occurred while processing your request.');
                $("#submitBtn").html("Create Budget");
            }
        });
    });

    function displayBudgets() {
        $.ajax({
            type: 'GET',
            url: 'finances/fetchBudget',
            success: function(response) {
                $('#budgetTable').html(response);
            }
        });

        $.ajax({
            type: 'GET',
            url: 'finances/fetchBudgetsTotal',
            success: function(response) {
                $('#budgetTableTotal').html(response);
            }
        });

        budgetGraphs();
    }

    function budgetGraphs() {
        fetch('finances/budgetData')
            .then(response => response.json())
            .then(data => {
                const categoryLabels = Object.keys(data.categories);
                const categorySeries = Object.values(data.categories);

                var optionsCategoriesBar = {
                    series: [{
                        name: 'Categories',
                        data: categorySeries
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                        },
                    },
                    xaxis: {
                        categories: categoryLabels,
                        title: {
                            text: 'Category'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Amount'
                        }
                    }
                };

                var optionsCategoriesDonut = {
                    series: categorySeries,
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    labels: categoryLabels,
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                // Destroy existing charts if they exist
                if (categoriesBarChart) {
                    categoriesBarChart.destroy();
                }
                if (categoriesDonutChart) {
                    categoriesDonutChart.destroy();
                }

                categoriesBarChart = new ApexCharts(document.querySelector("#categoriesBarChart"), optionsCategoriesBar);
                categoriesDonutChart = new ApexCharts(document.querySelector("#categoriesDonutChart"), optionsCategoriesDonut);

                categoriesBarChart.render();
                categoriesDonutChart.render();
            });
    }

    displayBudgets();
});


$(document).ready(function() {
    // Edit budget button click event
    $(document).on('click', '.editBudget', function() {
        var budgetId = $(this).data('id');
        // Populate the form with the budget details
        $.ajax({
            type: 'POST',
            url: 'finances/fetchSelecteBudget',
            data: { budget_id: budgetId },
            dataType: 'json',
            success: function(response) {
                $('#editBudgetId').val(response.id);
                $('#category').val(response.category);
                $('#amount').val(response.amount);
                $('#year').val(response.year);
                $('#budget_id').val(budgetId);
                $('#confirmation').prop('checked', true);

                // Show the modal
                $('#budgetsModal').modal('show');
            },
            error: function() {
                alert('An error occurred while fetching budget details.');
            }
        });
    });

    // Delete budget button click event
    $(document).on('click', '.deleteBudget', function() {
        var budgetId = $(this).data('id');
        if (confirm('Are you sure you want to delete this budget?')) {
            $.ajax({
                type: 'POST',
                url: 'finances/deleteBudget',
                data: { budget_id: budgetId },
                success: function(response) {
                    // Refresh the budget table or perform any other necessary actions
                    displayBudgets();
                },
                error: function() {
                    alert('An error occurred while deleting the budget.');
                }
            });
        }
    });
});



