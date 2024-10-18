<!-- build:js assets/vendor/js/core.js -->
<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="../assets/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

<!-- Main JS -->
<script src="../assets/js/main.js"></script>

<!-- Page JS -->
<script src="../assets/js/dashboards-analytics.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<!-- Include Trumbowyg JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/trumbowyg.min.js"></script>
<!-- Data Tables -->
<script type="text/javascript" src="../assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../assets/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../assets/js/jszip.min.js"></script>
<script type="text/javascript" src="../assets/js/pdfmake.min.js"></script>
<script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
<script type="text/javascript" src="../assets/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="../assets/js/buttons.print.min.js"></script>


<script src="../assets/js/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="../assets/custom/timeTrack.js"></script>
<script type="text/javascript" src="../assets/custom/timeme2.js"></script>


<script>

    
    const currentPageUrl = getPageNameFromUrl(window.location.href);
    TimeMe.initialize({
        currentPageName: currentPageUrl,
        idleTimeoutInSeconds: 30,
        websocketOptions: {
            websocketHost: "wss://milatucases.com:8080",
            appId: "milatucases.com"
        },
        userId: "<?php echo $userId?>",
        lawFirmId: "<?php echo $lawFirmId ?>"
    });

    TimeMe.callAfterTimeElapsedInSeconds(15, function(){
        console.log("The user has been actively using the page " + currentPageUrl + " for 15 seconds! Let's prompt them with something.");
    });

    let timeSpentOnPage = TimeMe.getTimeOnCurrentPageInSeconds();

    TimeMe.callWhenUserLeaves(function(){
        console.log("User has left after "+timeSpentOnPage +" on page ");
    }, 5);

    TimeMe.callWhenUserReturns(function(){
        console.log("The user has come back!");
    });

    const areasOfInterest = [
        'milestoneModal',
        'matter',
        'reading-matter',
        'Consolidation'
    ];

    areasOfInterest.forEach(area => {
        TimeMe.trackTimeOnElement(area);
    });

    function sendTimeSpentOnArea(area) {
        const timeSpent = TimeMe.getTimeOnElementInSeconds(area);
        console.log(`Time spent on ${area}: ${timeSpent} seconds`);
        if (timeSpent > 0) {
            const data = {
                type: 'INSERT_ACTIVITY_TIME',
                userId: "<?php echo $userId?>",
                lawFirmId: "<?php echo $lawFirmId ?>",
                activity: area,
                timeSpentSeconds: timeSpent,
                pageName: getPageNameFromUrl(window.location.href, true)
            };
            console.log('Sending data:', data);
            TimeMe.websocketSend(JSON.stringify(data));
        }
    }

    // setInterval(() => {
    //     areasOfInterest.forEach(sendTimeSpentOnArea);
    // }, 5 * 60 * 1000);

    // window.addEventListener('beforeunload', () => {
    //     areasOfInterest.forEach(sendTimeSpentOnArea);
    // });

    TimeMe.trackTimeOnElement('Consolidation');
    // some time later...
    let timeSpentOnElement = TimeMe.getTimeOnElementInSeconds('Consolidation');

    TimeMe.callWhenUserLeaves(function(){
        console.log("User has left after "+timeSpentOnElement +" on Element ");
    }, 5);

</script>

