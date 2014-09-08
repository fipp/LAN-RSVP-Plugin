<?php
$eventsTable = new Events_Table();
?>

<div id="lanrsvp">
    <h2>Events</h2>
    <a href="?page=lanrsvp_event" class="button button-primary">Create new event</a>
    <p>Click on the event title to edit an event.</p>
    <p>Click on the number of attendees to see the attendees list.</p>
    <p>To include an event in a public post, use the following syntax: <strong>[lanrsvp event_id="1"]</strong>.</p>
    <div id="lanrsvp-events">
        <?php
        $eventsTable->prepare_items();
        $eventsTable->display();
        ?>
    </div>
</div>