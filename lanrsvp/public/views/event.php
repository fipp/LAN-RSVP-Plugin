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