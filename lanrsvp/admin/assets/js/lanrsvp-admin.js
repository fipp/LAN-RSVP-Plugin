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

            var data = Object();
            $('form.lanrsvp-event-form').find('input').each(function(){
               data[$(this).attr('name')] = $(this).val();
            });

            data['action']  = ('lanrsvp-event-id' in data ? 'update_event' : 'create_event');

            data['lanrsvp-event-type'] = $('input[name=lanrsvp-event-type]:checked', '.lanrsvp-event-form').val();

            if (typeof(Storage) !== "undefined") {
                var val = sessionStorage.getItem('seats');
                if (val !== null) {
                    data['lanrsvp-event-seatmap'] = JSON.parse(val);
                }
            }

            $.post( ajaxurl, data, function(response) {
                if (response.length > 0) {
                    $('.lanrsvp-error').html(response);
                } else {
                     window.location.replace('?page=lanrsvp');
                }
            });
        });

        // Handling event deletion
        $(".remove-event").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('id');
            var ok = confirm('Are you sure you want to delete this event (' + id + ')? This action cannot be undone.');
            if (ok) {
                $.post( ajaxurl, { action: 'delete_event', event_id: id }, function(response) {
                    if (response.length > 0) {
                        alert(response);
                    } else {
                        window.location.replace('?page=lanrsvp');
                    }
                });
            }
        });
    });
}(jQuery));