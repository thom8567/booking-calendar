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

    <div id="top-menu" class="container top-menu topMenuOptions">
        <div class="row">
            <div class="col-sm-3">
                <div class="row">
                    <span>Remove all placed markers for the month</span>
                </div>
                <div class="row">
                    <button class="btn btn-danger" type="button" data-type="reset">Reset</button>
                </div>
            </div>

            <!-- Admin only -->
            <div class="col-sm-3" id="admin-buttons"></div>
        </div>
    </div>

    <script id="admin-buttons-templates" type="text/x-handlebars-template">
        <div class="row">
            <span>Update all events in the Database</span>
        </div>
        <div class="row">
            <button class="btn btn-success" type="button" data-type="updateEvents">Update Events</button>
        </div>
    </script>

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