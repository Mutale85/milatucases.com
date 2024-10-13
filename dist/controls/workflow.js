/*
$(document).ready(function() {
    var timers = {};

    // Load timer state and textarea content from local storage
    loadTimers();
    loadTextareaContent();

    // Listen for storage events to synchronize timers across tabs
    window.addEventListener('storage', function(event) {
        if (event.key === 'timers') {
            loadTimers();
        } else if (event.key === 'taskDescription') {
            loadTextareaContent();
        }
    });

    // Capture start timer button click event
    $('.start-timer').click(function() {
        var caseId = $(this).data('caseid');
        var caseDbId = $(this).data('id');
        var hourlyRate = $(this).data('hourlyrate');
        var currency = $(this).data('currency');
        var clientId = $(this).data('client_id');

        // Fill modal hidden inputs
        $('#modalCaseId').val(caseId);
        $('#modalCaseDbId').val(caseDbId);
        $('#modalHourlyRate').val(hourlyRate);
        $('#modalCurrency').val(currency);
        $('#clientId').val(clientId);

        // Show the modal
        $('#timerModal').modal('show');
    });

    // Handle form submission and start timer
    $('#timerForm').submit(function(event) {
        event.preventDefault();

        var caseDbId = $('#modalCaseDbId').val();

        // Start the timer
        startTimer(caseDbId);

        // Close the modal
        $('#timerModal').modal('hide');
    });

    function startTimer(caseDbId) {
        if (timers[caseDbId]) {
            clearInterval(timers[caseDbId].interval);
        }

        timers[caseDbId] = {
            startTime: new Date().getTime(),
            pausedTime: 0
        };

        saveTimers();

        $('#startButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();
        $('#continueButton-' + caseDbId).hide();
        $('#stopButton-' + caseDbId).show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            saveTimers();
        }, 1000);
    }

    function updateTimerDisplay(caseDbId) {
        var currentTime = new Date().getTime();
        var elapsedTime = currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime;
        var seconds = Math.floor(elapsedTime / 1000);
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        var formattedTime = hours.toString().padStart(2, '0') + ':' + 
                            minutes.toString().padStart(2, '0') + ':' + 
                            secs.toString().padStart(2, '0');
        $('#timerDisplay-' + caseDbId).text(formattedTime);
    }

    // Handle pause timer
    $('.pause-timer').click(function() {
        var caseDbId = $(this).data('id');

        clearInterval(timers[caseDbId].interval);
        timers[caseDbId].pauseStartTime = new Date().getTime();
        $('#pauseButton-' + caseDbId).hide();
        $('#continueButton-' + caseDbId).show();

        saveTimers();
    });

    // Handle continue timer
    $('.continue-timer').click(function() {
        var caseDbId = $(this).data('id');

        var pauseDuration = new Date().getTime() - timers[caseDbId].pauseStartTime;
        timers[caseDbId].pausedTime += pauseDuration;
        delete timers[caseDbId].pauseStartTime;

        $('#continueButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            saveTimers();
        }, 1000);
    });

    // Handle stop timer
    $('.stop-timer').click(function() {

        var caseDbId = $(this).data('id');
        var caseId = $(this).data('caseid');
        var lawFirmId = $('#lawFirmId').val();
        var clientId = $(this).data('client_id');
        var hourlyRate = $(this).data('hourlyrate');
        var taskDescription = $('#taskDescription').val();
        var currency = $(this).data('currency');
        if(confirm("You are about to end the works and record the time in the fee note!")){

            // $("#timerModal").modal("show");

            clearInterval(timers[caseDbId].interval);

            var currentTime = new Date().getTime();
            var timeSpent = Math.floor((currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime) / 1000);
            var totalAmount = (timeSpent / 3600) * hourlyRate;

            postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId);

            delete timers[caseDbId];
            saveTimers();
            $('#startButton-' + caseDbId).show();
            $('#pauseButton-' + caseDbId).hide();
            $('#continueButton-' + caseDbId).hide();
            $('#stopButton-' + caseDbId).hide();
            $('#timerDisplay-' + caseDbId).text('00:00:00');
        }else{
            return false;
        }
    });

    function postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId) {
        $.ajax({
            url: 'cases/createTimeData',
            method: 'POST',
            data: {
                caseDbId:caseDbId,
                caseId: caseId,
                currency:currency,
                hourlyRate: hourlyRate,
                timeSpent: timeSpent,
                totalAmount: totalAmount,
                taskDescription: taskDescription,
                lawFirmId:lawFirmId, 
                clientId:clientId
            },
            success: function(response) {
                console.log(response);
                alert(response);
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
                alert('Failed to save timer data.');
            }
        });
    }

    function saveTimers() {
        localStorage.setItem('timers', JSON.stringify(timers));
        // Trigger storage event manually for current tab
        window.dispatchEvent(new Event('storage'));
    }

    function loadTimers() {
        var savedTimers = localStorage.getItem('timers');
        if (savedTimers) {
            timers = JSON.parse(savedTimers);
            Object.keys(timers).forEach(function(caseDbId) {
                var savedTimer = timers[caseDbId];
                savedTimer.interval = setInterval(function() {
                    updateTimerDisplay(caseDbId);
                    saveTimers();
                }, 1000);
                $('#startButton-' + caseDbId).hide();
                $('#pauseButton-' + caseDbId).show();
                $('#continueButton-' + caseDbId).hide();
                $('#stopButton-' + caseDbId).show();
            });
        }
    }

    // Save textarea content
    $('#taskDescription').on('input', function() {
        localStorage.setItem('taskDescription', $(this).val());
    });

    // Load textarea content
    function loadTextareaContent() {
        var savedTaskDescription = localStorage.getItem('taskDescription');
        if (savedTaskDescription) {
            $('#taskDescription').val(savedTaskDescription);
        }
    }
});
*/

