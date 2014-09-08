<div id="lanrsvp">
    <div id="lanrsvp-attendees">
        <?php echo sprintf("<h2>Attendees for Event ID %d (%s)</h2>", $event['event_id'], $event['event_title']); ?>
        <?php
        $attendeesTable->prepare_items();
        $attendeesTable->display();
        ?>
        <button id="saveAttendees" class="button button-primary">Save attendees comments</button>
    </div>
</div>