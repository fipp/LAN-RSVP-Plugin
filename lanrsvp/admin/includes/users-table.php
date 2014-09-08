<?php
class Users_Table extends WP_List_Table_Copy {

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct() {
        parent::__construct( array(
            'singular'=> 'lanrsvp-user', // Singular label
            'plural' => 'lanrsvp-users', // plural label, also this well be one of the table css class
            'ajax'   => false // We won't support Ajax for this table
        ) );

        $users = DB::get_users();
        $this->users = $users;

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
        usort( $this->users, array( &$this, 'usort_reorder' ) );
        $this->items = $this->users;
    }

    /*
     * The method get_columns() is needed to label the columns on the top and bottom of the table. The keys in the
     * array have to be the same as in the data array otherwise the respective columns aren't displayed.
     */
    function get_columns(){
        $columns = array(
            'user_id'           => 'ID',
            'is_activated'      => 'Active',
            'email'             => 'E-mail Address',
            'first_name'        => 'First Name',
            'last_name'         => 'Last Name',
            'comment'           => 'Comment',
            'registration_date' => 'Registration Date'
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
            'user_id'           => array('user_id',false),
            'email'             => array('email',false),
            'first_name'        => array('first_name',false),
            'last_name'         => array('first_name',false),
            'registration_date' => array('registration_date',false),
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
            case 'user_id':
            case 'email':
            case 'first_name':
            case 'last_name':
                return $item[ $column_name];
            case 'registration_date';
                $val = $item[ $column_name ];
                if ($val == 0) {
                    return "None";
                }
                $timestamp = strtotime( $val );
                return date( 'l d/m/y H:i', $timestamp );
            case 'is_activated':
                return ( $item[ $column_name ] == '0' ) ? 'No' : 'Yes';
            case 'comment':
                $text = $item[ $column_name ];
                return "<textarea placeholder='Admin notes about this user ...'>$text</textarea>";
            default:
                return print_r( $item, true ) ; // Show the whole array for troubleshooting purposes
        }
    }
}