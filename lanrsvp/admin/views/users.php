<?php
$usersTable = new Users_Table();
?>

<div class="wrap">
    <h2>Users</h2>
    <?php
    $usersTable->prepare_items();
    $usersTable->display();
    ?>
</div>