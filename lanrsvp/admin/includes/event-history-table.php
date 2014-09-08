<?php
class Event_History_Table extends WP_List_Table_Copy {

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct($user_id) {
        parent::__construct( array(
            'singular'=> 'lanrsvp-user-history-entry', // Singular label
            'plural' => 'lanrsvp-user-history', // plural label, also this well be one of the table css class
            'ajax'   => false // We won't support Ajax for this table
        ) );

        $eventHistory = DB::get_event_history($user_id);
        $this->eventHistory = $eventHistory;
    }

    /*
     * prepare_items defines two arrays controlling the behaviour of the table:
     *  - $hidden defines the hidden columns (see Screen Options)
     *  - $sortable defines if the table can be sorted by this column.
     * Finally the method assigns the example data to the class' data representation variable items.
     */
    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        usort( $this->eventHistory, array( &$this, 'usort_reorder' ) );
        $this->items = $this->eventHistory;
    }

    /*
     * The method get_columns() is needed to label the columns on the top and bottom of the table. The keys in the
     * array have to be the same as in the data array otherwise the respective columns aren't displayed.
     */
    function get_columns(){
        $columns = array(
            'event_id'          => 'Event ID',
            'registration_date' => 'Registration Date',
            'ip_address'        => 'IP Address',
            'seat'              => 'Chosen Seat',
            'comment'           => 'Comment'
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
            'event_id'          => array('event_id',false),
            'registration_date' => array('registration_date',false),
            'ip_address'        => array('ip_address',false)
        );
        return $sortable_columns;
    }

    function usort_reorder( $a, $b ) {
        // If no sort, default to user id
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'user_id';

        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = 0;
        switch ($orderby) {
            case 'registration_date':
                $timestamp_a = strtotime( $a[$orderby] );
                $timestamp_b = strtotime( $b[$orderby] );
                $result = $timestamp_a - $timestamp_b;
                break;
            case 'event_id':
                $result = $a[$orderby] - $b[$orderby];
                break;
            case 'ip_address':
                $result = strcmp( $a[$orderby], $b[$orderby] );
                break;
            default:
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
            case 'comment':
                return $item[ $column_name];
            case 'registration_date';
                $val = $item[ $column_name ];
                if ($val == 0) {
                    return "None";
                }
                $timestamp = strtotime( $val );
                return date( 'l d/m/y H:i', $timestamp );
            case 'ip_address':
                $remote_addr = $item['registered_ip_remote_addr'];
                $x_forwarded_for = $item['registered_ip_x_forwarded_for'];
                return $remote_addr . (strlen($x_forwarded_for) > 0 ? " (x-forwarded-for: $x_forwarded_for)" : '');
            case 'seat':
                if (strlen($item['seat_row']) == 0 || strlen($item['seat_column']) == 0) {
                    return '-';
                } else {
                    return sprintf("%d-%d",$item['seat_row'], $item['seat_column']);
                }
            default:
                return print_r( $item, true ) ; // Show the whole array for troubleshooting purposes
        }
    }
}