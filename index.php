<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Availability Calendar</title>

    <?php
        require __DIR__ . '/includes.php';
    ?>

    <script>

      //JQuery Selectors
      var $modalEventTitle = $("#eventTitle");
      var $modalEventStartTime = $("#eventStartTime");
      var $modalEventRegion = $("#eventRegion");
      var $modalEventCategory = $("#eventCategory");
      var $modalEventStatus = $("#eventStatus");
      var $modalEventDeadline = $("#eventDeadline");
      var $modalEventClosingDate = $("#eventClosing");
      var $eventModal = $("#eventModal");

      $(function() {

        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
          schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
          plugins: [ 'resourceDayGrid', 'interaction', 'bootstrap' ],
          themeSystem: 'bootstrap',
          buttonText: {
            today:  'Today',
            month:  'Month',
            week:   'Week',
            day:    'Day',
            list:   'List'
          },
          firstDay: 1,
          eventClick: function(info) {
            let event = info['event'];
            $modalEventTitle.html(event.title);
            $modalEventStartTime.html('Date: ' + event.start);
            $modalEventRegion.html('Region: ' + event.extendedProps.region);
            $modalEventRegion.toggle(!!event.extendedProps.region);
            $modalEventCategory.html('Category: ' + event.extendedProps.category);
            $modalEventCategory.toggle(!!event.extendedProps.category);
            $modalEventStatus.html('Status: ' + event.extendedProps.status);
            $modalEventStatus.toggle(!!event.extendedProps.status);
            $modalEventDeadline.html('Booking Deadline: ' + event.extendedProps.booking_deadline);
            $modalEventDeadline.toggle(!!event.extendedProps.booking_deadline);
            $modalEventClosingDate.html('Planned Closing Date: ' + event.extendedProps.planned_closing_date);
            $modalEventClosingDate.toggle(!!event.extendedProps.planned_closing_date);
            $eventModal.modal('toggle');
          }
          // dayRender: function(date) {
          //   var cellDate = new Date(date['date']);
          //   var cellDay = cellDate.getDay();
          //   var element = date['el'];
          //
          //   if (cellDay === 0 || cellDay === 6) {
          //     $(element).append(
          //         '<br/><label>1st Session</label><br/>' +
          //         '<input type="checkbox"/><br/>' +
          //         '<label>2nd Session</label><br/>' +
          //         '<input type="checkbox"/>'
          //     );
          //   } else {
          //     $(element).append(
          //         '<br/><label>PM Session</label><br/>' +
          //         '<input type="checkbox"/><br/>'
          //     );
          //   }
          // },
        });
        calendar.render();

        $.get('/scraper.php', function(returnedData) {
          var rowingEvents = JSON.parse(returnedData);
          Object.values(rowingEvents).forEach(function (item) {
            //if there is no title or no date then do not attempt to render the event
            if (!item['title'] || !item['date']) {
              return;
            }
            calendar.addEvent({
              title: item['title'],
              start: item['date'],
              category: item['category'] || false,
              region: item['region'] || false,
              status: item['status'] || false,
              booking_deadline: item['booking_deadline'] || false,
              planned_closing_date: item['planned_closing_date'] || false,
            });
          });
        });
      });
    </script>

</head>
<body>

    <div id="eventModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span hidden>×</span>
                    </button>
                    <h4 id="eventTitle" class="modal-title"></h4>
                </div>
                <div id="modalBody" class="modal-body">
                    <div class="container">
                        <div class="row">
                            <span id="eventStartTime"></span>
                        </div>
                        <div class="row">
                            <span id="eventRegion"></span>
                        </div>
                        <div class="row">
                            <span id="eventCategory"></span>
                        </div>
                        <div class="row">
                            <span id="eventStatus"></span>
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

    <div id='calendar'></div>

</body>
</html>