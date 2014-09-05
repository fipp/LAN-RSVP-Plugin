<?php

if ($isLoggedIn) {
    echo ' <button id="signUp" disabled>Sign up</button>';
} else {
    echo '<p>To register or change your registration, you have to log in in using your LAN RSVP System account:</p>';
    echo ' <button id="logIn">Log in</button>';
}

echo "</p>";

?>

<p></p><button id="showSeatmap">Show Seatmap</button> <button id="showAttendees">List Attendees</button></p>