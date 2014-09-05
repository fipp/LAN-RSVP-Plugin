<?php
$start_date = date( 'D d.m, H:i', strtotime( $event['start_date'] ) );

$end_date = $event['end_date'];
if (strlen($end_date) == 0) {
    $end_date = 'No end date';
} else {
    $end_date = date('D d.m, H:i', strtotime($end_date));
}

$places_left = 'Unlimited';
if ($has_seatmap) {
    $places_left = $seats_count - $attendees_count;
} elseif ($event['max_attendees'] != 0) {
    $places_left = $event['max_attendees'] - $attendees_count;
}

$has_seatmap_text = ($has_seatmap ? 'Yes' : 'No');

echo <<<HTML
<h2>Event details</h2>
<table>
    <tr><th>Event title</th><th>{$event['event_title']}</th></tr>
    <tr><td>Start date</td><td>{$start_date}</td></tr>
    <tr><td>End date</td><td>{$end_date}</td></tr>
    <tr><td>Attendees needed</td><td>{$event['min_attendees']}</td></tr>
    <tr><td>Places taken</td><td>{$attendees_count}</td></tr>
    <tr><td>Places left</td><td>{$places_left}</td></tr>
    <tr><td>Has seatmap</td><td>{$has_seatmap_text}</td></tr>
</table>
HTML;
