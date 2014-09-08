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
            data['lanrsvp-event-status'] = $('input[name=lanrsvp-event-status]:checked', '.lanrsvp-event-form').val();

            if (data['lanrsvp-event-type'] === 'seatmap' && window.seats !== undefined) {
                // Crop the seats which are outside the limits
                var foundSeat = false;
                for (var row = 0; row < window.seats.length; row++) {
                    if (window.seatmapRowSize < row + 1) {
                        delete window.seats[row];
                    } else if (window.seats[row] !== undefined) {
                        for (var col = 0; col < window.seats[row].length; col++) {
                            if (window.seatmapColSize < col + 1) {
                                delete window.seats[row][col];
                            }
                            if (window.seats[row][col] !== undefined) {
                                foundSeat = true;
                            }
                        }
                    }
                }

                if (!foundSeat) {
                    alert("No seats defined!");
                    return;
                }

                data['lanrsvp-event-seatmap'] = window.seats;
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
        $(".delete-event").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('id');
            var ok = confirm('Are you sure you want to delete this event along with all its attendees?');
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

        // Handling attendee deletion
        $("a.delete-attendee").click(function(e) {
            e.preventDefault();
            var user_id = $(this).attr('id');
            var ok = confirm('Are you sure you want to delete this attendee?');
            if (ok) {
                var data = {
                    action: 'delete_attendee',
                    user_id: user_id,
                    event_id: LanRsvpAdmin.event_id
                };

                $.post( ajaxurl, data, function(response) {
                    if (response.length > 0) {
                        alert(response);
                    } else {
                        window.location.replace('?page=lanrsvp_attendees&event_id=' + LanRsvpAdmin.event_id);
                    }
                });
            }
        });

        // Handling attendees comments
        $('#saveAttendees').click(function() {
            var data = {
                action: 'save_attendee_comments',
                event_id: LanRsvpAdmin.event_id,
                attendee_comments: {}
            };
            $.each($('textarea.attendee-comment'), function() {
                var user_id = $(this).attr('id');
                var comment = $(this).val();
                data['attendee_comments'][user_id] = comment;
            });
            $.post( ajaxurl, data, function(response) {
                if (response.length > 0) {
                    alert(response);
                } else {
                    window.location.replace('?page=lanrsvp_attendees&event_id=' + LanRsvpAdmin.event_id);
                }
            });
        });

        // Handling users comments
        $('#saveUsers').click(function() {
            var data = {
                action: 'save_user_comments',
                user_comments: {}
            };
            $.each($('textarea.user-comment'), function() {
                var user_id = $(this).attr('id');
                var comment = $(this).val();
                data['user_comments'][user_id] = comment;
            });
            $.post( ajaxurl, data, function(response) {
                if (response.length > 0) {
                    alert(response);
                } else {
                    window.location.replace('?page=lanrsvp_users');
                }
            });
        });


    });
}(jQuery));