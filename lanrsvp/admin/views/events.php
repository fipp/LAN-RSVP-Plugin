<?php
$eventsTable = new Events_Table();
?>

<div class="wrap">
    <h2>Events</h2>
    <a href="?page=lanrsvp_event" class="button button-primary">Create new event</a>
    <?php
    $eventsTable->prepare_items();
    $eventsTable->display();
    ?>
</div>