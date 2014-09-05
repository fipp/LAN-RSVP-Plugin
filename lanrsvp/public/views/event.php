<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

include_once('event-details.php');
include_once('navigation.php');
include_once('login.php');

?>



<div class="lanrsvp-actions"<?php if (!$isLoggedIn): ?> style="display:none";<?php endif; ?>>
    <button>RSVP</button>
</div>

<?php
if ($has_seatmap) {
    chdir(__DIR__);
    echo '<div id="lanrsvp-seatmap-wrapper" style="display:none;">';
    echo "<h2>Seat map</h2>";
    include_once(realpath('./../../views/seatmap.php'));
    echo '</div>';
}
?>

<div id="lanrsvp-attendees-table-wrapper" style="display:none;">
    <h2>Attendees</h2>
    <?php
        $attendeesTable->prepare_items();
        $attendeesTable->display();
    ?>
</div>


