<?php

   
  // ****************  
  // Lifetime Max Validity Rule Test
  // ****************             
   function vtprd_rule_lifetime_validity_test($i,$shortcode = null) {  
     global $vtprd_rules_set;

     //if this came from a shortcode, access hist via session vars.  otherwise, the data will already be there.
     if ($shortcode) {
       vtprd_get_purch_hist_from_session($i);     
     }

     //*******************************************************************************************
     //these test are >=, since we're testing the current state, not trying to add in a discount
     //*******************************************************************************************
     switch( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_type'] ) {
        case 'none':
            return false; //marks test as not yet having reached the lifetime limit
          break;
        case 'quantity':
            if ($vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_count'] ) { 
              return true;
            } else {
              return false; //marks test as not yet having reached the lifetime limit
            }            
          break;        
        case 'currency':
            if ($vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_count'] ) { 
              return true;
            } else {
              return false; //marks test as not yet having reached the lifetime limit
            } 
          break;        
     }

     return false; //marks test as not yet having reached the lifetime limit
   }


   function vtprd_get_purch_hist_from_session($i) {
      global $vtprd_rules_set, $vtprd_cart;    
      
      if(!isset($_SESSION)){
        session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
      }
      
      if(isset($_SESSION['vtprd_rule_purch_hist_'.$i])) {
        //get the data from the session var, put into rules_set
        $vtprd_rule_hist_array = unserialize($_SESSION['vtprd_rule_purch_hist_'.$i]);
        $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts    =  $vtprd_rule_hist_array[0];
        $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts  =  $vtprd_rule_hist_array[1];       
      } else {
        //get the data from the DB (which puts it into rules_set), and create the session var
        $vtprd_cart = new VTPRD_Cart;
        $vtprd_cart->purchaser_ip_address = $vtprd_info['purchaser_ip_address'];
        vtprd_get_rule_purchaser_history($i);  //get hist based on IP only
        $vtprd_rule_hist_array = array();
        $vtprd_rule_hist_array[] = $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts;
        $vtprd_rule_hist_array[] = $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts;
        $_SESSION['vtprd_rule_purch_hist_'.$i] = serialize($vtprd_rule_hist_array);
      }     
   
   }

   function vtprd_get_purchaser_info_from_screen() { 
    //Get purchaser info off of global from screen. executed out of parent-functions.php 

    global $wpdb, $vtprd_setup_options, $vtprd_cart, $vtprd_info; //$vtprd_cart already in progress in calling function

    if (isset($_POST['billing_email'])) {
      $vtprd_cart->purchaser_email  = $_POST['billing_email'];
    }              
    
    if (isset($_POST['billing_first_name'])) {
      $vtprd_cart->billto_name      = $_POST['billing_first_name'];
    }                                                                 
                                                                   
    if (isset($_POST['billing_last_name'])) {
      $vtprd_cart->billto_name     .= ' ' . $_POST['billing_last_name']; //concat space, lastname to firstname in name field
    } 

    if (isset($_POST['billing_address_1'])) {
      $vtprd_cart->billto_address   = $_POST['billing_address_1'];     
    }                                                                 

    if (isset($_POST['billing_address_2'])) {
      $vtprd_cart->billto_address  .= ' ' . $_POST['billing_address_2'];  //concat space, 2nd address line to 1st addres line    
    }     


    if (isset($_POST['billing_city'])) {
      $vtprd_cart->billto_city      = $_POST['billing_city'];
    }                                                              
    

    if (isset($_POST['billing_state'])) {
      $vtprd_cart->billto_state     = $_POST['billing_state'];
    }                                                                 
    

    if (isset($_POST['billing_postcode'])) {
      $vtprd_cart->billto_postcode  = $_POST['billing_postcode'];
    }                                                                  
     

    if (isset($_POST['billing_country'])) {
       $vtprd_cart->billto_country   = $_POST['billing_country'];
    }                                                                    
     

    if (isset($_POST['shipping_first_name'])) {
      $vtprd_cart->shipto_name      = $_POST['shipping_first_name'];
    }                                                                 
     

    if (isset($_POST['shipping_last_name'])) {
      $vtprd_cart->shipto_name     .= ' ' . $_POST['shipping_last_name'];
    }    
    

    if (isset($_POST['shipping_address_1'])) {
      $vtprd_cart->shipto_address   = $_POST['shipping_address_1'];
    }                                                              
    

    if (isset($_POST['shipping_address_2'])) {
      $vtprd_cart->shipto_address  .= ' ' . $_POST['shipping_address_2'];
    }    
    

    if (isset($_POST['shipping_city'])) {
      $vtprd_cart->shipto_city      = $_POST['shipping_city'];
    }                                                                  
     

    if (isset($_POST['shipping_state'])) {
      $vtprd_cart->shipto_state     = $_POST['shipping_state'];
    }                                                                
    

    if (isset($_POST['shipping_postcode'])) {
      $vtprd_cart->shipto_postcode  = $_POST['shipping_postcode'];
    }                                                                    
    

    if (isset($_POST['shipping_country'])) {
      $vtprd_cart->shipto_country   = $_POST['shipping_country'];
    }                                                                  
    

    
    //always supply ip_address
    $vtprd_cart->purchaser_ip_address = $vtprd_info['purchaser_ip_address'];   
  }

 function vtprd_save_lifetime_purchase_info($log_id) {
  // echo '<br> IN SAVE PURCHASE <br>'; //mwnt
    global $post, $wpdb, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info;
   
    /* NO LONGER NECESSARY - THIS CODE now only done once...      
    //action wpsc_confirm_checkout in wpsc-transaction_results_functions.php  is in a 'for' loop, and we only want to do this once.    Control via a 'done' switch
    if ($vtprd_info['purch_hist_done'] == 'yes') {
      return;
    }
    */

			
    $sizeof_rules_set = sizeof($vtprd_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) {                                                               
/*
echo '$vtprd_rules_set[$i]->rule_status= ' .$vtprd_rules_set[$i]->rule_status. '<br>' ;  //mwnt echo 
echo 'discount_lifetime_max_amt_type= ' .$vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_type']. '<br>' ;  //mwnt echo 
echo '$vtprd_rules_set[$i]->discount_total_qty_for_rule= ' .$vtprd_rules_set[$i]->discount_total_qty_for_rule. '<br>' ;  //mwnt echo         
*/
      if ( ( $vtprd_rules_set[$i]->rule_status == 'publish' ) && 
           ( $vtprd_rules_set[$i]->discount_total_qty_for_rule > 0 )  )  {           
         if  ( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_type'] == 'none') {
             return;
         }
        
        /*  apply cart info to purchaser table
         $purchaser_max_purchase_row_id, $purchaser_max_purchase_row_qty_total, $purchaser_max_purchase_row_price_total all computed
         during apply-rules processing, so it's all ready for update here
        */
        //add new cart totals to existing history totals ...
        $rule_currency_total  =  $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts ;
        $rule_units_total     =  $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts ;
        $rule_percent_total   =  $vtprd_rules_set[$i]->purch_hist_rule_percent_total;
        $discount_lifetime_max_amt_type = $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_type'];
        $discount_lifetime_max_amt_count = $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_count'];

        if ($vtprd_rules_set[$i]->purch_hist_rule_row_id > 0) {
          //update totals only, as needed
          $sql = "UPDATE `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` SET `rule_currency_total` = ".$rule_currency_total.", `rule_units_total` = ".$rule_units_total." WHERE `rule_table_id`=".$vtprd_rules_set[$i]->purch_hist_rule_row_id."";
    	   	$wpdb->query($sql);
          $rule_purchaser_row_id = $vtprd_rules_set[$i]->purch_hist_rule_row_id;

//echo '$rule_currency_total= ' .$rule_currency_total. '<br>' ;  //mwnt echo 
//echo '$rule_units_total= ' .$rule_units_total. '<br>' ;  //mwnt echo 

 

        } else {
          //add max_purchase_row          
          //$next_rule_purch_id = $wpdb->get_var("SELECT LAST_INSERT_ID() AS `id` FROM `".VTPRD_MAX_PURCHASE_RULE_PURCHASER."` LIMIT 1");
          //$next_rule_purch_id = $next_rule_purch_id + 1;
          $next_id; //supply null value for use with autoincrement table key
          $vtprd_cart->purchaser_ip_address = $vtprd_info['purchaser_ip_address'];
                    


          //if purchaser not previously found, find it or create a new purchaser row
          if (!$vtprd_cart->purchaser_table_id) {
             vtprd_find_create_purchaser();
          }
          
          $next_id; //supply null value for use with autoincrement table key
          $orig_rule_object = serialize($vtprd_rules_set[$i]);
                    
          //create the RULE TOTALS across all purchases for purchaser
          $wpdb->query("INSERT INTO `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` (`rule_table_id`,`rule_id`,`purchaser_table_id`,
          `rule_currency_total`,`rule_units_total`,`rule_percent_total`, 
          `orig_rule_object`,`orig_rule_maximum_type`,`orig_rule_maximum_amt`) 
          VALUES ('{$next_id}','{$vtprd_rules_set[$i]->post_id}','{$vtprd_cart->purchaser_table_id}',
          '{$rule_currency_total}','{$rule_units_total}','{$rule_percent_total}',
          '{$orig_rule_object}','{$discount_lifetime_max_amt_type}','{$discount_lifetime_max_amt_count}' );");
          //
          $vtprd_rules_set[$i]->purch_hist_rule_row_id = $wpdb->get_var("SELECT LAST_INSERT_ID() AS `rule_table_id` FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` LIMIT 1");          
          
        }

          $next_id; //supply null value for use with autoincrement table key
          $logid_status = 'active';
          
        //create the LOGID table entry for the RULE, track rule purchase totals for THIS logid
          $wpdb->query("INSERT INTO `".VTPRD_LIFETIME_LIMITS_PURCHASER_LOGID_RULE."` (`logid_table_id`,`purchase_log_id`,`rule_id`,
          `rule_table_id`,`purchaser_table_id`,`logid_status`,`rule_currency_total`,`rule_units_total`) 
          VALUES ('{$next_id}','{$log_id}','{$vtprd_rules_set[$i]->post_id}',
          '{$vtprd_rules_set[$i]->purch_hist_rule_row_id}','{$vtprd_cart->purchaser_table_id}',
          '{$logid_status}','{$vtprd_rules_set[$i]->actionPop_rule_yousave_amt}','{$vtprd_rules_set[$i]->actionPop_rule_yousave_qty}' );");
                                                     
      } 
    } //end for loop  

//echo '$vtprd_rules_set= ' .'<pre>'.print_r($vtprd_rules_set, true).'</pre>'  .'<br>' ;  //mwnt echo 
//echo '$vtprd_cart= ' .'<pre>'.print_r($vtprd_cart, true).'</pre>'  .'<br>' ;  //mwnt echo 
//wp_die( __('<strong>UPDATE Lifetime.</strong>', 'vtprd'), __('VT Pricing Deals not compatible - WP', 'vtprd'), array('back_link' => true));     

    return;                                   
  }

  /*
  NEEDS AJAX...  on page=wpsc-purchase-logs, change to .wpsc-purchase-log-status
  
  <select class="wpsc-purchase-log-status" data-log-id="285">
  <option value="1">Incomplete Sale</option>
  <option selected="selected" value="2">Order Received</option>
  <option value="3">Accepted Payment</option>
  <option value="4">Job Dispatched</option>
  <option value="5">Closed Order</option>
  <option value="6">Payment Declined</option>
  
  
  AJAX FROM wpsc-admin/js/purchase-logs.js
		event_log_status_change : function() {
			var post_data = {
					nonce      : WPSC_Purchase_Logs_Admin.change_purchase_log_status_nonce,
					action     : 'change_purchase_log_status',      //*********************************
					id         : $(this).data('log-id'),            //THIS PICKS UP THE "data-log-id="
					new_status : $(this).val(),                     //this picks up the select value
					m          : WPSC_Purchase_Logs_Admin.current_filter,
					status     : WPSC_Purchase_Logs_Admin.current_view,
					paged      : WPSC_Purchase_Logs_Admin.current_page,
					_wp_http_referer : window.location.href
				},
  
 also:  in wpsc-includes/purchase-log-class.php  (from 3.9)
 		do_action( 'wpsc_purchase_log_before_delete', $log_id ); 
  
  */
  
  /*
    roll log totals in or out based on status change
       from: wpsc-admin/display-sales-logs.php
       		 function process_bulk_action()
           do_action( 'wpsc_sales_log_process_bulk_action', $current_action );
  */ 
  function vtprd_maybe_lifetime_bulk_roll_log_totals_out($current_action) {  
    global $post, $wpdb; //global $post already in calling routine
    switch( true ) {
      case ( $current_action == 'delete' ):   
   				if ( empty( $_REQUEST['confirm'] ) ) {
  					 return;         
          }
          if ( empty( $_REQUEST['post'] ) ) {
  					 return;
          }
  				foreach ( $_REQUEST['post'] as $log_id ) {
            vtprd_maybe_lifetime_roll_log_totals_out($log_id);
          }   
        break;
      //status change
      case ( is_numeric( $current_action ) && $current_action < 7 && ! empty( $_REQUEST['post'] ) ):   
          switch( $current_action ) {
              case ( 1 ):    //<option value="1">Incomplete Sale</option>
              case ( 5 ):    //<option value="5">Closed Order</option>
              case ( 6 ):    //<option value="6">Payment Declined</option>
                  foreach ( $_REQUEST['post'] as $log_id ) {
			               vtprd_maybe_lifetime_roll_log_totals_out($log_id);
                  }
                break;
              case ( 3 ):    //  <option value="3">Accepted Payment</option>
              case ( 4 ):    //<option value="4">Job Dispatched</option>
                  foreach ( $_REQUEST['post'] as $log_id ) {
			               vtprd_maybe_lifetime_add_log_totals_in($log_id);
                  }
                break;                
          }
        break;        
    }

    return;
  } 
   
  
  //roll log totals in or out based on status change
  function vtprd_maybe_lifetime_log_bulk_modify() {  
      global $post, $wpdb; //global $post already in calling routine 
    	if ( $_POST['purchlog_multiple_status_change'] != -1 ) {
    		if ( is_numeric( $_POST['purchlog_multiple_status_change'] ) && $_POST['purchlog_multiple_status_change'] != 'delete' ) {
    			foreach ( (array)$_POST['purchlogids'] as $purchlogid ) {
    				vtprd_maybe_lifetime_add_log_totals_in($purchlogid);
    			}
    		} elseif ( $_POST['purchlog_multiple_status_change'] == 'delete' ) {
    			foreach ( (array)$_POST['purchlogids'] as $purchlogid ) {
    				vtprd_maybe_lifetime_roll_log_totals_out($purchlogid);
    			}
    		}
    	}
    return;
  } 
 
  
  function vtprd_maybe_lifetime_log_roll_out_cntl($log_id=null) {  
    global $post, $wpdb; //global $post already in calling routine 
   	if ( $log_id == '' ) {
  		$log_id = absint( $_GET['purchlog_id'] );
  	}
    if ($log_id) {
      vtprd_maybe_lifetime_roll_log_totals_out($log_id);
    }
  } 
 
  //roll the totals out
  function vtprd_maybe_lifetime_roll_log_totals_out($log_id) {  
    global $post, $wpdb; //global $post already in calling routine 
    $purchaser_logid_rule_rows = $wpdb->get_results("SELECT * FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER_LOGID_RULE."` WHERE `purchase_log_id`  = ".$log_id." ",ARRAY_A);
    foreach ($purchaser_logid_rule_rows as $purchaser_logid_rule_row) { 
       //get the matching PURCHASER_RULE row, subtract out the logid totals and update 
       if ($purchaser_logid_rule_row['logid_status'] == 'active') {
          $rule_table_id = $purchaser_logid_rule_row['rule_table_id'];
          $purchaser_rule_rows = $wpdb->get_results("SELECT * FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` WHERE `rule_table_id`  = ".$rule_table_id." ",ARRAY_A);
          if (sizeof($purchaser_rule_rows) > 0) {
             $rule_currency_total = $purchaser_rule_rows[0]['rule_currency_total'] - $purchaser_logid_rule_row['rule_currency_total'];
             $rule_currency_total = $purchaser_rule_rows[0]['rule_units_total']    - $purchaser_logid_rule_row['rule_units_total'];
             $sql = "UPDATE `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` SET `rule_currency_total` = ".$rule_currency_total.", `rule_units_total` = ".$rule_units_total." WHERE `rule_table_id`=".$rule_table_id."";
    	   	   $wpdb->query($sql);
          }
          $order_canceled_status = 'canceled';
          $sql = "UPDATE `".VTPRD_LIFETIME_LIMITS_PURCHASER_LOGID_RULE."` SET `logid_status` = ".$order_canceled_status." WHERE  `purchase_log_id`  = ".$log_id."  ";
	   	    $wpdb->query($sql);        
       }   
    } //end foreach
    
    return;
  }  
 
  //roll the totals out
  function vtprd_maybe_lifetime_add_log_totals_in($log_id) {  
    global $post, $wpdb; //global $post already in calling routine 
    
    
    $purchaser_logid_rule_rows = $wpdb->get_results("SELECT * FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER_LOGID_RULE."` WHERE `purchase_log_id`  = ".$log_id." ",ARRAY_A);
    foreach ($purchaser_logid_rule_rows as $purchaser_logid_rule_row) { 
       //get the matching PURCHASER_RULE row, subtract out the logid totals and update 
       if ($purchaser_logid_rule_row['logid_status'] == 'canceled') {
          $rule_table_id = $purchaser_logid_rule_row['rule_table_id'];
          $purchaser_rule_rows = $wpdb->get_results("SELECT * FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` WHERE `rule_table_id`  = ".$rule_table_id." ",ARRAY_A);
          if (sizeof($purchaser_rule_rows) > 0) {
             $rule_currency_total = $purchaser_rule_rows[0]['rule_currency_total'] + $purchaser_logid_rule_row['rule_currency_total'];
             $rule_currency_total = $purchaser_rule_rows[0]['rule_units_total']    + $purchaser_logid_rule_row['rule_units_total'];
             $sql = "UPDATE `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` SET `rule_currency_total` = ".$rule_currency_total.", `rule_units_total` = ".$rule_units_total." WHERE `rule_table_id`=".$rule_table_id."";
    	   	   $wpdb->query($sql);
          } 
          $order_canceled_status = 'active';
          $sql = "UPDATE `".VTPRD_LIFETIME_LIMITS_PURCHASER_LOGID_RULE."` SET `logid_status` = ".$order_canceled_status." WHERE  `purchase_log_id`  = ".$log_id."  ";
	   	    $wpdb->query($sql);                
       }   
    } //end foreach
    
    return;
  }  
              
  function vtprd_get_rule_purchaser_history($i) {     
      global $wpdb, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info; 

      //Does this rule have a lifetime limit?
      if ( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_type'] == 'none') {          
        return;
      }

       $vtprd_cart->lifetime_limit_applies_to_cart = 'yes'; 

      //if purchase history already found...
      if ($vtprd_rules_set[$i]->purch_hist_rule_row_id > ' ') {
        //reset the 'plus_discounts' fields to the original amounts and exit
        $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts    =   $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_orig;  
        $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts  =   $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_orig;         
        return;
      }

//      $rule_purchaser_rows = $wpdb->get_results("SELECT * FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` WHERE `rule_id`  = ".$vtprd_rules_set[$i]->post_id." ",ARRAY_A);

    	$varsql = "SELECT  
                pr.`rule_table_id` , 
                pr.`rule_currency_total` ,
                pr.`rule_units_total` ,
                pur.`purchaser_table_id` ,                
                pur.`purchaser_ip_address` ,                 
                pur.`purchaser_email` ,
                pur.`billto_name` ,
                pur.`billto_address` ,
                pur.`billto_city` ,
                pur.`billto_state` ,
                pur.`billto_postcode` ,
                pur.`billto_country` ,
                pur.`shipto_name` ,
                pur.`shipto_address` ,
                pur.`shipto_city` ,
                pur.`shipto_state` ,
                pur.`shipto_postcode` ,
                pur.`shipto_country`   
          FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` AS pr 
    			LEFT JOIN `".VTPRD_LIFETIME_LIMITS_PURCHASER."` AS pur
          ON  pr.`purchaser_table_id` = 	pur.`purchaser_table_id`	
    			WHERE  pr.`rule_id` = '".$vtprd_rules_set[$i]->post_id."'  ";
                      
    	$rule_purchaser_rows = $wpdb->get_results($varsql, ARRAY_A);  // yields an array of child post ids (variations, where the $$, sku etc are held).

      $vtprd_cart->purchaser_ip_address = $vtprd_info['purchaser_ip_address'];
     
      if (sizeof($rule_purchaser_rows) > 0 )  {
      
         if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ) {
          //  echo '$vtprd_setup_options <pre>'.print_r($vtprd_setup_options, true).'</pre>' ;
          //  echo '$vtprd_cart <pre>'.print_r($vtprd_cart, true).'</pre>' ;
         }
                 
        foreach ($rule_purchaser_rows as $rule_purchaser_row) {        
          
          if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ) {
           // echo '$rule_purchaser_row <pre>'.print_r($rule_purchaser_row, true).'</pre>' ; 
          }
                   
          //ip - compare db to current
          if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_ip'] == 'yes' )  {         
             if ( $rule_purchaser_row['purchaser_ip_address'] == $vtprd_cart->purchaser_ip_address ) {           
                vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                $vtprd_rules_set[$i]->purch_hist_found_why = 'ip - compare db to current';  
/*
echo 'max_purch_rule_lifetime_limit_by_ip <pre>'.print_r($vtprd_setup_options, true).'</pre>' ; //mwnt
echo '$rule_purchaser_row <pre>'.print_r($rule_purchaser_row, true).'</pre>' ; //mwnt
echo '$vtprd_cart <pre>'.print_r($vtprd_cart, true).'</pre>' ; //mwnt
echo '$vtprd_rules_set[$i] <pre>'.print_r($vtprd_rules_set[$i], true).'</pre>' ; //mwnt
 wp_die( __('<strong>Should have found purchaser row</strong>', 'vtprd'), __('VT Pricing Deals not compatible - WP', 'vtprd'), array('back_link' => true));          
*/ 
                break;  //breaks all the way out of the foreach
             }
          }
/*
echo 'max_purch_rule_lifetime_limit_by_ip <pre>'.print_r($vtprd_setup_options, true).'</pre>' ; //mwnt
echo '$rule_purchaser_row <pre>'.print_r($rule_purchaser_row, true).'</pre>' ; //mwnt
echo '$vtprd_cart <pre>'.print_r($vtprd_cart, true).'</pre>' ; //mwnt
echo '$vtprd_rules_set[$i] <pre>'.print_r($vtprd_rules_set[$i], true).'</pre>' ; //mwnt
 wp_die( __('<strong>Should have found purchaser row</strong>', 'vtprd'), __('VT Pricing Deals not compatible - WP', 'vtprd'), array('back_link' => true));          
*/ 
          //email - compare db to current
          if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_email'] == 'yes' )  { 
             if ( $rule_purchaser_row['purchaser_email'] == $vtprd_cart->purchaser_email ) {
                vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                $vtprd_rules_set[$i]->purch_hist_found_why = 'email - compare db to current';   
                break;
             }
          }
                    
          //name - compare db billto to current billto
          if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_billto_name'] == 'yes' )  { 
             if ( $rule_purchaser_row['billto_name'] == $vtprd_cart->billto_name ) {
                vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                $vtprd_rules_set[$i]->purch_hist_found_why = 'name - compare db billto to current billto';    
                break;
             }
          }
           
                    
          //address - compare db billto to current billto                                                           
          if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_billto_addr'] == 'yes' ) { 
             if (  ( $rule_purchaser_row['billto_address'] == $vtprd_cart->billto_address ) && 
                   ( $rule_purchaser_row['billto_city']    == $vtprd_cart->billto_city )    &&
                   ( $rule_purchaser_row['billto_country'] == $vtprd_cart->billto_country ) )   {
                vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                $vtprd_rules_set[$i]->purch_hist_found_why = 'address - compare db billto to current billto';    
                break;
             }
          } 


          //shipto info may be blank...
          
          //name - compare db shipto to current billto
          if ( $rule_purchaser_row['shipto_name'] > ' ' ) {
            if ( ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_billto_name'] == 'yes' ) && ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_shipto_name'] == 'yes' ) )  { 
               if ( $rule_purchaser_row['shipto_name'] == $vtprd_cart->billto_name ) {
                  vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                  $vtprd_rules_set[$i]->purch_hist_found_why = 'name - compare db shipto to current billto';    
                  break;
               }
            }
          }
          
          //name - compare db billto to current shipto
          if ( $vtprd_cart->shipto_name > ' ' ) { 
            if ( ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_billto_name'] == 'yes' ) && ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_shipto_name'] == 'yes' ) )  { 
               if ( $rule_purchaser_row['billto_name'] == $vtprd_cart->shipto_name ) {
                  vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                  $vtprd_rules_set[$i]->purch_hist_found_why = 'name - compare db billto to current shipto';    
                  break;
               }
            }
          }
          
          //name - compare db shipto to current shipto                      
          if ( $vtprd_cart->shipto_name > ' ' ) { 
            if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_shipto_name'] == 'yes' ) {
               if ( $rule_purchaser_row['shipto_name'] == $vtprd_cart->shipto_name ) {
                  vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                  $vtprd_rules_set[$i]->purch_hist_found_why = 'name - compare db shipto to current shipto';   
                  break;
               }
            }
          }                                                               
            
          //address - compare db billto to current shipto                    
          if ( $vtprd_cart->shipto_address > ' ' ) {  
            if ( ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_billto_addr'] == 'yes' ) && ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_shipto_addr'] == 'yes' ) ) { 
               if (( $rule_purchaser_row['billto_address'] == $vtprd_cart->shipto_address ) && 
                   ( $rule_purchaser_row['billto_city']    == $vtprd_cart->shipto_city )    &&
                   ( $rule_purchaser_row['billto_country'] == $vtprd_cart->shipto_country ) ) {
                  vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                  $vtprd_rules_set[$i]->purch_hist_found_why = 'address - compare db billto to current shipto';    
                  break;
               }
            }
          }  

          //address - compare db shipto to current shipto                    
          if ( $vtprd_cart->shipto_address > ' ' ) {            
            if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_shipto_addr'] == 'yes' ) {
               if ( ( $rule_purchaser_row['shipto_address'] == $vtprd_cart->shipto_address ) && 
                    ( $rule_purchaser_row['shipto_city']    == $vtprd_cart->shipto_city )    &&
                    ( $rule_purchaser_row['shipto_country'] == $vtprd_cart->shipto_country )  )  {
                  vtprd_load_purchaser_from_db_into_rule($i, $rule_purchaser_row);
                  $vtprd_rules_set[$i]->purch_hist_found_why = 'address - compare db shipto to current shipto';   
                  break;
               }
            }
          }

        }  //end foreach
      } //end sizeof                     = $wpdb->get_var("SELECT LAST_INSERT_ID() AS `rule_table_id`
  }   
      
     
  function vtprd_load_purchaser_from_db_into_rule ($i, $rule_purchaser_row) {     
      global $vtprd_rules_set, $vtprd_cart, $vtprd_setup_options; 
    	$vtprd_rules_set[$i]->purch_hist_rule_row_id                          =   $rule_purchaser_row['rule_table_id'];           
      $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_orig              =   $rule_purchaser_row['rule_units_total']; 
      $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts    =   $rule_purchaser_row['rule_units_total']; 
      $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_orig            =   $rule_purchaser_row['rule_currency_total']; 
      $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts  =   $rule_purchaser_row['rule_currency_total']; 
      $vtprd_cart->purchaser_table_id                                       =   $rule_purchaser_row['purchaser_table_id']; //purchaser table key
      if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ) {
       // echo '$rule_purchaser_row <pre>'.print_r($rule_purchaser_row, true).'</pre>' ;  
      } 
 
  }
  
     
  function vtprd_find_create_purchaser()  {           
    global $post, $wpdb, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info;
    //************************
    //find the  PURCHASER
    //************************

    $sql_where;
    $sql_count = 0;
               
      //ip - compare db to current
      if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_ip'] == 'yes' )  {         
         $sql_where += ' `purchaser_ip_address` = ".$vtprd_cart->purchaser_ip_address."  ';
         $sql_count++;
      }

      //email - compare db to current
      if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_email'] == 'yes' )  { 
         if ($vtprd_cart->purchaser_email > ' ' ) {
            if ( $sql_count > 0 ) {
              $sql_where += ' or ' ;
            }     
            $sql_where += ' `purchaser_email` = ".$vtprd_cart->purchaser_email."  ';
            $sql_count++;   
         }
      }
                
      //name - compare db billto to current billto
      if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_billto_name'] == 'yes' )  { 
         if ( $vtprd_cart->billto_name > ' ' ) {
            if ( $sql_count > 0 ) {
              $sql_where += ' or ' ;
            }                        
            $sql_where += ' `billto_name` = ".$vtprd_cart->billto_name."  ';
            $sql_count++;     
         }
      }
       
                
      //address - compare db billto to current billto                                                           
      if ( $vtprd_setup_options['max_purch_rule_lifetime_limit_by_billto_addr'] == 'yes' ) { 
         if (  ( $vtprd_cart->billto_address > ' ' ) && 
               ( $vtprd_cart->billto_city  > ' ')    &&
               ( $vtprd_cart->billto_country  > ' ') )   {
            if ( $sql_count > 0 ) {
              $sql_where += ' or ' ;
            }                        
            $sql_where += ' ( `billto_address` = ".$vtprd_cart->billto_address." and `billto_city` = ".$vtprd_cart->billto_city." and `billto_country` = ".$vtprd_cart->billto_country."  )';
            $sql_count++;     
         }  
      } 

   
    
      $sql = "SELECT  `purchaser_table_id` 
            FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER."` 	
      			WHERE  $sql_where  ";
      
      $purchaser_table_id_rows = $wpdb->get_var("SELECT  `purchaser_table_id` FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER."` WHERE $sql_where LIMIT 1",ARRAY_A);       
      if (sizeof($purchaser_table_id_rows) > 0) {
         $vtprd_cart->purchaser_table_id = $purchaser_table_id_rows[0];
         return;
      }     
     
      //************************
      //IF ***not found above***, create the  PURCHASER
      //************************
      $next_id; //supply null value for use with autoincrement table key      

      $wpdb->query("INSERT INTO `".VTPRD_LIFETIME_LIMITS_PURCHASER."` (`purchaser_table_id`,`purchaser_ip_address`,`purchaser_email`,
      `billto_name`,`billto_address`, `billto_city`,`billto_state`,`billto_postcode`,`billto_country`,
      `shipto_name`,`shipto_address`, `shipto_city`,`shipto_state`,`shipto_postcode`,`shipto_country`) 
      VALUES ('{$next_id}','{$vtprd_cart->purchaser_ip_address}','{$vtprd_cart->purchaser_email}', 
      '{$vtprd_cart->billto_name}','{$vtprd_cart->billto_address}','{$vtprd_cart->billto_city}','{$vtprd_cart->billto_state}','{$vtprd_cart->billto_postcode}','{$vtprd_cart->billto_country}',
      '{$vtprd_cart->shipto_name}','{$vtprd_cart->shipto_address}','{$vtprd_cart->shipto_city}','{$vtprd_cart->shipto_state}','{$vtprd_cart->shipto_postcode}','{$vtprd_cart->shipto_country}' );");
      //
      $vtprd_cart->purchaser_table_id  = $wpdb->get_var("SELECT LAST_INSERT_ID() AS `purchaser_table_id` FROM `".VTPRD_LIFETIME_LIMITS_PURCHASER."` LIMIT 1");
    
    
    return;
      
 }
    
    