(function() {
  function getEvents(callback) {
    $.get('/retrieveEvents.php', function(returnedData) {

      if ('fail' === JSON.parse(returnedData)) {
        alert('Event saving has failed!');
      } else {
        callback(JSON.parse(returnedData));
      }

    }).fail(function() {
      alert('Events could not be retrieved!')
    });
  }

  function getColoursFromStatus(status) {
    const MAP = {
      'Cancelled': ['#cc0000', 'white'],
      'Suspended': ['#ffa31a', 'black'],
      'Closed for Bookings': ['#003e80', 'white'],
      'Closed for Entries': ['#ff1a1a', 'black'],
      'Accepting Bookings': ['#007bff', 'white'],
      'Open for Entries': ['#009933', 'black'],
      'Fully Closed': ['#801a00', 'white'],
      'Planning': ['#d1e0e0', 'black'],
      '_default': ['#007bff', 'white']
    };

    return MAP[status] || MAP._default;
  }

  function addEventsToCalendar(events) {
    Object.values(events).forEach(function (item) {
      //if there is no title or no date then do not attempt to render the event
      if (!item['title'] || !item['date']) {
        return;
      }
      const [
        colour,
        textColour
      ] = getColoursFromStatus(item['status']);
      calendar.addEvent({
        title: item['title'],
        start: item['date'],
        dateOfEvent: convertToLongDate(item['date']),
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
  }

  function getTodaysDate() {
    let today = dayjs(new Date());

    return today.format('DD/MM/YYYY');
  }

  function convertToLongDate(dateToFormat) {
    let date = dayjs(new Date(dateToFormat));

    return date.format('dddd, D MMMM YYYY');
  }
  var calendar;

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
    var $resetButton = $("[data-type='reset']");
    var $updateEventsButton = $("[data-type='updateEvents']");
    var $topMenu = $("#top-menu");

    var calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
      schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
      plugins: ['resourceDayGrid', 'interaction', 'bootstrap'],
      themeSystem: 'bootstrap',
      customButtons: {
        topMenuButton: {
          bootstrapFontAwesome: 'fa-bars',
          click: function() {
            //open top menu to show option buttons
            if ($topMenu.is(":visible")) {
              $topMenu.slideUp();
            } else {
              $topMenu.slideDown();
            }
          }
        }
      },
      header: {
        left: 'topMenuButton',
        center: 'title',
        right: 'prevYear,prev,today,next,nextYear',
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
      dateClick: function(info) {
        let element = info['dayEl'];
        let date = info['dateStr'];
        let clickedDate = new Date(date);
        let calendarDate = calendar.getDate();
        let $crossSelector = $('[data-date="' + date + '"].fa-times');
        let $tickSelector = $('[data-date="' + date + '"].fa-check');

        if (clickedDate < calendarDate) {
          return;
        }
        //On first click add a Tick
        //On second click remove Tick and add a cross
        //On third click remove the cross to leave the day blank
        if ($tickSelector.length) {
          $tickSelector.remove();
          $(element).append(
              '<i class="fas fa-times fa-2x availabilityMarker" data-date=' + date + '></i>'
          );
        } else if ($crossSelector.length) {
          $crossSelector.remove();
        } else {
          $(element).append(
              '<i class="fas fa-check fa-2x availabilityMarker" data-date=' + date + '></i>'
          );
        }
      },
    });
    calendar.render();

    $resetButton.click(function() {
      let date = dayjs(calendar.getDate()).format('YYYY-MM');
      let $markerSelector = $(`[data-date*="${date}"].availabilityMarker`);
      $markerSelector.remove();

      if ($markerSelector.length) {
        alertify.error('Calendar selections have not been reset')
      }
      alertify.success('Calendar selections have been reset');
    });

    $updateEventsButton.click(function() {
      $topMenu.hide();
      $('html').addClass('loading');

      getEvents(function(events) {
        addEventsToCalendar(events);
        $("#loaderContainer").hide();
        $("html").removeClass("loading");
      });
    })
  });
}());