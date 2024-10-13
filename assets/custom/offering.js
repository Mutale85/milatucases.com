$(document).ready(function() {
    $('#offeringForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'finances/createOffering',
            data: formData,
            beforeSend:function(){
                $("#submitBtn").html('Processing...');
            },
            success: function(response) {
                $('#offeringModal').modal('hide');
                displayOfferings();
                
                $('#offeringForm')[0].reset();
                $("#submitBtn").html("Submit Offering");
            },
            error: function() {
                alert('An error occurred while processing your request.');
                $("#submitBtn").html("Submit Offering");
            }
        });
    });

    function displayOfferings() {
        $.ajax({
            type: 'GET',
            url: 'finances/fetchOfferings',
            success: function(response) {
                $('#offeringTable').html(response);
            }
        });

        $.ajax({
            type: 'GET',
            url: 'finances/fetchOfferingTotal',
            success: function(response) {
                $('#offeringTableTotal').html(response);
            }
        });
        displayOfferingData();
    }

    displayOfferings();
});

var categoriesBarChart = null;
var categoriesLineChart = null;

function displayOfferingData() {
    fetch('finances/offeringData')
        .then(response => response.json())
        .then(data => {
            if (!data || !data.monthlyOffering) {
                throw new Error("Invalid data format");
            }

            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyOffering = monthLabels.map(month => data.monthlyOffering[month] || 0);

            var optionsCategoriesBar = {
                series: [{
                    name: 'Offering',
                    data: monthlyOffering
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
                },
                grid: {
                    padding: {
                        left: 20,
                        right: 20
                    }
                }
            };

            var optionsCategoriesLine = {
                series: [{
                    name: 'Offering',
                    data: monthlyOffering
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
                },
                grid: {
                    padding: {
                        left: 20,
                        right: 20
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

            categoriesBarChart = new ApexCharts(document.querySelector("#offeringBarChart"), optionsCategoriesBar);
            categoriesLineChart = new ApexCharts(document.querySelector("#offeringLineChart"), optionsCategoriesLine);

            categoriesBarChart.render();
            categoriesLineChart.render();
        })
        .catch(error => {
            console.error('Error fetching or processing data:', error);
        });
}




