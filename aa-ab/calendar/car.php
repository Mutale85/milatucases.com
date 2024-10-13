<div class="calendar-container">
  <div class="calendar-month-arrow-container">
    <div class="calendar-month-year-container">
      <select class="calendar-years"></select>
      <select class="calendar-months">
      </select>
    </div>
    <div class="calendar-month-year">
    </div>
    <div class="calendar-arrow-container">
      <button class="calendar-today-button"></button>
      <button class="calendar-left-arrow">← </button>
      <button class="calendar-right-arrow"> →</button>
    </div>
  </div>
  <ul class="calendar-week">
  </ul>
  <ul class="calendar-days">
  </ul>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">New Event</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"></span>
        </button>
      </div>
      <form id="eventForm">
        <div class="modal-body">
          
            <div class="form-group mb-3">
                <label class="mb-2" for="eventName">Event Name</label>
                <input type="text" class="form-control" id="eventName" name="eventName" required>
            </div>
            <div class="form-group mb-3">
                <label class="mb-2" for="eventDetails">Details</label>
                <textarea class="form-control" id="eventDetails" name="eventDetails" required></textarea>
            </div>
            <div class="form-group mb-3">
                <label class="mb-2" for="eventDate">Start Date</label>
                <input type="date" class="form-control" id="eventDate" name="eventDate" required>
            </div>
            <div class="form-group mb-3">
                <label class="mb-2" for="endDate">End Date</label>
                <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>
            <div class="form-group mb-3">
                <label class="mb-2" for="runningDays">Running Days</label>
                <input type="text" class="form-control" id="runningDays" name="runningDays" readonly>
            </div>
            <input type="hidden" name="id" id="event_id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveEvent">Create Event</button>
        </div>
      </form>
    </div>
  </div>
</div>

<button type="button" class="btn btn-primary" id="eventBtn" data-bs-toggle="modal" data-bs-target="#eventModal" style="display: none;">
</button>


<!-- Event Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventDetailModalLabel">Event Details</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="eventDetail"></p>
      </div>
    </div>
  </div>
</div>

<button type="button" class="btn btn-primary" id="eventDetailBtn" data-bs-toggle="modal" data-bs-target="#eventDetailModal" style="display: none;">
</button>

<style>
  @import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");

  * {
    box-sizing: border-box;
    padding: 0;
    margin: 0;
  }

  .calendar-container {
    height: auto;
    width: 100%;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0px 0px 20px rgba(255, 255, 255, 0.4);
    padding: 15px 10px;
    border: 1px solid gray;
  }

  .calendar-week {
    display: flex;
    list-style: none;
    align-items: center;
    padding-inline-start: 0px;
  }

  .calendar-week-day {
    max-width: 57.1px;
    width: 100%;
    text-align: center;
    color: #525659;
  }

  .calendar-days {
    margin-top: 30px;
    list-style: none;
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 20px;
    padding-inline-start: 0px;
    cursor: pointer;
  }

  .calendar-day {
    text-align: center;
    color: #525659;
    padding: 10px;
  }

  .calendar-month-arrow-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .calendar-month-year-container {
    padding: 10px 10px 20px 10px;
    color: #525659;
    cursor: pointer;
  }

  .calendar-arrow-container {
    margin-top: -5px;
  }

  .calendar-left-arrow,
  .calendar-right-arrow {
    height: 30px;
    width: 30px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    color: #525659;
  }

  .calendar-today-button {
    margin-top: -10px;
    border-radius: 10px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    color: #525659;
    padding: 2.5px 13px;
  }

  .calendar-today-button {
    height: 27px;
    margin-right: 10px;
    background-color: #ec7625;
    color: white;
  }

  .calendar-months,
  .calendar-years {
    flex: 1;
    border-radius: 10px;
    height: 30px;
    border: none;
    cursor: pointer;
    outline: none;
    color: #525659;
    font-size: 15px;
  }

  .calendar-day-active {
    background-color: #ec7625;
    color: white;
    border-radius: 50%;
  }

  .event-day {
    background-color: green;
    color: white;
  }

  @media (max-width: 768px) {
    .calendar-container {
      height: auto;
      width: 100%;
      background-color: white;
      box-shadow: none;
      padding: 7px 5px;
      border: none;
      margin-left: -10px;
    }
    .calendar-week-day, .calendar-day {
      font-size: 12px;
    }

    .calendar-arrow-container, .calendar-today-button, .calendar-months, .calendar-years {
      font-size: 14px; 
    }

    .calendar-days {
      grid-template-columns: repeat(7, 1fr);
      gap: 10px; /* Smaller gap */
    }

    .row > .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%; /* Stack columns vertically on smaller screens */
    }
  }