/* Working Code 
$(document).ready(function() {
    var timers = {};
    var footerTimerStart = $('#footer-timer #timerStartDisplay');

    // Load timer state and textarea content from local storage
    loadTimers();
    loadTextareaContent();

    // Listen for storage events to synchronize timers across tabs
    window.addEventListener('storage', function(event) {
        if (event.key === 'timers') {
            loadTimers();
        } else if (event.key === 'taskDescription') {
            loadTextareaContent();
        }
    });

    // Capture start timer button click event
    $('.start-timer').click(function() {
        var caseId = $(this).data('caseid');
        var caseDbId = $(this).data('id');
        var hourlyRate = $(this).data('hourlyrate');
        var currency = $(this).data('currency');
        var clientId = $(this).data('client_id');

        // Fill modal hidden inputs
        $('#modalCaseId').val(caseId);
        $('#modalCaseDbId').val(caseDbId);
        $('#modalHourlyRate').val(hourlyRate);
        $('#modalCurrency').val(currency);
        $('#clientId').val(clientId);

        // Show the modal
        $('#timerModal').modal('show');
    });

    // Handle form submission and start timer
    $('#timerForm').submit(function(event) {
        event.preventDefault();

        var caseDbId = $('#modalCaseDbId').val();

        // Start the timer
        startTimer(caseDbId);

        // Close the modal
        $('#timerModal').modal('hide');
    });

    function startTimer(caseDbId) {
        if (timers[caseDbId]) {
            clearInterval(timers[caseDbId].interval);
        }

        timers[caseDbId] = {
            startTime: new Date().getTime(),
            pausedTime: 0
        };

        // Update the footer timer display
        updateFooterTimerDisplay(timers[caseDbId].startTime);

        saveTimers();

        $('#startButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();
        $('#continueButton-' + caseDbId).hide();
        $('#stopButton-' + caseDbId).show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            saveTimers();
        }, 1000);
    }

    function updateTimerDisplay(caseDbId) {
        var currentTime = new Date().getTime();
        var elapsedTime = currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime;
        var seconds = Math.floor(elapsedTime / 1000);
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        var formattedTime = hours.toString().padStart(2, '0') + ':' + 
                            minutes.toString().padStart(2, '0') + ':' + 
                            secs.toString().padStart(2, '0');
        $('#timerDisplay-' + caseDbId).text(formattedTime);
    }

    function updateFooterTimerDisplay(startTime) {
        var currentTime = new Date().getTime();
        var elapsedTime = currentTime - startTime;
        var seconds = Math.floor(elapsedTime / 1000);
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        var formattedTime = hours.toString().padStart(2, '0') + ':' + 
                            minutes.toString().padStart(2, '0') + ':' + 
                            secs.toString().padStart(2, '0');
        footerTimerStart.text(formattedTime);
    }

    // Handle pause timer
    $('.pause-timer').click(function() {
        var caseDbId = $(this).data('id');

        clearInterval(timers[caseDbId].interval);
        timers[caseDbId].pauseStartTime = new Date().getTime();
        $('#pauseButton-' + caseDbId).hide();
        $('#continueButton-' + caseDbId).show();

        saveTimers();
    });

    // Handle continue timer
    $('.continue-timer').click(function() {
        var caseDbId = $(this).data('id');

        var pauseDuration = new Date().getTime() - timers[caseDbId].pauseStartTime;
        timers[caseDbId].pausedTime += pauseDuration;
        delete timers[caseDbId].pauseStartTime;

        $('#continueButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            saveTimers();
        }, 1000);
    });

    // Handle stop timer
    $('.stop-timer').click(function() {
        var caseDbId = $(this).data('id');
        var caseId = $(this).data('caseid');
        var lawFirmId = $('#lawFirmId').val();
        var clientId = $(this).data('client_id');
        var hourlyRate = $(this).data('hourlyrate');
        var taskDescription = $('#taskDescription').val();
        var currency = $(this).data('currency');
        if(confirm("You are about to end the works and record the time in the fee note!")){

            clearInterval(timers[caseDbId].interval);

            var currentTime = new Date().getTime();
            var timeSpent = Math.floor((currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime) / 1000);
            var totalAmount = (timeSpent / 3600) * hourlyRate;

            postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId);

            delete timers[caseDbId];
            saveTimers();
            $('#startButton-' + caseDbId).show();
            $('#pauseButton-' + caseDbId).hide();
            $('#continueButton-' + caseDbId).hide();
            $('#stopButton-' + caseDbId).hide();
            $('#timerDisplay-' + caseDbId).text('00:00:00');
        } else {
            return false;
        }
    });

    function postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId) {
        $.ajax({
            url: 'cases/createTimeData',
            method: 'POST',
            data: {
                caseDbId: caseDbId,
                caseId: caseId,
                currency: currency,
                hourlyRate: hourlyRate,
                timeSpent: timeSpent,
                totalAmount: totalAmount,
                taskDescription: taskDescription,
                lawFirmId: lawFirmId,
                clientId: clientId
            },
            success: function(response) {
                console.log(response);
                alert(response);
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
                alert('Failed to save timer data.');
            }
        });
    }

    function saveTimers() {
        localStorage.setItem('timers', JSON.stringify(timers));
        // Trigger storage event manually for current tab
        window.dispatchEvent(new Event('storage'));
    }

    function loadTimers() {
        var savedTimers = localStorage.getItem('timers');
        if (savedTimers) {
            timers = JSON.parse(savedTimers);
            Object.keys(timers).forEach(function(caseDbId) {
                var savedTimer = timers[caseDbId];
                savedTimer.interval = setInterval(function() {
                    updateTimerDisplay(caseDbId);
                    saveTimers();
                }, 1000);
                $('#startButton-' + caseDbId).hide();
                $('#pauseButton-' + caseDbId).show();
                $('#continueButton-' + caseDbId).hide();
                $('#stopButton-' + caseDbId).show();

                // Update the footer timer display
                updateFooterTimerDisplay(savedTimer.startTime);
            });
        }
    }

    // Save textarea content
    $('#taskDescription').on('input', function() {
        localStorage.setItem('taskDescription', $(this).val());
    });

    // Load textarea content
    function loadTextareaContent() {
        var savedTaskDescription = localStorage.getItem('taskDescription');
        if (savedTaskDescription) {
            $('#taskDescription').val(savedTaskDescription);
        }
    }
});
*/
/* Third Working Code
$(document).ready(function() {
    var timers = {};
    var footerTimerStart = $('#footer-timer #timerStartDisplay');

    // Load timer state and textarea content from local storage
    loadTimers();
    loadTextareaContent();

    // Listen for storage events to synchronize timers across tabs
    window.addEventListener('storage', function(event) {
        if (event.key === 'timers') {
            loadTimers();
        } else if (event.key === 'taskDescription') {
            loadTextareaContent();
        }
    });

    // Capture start timer button click event
    $('.start-timer').click(function() {
        var caseId = $(this).data('caseid');
        var caseDbId = $(this).data('id');
        var hourlyRate = $(this).data('hourlyrate');
        var currency = $(this).data('currency');
        var clientId = $(this).data('client_id');

        // Fill modal hidden inputs
        $('#modalCaseId').val(caseId);
        $('#modalCaseDbId').val(caseDbId);
        $('#modalHourlyRate').val(hourlyRate);
        $('#modalCurrency').val(currency);
        $('#clientId').val(clientId);

        // Show the modal
        $('#timerModal').modal('show');
    });

    // Handle form submission and start timer
    $('#timerForm').submit(function(event) {
        event.preventDefault();

        var caseDbId = $('#modalCaseDbId').val();

        // Start the timer
        startTimer(caseDbId);

        // Close the modal
        $('#timerModal').modal('hide');
    });

    function startTimer(caseDbId) {
        if (timers[caseDbId]) {
            clearInterval(timers[caseDbId].interval);
        }

        timers[caseDbId] = {
            startTime: new Date().getTime(),
            pausedTime: 0
        };

        // Update the footer timer display
        updateFooterTimerDisplay(timers[caseDbId].startTime);

        saveTimers();

        $('#startButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();
        $('#continueButton-' + caseDbId).hide();
        $('#stopButton-' + caseDbId).show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            updateFooterTimerDisplay(timers[caseDbId].startTime);
            saveTimers();
        }, 1000);
    }

    function updateTimerDisplay(caseDbId) {
        var currentTime = new Date().getTime();
        var elapsedTime = currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime;
        var seconds = Math.floor(elapsedTime / 1000);
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        var formattedTime = hours.toString().padStart(2, '0') + ':' + 
                            minutes.toString().padStart(2, '0') + ':' + 
                            secs.toString().padStart(2, '0');
        $('#timerDisplay-' + caseDbId).text(formattedTime);
    }

    function updateFooterTimerDisplay(startTime) {
        var currentTime = new Date().getTime();
        var elapsedTime = currentTime - startTime;
        var seconds = Math.floor(elapsedTime / 1000);
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        var formattedTime = hours.toString().padStart(2, '0') + ':' + 
                            minutes.toString().padStart(2, '0') + ':' + 
                            secs.toString().padStart(2, '0');
        footerTimerStart.text(formattedTime);
    }

    // Handle pause timer
    $('.pause-timer').click(function() {
        var caseDbId = $(this).data('id');

        clearInterval(timers[caseDbId].interval);
        timers[caseDbId].pauseStartTime = new Date().getTime();
        $('#pauseButton-' + caseDbId).hide();
        $('#continueButton-' + caseDbId).show();
        $('#footer-timer').hide();

        saveTimers();
    });

    // Handle continue timer
    $('.continue-timer').click(function() {
        var caseDbId = $(this).data('id');

        var pauseDuration = new Date().getTime() - timers[caseDbId].pauseStartTime;
        timers[caseDbId].pausedTime += pauseDuration;
        delete timers[caseDbId].pauseStartTime;

        $('#continueButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();
        $('#footer-timer').show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            updateFooterTimerDisplay(timers[caseDbId].startTime);
            saveTimers();
        }, 1000);
    });

    // Handle stop timer
    $('.stop-timer').click(function() {
        var caseDbId = $(this).data('id');
        var caseId = $(this).data('caseid');
        var lawFirmId = $('#lawFirmId').val();
        var clientId = $(this).data('client_id');
        var hourlyRate = $(this).data('hourlyrate');
        var taskDescription = $('#taskDescription').val();
        var currency = $(this).data('currency');
        if(confirm("You are about to end the works and record the time in the fee note!")){

            clearInterval(timers[caseDbId].interval);

            var currentTime = new Date().getTime();
            var timeSpent = Math.floor((currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime) / 1000);
            var totalAmount = (timeSpent / 3600) * hourlyRate;

            postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId);

            delete timers[caseDbId];
            saveTimers();
            $('#startButton-' + caseDbId).show();
            $('#pauseButton-' + caseDbId).hide();
            $('#continueButton-' + caseDbId).hide();
            $('#stopButton-' + caseDbId).hide();
            $('#timerDisplay-' + caseDbId).text('00:00:00');
            $('#footer-timer').hide();
            footerTimerStart.text('00:00:00');
        } else {
            return false;
        }
    });

    function postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId) {
        $.ajax({
            url: 'cases/createTimeData',
            method: 'POST',
            data: {
                caseDbId: caseDbId,
                caseId: caseId,
                currency: currency,
                hourlyRate: hourlyRate,
                timeSpent: timeSpent,
                totalAmount: totalAmount,
                taskDescription: taskDescription,
                lawFirmId: lawFirmId,
                clientId: clientId
            },
            success: function(response) {
                console.log(response);
                alert(response);
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
                alert('Failed to save timer data.');
            }
        });
    }

    function saveTimers() {
        localStorage.setItem('timers', JSON.stringify(timers));
        // Trigger storage event manually for current tab
        window.dispatchEvent(new Event('storage'));
    }

    function loadTimers() {
        var savedTimers = localStorage.getItem('timers');
        if (savedTimers) {
            timers = JSON.parse(savedTimers);
            Object.keys(timers).forEach(function(caseDbId) {
                var savedTimer = timers[caseDbId];
                savedTimer.interval = setInterval(function() {
                    updateTimerDisplay(caseDbId);
                    updateFooterTimerDisplay(savedTimer.startTime);
                    saveTimers();
                }, 1000);
                $('#startButton-' + caseDbId).hide();
                $('#pauseButton-' + caseDbId).show();
                $('#continueButton-' + caseDbId).hide();
                $('#stopButton-' + caseDbId).show();

                // Update the footer timer display
                updateFooterTimerDisplay(savedTimer.startTime);
            });
        }
    }

    // Save textarea content
    $('#taskDescription').on('input', function() {
        localStorage.setItem('taskDescription', $(this).val());
    });

    // Load textarea content
    function loadTextareaContent() {
        var savedTaskDescription = localStorage.getItem('taskDescription');
        if (savedTaskDescription) {
            $('#taskDescription').val(savedTaskDescription);
        }
    }
});
timerStartDisplay
*/

