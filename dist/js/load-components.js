// load-components.js
document.addEventListener("DOMContentLoaded", function() {
    fetch('addon_header')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header').innerHTML = data;
        });

    fetch('addon_nav')
        .then(response => response.text())
        .then(data => {
            document.getElementById('nav').innerHTML = data;
        });

    fetch('addon_footer')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer').innerHTML = data;
        });
});
