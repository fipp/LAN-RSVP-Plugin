<?php
$usersTable = new Users_Table();
?>

<div id="lanrsvp">
    <div id="lanrsvp-users">
        <h2>Users</h2>
        <p>Click on the number of events to see the event history for each user.</p>
        <?php
        $usersTable->prepare_items();
        $usersTable->display();
        ?>
        <button id="saveUsers" class="button button-primary">Save user comments</button>
    </div>
</div>