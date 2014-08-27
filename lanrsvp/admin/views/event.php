<?php

$data;
$has_seatmap = true;
if ( isset ( $_REQUEST['event_id'] ) ) {
    $event_id = $_REQUEST['event_id'];
    $event = DB::get_event( $event_id );
    if ( isset($event[0]) && is_object($event[0])) {
        $data = get_object_vars($event[0]);
        if (isset($data['has_seatmap']) && $data['has_seatmap'] == 0) {
            $has_seatmap = false;
        }
    }
}

?>

<?php

if (isset($data['event_id'])) {
    echo "<h1>Update event</h1>";
} else {
    echo "<h1>Create new event</h1>";
}
?>

<form method="post" class="lanrsvp-event-form">
    <table class="form-table">
        <?php if (isset($data['event_id'])): ?>
        <tr>
            <th scope="row"><label for="lanrsvp-event-title">Event ID</label></th>
            <td>
                <input
                    name="lanrsvp-event-title"
                    type="text"
                    required
                    class="regular-text code"
                    value="<?php echo $data['event_id'];  ?>"
                    disabled
                    />
            </td>
        </tr>
        <?php endif ?>
        <tr>
            <th scope="row"><label for="lanrsvp-event-title">Event Title</label></th>
            <td>
                <input
                    name="lanrsvp-event-title"
                    type="text"
                    pattern=".{2,64}"
                    required
                    placeholder="Between 2 and 64 characters ..."
                    class="regular-text code"
                    value="<?php echo (isset($data['event_title']) ? $data['event_title'] : '')  ?>"
                    />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="lanrsvp-event-startdate">Start date</label></th>
            <td>
                <input
                    name="lanrsvp-event-startdate"
                    type="text"
                    pattern="\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}"
                    required
                    placeholder="Example: '2014-08-10 18:30:00'"
                    class="regular-text code"
                    value="<?php echo (isset($data['start_date']) ? $data['start_date'] : '')  ?>"
                    />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="lanrsvp-event-enddate">End date</label></th>
            <td>
                <input
                    name="lanrsvp-event-enddate"
                    type="text"
                    pattern="\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}:"
                    placeholder="Optional ..."
                    class="regular-text code"
                    value="<?php echo (isset($data['end_date']) ? $data['end_date'] : '')  ?>"
                    />
            </td>
        </tr>
        <tr>
            <th scope="row">Registration type</th>
            <td>
                <fieldset><legend class="screen-reader-text"><span>Registration type</span></legend>
                    <label title="seatmap">
                        <input
                            type="radio"
                            name="lanrsvp-event-type"
                            value="seatmap"
                            <?php echo $has_seatmap ? 'checked' : ''; ?>
                            />
                        <span>With seat map</span>
                    </label> <br />
                    <label title="general">
                        <input
                            type="radio"
                            name="lanrsvp-event-type"
                            value="general"
                            <?php echo !$has_seatmap ? 'checked' : ''; ?>
                            />
                        <span>Without seat map</span>
                    </label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="lanrsvp-event-minattendees">Minimum number of attendees</label></th>
            <td>
                <input
                    type="number"
                    name="lanrsvp-event-minattendees"
                    min="0"
                    max="100"
                    step="1"
                    placeholder="Optional"
                    value="<?php echo (isset($data['min_attendees']) ? $data['min_attendees'] : 0)  ?>"
                    />
            </td>
        </tr>
        <tr id="lanrsvp-maxlimit" style="<?php echo $has_seatmap ? 'display:none;' : ''; ?>">
            <th scope="row"><label for="lanrsvp-event-maxattendees">Maximum number of attendees</label></th>
            <td>
                <input
                    type="number"
                    name="lanrsvp-event-maxattendees"
                    min="0"
                    max="100"
                    step="1"
                    placeholder="Optional"
                    value="<?php echo (isset($data['max_attendees']) ? $data['max_attendees'] : 0)  ?>"
                    />
            </td>
        </tr>
    </table>

    <div id="lanrsvp-seatmap-wrapper" style="<?php echo (!$has_seatmap ? 'display:none;' : ''); ?>">
        <h2 class="title">Create the seat map</h2>
        <p>
            The seatmap creation process makes use of HTML5 'number' and 'canvas' element. In case you're experiencing
            problems creating the seat map, try to switch to another web browser.
        </p>

        <h3>Map size</h3>
        <p>
            Pull the sliders below to adjust the size of the seat map. For example: 10 column and 5 rows gives a
            potential of 10 x 5 = 50 seats.
        </p>
        <table class="form-table lanrsvp-update-seatmap">
            <tr>
                <th scope="row"><label for="lanrsvp-seatmap-cols">Columns</label></th>
                <td>
                    <input type="number" name="lanrsvp-seatmap-cols" min="5" max="100" step="1" value="10" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lanrsvp-seatmap-rows">Rows</label></th>
                <td>
                    <input type="number" name="lanrsvp-seatmap-rows" min="5" max="100" step="1" value="10" />
                </td>
            </tr>
        </table>

        <h3>Map definition</h3>
        <p>
            Click on the grid below to draw the initial seat map. Click on the cells to change their status.
        </p>
        <div id="lanrsvp-seatmap-legends">
            <span class="lanrsvp-seatmap-legend-container">
                <span class="lanrsvp-seatmap-legend" style="background-color:#ffffff"></span>
                <span class="lanrsvp-align-middle">Undefined space - not the same as a available seat.</span>
            </span>
            <span class="lanrsvp-seatmap-legend-container">
                <span class="lanrsvp-seatmap-legend" style="background-color:#138e10"></span>
                <span class="lanrsvp-align-middle">Seat available for registration.</span>
            </span>
            <span class="lanrsvp-seatmap-legend-container">
                <span class="lanrsvp-seatmap-legend" style="background-color:#9c1616"></span>
                <span class="lanrsvp-align-middle">Seat taken.</span>
            </span>
        </div>
        <canvas id="lanrsvp-seatmap"></canvas>
    </div>

    <div class="lanrsvp-error"></div>

    <p class="submit">
        <input
            type="submit"
            name="submit"
            id="submit"
            class="button button-primary"
            value="<?php echo isset($data['event_id']) ? 'Update event' : 'Create event'; ?>"
            />
    </p>

</form>