<?php
class Events_Table extends WP_List_Table_Copy {

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct() {
        parent::__construct( array(
            'singular'=> 'wp_list_text_link', // Singular label
            'plural' => 'wp_list_test_links', // plural label, also this well be one of the table css class
            'ajax'   => false // We won't support Ajax for this table
        ) );
    }

    /*
     * prepare_items defines two arrays controlling the behaviour of the table:
     *  - $hidden defines the hidden columns (see Screen Options)
     *  - $sortable defines if the table can be sorted by this column.
     * Finally the method assigns the example data to the class' data representation variable items.
     */
    function prepare_items() {
        $events = DB::get_events();
        foreach ($events as $key => $val) {
            $events[$key] = get_object_vars($val);
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        usort( $events, array( &$this, 'usort_reorder' ) );
        $this->items = $events;
    }

    /*
     * The method get_columns() is needed to label the columns on the top and bottom of the table. The keys in the
     * array have to be the same as in the data array otherwise the respective columns aren't displayed.
     */
    function get_columns(){
        $columns = array(
            'event_id'              => 'ID / Change',
            'is_active'             => 'Active',
            'event_title'           => 'Event Title',
            'start_date'            => 'Start Date',
            'end_date'              => 'End Date',
            'attendees_registered'  => 'Attendees',
            'min_attendees'         => 'Min. Attendees',
            'max_attendees'         => 'Max. Attendees',
            'has_seatmap'           => 'Seatmap',
        );
        return $columns;
    }

    /*
     * The second parameter in the value array of $sortable_columns takes care of a possible pre-ordered column.
     * If the value is true the column is assumed to be ordered ascending, if the value is false the column is
     * assumed descending or unordered.
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'event_id'              => array('event_id',false),
            //'is_active'             => array('is_active',false),
            'event_title'           => array('event_title',false),
            'attendees_registered'  => array('attendees_registered',false),
            'start_date'            => array('start_date',false),
            'end_date'              => array('end_date',false),
            'min_attendees'         => array('min_attendees',false),
            'max_attendees'         => array('max_attendees',false),
            //'has_seatmap'           => array('has_seatmap',false),
        );
        return $sortable_columns;
    }

    function usort_reorder( $a, $b ) {
        // If no sort, default to event id
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'event_id';

        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = 0;
        switch ($orderby) {
            case 'start_date':
            case 'end_date':
                $timestamp_a = strtotime( $a[$orderby] );
                $timestamp_b = strtotime( $b[$orderby] );
                $result = $timestamp_a - $timestamp_b;
                break;
            case 'attendees_registered':
            case 'min_attendees':
            case 'max_attendees':
                if ($orderby == "max_attendees") {
                    $a[$orderby] = ( $a[$orderby] == 0) ? 99999999999 : $a[$orderby];
                    $b[$orderby] = ( $b[$orderby] == 0) ? 99999999999 : $b[$orderby];
                }
                $result = $a[$orderby] - $b[$orderby];
                break;
            default:
                $result = strcmp( $a[$orderby], $b[$orderby] );
                break;
        }

        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;

    }

    /*
     * Before actually displaying each column WordPress looks for methods called column_{key_name}, e.g. function
     * column_booktitle. There has to be such a method for every defined column. To avoid the need to create a method
     * for each column there is column_default that will process any column for which no special method is defined:
     */
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'event_id':
                $editBtn = sprintf(
                    '<a href="?page=lanrsvp_event&event_id=%d"><i class="fa fa-pencil-square-o fa-lg"></i></a>',
                    $item['event_id']
                );

                $removeBtn = sprintf(
                    '<a href="&oo" id="%d" class="remove-event" style="color: red;"><i class="fa fa-times fa-lg"></i></a>',
                    $item['event_id']
                );

                return $item[ $column_name ] . " / $editBtn $removeBtn";
            case 'event_title':
            case 'min_attendees':
            case 'attendees_registered':
                return $item[ $column_name ];
            case 'start_date';
            case 'end_date':
                $val = $item[ $column_name ];
                if ($val == 0) {
                    return "None";
                }
                $timestamp = strtotime( $val );
                return date( 'l d/m/y H:i', $timestamp );
            case 'is_active':
            case 'has_seatmap':
                return ( $item[ $column_name ] == 0 ) ? 'No' : 'Yes';
            case 'max_attendees':
                $val = $item[ $column_name ];
                return ( $val == 0 ) ? 'Unlimited' : $val;
            default:
                return print_r( $item, true ) ; // Show the whole array for troubleshooting purposes
        }
    }
}