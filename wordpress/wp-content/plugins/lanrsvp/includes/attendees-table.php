<?php

class Attendees_Table extends WP_List_Table_Copy
{

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct($attendees, $admin = false, $has_seatmap = false)
    {
        parent::__construct(array(
            'singular' => 'lanrsvp-attendee', // Singular label
            'plural' => 'lanrsvp-attendees', // plural label, also this well be one of the table css class
            'ajax' => false, // We won't support Ajax for this table
            'screen' => 'interval-list' // hook suffix
        ));

        if ($has_seatmap) {
            foreach ($attendees as $key => $attendee) {
                $seat_row = $attendees[$key]['seat_row'];
                unset($attendees[$key]['seat_row']);
                $seat_col = $attendees[$key]['seat_column'];
                unset($attendees[$key]['seat_col']);
                $attendees[$key]['seat'] = "$seat_row - $seat_col";
            }
        }

        $this->attendees = $attendees;

        $this->isAdmin = $admin;
        $this->has_seatmap = $has_seatmap;
    }

    /*
     * prepare_items defines two arrays controlling the behaviour of the table:
     *  - $hidden defines the hidden columns (see Screen Options)
     *  - $sortable defines if the table can be sorted by this column.
     * Finally the method assigns the example data to the class' data representation variable items.
     */
    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        usort($this->attendees, array(&$this, 'usort_reorder'));
        $this->items = $this->attendees;
    }

    /*
     * The method get_columns() is needed to label the columns on the top and bottom of the table. The keys in the
     * array have to be the same as in the data array otherwise the respective columns aren't displayed.
     */
    function get_columns()
    {
        $columns = [];

        if ($this->isAdmin) {
            $columns['user_id'] = 'ID';
            $columns['delete_attendee'] = 'Delete';
        }

        $columns['full_name'] = 'Name';
        if ($this->isAdmin) {
            $columns['email'] = 'E-mail';
        }

        if ($this->has_seatmap) {
            $columns['seat'] = 'Seat';
        }

        if ($this->isAdmin) {
            $columns['has_paid'] = 'Paid';
            $columns['comment'] = 'Comment';
        }

        $columns['registration_date'] = 'Registration Date';
        return $columns;
    }

    /*
     * The second parameter in the value array of $sortable_columns takes care of a possible pre-ordered column.
     * If the value is true the column is assumed to be ordered ascending, if the value is false the column is
     * assumed descending or unordered.
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'user_id' => array('user_id', false),
            'full_name' => array('full_name', false),
            'email' => array('email', false),
            'seat' => array('seat', false),
            'has_paid' => array('has_paid', false),
            'registration_date' => array('registration_date', false),
        );
        return $sortable_columns;
    }

    function usort_reorder($a, $b)
    {
        // If no sort, default to user id
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'registration_date';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';

        // Determine sort order
        $result = 0;
        switch ($orderby) {
            case 'user_id':
            case 'has_paid':
                $result = $a[$orderby] - $b[$orderby];
                break;
            case 'seat':
            case 'email':
                $result = strcmp($a[$orderby], $b[$orderby]);
                break;
            case 'full_name':
                $full_name_a = $a['first_name'] . ' ' . $a['last_name'];
                $full_name_b = $b['first_name'] . ' ' . $b['last_name'];
                $result = strcmp($full_name_a, $full_name_b);
                break;
            case 'registration_date':
                $timestamp_a = strtotime($a[$orderby]);
                $timestamp_b = strtotime($b[$orderby]);
                $result = $timestamp_a - $timestamp_b;
                break;
            default:
                break;
        }

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    /*
     * Before actually displaying each column WordPress looks for methods called column_{key_name}, e.g. function
     * column_booktitle. There has to be such a method for every defined column. To avoid the need to create a method
     * for each column there is column_default that will process any column for which no special method is defined:
     */
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'user_id':
            case 'email':
            case 'seat':
                return $item[$column_name];
            case 'full_name':
                return $item['first_name'] . ' ' . $item['last_name'];
                break;
            case 'registration_date';
                $timestamp = strtotime($item[$column_name]);
                return date('D d.m, H:i', $timestamp);
            case 'comment':
                return sprintf(
                    '<textarea id="%d" class="attendee-comment" placeholder="Admin notes about this attendee ...">%s</textarea>',
                    $item['user_id'],
                    $item[$column_name]
                );
            case 'delete_attendee':
                return sprintf(
                    '<a href="#" id="%d" class="delete-attendee" style="color: red;"><i class="fa fa-times fa-lg"></i></a>',
                    $item['user_id']
                );
            case 'has_paid':
                $has_paid = ($item[$column_name] == '1' ? true : false);
                $html = sprintf('<select class="attendee-has_paid" id="%s">', $item['user_id']);
                $html .= sprintf("<option value='1' %s>Yes</option>", ($has_paid ? 'selected' : ''));
                $html .= sprintf("<option value='0' %s>No</option>", ($has_paid ? '' : 'selected'));
                $html .= '</select>';
                return $html;
            default:
                return print_r($item, true); // Show the whole array for troubleshooting purposes
        }
    }
}