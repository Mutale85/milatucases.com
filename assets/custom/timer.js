$(document).ready(function() {
      // Find all tables with id="divTable"
    var tables = $('table[id="allTables"]');
      
    // Initialize DataTables for each of these tables
    tables.each(function() {
        $(this).DataTable({
            dom: 'Bfrtip',
            buttons: [
                
                'excelHtml5',
                // 'csvHtml5',
                // 'pdfHtml5'
            ]
        });
    });
});

function sweetSuccess(message){
	Swal.fire({
		position: "top-end",
		icon: "success",
		text:message,
	});
}

function sweetError(message){
	Swal.fire({
		position: "top-end",
	  	icon: "error",
	  	title: "Oops...",
	  	text: message
	});
}

function sweetBeforeSend(message){
	Swal.fire({
	  position: "top-end",
	  icon: "success",
	  title: "Your work has been saved",
	  showConfirmButton: false,
	  timer: 1500
	});
}


/*Timer*/ 

$(document).ready(function() {
    let timers = [];
    let lastStartedTimer = null;

    function initializeTimers() {
        let storedTimers = localStorage.getItem('timers');
        let storedLastStartedTimer = localStorage.getItem('lastStartedTimer');
        
        if (storedTimers) {
            try {
                timers = JSON.parse(storedTimers);
                if (!Array.isArray(timers)) {
                    timers = [];
                }
            } catch (e) {
                timers = [];
            }
        } else {
            timers = [];
        }

        if (storedLastStartedTimer) {
            try {
                lastStartedTimer = JSON.parse(storedLastStartedTimer);
                if (lastStartedTimer) {
                    const timer = timers.find(t => t.id === lastStartedTimer.id);
                    if (timer) {
                        lastStartedTimer = timer;
                        if (!timer.stopped && timer.intervalId) {
                            timer.startTime = Date.now() - timer.elapsedTime;
                            startTimer(timer);
                        }
                    } else {
                        lastStartedTimer = null;
                    }
                }
            } catch (e) {
                lastStartedTimer = null;
            }
        }

        timers.forEach(timer => {
            createTimerElement(timer);
            updateTimerButtons(timer);
        });

        updateFooter();
        return timers;
    }

    function createTimerElement(timer) {
        const timerHtml = `
            <div class="timer" id="timer-${timer.id}">
                <div class="timer-description">${timer.description} - <span class="timer-display" id="display-${timer.id}">${formatTime(timer.elapsedTime)}</span></div>
                <div class="timer-controls">
                    <button class="btn btn-sm btn-success start" data-id="${timer.id}"><i class="bi bi-play-circle"></i></button>
                    <button class="btn btn-sm btn-warning pause" data-id="${timer.id}"><i class="bi bi-pause-circle"></i></button>
                    <button class="btn btn-sm btn-danger stop" data-id="${timer.id}"><i class="bi bi-stop-circle"></i></button>
                    <button class="btn btn-sm btn-danger remove" data-id="${timer.id}"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
        $('#timers-list').append(timerHtml);
    }

    function updateTimerButtons(timer) {
        if (timer.stopped) {
            $(`.start[data-id="${timer.id}"]`).hide();
            $(`.pause[data-id="${timer.id}"]`).hide();
            $(`.stop[data-id="${timer.id}"]`).hide();
        } else if (timer.intervalId) {
            $(`.start[data-id="${timer.id}"]`).hide();
            $(`.pause[data-id="${timer.id}"]`).show();
            $(`.stop[data-id="${timer.id}"]`).show();
        } else {
            $(`.start[data-id="${timer.id}"]`).show();
            $(`.pause[data-id="${timer.id}"]`).hide();
            $(`.stop[data-id="${timer.id}"]`).show();
        }
    }

    $('#start-timer').click(function() {
        const description = $('#task-description').val();
        const hourlyRate = $('#hourly-rate').val();
        const clientId = $('#client-id').val();
        const caseId = $('#case-id').val();

        if (!description || !hourlyRate) return;

        const id = new Date().getTime();
        const timer = {
            id,
            description,
            startTime: Date.now(),
            elapsedTime: 0,
            intervalId: null,
            hourlyRate: parseFloat(hourlyRate),
            clientId,
            caseId,
            stopped: false
        };
        createTimerElement(timer);
        timers.push(timer);
        startTimer(timer);
        saveTimers();
        $('#task-description').val('');
        $('#hourly-rate').val('');
        updateTimerButtons(timer);
        updateFooter();
    });

    $(document).on('click', '.start', function() {
        const id = $(this).data('id');
        const timer = timers.find(t => t.id === id);
        if (timer.intervalId) return;

        timer.startTime = Date.now() - timer.elapsedTime;
        startTimer(timer);
        updateTimerButtons(timer);
        updateFooter();
    });

    $(document).on('click', '.pause', function() {
        const id = $(this).data('id');
        const timer = timers.find(t => t.id === id);
        clearInterval(timer.intervalId);
        timer.intervalId = null;
        timer.elapsedTime = Date.now() - timer.startTime;
        saveTimers();
        updateTimerButtons(timer);
        updateFooter();
    });

    $(document).on('click', '.stop', function() {
        const id = $(this).data('id');
        const timer = timers.find(t => t.id === id);
        if (timer.intervalId) {
            clearInterval(timer.intervalId);
            timer.intervalId = null;
            timer.elapsedTime = Date.now() - timer.startTime;
        }
        timer.stopped = true;
        saveTimers();
        
        $.post('base/createWorkedTime', {
            client_id: timer.clientId,
            case_id: timer.caseId,
            description: timer.description,
            hourly_rate: timer.hourlyRate,
            total_amount: (timer.hourlyRate * (timer.elapsedTime / 3600000)).toFixed(2),
            start_time: new Date(timer.startTime).toISOString(),
            end_time: new Date().toISOString(),
            elapsed_time: timer.elapsedTime
        }, function(response) {
            sweetSuccess("Time logged successfully!");
        });

        updateTimerButtons(timer);
        updateFooter();
    });

    $(document).on('click', '.remove', function() {
        if (confirm('Are you sure you want to remove this timer?')) {
            const id = $(this).data('id');
            timers = timers.filter(t => t.id !== id);
            $(`#timer-${id}`).remove();
            saveTimers();
            if (lastStartedTimer && lastStartedTimer.id === id) {
                lastStartedTimer = timers.find(t => t.intervalId);
                updateFooter();
            }
        }
    });

    function startTimer(timer) {
        timer.intervalId = setInterval(() => {
            timer.elapsedTime = Date.now() - timer.startTime;
            const displayTime = formatTime(timer.elapsedTime);
            $(`#display-${timer.id}`).text(displayTime);
            saveTimers();
            updateFooter();
        }, 1000);
        lastStartedTimer = timer;
        updateFooter();
    }

    function formatTime(milliseconds) {
        const seconds = Math.floor(milliseconds / 1000);
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return [hours, minutes, secs].map(num => num.toString().padStart(2, '0')).join(':');
    }

    function saveTimers() {
        localStorage.setItem('timers', JSON.stringify(timers));
        localStorage.setItem('lastStartedTimer', JSON.stringify(lastStartedTimer));
    }

    function updateFooter() {
        if (lastStartedTimer) {
            if (lastStartedTimer.intervalId) {
                const runningTime = formatTime(Date.now() - lastStartedTimer.startTime);
                $('#footer-time').text(` ${runningTime}`);
                $('#footer-pause').show();
                $('#footer-start').hide();
            } else {
                $('#footer-time').text(formatTime(lastStartedTimer.elapsedTime));
                $('#footer-pause').hide();
                $('#footer-start').show();
            }
            $('.action-buttons').addClass('show');
        } else {
            $('#footer-time').text('00:00:00');
            $('#footer-pause').hide();
            $('#footer-start').hide();
            $('.action-buttons').removeClass('show');
        }
    }

    $('#footer-pause').click(function() {
        if (lastStartedTimer) {
            clearInterval(lastStartedTimer.intervalId);
            lastStartedTimer.intervalId = null;
            lastStartedTimer.elapsedTime = Date.now() - lastStartedTimer.startTime;
            saveTimers();
            updateTimerButtons(lastStartedTimer);
            updateFooter();
        }
    });

    $('#footer-start').click(function() {
        if (lastStartedTimer) {
            lastStartedTimer.startTime = Date.now() - lastStartedTimer.elapsedTime;
            startTimer(lastStartedTimer);
            updateTimerButtons(lastStartedTimer);
            updateFooter();
        }
    });

    $('#stop-all').click(function() {
        timers.forEach(timer => {
            if (timer.intervalId) {
                clearInterval(timer.intervalId);
                timer.intervalId = null;
                timer.elapsedTime = Date.now() - timer.startTime;
                timer.stopped = true;
                
                $.post('base/createWorkedTime', {
                    client_id: timer.clientId,
                    case_id: timer.caseId,
                    description: timer.description,
                    hourly_rate: timer.hourlyRate,
                    total_amount: (timer.hourlyRate * (timer.elapsedTime / 3600000)).toFixed(2),
                    start_time: new Date(timer.startTime).toISOString(),
                    end_time: new Date().toISOString(),
                    elapsed_time: timer.elapsedTime
                }, function(response) {
                    console.log("Timer logged:", timer.id);
                });
                
                updateTimerButtons(timer);
            }
        });
        lastStartedTimer = null;
        saveTimers();
        updateFooter();
    });

    timers = initializeTimers();
});