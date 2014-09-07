<?php
$start_date = date( 'D d.m, H:i', strtotime( $event['start_date'] ) );

$end_date = $event['end_date'];
if (strlen($end_date) == 0) {
    $end_date = 'No end date';
} else {
    $end_date = date('D d.m, H:i', strtotime($end_date));
}

$places_left_text = isset( $places_left) ? $places_left : 'Unlimited';
$has_seatmap_text = ($has_seatmap ? 'Yes' : 'No');

echo <<<HTML
<div id="lanrsvp-event-details">
<h2>Event details</h2>
	<table>
	    <tr><th>Event title</th><th>{$event['event_title']}</th></tr>
	    <tr><td>Start date</td><td>{$start_date}</td></tr>
	    <tr><td>End date</td><td>{$end_date}</td></tr>
	    <tr><td>Attendees needed</td><td>{$event['min_attendees']}</td></tr>
	    <tr><td>Places taken</td><td>{$attendees_count}</td></tr>
	    <tr><td>Places left</td><td>{$places_left_text}</td></tr>
	    <tr><td>Has seatmap</td><td>{$has_seatmap_text}</td></tr>
	</table>
</div>
HTML;
