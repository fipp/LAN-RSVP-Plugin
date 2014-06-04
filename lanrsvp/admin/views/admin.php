<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */
?>

<div class="lanrsvp-admin wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <?php

    if ( !isset ( $_REQUEST['lanrsvp-action'] )) {
        $_REQUEST['lanrsvp-action'] = 'overview';
    }

    switch ( $_REQUEST['lanrsvp-action'] ) {
        case 'overview' :
            include_once( 'overview.php' );
            break;
        case 'create-event' :
            include_once( 'create-event.php' );
            break;
        default:
            break;
    }

    ?>

    <!--
    <form method="get" class="lanrsvp-update-grid">
        <table>
            <tr><th colspan="3" style="text-align: left;">Seat map properties:</th></tr>
            <tr>
                <td>Columns</td>
                <td><input type="range" name="cols-range" min="5" max="100" step="1" value="10" /></td>
                <td><input type="number" name="cols-number" min="5" max="100" step="1" value="10" style="width: 50px;" /></td>
            </tr>
            <tr>
                <td>Rows</td>
                <td><input type="range" name="rows-range" min="5" max="100" step="1" value="10" /></td>
                <td><input type="number" name="rows-number" min="5" max="100" step="1" value="10" style="width: 50px;" /></td>
            </tr>
        <table>
    </form>

    <canvas id="lanrsvp-grid"></canvas>

    <p><button class="lanrsvp-save-grid">Save</button></p>

    -->

</div>