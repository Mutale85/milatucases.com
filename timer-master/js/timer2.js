/* 
 * This file contains the logic of the Timer application. It may seem complex,
 * but it strictly follows the MVC pattern, which dictates to separate model
 * (the Project and Projects classes) from the code manipulating the HTML.
 */

/* ===== Project ===== */

function Project(name) {
  this._name = name;
  this._state = Project.State.STOPPED;
  this._timeSpentInPreviousIterations = 0;
  this._currentIterationStartTime = 0;
  this._onChange = null;
}

Project.State = {
  STOPPED: "stopped",
  RUNNING: "running"
}

Project.prototype = {
  /* Returns the project name. */
  getName:  function() {
    return this._name;
  },

  /* Returns the project state. */
  getState: function() {
    return this._state;
  },

  /* Is the project stopped? */
  isStopped: function() {
    return this._state == Project.State.STOPPED;
  },

  /* Is the project running? */
  isRunning: function() {
    return this._state == Project.State.RUNNING;
  },

  /*
   * Sets the "onChange" event handler. The "onChange" event is fired when the
   * project is started, stopped, or reset.
   */
  setOnChange: function(onChange) {
    this._onChange = onChange;
  },

  /*
   * Returns the time spent on the project in the current work iteration. Works
   * correctly only when the project is running.
   */
  _getCurrentIterationTime: function() {
    return (new Date).getTime() - this._currentIterationStartTime;
  },

  /*
   * Returns the total time spent on the project. This includes time spent in
   * the current work iteration if the project is running.
   */
  getTimeSpent: function() {
    var result = this._timeSpentInPreviousIterations;
    if (this._state == Project.State.RUNNING) {
      result += this._getCurrentIterationTime();
    }
    return result;
  },

  /* Calls the "onChange" event handler if set. */
  _callOnChange: function() {
    if (typeof this._onChange == "function") {
      this._onChange();
    }
  },

  /* Starts a new project work iteration. */
  start: function() {
    if (this._state == Project.State.RUNNING) { return };

    this._state = Project.State.RUNNING;
    this._currentIterationStartTime = (new Date).getTime();
    this._callOnChange();
  },

  /* Stops the current project work iteration. */
  stop: function() {
    if (this._state == Project.State.STOPPED) { return };

    this._state = Project.State.STOPPED;
    this._timeSpentInPreviousIterations += this._getCurrentIterationTime();
    this._currentIterationStartTime = 0;
    this._callOnChange();
  },

  /* Stops the current project work iteration and resets the time data. */
  reset: function() {
    this.stop();
    this._timeSpentInPreviousIterations = 0;
    this._callOnChange();
  },

  /* Serializes the project into a string. */
  serialize: function() {
    /*
     * Originally, I wanted to use "toSource" and "eval" for serialization and
     * deserialization, but "toSource" is not supported by WebKit, so I resorted
     * to ugly hackery...
     */
    return [
      encodeURIComponent(this._name),
      this._state,
      this._timeSpentInPreviousIterations,
      this._currentIterationStartTime
    ].join("&");
  },

  /* Deserializes the project from a string. */
  deserialize: function(serialized) {
    var parts = serialized.split("&");

    this._name                          = decodeURIComponent(parts[0]);
    this._state                         = parts[1];
    this._timeSpentInPreviousIterations = parseInt(parts[2]);
    this._currentIterationStartTime     = parseInt(parts[3]);
  },

  // Update getTimeSpent to include milliseconds
  getTimeSpent: function() {
    var result = this._timeSpentInPreviousIterations;
    if (this._state == Project.State.RUNNING) {
      result += this._getCurrentIterationTime();
    }
    return result;
  },
}

/* ===== Projects ===== */

function Projects() {
  this._projects = [];
  this._onAdd = null;
  this._onRemove = null;
}