$(document).ready(function() {
    var timers = {};
    var footerTimerStart = $('#footer-timer #timerStartDisplay');
    var activeTimerId;

    // Load timer state and textarea content from local storage
    loadTimers();
    loadTextareaContent();
    startFooterTimer();

    // Listen for storage events to synchronize timers across tabs
    window.addEventListener('storage', function(event) {
        if (event.key === 'timers') {
            loadTimers();
            startFooterTimer();
        } else if (event.key === 'taskDescription') {
            loadTextareaContent();
        }
    });

    // Capture start timer button click event
    $('.start-timer').click(function() {
        var caseId = $(this).data('caseid');
        var caseDbId = $(this).data('id');
        var hourlyRate = $(this).data('hourlyrate');
        var currency = $(this).data('currency');
        var clientId = $(this).data('client_id');

        // Fill modal hidden inputs
        $('#modalCaseId').val(caseId);
        $('#modalCaseDbId').val(caseDbId);
        $('#modalHourlyRate').val(hourlyRate);
        $('#modalCurrency').val(currency);
        $('#clientId').val(clientId);

        // Show the modal
        $('#timerModal').modal('show');
    });

    // Handle form submission and start timer
    $('#timerForm').submit(function(event) {
        event.preventDefault();

        var caseDbId = $('#modalCaseDbId').val();

        // Start the timer
        startTimer(caseDbId);

        // Close the modal
        $('#timerModal').modal('hide');
    });

    function startTimer(caseDbId) {
        if (timers[caseDbId]) {
            clearInterval(timers[caseDbId].interval);
        }

        timers[caseDbId] = {
            startTime: new Date().getTime(),
            pausedTime: 0
        };

        // Set active timer ID
        activeTimerId = caseDbId;
        localStorage.setItem('activeTimerId', activeTimerId);

        // Update the footer timer display
        updateFooterTimerDisplay(timers[caseDbId].startTime);

        saveTimers();

        $('#startButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();
        $('#continueButton-' + caseDbId).hide();
        $('#stopButton-' + caseDbId).show();

        // Show footer buttons
        $('#footer-timer').show();
        $('#floating-footer .pause-timer').show();
        $('#floating-footer .stop-timer').show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            updateFooterTimerDisplay(timers[caseDbId].startTime);
            saveTimers();
        }, 1000);
    }

    function updateTimerDisplay(caseDbId) {
        var currentTime = new Date().getTime();
        var elapsedTime = currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime;
        var seconds = Math.floor(elapsedTime / 1000);
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        var formattedTime = hours.toString().padStart(2, '0') + ':' + 
                            minutes.toString().padStart(2, '0') + ':' + 
                            secs.toString().padStart(2, '0');
        $('#timerDisplay-' + caseDbId).text(formattedTime);
    }

    function updateFooterTimerDisplay(startTime) {
        var currentTime = new Date().getTime();
        var elapsedTime = currentTime - startTime;
        var seconds = Math.floor(elapsedTime / 1000);
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        var formattedTime = hours.toString().padStart(2, '0') + ':' + 
                            minutes.toString().padStart(2, '0') + ':' + 
                            secs.toString().padStart(2, '0');
        footerTimerStart.text(formattedTime);
    }

    function startFooterTimer() {
        activeTimerId = localStorage.getItem('activeTimerId');
        if (activeTimerId && timers[activeTimerId]) {
            $('#footer-timer').show();
            $('#floating-footer .pause-timer').show();
            $('#floating-footer .stop-timer').show();
            setInterval(function() {
                updateFooterTimerDisplay(timers[activeTimerId].startTime);
            }, 1000);
        } else {
            $('#footer-timer').hide();
            $('#floating-footer .pause-timer').hide();
            $('#floating-footer .stop-timer').hide();
        }
    }

    // Handle pause timer
    $('.pause-timer').click(function() {
        var caseDbId = $(this).data('id') || activeTimerId;

        clearInterval(timers[caseDbId].interval);
        timers[caseDbId].pauseStartTime = new Date().getTime();
        $('#pauseButton-' + caseDbId).hide();
        $('#continueButton-' + caseDbId).show();
        $('#floating-footer .pause-timer').hide();
        $('#floating-footer .continue-timer').show();

        saveTimers();
    });

    // Handle continue timer
    $('.continue-timer').click(function() {
        var caseDbId = $(this).data('id') || activeTimerId;

        var pauseDuration = new Date().getTime() - timers[caseDbId].pauseStartTime;
        timers[caseDbId].pausedTime += pauseDuration;
        delete timers[caseDbId].pauseStartTime;

        $('#continueButton-' + caseDbId).hide();
        $('#pauseButton-' + caseDbId).show();
        $('#floating-footer .continue-timer').hide();
        $('#floating-footer .pause-timer').show();

        timers[caseDbId].interval = setInterval(function() {
            updateTimerDisplay(caseDbId);
            updateFooterTimerDisplay(timers[caseDbId].startTime);
            saveTimers();
        }, 1000);
    });

    // Handle stop timer
    $('.stop-timer').click(function() {
        var caseDbId = $(this).data('id') || activeTimerId;
        var caseId = $(this).data('caseid');
        var lawFirmId = $('#lawFirmId').val();
        var clientId = $(this).data('client_id');
        var hourlyRate = $(this).data('hourlyrate');
        var taskDescription = $('#taskDescription').val();
        var currency = $(this).data('currency');
        if(confirm("You are about to end the works and record the time in the fee note!")){

            clearInterval(timers[caseDbId].interval);

            var currentTime = new Date().getTime();
            var timeSpent = Math.floor((currentTime - timers[caseDbId].startTime - timers[caseDbId].pausedTime) / 1000);
            var totalAmount = (timeSpent / 3600) * hourlyRate;

            postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId);

            delete timers[caseDbId];
            saveTimers();
            $('#startButton-' + caseDbId).show();
            $('#pauseButton-' + caseDbId).hide();
            $('#continueButton-' + caseDbId).hide();
            $('#stopButton-' + caseDbId).hide();
            $('#timerDisplay-' + caseDbId).text('00:00:00');
            
            // Clear active timer and hide footer
            localStorage.removeItem('activeTimerId');
            $('#footer-timer').hide();
            $('#floating-footer .pause-timer').hide();
            $('#floating-footer .continue-timer').hide();
            $('#floating-footer .stop-timer').hide();
        } else {
            return false;
        }
    });

    function postTimerData(caseDbId, caseId, currency, hourlyRate, timeSpent, totalAmount, taskDescription, lawFirmId, clientId) {
        $.ajax({
            url: 'cases/createTimeData',
            method: 'POST',
            data: {
                caseDbId: caseDbId,
                caseId: caseId,
                currency: currency,
                hourlyRate: hourlyRate,
                timeSpent: timeSpent,
                totalAmount: totalAmount,
                taskDescription: taskDescription,
                lawFirmId: lawFirmId,
                clientId: clientId
            },
            success: function(response) {
                console.log(response);
                alert(response);
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
                alert('Failed to save timer data.');
            }
        });
    }

    function saveTimers() {
        localStorage.setItem('timers', JSON.stringify(timers));
        // Trigger storage event manually for current tab
        window.dispatchEvent(new Event('storage'));
    }

    function loadTimers() {
        var savedTimers = localStorage.getItem('timers');
        if (savedTimers) {
            timers = JSON.parse(savedTimers);
            Object.keys(timers).forEach(function(caseDbId) {
                var savedTimer = timers[caseDbId];
                savedTimer.interval = setInterval(function() {
                    updateTimerDisplay(caseDbId);
                    updateFooterTimerDisplay(savedTimer.startTime);
                    saveTimers();
                }, 1000);
                $('#startButton-' + caseDbId).hide();
                $('#pauseButton-' + caseDbId).show();
                $('#continueButton-' + caseDbId).hide();
                $('#stopButton-' + caseDbId).show();

                // Update the footer timer display
                updateFooterTimerDisplay(savedTimer.startTime);
            });
        }
    }

    // Save textarea content
    $('#taskDescription').on('input', function() {
        localStorage.setItem('taskDescription', $(this).val());
    });

    // Load textarea content
    function loadTextareaContent() {
        var savedTaskDescription = localStorage.getItem('taskDescription');
        if (savedTaskDescription) {
            $('#taskDescription').val(savedTaskDescription);
        }
    }
});