</style>

<script>
  const weekArray = ["Sun", "Mon", "Tue", "Wed", "Thr", "Fri", "Sat"];
  const monthArray = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  ];
  const current = new Date();
  const todaysDate = current.getDate();
  const currentYear = current.getFullYear();
  const currentMonth = current.getMonth();

  let eventDates = [];
  
  window.onload = function () {

    fetch('papa/fetch_events')
      .then(response => response.json())
      .then(data => {
          eventDates = data;
          generateCalendarDays(new Date()); // Call this with the current date
      });
    const currentDate = new Date();
    generateCalendarDays(currentDate);

    let calendarWeek = document.getElementsByClassName("calendar-week")[0];
    let calendarTodayButton = document.getElementsByClassName(
      "calendar-today-button"
    )[0];
    calendarTodayButton.textContent = `Today ${todaysDate}`;

    calendarTodayButton.addEventListener("click", () => {
      generateCalendarDays(currentDate);
    });

    weekArray.forEach((week) => {
      let li = document.createElement("li");
      li.textContent = week;
      li.classList.add("calendar-week-day");
      calendarWeek.appendChild(li);
    });

    const calendarMonths = document.getElementsByClassName("calendar-months")[0];
    const calendarYears = document.getElementsByClassName("calendar-years")[0];
    const monthYear = document.getElementsByClassName("calendar-month-year")[0];

    const selectedMonth = parseInt(monthYear.getAttribute("data-month") || 0);
    const selectedYear = parseInt(monthYear.getAttribute("data-year") || 0);

    monthArray.forEach((month, index) => {
      let option = document.createElement("option");
      option.textContent = month;
      option.value = index;
      option.selected = index === selectedMonth;
      calendarMonths.appendChild(option);
    });

    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 60;
    const endYear = currentYear + 60;
    let newYear = startYear;
    while (newYear <= endYear) {
      let option = document.createElement("option");
      option.textContent = newYear;
      option.value = newYear;
      option.selected = newYear === selectedYear;
      calendarYears.appendChild(option);
      newYear++;
    }

    const leftArrow = document.getElementsByClassName("calendar-left-arrow")[0];

    leftArrow.addEventListener("click", () => {
      const monthYear = document.getElementsByClassName("calendar-month-year")[0];
      const month = parseInt(monthYear.getAttribute("data-month") || 0);
      const year = parseInt(monthYear.getAttribute("data-year") || 0);

      let newMonth = month === 0 ? 11 : month - 1;
      let newYear = month === 0 ? year - 1 : year;
      let newDate = new Date(newYear, newMonth, 1);
      generateCalendarDays(newDate);
    });

    const rightArrow = document.getElementsByClassName("calendar-right-arrow")[0];

    rightArrow.addEventListener("click", () => {
      const monthYear = document.getElementsByClassName("calendar-month-year")[0];
      const month = parseInt(monthYear.getAttribute("data-month") || 0);
      const year = parseInt(monthYear.getAttribute("data-year") || 0);
      let newMonth = month + 1;
      newMonth = newMonth === 12 ? 0 : newMonth;
      let newYear = newMonth === 0 ? year + 1 : year;
      let newDate = new Date(newYear, newMonth, 1);
      generateCalendarDays(newDate);
    });

    calendarMonths.addEventListener("change", function () {
      let newDate = new Date(calendarYears.value, calendarMonths.value, 1);
      generateCalendarDays(newDate);
    });

    calendarYears.addEventListener("change", function () {
      let newDate = new Date(calendarYears.value, calendarMonths.value, 1);
      generateCalendarDays(newDate);
    });
  };
  
  function generateCalendarDays(currentDate, eventsData = eventDates) {
    const newDate = new Date(currentDate);
    const year = newDate.getFullYear();
    const month = newDate.getMonth();
    const totalDaysInMonth = getTotalDaysInAMonth(year, month);
    const firstDayOfWeek = getFirstDayOfWeek(year, month);
    let calendarDays = document.getElementsByClassName("calendar-days")[0];

    removeAllChildren(calendarDays);

    let firstDay = 1;
    while (firstDay <= firstDayOfWeek) {
      let li = document.createElement("li");
      li.classList.add("calendar-day");
      calendarDays.appendChild(li);
      firstDay++;
    }

    let day = 1;
    while (day <= totalDaysInMonth) {
        let li = document.createElement("li");
        li.textContent = day;
        li.classList.add("calendar-day");

        let fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        let event = eventDates.find(e => e.event_date === fullDate);

        if (event) {
            li.classList.add("event-day");
            li.setAttribute('data-has-event', 'true');
            li.addEventListener('click', () => showEventDetails(event));
        } else {
            li.addEventListener('click', (function(currentDay) {
                return function() {
                    openModal(year, month, currentDay);
                }
            })(day));
        }

        calendarDays.appendChild(li);
        day++;
    }

    const monthYear = document.getElementsByClassName("calendar-month-year")[0];
    monthYear.setAttribute("data-month", month);
    monthYear.setAttribute("data-year", year);
    const calendarMonths = document.getElementsByClassName("calendar-months")[0];
    const calendarYears = document.getElementsByClassName("calendar-years")[0];
    calendarMonths.value = month;
    calendarYears.value = year;
  }

  function getTotalDaysInAMonth(year, month) {
    return new Date(year, month + 1, 0).getDate();
  }

  function getFirstDayOfWeek(year, month) {
    return new Date(year, month, 1).getDay();
  }

  function removeAllChildren(parent) {
    while (parent.firstChild) {
      parent.removeChild(parent.firstChild);
    }
  }

  function showEventDetails(event) {
    // $('#eventDetailBtn').click();
    $("#eventDetailModal").modal("show");
    $('#eventDetailModalLabel').text(event.name);
    $('#eventDetail').text(event.details);
    // Set data attributes for edit and delete
    $('#editEventButton').attr('data-id', event.id);
    $('#deleteEventButton').attr('data-id', event.id);
    resetForm();
  }

  function openModal(year, month, day) {
    let displayMonth = month + 1;
    let formattedDate = `${year}-${displayMonth.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    resetForm();
    let dayElement = document.querySelector(`.calendar-day[data-has-event][data-date="${formattedDate}"]`);
    if (!dayElement) {
        $('#eventBtn').click();
        $('#eventModalLabel').text(`New Event for ${formattedDate}`);
        $("#eventDate").val(formattedDate);
    }
  }

  function resetForm() {
    $('#eventForm')[0].reset();
    $('#event_id').val('');
  }


  document.addEventListener("DOMContentLoaded", function () {
        var eventDateInput = document.getElementById("eventDate");
        var endDateInput = document.getElementById("endDate");
        var runningDaysInput = document.getElementById("runningDays");
        endDateInput.addEventListener("change", function () {
            var eventDate = new Date(eventDateInput.value);
            var endDate = new Date(endDateInput.value);
            var timeDiff = Math.abs(endDate.getTime() - eventDate.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // Add 1 to include both start and end days
            runningDaysInput.value = diffDays;
        });

        eventDateInput.addEventListener("change", function () {
            var selectedDate = new Date(eventDateInput.value);
            endDateInput.min = eventDateInput.value;
            endDateInput.disabled = false;
            endDateInput.value = eventDateInput.value;
            runningDaysInput.value = 1; 
            
            var today = new Date();
            if (selectedDate <= today) {
                endDateInput.disabled = true;
            }
        });
    });

</script>