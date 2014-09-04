<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

$start_date = date( 'D d.m, H:i', strtotime( $event['start_date'] ) );

$end_date = $event['end_date'];
if (strlen($end_date) == 0) {
    $end_date = 'No end date';
} else {
    $end_date = date( 'D d.m, H:i', strtotime( $end_date ) );
}


$places_left = 'Unlimited';
if ($has_seatmap) {
    $places_left = $seats_count - $attendees_count;
} elseif ($event['max_attendees'] != 0) {
    $places_left = $event['max_attendees'] - $attendees_count;
}



$has_seatmap_text = ($has_seatmap ? 'Yes' : 'No');

echo <<<HTML
<h1>{$event['event_title']}</h1>
<h2>Event details</h2>
<table>
    <tr><td>Start date</td><td>{$start_date}</td></tr>
    <tr><td>End date</td><td>{$end_date}</td></tr>
    <tr><td>Attendees needed</td><td>{$event['min_attendees']}</td></tr>
    <tr><td>Places taken</td><td>{$attendees_count}</td></tr>
    <tr><td>Places left</td><td>{$places_left}</td></tr>
    <tr><td>Has seatmap</td><td>{$has_seatmap_text}</td></tr>
</table>

<h2>Log in to register/change/deregister</h2>
<div class="lanrsvp-user">
    <div class="lanrsvp-user-message"></div>
    <form class="lanrsvp-login-form">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" required /></td></tr>
            <tr><td>Password:</td><td><input type="password" name="password" required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Log in" /></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="resetPassword">Reset password</a> -
                    <a href="#" class="register">Register new user</a> -
                    <a href="#" class="activate">Activate account</a>
                </td>
            </tr>
        </table>
    </form>
    <form class="lanrsvp-resetpassword-form" style="display:none;">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Reset password"/></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="logIn">Log in</a> -
                    <a href="#" class="register">Register new user</a> -
                    <a href="#" class="activate">Activate account</a>
                </td>
            </tr>
        </table>
    </form>
    <form class="lanrsvp-register-form" style="display:none;">
        <table>
            <tr><td>First Name:</td><td><input type="text" name="firstName" required value="Terje Ness"/></td></tr>
            <tr><td>Last Name:</td><td><input type="text" name="lastName" required value="Andersen" /></td></tr>
            <tr><td>E-mail:</td><td><input type="email" name="email" required value="terje.andersen@gmail.com" /></td></tr>
            <tr><td>Confirm E-mail:</td><td><input type="email" name="emailConfirm" required value="terje.andersen@gmail.com" /></td></tr>
            <tr><td>Password:</td><td><input type="password" name="password" required value="foobar" /></td></tr>
            <tr><td>Confirm Password:</td><td><input type="password" name="passwordConfirm" required value="foobar" /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Register" /></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="logIn">Log in</a> -
                    <a href="#" class="resetPassword">Reset password</a> -
                    <a href="#" class="activate">Activate account</a>
                </td>
            </tr>
        </table>
    </form>
    <form class="lanrsvp-activate-form" style="display:none;">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" required /></td></tr>
            <tr><td>Activation code:</td><td><input type="text" name="activationCode" required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Activate" /></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="logIn">Log in</a> -
                    <a href="#" class="resetPassword">Reset password</a> -
                    <a href="#" class="register">Register new user</a>
                </td>
            </tr>
        </table>
    </form>

</div>
HTML;

if ($has_seatmap) {
    echo "<h2>Seat map</h2>";
    chdir(__DIR__);
    include_once(realpath('./../../views/seatmap.php'));
}

echo "<h2>Attendees</h2>";
$attendeesTable->prepare_items();
$attendeesTable->display();

?>