Projects.prototype = {
  /*
   * Sets the "onAdd" event handler. The "onAdd" event is fired when a project
   * is added to the list.
   */
  setOnAdd: function(onAdd) {
    this._onAdd = onAdd;
  },

  /*
   * Sets the "onRemove" event handler. The "onRemove" event is fired when a
   * project is removed from the list.
   */
  setOnRemove: function(onRemove) {
    this._onRemove = onRemove;
  },

  /* Returns the length of the project list. */
  length: function() {
    return this._projects.length
  },

  /*
   * Returns index-th project in the list, or "undefined" if the index is out of
   * bounds.
   */
  get: function(index) {
    return this._projects[index];
  },

  /*
   * Calls the callback function for each project in the list. The function is
   * called with three parameters - the project, its index and the project list
   * object. This is modeled after "Array.forEach" in JavaScript 1.6.
   */
  forEach: function(callback) {
    for (var i = 0; i < this._projects.length; i++) {
      callback(this._projects[i], i, this);
    }
  },

  /* Calls the "onAdd" event handler if set. */
  _callOnAdd: function(project) {
    if (typeof this._onAdd == "function") {
      this._onAdd(project);
    }
  },

  /* Adds a new project to the end of the list. */
  add: function(project) {
    this._projects.push(project);
    this._callOnAdd(project);
  },

  /* Calls the "onRemove" event handler if set. */
  _callOnRemove: function(index) {
    if (typeof this._onRemove == "function") {
      this._onRemove(index);
    }
  },

  /*
   * Removes index-th project from the list. Does not do anything if the index
   * is out of bounds.
   */
  remove: function(index) {
    this._callOnRemove(index);
    this._projects.splice(index, 1);
  },

  /* Serializes the list of projects into a string. */
  serialize: function() {
    var serializedProjects = [];
    this.forEach(function(project) {
      serializedProjects.push(project.serialize());
    });
    return serializedProjects.join("|");
  },

  /* Deserializes the list of projects from a string. */
  deserialize: function(serialized) {
    /*
     * Repeatedly use "remove" so the "onRemove" event is triggered. Do the same
     * with the "add" method below.
     */
    while (this._projects.length > 0) {
      this.remove(0);
    }

    var serializedProjects = serialized.split("|");
    for (var i = 0; i < serializedProjects.length; i++) {
      var project = new Project("");
      project.deserialize(serializedProjects[i]);
      this.add(project);
    }
  }
}

/* ===== Extensions ===== */

String.prototype.pad = function(length, padding) {
  var result = this;
  while (result.length < length) {
    result = padding + result;
  }
  return result;
}

/* ===== Project List + DOM Storage ===== */

var projects = new Projects();
var lastSerializedProjectsString;
var PROJECTS_DOM_STORAGE_KEY = "timerProjects";

function getStorage() {
  if (window.localStorage !== undefined) {
    return window.localStorage;
  } else if (window.globalStorage !== undefined) {
    return window.globalStorage[location.hostname];
  } else {
    return null;
  }
}

function saveProjects() {
  var serializedProjectsString = projects.serialize();
  getStorage()[PROJECTS_DOM_STORAGE_KEY] = serializedProjectsString;
  lastSerializedProjectsString = serializedProjectsString;
}

function loadSerializedProjectsString() {
  var storedValue = getStorage()[PROJECTS_DOM_STORAGE_KEY];
  if (storedValue !== null && storedValue !== undefined) {
    return (window.localStorage === undefined) ? storedValue.value : storedValue;
  } else {
    return undefined;
  }
}

function loadProjects() {
  var serializedProjectsString = loadSerializedProjectsString();
  if (serializedProjectsString !== undefined) {
    projects.deserialize(serializedProjectsString);
    lastSerializedProjectsString = serializedProjectsString;
  }
}

function projectsHaveChangedOutsideApplication() {
  return loadSerializedProjectsString() != lastSerializedProjectsString;
}

/* ===== View ===== */

var MILISECONDS_IN_SECOND = 1000;
var MILISECONDS_IN_MINUTE = 60 * MILISECONDS_IN_SECOND;
var MINUTES_IN_HOUR = 60;

function formatTime(time) {
  var timeInSeconds = Math.floor(time / 1000);
  var hours = Math.floor(timeInSeconds / 3600);
  var minutes = Math.floor((timeInSeconds % 3600) / 60);
  var seconds = timeInSeconds % 60;
  var milliseconds = time % 1000;
  return hours + ":" + String(minutes).pad(2, "0") + ":" + String(seconds).pad(2, "0") + "." + String(milliseconds).pad(3, "0");
}

function computeStartStopLinkImageUrl(state) {
  switch (state) {
    case Project.State.STOPPED:
      return "img/start.png";
    case Project.State.RUNNING:
      return "img/stop.png";
    default:
      throw "Invalid project state."
  }
}

