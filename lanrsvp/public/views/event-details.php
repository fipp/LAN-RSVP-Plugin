<?php
$start_date = date( 'D d.m, H:i', strtotime( $event['start_date'] ) );

$status_text = '';
if (isset($places_left) && $places_left == 0) {
    $status_text = '<span class="red">No places left</span>';
} else if (!$event_is_active) {
    $status_text = '<span class="red">Registration closed</span>';
} else {
    $status_text = '<span class="green">Registration open</span>';
}

$end_date_row = '';
if (strlen($event['end_date']) > 0) {
    $end_date_row = sprintf("<tr><td>End date</td><td>%s</td></tr>", date('D d.m, H:i', strtotime($event['end_date'])));
}

$has_seatmap_text = ($has_seatmap ? 'Yes' : 'No');

$places_left_row = '';
if (isset($places_left) && $places_left > 0) {
    $places_left_row = sprintf("<tr><td>Places left</td><td>%d</td></tr>", $places_left);
}

$min_attendees_row = '';
if ($event['min_attendees'] > 0) {
    $min_attendees_row = sprintf("<tr><td>Min. attendees needed</td><td>%d</td></tr>", $event['min_attendees']);
}

echo <<<HTML
<div id="lanrsvp-event-details">
<h2>Event details</h2>
	<table>
	    <tr><td>Event title</td><td>{$event['event_title']}</td></tr>
	    <tr><td>Status</td><td>{$status_text}</td></tr>
	    <tr><td>Start date</td><td>{$start_date}</td></tr>
	    {$end_date_row}
	    {$min_attendees_row}
	    <tr><td>Places taken</td><td>{$attendees_count}</td></tr>
	    {$places_left_row}
	</table>
</div>
HTML;
