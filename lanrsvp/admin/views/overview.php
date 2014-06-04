<div class="lanrsvp-admin wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <h3>Registered Events</h3>
    <?php echo LanRsvpAdmin::getEventsTable(); ?>
    <a href="<?php echo add_query_arg( 'lanrsvp-action', 'create-event' ); ?>">Create new event</a>

    <h3>Registered Users</h3>
    <?php echo LanRsvpAdmin::getUsersTable(); ?>
</div>