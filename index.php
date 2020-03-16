<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Availability Calendar</title>

    <?php
        require __DIR__ . '/includes/includes.php';
    ?>

</head>
<body>

    <div id="top-menu" class="container-fluid top-menu">
        <div id="topMenuOptions" class="row">
            <div class="column">
                <button class="btn btn-danger" type="button" data-type="reset">Reset</button>
                <label>Click the button below to update the events in the database</label>
                <button class="btn btn-success" type="button" data-type="updateEvents">Update Events</button>
            </div>
        </div>
    </div>

    <div id="eventModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">
                        <span id="eventTitle"></span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>×</span>
                    </button>
                </div>
                <div id="modalBody" class="modal-body">
                    <div class="container">
                        <div class="row">
                            <span id="eventStatus"></span>
                        </div>
                        <div class="row">
                            <span id="eventDate"></span>
                        </div>
                        <div class="row">
                            <span id="eventRegion"></span>
                        </div>
                        <div class="row">
                            <span id="eventCategory"></span>
                        </div>
                        <div class="row">
                            <span id="eventDeadline"></span>
                        </div>
                        <div class="row">
                            <span id="eventClosing"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loaderContainer">
        <div id="loaderBackground"></div>
        <div id="loader"></div>
    </div>

    <div id="calendar"></div>

</body>
</html>