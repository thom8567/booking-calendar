<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Availability Calendar</title>

    <?php
        require __DIR__ . '/includes.php';
    ?>

    <script>

      function showCalendar()
      {
        $("#loaderContainer").hide();
        $("body").removeClass("hideOverflow");
      }

      function getTodaysDate()
      {
        var today = new Date();
        return today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();
      }

      function formatDate(dateToFormat)
      {
        var date = new Date(dateToFormat);

        return new Intl.DateTimeFormat('en-GB', {weekday: 'long'}).format(date.getDay())
            + ', ' + date.getDate() + ' '
            + new Intl.DateTimeFormat('en-GB', {month: 'long'}).format(date.getMonth()) + ' '
            + date.getFullYear();
      }

      $(function() {
        //JQuery Selectors
        var $modalEventTitle = $("#eventTitle");
        var $modalEventDate = $("#eventDate");
        var $modalEventRegion = $("#eventRegion");
        var $modalEventCategory = $("#eventCategory");
        var $modalEventStatus = $("#eventStatus");
        var $modalEventDeadline = $("#eventDeadline");
        var $modalEventClosingDate = $("#eventClosing");
        var $eventModal = $("#eventModal");

        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
          schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
          plugins: ['resourceDayGrid', 'interaction', 'bootstrap'],
          themeSystem: 'bootstrap',
          customButtons: {
            nextYearButton: {
              bootstrapFontAwesome: 'fa-angle-double-right',
              click: function() {
                calendar.nextYear();
              }
            },
            prevYearButton: {
              bootstrapFontAwesome: 'fa-angle-double-left',
              click: function() {
                calendar.prevYear();
              }
            }
          },
          header: {
            right: 'prevYearButton,prev,today,next,nextYearButton',
          },
          buttonText: {
            today:  'Today: ' + getTodaysDate(),
            month:  'Month',
            week:   'Week',
            day:    'Day',
            list:   'List'
          },
          firstDay: 1,
          eventOrder: function(a, b) {
            let statusA = a['status'];
            let statusB = b['status'];
            let priorities = {
              'Cancelled': 10,
              'Suspended': 9,
              'Fully Closed': 8,
              'Closed for Entries': 7,
              'Closed for Bookings': 6,
              'Open for Entries': 5,
              'Accepting Bookings': 4,
              'Planning': 3
            };
            if (!(statusA in priorities) || !(statusB in priorities)){
              return -1;
            }
            let aPriority = priorities[statusA];
            let bPriority = priorities[statusB];
            if (aPriority > bPriority) {
              return -1;
            }
            if (aPriority < bPriority) {
              return 1;
            }
          },
          eventClick: function(info) {
            let event = info['event'];
            $modalEventTitle.html(event.title);

            $modalEventStatus.html('Status: ' + event.extendedProps.status);
            $modalEventStatus.toggle(!!event.extendedProps.status);

            $modalEventDate.html('Date: ' + event.extendedProps.dateOfEvent);

            $modalEventRegion.html('Region: ' + event.extendedProps.region);
            $modalEventRegion.toggle(!!event.extendedProps.region);

            $modalEventCategory.html('Category: ' + event.extendedProps.category);
            $modalEventCategory.toggle(!!event.extendedProps.category);

            $modalEventDeadline.html('Booking Deadline: ' + event.extendedProps.booking_deadline);
            $modalEventDeadline.toggle(!!event.extendedProps.booking_deadline);

            $modalEventClosingDate.html('Planned Closing Date: ' + event.extendedProps.planned_closing_date);
            $modalEventClosingDate.toggle(!!event.extendedProps.planned_closing_date);

            $eventModal.modal();
          },
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
          dateClick: function(info) {
            let element = info['dayEl'];
            if ($('.fa-check').length) {
              $('.fa-check').remove();
              $(element).append(
                '<div class="d-flex justify-content-center align-items-center">' +
                  '<i class="fas fa-times fa-9x availability-marker"></i>' +
                '</div>'
              )
            } else if ($('.fa-times').length) {
              $('.fa-times').remove();
            } else {
              $(element).append(
                '<div class="d-flex justify-content-center align-items-center">' +
                  '<i class="fas fa-check fa-9x availability-marker"></i>' +
                '</div>'
              );
            }
          },
        });
        calendar.render();

        $.get('/scraper.php', function(returnedData) {
          var colour = '';
          var textColour = 'black';
          var rowingEvents = JSON.parse(returnedData);
          Object.values(rowingEvents).forEach(function (item) {
            //if there is no title or no date then do not attempt to render the event
            if (!item['title'] || !item['date']) {
              return;
            }
            switch (item['status']) {
              case 'Cancelled':
                colour = '#cc0000';
                textColour = 'white';
                break;
              case 'Suspended':
                colour = '#ffa31a';
                textColour = 'black';
                break;
              case 'Closed for Bookings':
                colour = '#003e80';
                textColour = 'white';
                break;
              case 'Closed for Entries':
                colour = '#ff1a1a';
                textColour = 'black';
                break;
              case 'Accepting Bookings':
                colour = '#007bff';
                textColour = 'white';
                break;
              case 'Open for Entries':
                colour = '#009933';
                textColour = 'black';
                break;
              case 'Fully Closed':
                colour = '#801a00';
                textColour = 'white';
                break;
              case 'Planning':
                colour = 'lightGrey';
                textColour = 'black';
                break;
              default:
                colour = '#007bff';
                textColour = 'white';
                break;
            }
            calendar.addEvent({
              title: item['title'],
              start: item['date'],
              dateOfEvent: formatDate(item['date']),
              category: item['category'] || false,
              region: item['region'] || false,
              status: item['status'] || false,
              booking_deadline: item['booking_deadline'] || false,
              planned_closing_date: item['planned_closing_date'] || false,
              color: colour,
              textColor: textColour,
              editable: false,
            });
          });
          showCalendar();
        });
      });
    </script>

</head>
<body class="hideOverflow">

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