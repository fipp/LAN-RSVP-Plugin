<?php $display = $is_authenticated ? 'block' : 'none'; ?>
<div id="lanrsvp-authenticated" style="display:<?php echo $display; ?>;">
    <?php
    echo sprintf('<h2>Welcome %s %s</h2>', $attendee['first_name'], $attendee['last_name']);
    if ($is_signed_up) {
        $registration_date_text = date( 'D d.m, H:i', strtotime( $attendee['registration_date'] ) );
        echo "<p>Looks like you're already signed up at $registration_date_text.</p>";
        if (strlen($attendee['seat_row']) > 0 && strlen($attendee['seat_column']) > 0) {
            echo sprintf("<p>You chose seat %s-%s</p>", $attendee['seat_row'], $attendee['seat_column']);
        }
        echo "<p>If you want to cancel your registration, click the button below.</p>";
        echo '<button class="cancelRegistration">Cancel registration</button>';
    } else if ($can_sign_up) {
        echo '<button class="signUp">Sign up</button>';
    }
    ?>
</div>