function buildProjectRow(project, index) {
  var result = $("<tr />");

  var startStopLink = $(
    "<a href='#' class='start-stop-link' title='Start/stop'>"
    + "<img src='" + computeStartStopLinkImageUrl(project.getState()) + "' width='16' height='16' alt='Start/stop' />"
    + "</a>"
  );
  startStopLink.click(function() {
    switch (project.getState()) {
      case Project.State.STOPPED:
        project.start();
        break;
      case Project.State.RUNNING:
        project.stop();
        break;
      default:
        throw "Invalid project state."
    }
    saveProjects();
    return false;
  });

  var resetLink = $(
    "<a href='#' title='Reset'>"
    + "<img src='img/reset.png' width='16' height='16' alt='Reset' />"
    + "</a>"
  );
  resetLink.click(function() {
    project.reset();
    saveProjects();
    return false;
  });

  var deleteLink = $(
    "<a href='#' title='Delete'>"
    + "<img src='img/delete.png' width='16' height='16' alt='Delete' />"
    + "</a>"
  );
  deleteLink.click(function() {
    if (confirm("Do you really want to delete project \"" + project.getName() + "\"?")) {
      projects.remove(index);
      saveProjects();
    }
    return false;
  });

  result
    .addClass("state-" + project.getState())
    .append($("<td class='project-name' />").text(project.getName()))
    .append($("<td class='project-time' />").text(formatTime(project.getTimeSpent())))
    .append($("<td class='project-actions' />")
      .append(startStopLink)
      .append(resetLink)
      .append("&nbsp;&nbsp;")
      .append(deleteLink)
    );

  return result;
}

function findRowWithIndex(index) {
  return $("#project-table").find("tr").slice(1).eq(index);
}

function updateProjectRow(row, project) {
  if (project.isStopped()) {
    row.removeClass("state-running");
    row.addClass("state-stopped");
  } else if (project.isRunning()) {
    row.removeClass("state-stopped");
    row.addClass("state-running");
  }

  row.find(".project-time").text(formatTime(project.getTimeSpent()))
  row.find(".start-stop-link img").attr(
    "src",
    computeStartStopLinkImageUrl(project.getState())
  );

  // Add submit button when stopped
  if (project.isStopped()) {
    if (row.find(".submit-button").length === 0) {
      var submitButton = $("<button class='submit-button'>Submit</button>");
      submitButton.click(function() {
        submitToPhp(project);
      });
      row.find(".project-actions").append(submitButton);
    }
  } else {
    row.find(".submit-button").remove();
  }
}

function submitToPhp(project) {
  $.ajax({
    url: 'submit_time.php',
    method: 'POST',
    data: {
      projectName: project.getName(),
      timeSpent: project.getTimeSpent()
    },
    success: function(response) {
      alert('Time submitted successfully!');
    },
    error: function() {
      alert('Error submitting time.');
    }
  });
}

/* ===== Initialization ===== */

function initializeProjectsEventHandlers() {
  projects.setOnAdd(function(project) {
    var row = buildProjectRow(project, projects.length() - 1);
    $("#project-table").append(row);
    project.setOnChange(function() {
      updateProjectRow(row, project);
    });
  });

  projects.setOnRemove(function(index) {
    findRowWithIndex(index).remove();
  });
}

function initializeGuiEventHandlers() {
  $("#add-project-button").removeAttr("disabled");
  $("#add-project-button").click(function() {
    var projectName = prompt("Enter project name:", "");
    if (projectName === null) { return; }

    var project = new Project(projectName);
    projects.add(project);
    saveProjects();
  });
}

function initializeTimer() {
  setInterval(function() {
    projects.forEach(function(project, index) {
      updateProjectRow(findRowWithIndex(index), project);
    });

    if (projectsHaveChangedOutsideApplication()) {
      loadProjects();
    }
  }, 100); // Update every 100ms for smoother display
}

$(document).ready(function(){
  try {
    if (!getStorage()) {
      alert("Timer requires a browser with DOM Storage support, such as Firefox 3+ or Safari 4+.");
      return;
    }
  } catch (e) {
    alert("Timer does not work with file: URLs in Firefox.");
    return;
  }

  initializeProjectsEventHandlers();
  loadProjects();
  initializeGuiEventHandlers();
  initializeTimer();
});