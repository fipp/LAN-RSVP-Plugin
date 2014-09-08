<?php
$usersTable = new Users_Table();
?>

<div id="lanrsvp">
    <div id="lanrsvp-users">
        <h2>Users</h2>
        <?php
        $usersTable->prepare_items();
        $usersTable->display();
        ?>
        <button id="saveUsers" class="button button-primary">Save user comments</button>
    </div>
</div>