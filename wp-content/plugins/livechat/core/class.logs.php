<?php
/**
 * SCREETS © 2013
 *
 * Logs Class
 *
 */

// Load the base class

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SC_chat_logs extends WP_List_Table {
	
  
	var $data = array();
	
    
    function __construct() {
        global $status, $page, $wpdb;
                
		$s_query = '';
		
        // Set parent defaults
        parent::__construct( array(
            'singular'  => 'log',    // Singular name of the listed records
            'plural'    => 'logs',    // Plural name of the listed records
            'ajax'      => false    // Does this table support ajax?
        ) );
		
		// Prepare search query
		if( !empty( $_REQUEST['log_s'] ) ) 
			$s_query = ' WHERE v.name LIKE "%' . $_REQUEST['log_s'] . '%"'
			. ' OR v.email LIKE "%' . $_REQUEST['log_s'] . '%"'
			. ' OR lg.chat_line LIKE "%' . $_REQUEST['log_s'] . '%"';
		
		// Get chat logs
		$this->data = $wpdb->get_results('
			SELECT * FROM ' . $wpdb->prefix . 'chat_logs as lg  
			LEFT JOIN ' . $wpdb->prefix . 'chat_visitors as v 
				ON v.ID = lg.visitor_ID
			' . $s_query .'
			GROUP BY lg.visitor_ID', ARRAY_A );
        
    }
        
    
    /**
     * Get table's columns and titles.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     */
    function get_columns() {
        $columns = array(
            'cb'        	=> '<input type="checkbox" />',
            'title'     	=> __( 'Visitor' , 'sc_chat' ),
            'email'			=> __( 'E-mail', 'sc_chat' ),
            'total_logs'	=> __( 'Total Logs', 'sc_chat' ),
			'last_date'		=> __( 'Last Chat Date', 'sc_chat' )
        );
        return $columns;
    }
	
	/**
     * Column title
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     */
    function column_title($item) {
        global $wpdb;
		
		
		// Get visitor
		$visitor = $wpdb->get_row( 
			$wpdb->prepare(
				'SELECT ID, name, email FROM ' . $wpdb->prefix . 'chat_visitors WHERE ID = %s LIMIT 1',
				$item['visitor_ID']
			)
		);
		
		
        // Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&visitor_ID=%s">' . __( 'Chat Logs', 'sc_chat' ) . '</a>', @$_REQUEST['page'], 'edit', @$visitor->ID)
        );
		
		// Add delete option for admins
		if ( current_user_can( 'manage_options' ) )
			$actions['delete'] = sprintf('<a href="?page=%s&action=%s&visitor_ID=%s"> ' . __( 'Delete', 'sc_chat' ) . '</a>', @$_REQUEST['page'], 'delete', @$visitor->ID );
        
		
		
        // Return the title contents
        return sprintf('<div class="sc-chat-list-avatar">%1$s</div><strong>%2$s</strong>%3$s',
            /*$1%s*/ get_avatar( @$visitor->email, 32),
            /*$2%s*/ @$visitor->name,
            /*$3%s*/ $this->row_actions($actions)
        );
    }
	
    /**
     * Organize columns
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     */
    function column_default($item, $column_name) {
        global $wpdb;
		
		switch($column_name) {
            case 'last_date':
				
				// Get last conversation date
				$last_chat_date = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT chat_date FROM ' . $wpdb->prefix . 'chat_logs WHERE visitor_ID = %d ORDER BY visitor_ID DESC LIMIT 1',
						$item['visitor_ID']
					)
				);
				
				return  date_i18n( get_option('date_format') .' H:i', $last_chat_date );
				
            case 'email':
				$email = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT email FROM ' . $wpdb->prefix . 'chat_visitors WHERE ID = %d',
						$item['visitor_ID']
					)
				);
				
				return '<a href="mailto:' . $email . '">' . $email . '</a>';
				
            case 'total_logs':
				
				$total_logs = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'chat_logs WHERE visitor_ID = %d',
						$item['visitor_ID']
					)
				);
				
				return $total_logs;
				
            default:
                return $column_name . ': ' . print_r($item, true); // Show the whole array for troubleshooting purposes
        }
    }
    
        
    /** 
     * Display checkboxes
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     */
    function column_cb($item) {
		return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['visitor_ID']                // The value of the checkbox should be the record's id
        );
    }
    
    
    /**
     * Sort columns
	 *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'last_date'    	=> array('chat_date', true)
        );
        return $sortable_columns;
    }
    
    
	/** 
     * Bulk actions
	 *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array();
		
		// Add delete options for admin
		if ( current_user_can( 'manage_options' ) )
            $actions['delete'] =  __( 'Delete', 'sc_chat' );
			
        return $actions;
    }
	
    
    /**
     * Handle bulk actions
     * 
     * @see $this->prepare_items()
     */
    function process_bulk_action() {
        global $wpdb;       
    }
    
	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 */
	function extra_tablenav( $which ) {
				
		if( $which == 'top' ) {
			echo '<label class="description">' . __( 'Search' ) . ':</label> <input type="text" name="log_s" id="" placeholder="' . __( 'Search in logs', 'sc_chat' ) . '" value="'. ( ( !empty( $_REQUEST['log_s'] ) ) ?  $_REQUEST['log_s'] : '' ) . '" />';
		}
		
	}
	

    /**
     * Prepare data
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->data;
                
		
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'chat_date'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
		
		
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count( $data );
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice( $data,( ( $current_page - 1 ) * $per_page ), $per_page );
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,// WE have to calculate the total number of items
            'per_page'    => $per_page, // WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page) // WE have to calculate the total number of pages
        ) );
    }
    
}
?>