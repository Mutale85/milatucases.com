$(document).ready(function() {
    var tables = $('table[id="allTables"]');
    tables.each(function() {
        $(this).DataTable();
    });
});

// function sweetSuccess(message){
//     Swal.fire({
//         position: "top-end",
//         icon: "success",
//         text:message,
//         title: "Your work has been saved",
//         showConfirmButton: false,
//         timer: 1500
//     });
// }

// function sweetError(message){
//   Swal.fire({
//       position: "top-end",
//         icon: "error",
//         title: "Oops...",
//         text: message
//   });
// }

function sweetBeforeSend(message){
  Swal.fire({
    position: "top-end",
    icon: "success",
    title: "Your work has been saved",
    showConfirmButton: false,
    timer: 1500
  });
}

function sweetSuccess(message){
    toastr["success"](message)

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
}

function sweetError(message){
    toastr["error"](message)

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
}


$(document).ready(function(){
    // Timer variables
    let timers = [];
    let currentTimerIndex = -1;

    // DOM elements
    const timerDisplay = document.getElementById('timerClock');
    const startBtn = document.getElementById('startBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const stopBtn = document.getElementById('stopBtn');
    const timersBtn = document.getElementById('timersBtn');
    const timeEntryModal = new bootstrap.Modal(document.getElementById('timeEntryModal'));
    const timersModal = new bootstrap.Modal(document.getElementById('timersModal'));
    const timersList = document.getElementById('timersList');
    const saveTimeEntryBtn = document.getElementById('saveTimeEntry');
    const addNewTimerBtn = document.getElementById('addNewTimerBtn');

    // Timer class
    class Timer {
        constructor(id, startTime = null, elapsedTime = 0, isRunning = false) {
            this.id = id;
            this.startTime = startTime;
            this.elapsedTime = elapsedTime;
            this.isRunning = isRunning;
        }

        start() {
            if (!this.isRunning) {
                this.startTime = Date.now() - this.elapsedTime;
                this.isRunning = true;
            }
        }

        pause() {
            if (this.isRunning) {
                this.elapsedTime = Date.now() - this.startTime;
                this.isRunning = false;
            }
        }

        stop() {
            if (this.isRunning) {
                this.elapsedTime = Date.now() - this.startTime;
            }
            this.isRunning = false;
        }

        getElapsedTime() {
            if (this.isRunning) {
                return Date.now() - this.startTime;
            }
            return this.elapsedTime;
        }
    }

    // Save timers state to local storage
    function saveTimersState() {
        const timersState = timers.map(timer => ({
            id: timer.id,
            startTime: timer.startTime,
            elapsedTime: timer.getElapsedTime(),
            isRunning: timer.isRunning
        }));
        localStorage.setItem('timersState', JSON.stringify(timersState));
        localStorage.setItem('currentTimerIndex', currentTimerIndex);
    }

    // Load timers state from local storage
    function loadTimersState() {
        const timersState = JSON.parse(localStorage.getItem('timersState'));
        if (timersState) {
            timers = timersState.map(state => new Timer(state.id, state.startTime, state.elapsedTime, state.isRunning));
            currentTimerIndex = parseInt(localStorage.getItem('currentTimerIndex'));
        }
    }

    // Update timer display
    function updateDisplay() {
        if (currentTimerIndex !== -1) {
            const timer = timers[currentTimerIndex];
            timerDisplay.textContent = formatTime(timer.getElapsedTime());
        } else {
            timerDisplay.textContent = '00:00:00';
        }
        updateButtons();
    }

    // Format time in HH:MM:SS
    function formatTime(ms) {
        const totalSeconds = Math.floor(ms / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    // Start or resume timer
    function startOrResumeTimer() {
        if (currentTimerIndex === -1) {
            startNewTimer();
        } else {
            resumeTimer();
        }
    }

    // Start a new timer
    function startNewTimer() {
        if (currentTimerIndex !== -1 && timers[currentTimerIndex].isRunning) {
            alert("You have a timer underway. Please stop or pause the current timer before starting a new one.");
            return;
        }
        
        if (currentTimerIndex !== -1) {
            timers[currentTimerIndex].pause();
        }
        const newTimer = new Timer(timers.length);
        timers.push(newTimer);
        currentTimerIndex = timers.length - 1;
        newTimer.start();
        updateTimersList();
        saveTimersState();
        
        if (timersModal._isShown) {
            showTimersModal();
        }
    }

    // Pause the current timer
    function pauseTimer() {
        if (currentTimerIndex !== -1) {
            timers[currentTimerIndex].pause();
            saveTimersState();
        }
    }

    // Resume the current timer
    function resumeTimer() {
        if (currentTimerIndex !== -1) {
            timers[currentTimerIndex].start();
            saveTimersState();
        }
    }

    // Stop the current timer
    function stopTimer() {
        if (currentTimerIndex !== -1) {
            const timer = timers[currentTimerIndex];
            timer.stop();
            
            showTimeEntryForm(currentTimerIndex);
            
            saveTimersState();
        }
    }

    // Show time entry form
    function showTimeEntryForm(timerIndex) {
        const timer = timers[timerIndex];
        let totalMinutes = Math.max(1, Math.floor(timer.getElapsedTime() / 60000));
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        
        document.getElementById('hours').value = hours;
        document.getElementById('minutes').value = minutes;
        
        // Set current date and time
        const now = new Date();
        document.getElementById('date').value = now.toISOString().split('T')[0];
        document.getElementById('time').value = now.toTimeString().slice(0, 5);
        
        // Store the timer index in the form for submission
        timeEntryModal._element.dataset.timerIndex = timerIndex;
        
        // Hide timers modal if it's open
        timersModal.hide();
        
        // Show time entry modal
        timeEntryModal.show();

        // Clear previous values
        document.getElementById('hourlyRate').value = '';
        document.getElementById('cost').value = '';

        // Add event listener for hourly rate input
        document.getElementById('hourlyRate').addEventListener('input', calculateCost);
        document.getElementById('hours').addEventListener('input', calculateCost);
        document.getElementById('minutes').addEventListener('input', calculateCost);
    }

    // Calculate cost based on time and hourly rate
    function calculateCost() {
        const hours = parseFloat(document.getElementById('hours').value) || 0;
        const minutes = parseFloat(document.getElementById('minutes').value) || 0;
        const hourlyRate = parseFloat(document.getElementById('hourlyRate').value) || 0;

        const totalHours = hours + (minutes / 60);
        const cost = totalHours * hourlyRate;

        document.getElementById('cost').value = cost.toFixed(2);
    }

    // Handle form submission
    $("#timeEntryForm").submit(function(e){
        e.preventDefault();
        const timerIndex = parseInt(timeEntryModal._element.dataset.timerIndex);
        const formDataInfo = $("#timeEntryForm").serialize();
        
        $.ajax({
            url: "billings/createTimeLog",
            method: "post",
            data: formDataInfo,
            beforeSend: function() {
                $('#saveTimeEntry').prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
            },
            success: function(response) {
                console.log("Server response:", response);
                
                if (response.trim().includes("New time entry created successfully")) {
                    console.log("New record created");
                    sweetSuccess(response);
                    setTimeout(function() {
                        timeEntryModal.hide();
                        removeTimer(timerIndex);
                    }, 2000);
                } else if (response.trim().includes("Time entry updated successfully")) {
                    console.log("Time entry updated");
                    sweetSuccess(response);
                    setTimeout(function() {
                        timeEntryModal.hide();
                        removeTimer(timerIndex);
                        location.reload();
                    }, 2000);
                } else {
                    console.log("Unexpected response:", response);
                    sweetError("Unexpected response from server");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                sweetError("An error occurred: " + error);
            },
            complete: function() {
                $('#saveTimeEntry').prop("disabled", false).html("Save Time Entry");
            }
        });
    })
    /*
    function handleFormSubmission(event) {
        event.preventDefault();
        const timerIndex = parseInt(timeEntryModal._element.dataset.timerIndex);
        
        // Here you would typically save the form data or send it to a server
        const formData = {
            hours: document.getElementById('hours').value,
            minutes: document.getElementById('minutes').value,
            date: document.getElementById('date').value,
            time: document.getElementById('time').value,
            hourlyRate: document.getElementById('hourlyRate').value,
            cost: document.getElementById('cost').value
        };
        const formDataInfo = $("#timeEntryForm").serialize();
        
        $.ajax({
            url: "billings/createTimeLog",
            method: "post",
            data: formDataInfo,
            dataType: "json",
            beforeSend: function() {
                $('#saveTimeEntry').prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
            },
            success: function(response) {
                if (response.success) {
                    if (response.message === "New record created successfully") {
                        sweetSuccess(response);
                        setTimeout(function() {
                            timeEntryModal.hide();
                            removeTimer(timerIndex);
                        }, 2000);
                    } else if (response.message === "Time entry updated successfully") {
                        sweetSuccess(response);
                        setTimeout(function() {
                            timeEntryModal.hide();
                            removeTimer(timerIndex);
                            location.reload();
                        }, 2000);
                    }
                    removeTimer(timerIndex);
                } else {
                    sweetError(response);
                }
            },
            error: function(xhr, status, error) {
                sweetError("An error occurred: " + error);
            },
            complete: function() {
                $('#saveTimeEntry').prop("disabled", false).html("Save Time Entry");
            }
        });
    }
    */


    // Update button visibility
    function updateButtons() {
        const hasActiveTimer = currentTimerIndex !== -1;
        const isCurrentTimerRunning = hasActiveTimer && timers[currentTimerIndex].isRunning;
        
        startBtn.style.display = !isCurrentTimerRunning ? 'inline-block' : 'none';
        pauseBtn.style.display = isCurrentTimerRunning ? 'inline-block' : 'none';
        stopBtn.style.display = hasActiveTimer ? 'inline-block' : 'none';
    }

    // Show timers modal
    function showTimersModal() {
        updateTimersList();
        timersModal.show();
    }

    // Update timers list in modal
    function updateTimersList() {
        timersList.innerHTML = '';
        timers.forEach((timer, index) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span>Timer ${index + 1}: <span class="timer-display">${formatTime(timer.getElapsedTime())}</span></span>
                <div class="timer-controls">
                    ${timer.isRunning ? 
                        `<button class="btn btn-sm btn-warning me-1 pause-timer" data-index="${index}">Pause</button>` :
                        `<button class="btn btn-sm btn-primary me-1 start-timer" data-index="${index}">Start</button>
                         <button class="btn btn-sm btn-success me-1 save-timer" data-index="${index}">Save</button>
                         <button class="btn btn-sm btn-danger remove-timer" data-index="${index}">Remove</button>`
                    }
                </div>
            `;
            timersList.appendChild(li);
        });
    }

    // Update only the display part of timers list
    function updateTimersListDisplay() {
        const timerDisplays = timersList.querySelectorAll('.timer-display');
        timerDisplays.forEach((display, index) => {
            if (index < timers.length) {
                display.textContent = formatTime(timers[index].getElapsedTime());
            }
        });
    }

    // Event delegation for timer list buttons
    timersList.addEventListener('click', function(event) {
        const target = event.target;
        if (target.tagName === 'BUTTON') {
            const index = parseInt(target.dataset.index);
            if (target.classList.contains('pause-timer')) {
                pauseTimerFromList(index);
            } else if (target.classList.contains('start-timer')) {
                startTimerFromList(index);
            } else if (target.classList.contains('save-timer')) {
                saveTimerFromList(index);
            } else if (target.classList.contains('remove-timer')) {
                removeTimer(index);
            }
            event.stopPropagation();
        }
    });

    // Start a timer from the list
    function startTimerFromList(index) {
        if (currentTimerIndex !== -1 && currentTimerIndex !== index) {
            timers[currentTimerIndex].pause();
        }
        currentTimerIndex = index;
        timers[currentTimerIndex].start();
        updateTimersList();
        saveTimersState();
    }

    // Pause a timer from the list
    function pauseTimerFromList(index) {
        timers[index].pause();
        if (index === currentTimerIndex) {
            updateDisplay();
        }
        updateTimersList();
        saveTimersState();
    }

    // Save a timer from the list
    function saveTimerFromList(index) {
        timers[index].pause();
        showTimeEntryForm(index);
        saveTimersState();
    }

    // Remove a timer
    function removeTimer(index) {
        // if (confirm(`Are you sure you want to remove Timer ${index + 1}?`)) {
            timers[index].stop();
            timers.splice(index, 1);
            if (currentTimerIndex === index) {
                currentTimerIndex = -1;
            } else if (currentTimerIndex > index) {
                currentTimerIndex--;
            }
            updateTimersList();
            saveTimersState();
        // }
    }

    // Continuous update function
    function continuousUpdate() {
        updateDisplay();
        if (timersModal._isShown) {
            updateTimersListDisplay();
        }
        requestAnimationFrame(continuousUpdate);
    }

    // Event listeners
    startBtn.addEventListener('click', startOrResumeTimer);
    pauseBtn.addEventListener('click', pauseTimer);
    stopBtn.addEventListener('click', stopTimer);
    timersBtn.addEventListener('click', showTimersModal);
    // saveTimeEntryBtn.addEventListener('click', handleFormSubmission);
    addNewTimerBtn.addEventListener('click', startNewTimer);

    // Initialize
    loadTimersState();
    continuousUpdate();



});

$(document).ready(function() {
    /*
    $(document).on('click', '.editTimerLog', function(e) {
        e.preventDefault();
        var timeEntryId = $(this).data('id');
        
        // Fetch time entry details using AJAX
        $.ajax({
            url: 'cases/fetch_selected_time_entry',
            type: 'POST',
            data: { id: timeEntryId },
            dataType: 'json',
            success: function(data) {
                // Populate the modal form with fetched data
                $('#selectCase').val(data.caseId);
                $('#date').val(data.dateCreated);
                $('#time').val(data.timeCreated);
                $('#hours').val(data.hours);
                $('#minutes').val(data.minutes);
                $('#selectCurrency').val(data.currency);
                $('#hourlyRate').val(data.hourlyRate);
                $('#cost').val(data.cost);
                $('#description').val(data.description);
                $('input[name="billableStatus"][value="' + data.billableStatus + '"]').prop('checked', true);
                
                // Set the timerId hidden input
                $('#timerId').val(timeEntryId);
                
                // Open the modal
                $('#timeEntryModal').modal('show');
                
                // Change the modal title and button text
                $('#timeEntryModalLabel').text('Edit Time Entry');
                $('#saveTimeEntry').text('Update Time Entry');
            },
            error: function() {
                alert('Error fetching time entry data');
            }
        });
    });
    */
    $(document).on('click', '.editTimerLog', function(e) {
        e.preventDefault();
        var timeEntryId = $(this).data('id');
        
        // Fetch time entry details using AJAX
        $.ajax({
            url: 'cases/fetch_selected_time_entry',
            type: 'POST',
            data: { id: timeEntryId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    // Populate the modal form with fetched data
                    $('#selectCase').val(data.caseId);
                    $('#date').val(data.dateCreated);
                    $('#time').val(data.timeCreated);
                    $('#hours').val(data.hours);
                    $('#minutes').val(data.minutes);
                    $('#selectCurrency').val(data.currency);
                    $('#hourlyRate').val(data.hourlyRate);
                    $('#cost').val(data.cost);
                    $('#description').val(data.description);
                    $('input[name="billableStatus"][value="' + data.billableStatus + '"]').prop('checked', true);
                    
                    // Set the timerId hidden input
                    $('#timerId').val(data.id);
                    
                    // Open the modal
                    $('#timeEntryModal').modal('show');
                    
                    // Change the modal title and button text
                    $('#timeEntryModalLabel').text('Edit Time Entry');
                    $('#saveTimeEntry').text('Update Time Entry');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error fetching time entry data: ' + textStatus);
                console.error('AJAX Error:', errorThrown);
            }
        });
    });


    $(document).on('click', '.deleteTimerLog', function(e) {
        e.preventDefault();
        var timeEntryId = $(this).attr('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed, proceed with deletion
                $.ajax({
                    url: 'cases/delete_selected_time_entry.php',
                    type: 'POST',  // Ensure this is 'POST'
                    data: { id: timeEntryId },
                    dataType:'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    
                });
            }
        });
    });
});




