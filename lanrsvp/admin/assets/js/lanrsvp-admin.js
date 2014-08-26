(function ( $ ) {
    "use strict";
    $(function () {

        var debug = true;
        function writeDebug (message) {
            if (debug) {
                console.log(message);
            }
        }

        // Listener for the radio button switching between the event holding a seat map or not
        $("input[name='lanrsvp-event-type']").change(function(){
            writeDebug("changed input[name='lanrsvp-event-type']");
            $('#lanrsvp-seatmap-wrapper').toggle();
            $('#lanrsvp-maxlimit').toggle();
        });

        // Handling event form submit
        $("form.lanrsvp-event-form").submit(function(e) {
            e.preventDefault();

            var data = {
                title : $('input[name="lanrsvp-event-title"]').val(),
                start_date : $('input[name="lanrsvp-event-startdate"]').val(),
                end_date: $('input[name="lanrsvp-event-enddate"]').val()
            };

            var type = $('input[name=lanrsvp-event-type]:checked', '.lanrsvp-event-form').val();
            data['type'] = type;
            if (type === 'seatmap') {
                if (typeof(Storage) !== "undefined") {
                    data['seatmap'] = JSON.parse(sessionStorage.getItem('seats'));
                } else {
                    console.log('Error! browser does not support sessionStorage. Event creation cannot continue.');
                }
            } else if (type === 'general') {
                data['min_attendees'] = $('input[name="lanrsvp-attendees-min-number"]').val();
                data['max_attendees'] = $('input[name="lanrsvp-attendees-max-number"]').val();
            } else {
                return;
            }

            data['action'] = 'create_event';

            $.post( ajaxurl, data, function(response) {
                console.log(response);
            });
        });

        // Handling event deletion
        $(".remove-event").click(function(e) {
            e.preventDefault();
            confirm('Are you sure you want to delete this event? This action cannot be undone.');

        });



    });
}(jQuery));