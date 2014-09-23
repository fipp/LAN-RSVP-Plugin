<?php
    $display = 'none';
    $user = [];
    if ($is_authenticated) {
        $display = 'block';
        $user = DB::get_user($_SESSION['lanrsvp-userid']);
    }
?>
<div id="lanrsvp-authenticated" style="display:<?php echo $display; ?>;">
    <?php
    echo sprintf('<h2>Welcome %s %s</h2>', $user['first_name'], $user['last_name']);
    if ($is_signed_up) {
        $registration_date_text = date( 'l M. j, H:i', strtotime( $attendee['registration_date'] ) );
        echo "<p>You signed up for this event at $registration_date_text.</p>";
        if (strlen($attendee['seat_row']) > 0 && strlen($attendee['seat_column']) > 0) {
            echo sprintf("<p>You chose seat %s-%s.</p>", $attendee['seat_row'], $attendee['seat_column']);
        }
        echo '<button class="unsubscribe">Unsubscribe</button>';
    } else if ($can_sign_up) {
        if ($has_seatmap) {
            echo '<p>Choose your seat in the seat map to sign up.</p>';
        }
        echo '<button class="signUp">Sign up</button>';
    }
    echo ' <button class="logOut">Log Out</button>';
    echo '<p class="lanrsvp-authenticated-message"></p>';
    ?>
</div>