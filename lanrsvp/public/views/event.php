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

$end_date = $event['end_date'];
if (strlen($end_date) == 0) {
    $end_date = 'not set';
}

$max_attendees = $event['max_attendees'];
if ($max_attendees == 0) {
    $max_attendees = 'unlimited';
}

$has_seatmap_text = ($has_seatmap ? 'yes' : 'no');

echo <<<HTML
<h1>{$event['event_title']}</h1>
<h2>Event details</h2>
<table>
    <tr><td>Start date</td><td>{$event['start_date']}</td></tr>
    <tr><td>End date</td><td>{$end_date}</td></tr>
    <tr><td>Minimum number of attendees needed</td><td>{$event['min_attendees']}</td></tr>
    <tr><td>Maximum number of attendees allowed</td><td>{$max_attendees}</td></tr>
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