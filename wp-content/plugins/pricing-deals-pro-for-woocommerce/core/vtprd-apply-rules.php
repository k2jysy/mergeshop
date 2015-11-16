<?php

/*
       *******************************
       *  v1.1.1.2 CHANGES       
       *******************************
       Alter auto_add_for_free to allow MULTIPLE rules processing       
       
       New cart fields:
          product_auto_insert_rule_id;
          
      Change in session_variable structure:
      
        $current_auto_add_array = array ();  ==>? now indexed by $free_product_id
          
        $current_auto_add_array_row = array (       
          'free_product_id' => '',
          'free_product_add_action_cnt' => '', 
          'free_product_in_inPop' => '',
          'free_rule_actionPop' => '',         
          'rule_id' => '',
          'current_qty' => '', 
          'purchased_qty' => '',
          'candidate_qty' => '',
          'free_qty' => '',
          'variations_parameter' => ''                  
      );         

      At add to INPOP and ACTIONPOP time add to array time, verify that any Candidate or free items
      already in the Cart **ARE ONLY** carried into the inpop and actionpop arrays
      for a given rule, **IF** the rule id == product_auto_insert_rule_id .

    *  v1.1.1.2 end 
*/


/*
v1.1.0.6 begin

Issue:
  When Buy qty/amt Pop 1 get *next* Pop2 (different) gwith repeating discount
    when the discount items are 'behind' the qualifying items in the cart
    there is an overall problem
    
  Approach:
    TOP
      - use inpop exploded copy
      - process inpop exploded until inpop criteria found
      - Remove found inpop from exploded inpop copy
      - Remove  found inpop from from exploded actionpop copy, as neeed
      - process actionpop
      - makr discounts in MASTER actionpop array
      - remove Discounted actionpop items from actionpop copy
      - return to TOP 

*/

       /*
       *******************************
       *  v1.1.0.6 CHANGES       
       *******************************
       
       *******
       PRE_Process
       *******
       *       
       Logic paths ==>>
       autoadds are now added REALTIME as needed, rather than in advance  
       
       always delete old autoadds in pre-process
       
       Internal:
          free product same as purchased product
       External:
          free product based on purchases of different product(s)
       Mixed:
          free product based on BOTH purchases of same and/or different product(s)
          
       **************   
       Deleting previous auto inserts:
        if previous auto-added product current qty = 0,
          all done!
        if previous auto-added product current qty >= previous total,
          roll out previous auto-added product qty
          if nothing left
            remove auto-added product from vtprd-cart
          all done!
        if previous auto-added product current qty < previous total
          If previous auto-added product had **any purchases**
            roll back to that previous purchase total
          else
            roll to zero
            remove auto-added product from vtprd-cart
        
        Log current purchased qty, if any, in current session variable
        
        Delete previous session variables
          
        
        ** $vtprd_setup_options['bogo_auto_add_the_same_product_type'] ** no longer needed, I think    
       
        
       *******
       PROCESS
       *******
       * 
       at top of function vtprd_process_actionPop_and_discount    
        - is rule an auto add
        - if so, check to see how many of auto add product should be added in this iteration
        - add auto add qty to:
          vtprd-cart
          action array (end of, or add to qty)
          action exploded array
            - if product not in array, add at end
            - if product in array, add in the middle of the array, just after last occurrence of product
        - ADD auto-add qty to Current Session Variable
        - check 'sizeof' statements to make sure they're local, not global 
        
        - Process as Normal!!          
       
       
       *******
       POST_Process
       *******
       * 
       Cleanup =>
        
       Add/Modify?delete in Woo Cart, 
        based on inserted qty
       
       
       - At end, make set Previous session variable = current
       - Delete current session variable
       
        *******************************
       *  v1.1.0.6 end CHANGES       
       *******************************     
 */       
class VTPRD_Apply_Rules{
	
	public function __construct(){
		global $woocommerce, $vtprd_cart, $vtprd_rules_set, $vtprd_info, $vtprd_setup_options, $vtprd_rule;
    
    
    //GET RULES SET     
    $vtprd_rules_set = get_option( 'vtprd_rules_set' );
    if ($vtprd_rules_set == FALSE) {
      return;
    }

    if ($vtprd_info['current_processing_request'] == 'cart') {  
 /* 
      //If Cart processing and nothing in the cart, exit...
      if (sizeof($woocommerce->cart_items) == 0) {
        return;
      } 
  */   
      
     //v1.0.9.4  moved here to cover 
     //  when JUST a catalog discount was processed, CART still needs loading               
     //Move parent cart contents to vtprd_cart 
      vtprd_load_vtprd_cart_for_processing(); 
      
      //sort for "cart" rules and delete "display" rules
      $this->vtprd_sort_rules_set_for_cart();
            
      //after sort for cart/remove display rows, are there rows left?
      if ( sizeof($vtprd_rules_set) == 0) {
        return;
      } 
      
      //**********************
      /*  At top of routine to set a coupon discount baseline as relevant
        (b) if we're on the checkout page, and a coupon has been added/removed
        (c) if an auto-add is in the cart (which should really be skipped), it doesn't matter, it'll get picked up and corrected in the  maybe_update_parent_cart_for_autoAdds function
        (d) new coupon behavior:  With an auto add, "apply with coupons" is required
            and the Coupon will ALWAYS be skipped instead of the rule.  this is accomplished by re-running the vtprd_maybe_compute_coupon_discount function again
            (i) after the previous auto adds have been rolled out and
            (ii) before any new auto adds are rolled in 
      */
      //v1.0.9.4 added if
      //v1.1.0.9  IF removed ==>> prevented the 'other coupoon = no' from working
      /*
      if ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon')  { 
        vtprd_count_other_coupons();
      }
      */
      vtprd_count_other_coupons();
      //**********************
               
     //v1.0.9.4  moved above
     //Move parent cart contents to vtprd_cart 
     // vtprd_load_vtprd_cart_for_processing(); 


      //autoAdd into internal arrays, as needed 
      $this->vtprd_pre_process_cart_for_autoAdds();     
      
      
      $this->vtprd_process_cart(); 
   
      
      //Update the parent cart for any auto add free products...
      $this->vtprd_post_process_cart_for_autoAdds();

    } else {
      
      //sort for "display" rules and delete "cart" rules
      $this->vtprd_sort_rules_set_for_display();
      
      //after sort for display/remove cart rows, are there rows left?
      if ( sizeof($vtprd_rules_set) == 0) {
        return;
      } 
            
      // **********************************************************  
      //  This path is for display rules only, where a SINGLE product
      //     has been loaded into the cart to test for a Display discount
      // **********************************************************       
      $this->vtprd_process_cart();
                 
    }  

    if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
      error_log( print_r(  '$woocommerce->cart at APPLY-RULES END', true ) );
      error_log( var_export($woocommerce->cart, true ) );
      error_log( print_r(  '$vtprd_info at APPLY-RULES END', true ) );
      error_log( var_export($vtprd_info, true ) );
      session_start();    //mwntest
      error_log( print_r(  '$_SESSION at APPLY-RULES END', true ) );
      error_log( var_export($_SESSION, true ) );
      error_log( print_r(  '$vtprd_rules_set at APPLY-RULES END', true ) );
      error_log( var_export($vtprd_rules_set, true ) );
      error_log( print_r(  '$vtprd_cart at APPLY-RULES END', true ) );
      error_log( var_export($vtprd_cart, true ) );
      error_log( print_r(  '$vtprd_setup_options at APPLY-RULES END', true ) );
      error_log( var_export($vtprd_setup_options, true ) );  
    }

/*
echo '<pre>'.print_r($vtprd_cart, true).'</pre>' ;
echo '<pre>'.print_r($vtprd_rules_set, true).'</pre>' ;
echo '<pre>'.print_r($woocommerce->cart, true).'</pre>' ;
echo 'vtprd_info <pre>'.print_r($vtprd_info, true).'</pre>' ;
echo 'SESSION data <pre>'.print_r($_SESSION, true).'</pre>' ;      
 

echo '<pre>'.print_r($vtprd_setup_options, true).'</pre>' ;
echo '<pre>'.print_r($vtprd_info, true).'</pre>' ; 
wp_die( __('<strong>Looks like</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WP', 'vtmin'), array('back_link' => true));         
   
*/


 
    return;      
	}
 

  public function vtprd_process_cart() { 
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info;	
      
    //error_log( print_r(  'vtprd_process_cart ', true ) ); 
 
    //cart may be empty...
    if (sizeof($vtprd_cart) == 0) {
      $vtprd_cart->cart_level_status = 'rejected';
      $vtprd_cart->cart_level_auditTrail_msg = 'No Products in the Cart.';
      return;
    }
    
    //v1.0.7.4 begin
    if ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon')  { //v1.0.9.4
      if ( ($vtprd_info['current_processing_request'] == 'cart') &&
           ($vtprd_info['skip_cart_processing_due_to_coupon_individual_use']) )  {
        $vtprd_cart->cart_level_status = 'rejected';
        $vtprd_cart->cart_level_auditTrail_msg = 'Another Coupon with Individual_use = "yes" has been activated.  Cart processing may not continue.';          
        return;
      }
    }
    //v1.0.7.4 end
        
    //test all rules for inPop and actionPop participation 
    $vtprd_cart->at_least_one_rule_actionPop_product_found = 'no';
    //
    $this->vtprd_test_cart_for_rules_populations();
    //        

   if ($vtprd_cart->at_least_one_rule_actionPop_product_found != 'yes') {
      $vtprd_cart->cart_level_status = 'rejected';
      $vtprd_cart->cart_level_auditTrail_msg = 'No actionPop Products found.  Processing ended.';     
      return;
   } 
    
    /* if price or template code request (display), there's only one product in the cart for the call
       if either of these conditions exist:
          no display rules found
          or product does not participate in a display rule
            product_in_rule_allowing_display will be 'no'      
    */
    if ( ($vtprd_info['current_processing_request'] == 'display') &&
         ($vtprd_cart->cart_items[0]->product_in_rule_allowing_display == 'no') )  {
      $vtprd_cart->cart_level_status = 'rejected';
      $vtprd_cart->cart_level_auditTrail_msg = 'A single product "Display" request sent, product not in any Display rule.  Processing ended.';          
      return;
    }

    //v1.0.9.3 begin
    if ($vtprd_info['current_processing_request'] == 'cart') {
      $vtprd_cart->cart_contents_orig_subtotal = vtprd_get_Woo_cartSubtotal(); 
    }
    //v1.0.9.3 end

    //test all rules whether in and out counts satisfied    
    $this->vtprd_process_cart_for_rules_discounts();


    return;
 }   

  //************************************************
  //Load inpop found list and actionopop found list
  //************************************************
  public function vtprd_test_cart_for_rules_populations() { 
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info;
    
    //error_log( print_r(  'vtprd_test_cart_for_rules_populations ', true ) );     
     
    //************************************************
    //BEGIN processing to mark product as participating in the rule or not...
    //************************************************
    
    /*  Analyze each rule, and load up any cart products found into the relevant rule
        fill rule array with product cart data :: load inPop info 
    */  

    //************************************************
    //FIRST PASS:
    //    - does the product participate in either inPop or actionPop 
    //************************************************
    $sizeof_rules_set = sizeof($vtprd_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) {                                                               

      //v1.1.0.8 begin
      //only activate if coupon presented
      //in wp-admin, new coupon should be created as a 'cart discount' with a 'coupon amount'.  (just used to activate the rule)
      if ($vtprd_rules_set[$i]->only_for_this_coupon_name > ' ') {
        $coupon_found = false;
        $applied_coupons = WC()->cart->get_coupons();
        foreach ( $applied_coupons as $code => $coupon ) {
          if ( $code == $vtprd_rules_set[$i]->only_for_this_coupon_name ) {
            $coupon_found = true;
            break;
          }	        
        }
        if (!$coupon_found) {
          $vtprd_rules_set[$i]->rule_status = 'noCouponFound';
        }
      }  
      //v1.1.0.8 end

      //pick up existing invalid rules
      if ( $vtprd_rules_set[$i]->rule_status != 'publish' ) { 
        continue;  //skip out of this for loop iteration
      } 
      
      $this->vtprd_manage_shared_rule_tests($i);      

            // test whether the product participates in either inPop or actionPop
      if ( $vtprd_rules_set[$i]->rule_status != 'publish' ) { 
          continue;  //skip out of this for loop iteration
      } 

      
      
      //****************************************************
      // ONLY FOR AUTO ADD - overwrite actionPop and discountAppliesWhere
      //******************
      //  - timing of this overwrite is different for auto adds...
      //  - NON auto adds are done below
      //**************************************************** 
      if ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') {
        if ($vtprd_rules_set[$i]->set_actionPop_same_as_inPop == 'yes') {
          $vtprd_rules_set[$i]->actionPop = 'sameAsInPop';
          //v1.1.0.6 begin
          // as most free candidate are in the ActionPop only, change to 'nextInActionPop'
          //$vtprd_rules_set[$i]->discountAppliesWhere =  'nextInInPop';
          $vtprd_rules_set[$i]->discountAppliesWhere =  'nextInActionPop';
          //v1.1.0.6 end
        }
      }
      
      
       
      //Cart Main Processing
      $sizeof_cart_items = sizeof($vtprd_cart->cart_items);
    //error_log( print_r(  '$sizeof_cart_items= ' .$sizeof_cart_items, true ) );
      for($k=0; $k < $sizeof_cart_items; $k++) {                 
        //only do this check if the product is on special!!
    //error_log( print_r(  '$vtprd_cart->cart_item $k= ' .$k, true ) );
    //error_log( var_export($vtprd_cart->cart_items[$k], true ) );        
        if ($vtprd_cart->cart_items[$k]->product_is_on_special == 'yes')  { 
          $do_continue = '';  //v1.0.4 set = to ''
          switch( $vtprd_rules_set[$i]->cumulativeSalePricing) {
            case 'no':              
                //product already on sale, can't apply further discount
                //v1.1.0.4 
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'rejected';
                $product_name = $vtprd_cart->cart_items[$k]->product_name;
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  'For product= ' .$product_name. '  No Discount - product already on sale, can"t apply further discount - discount in addition to sale pricing not allowed';
                //v1.1.0.4 end
                
                $do_continue = 'yes';                  
              break;
            case 'addToSalePrice':               
               //just act naturally, apply the discount to the price we find, which is already the Sale Price...
              break;
            case 'replaceSalePrice':     //ONLY applies if discount is greater than sale price!!!!!!!!
                /*  **********************************************************
                  At this point in time, unit and db_unit both contain the Sale Price,
                  Overwrite the sale price with the list price, process as normal, then check at the bottom...
                  if the discount is <= the existing sale price, DO NOT APPLY AS DISCOUNT!
                  ********************************************************** */ 
                $vtprd_cart->cart_items[$k]->unit_price     = $vtprd_cart->cart_items[$k]->db_unit_price_list;
                $vtprd_cart->cart_items[$k]->db_unit_price  = $vtprd_cart->cart_items[$k]->db_unit_price_list;               
              break;
          } //end cumulativeSalePricing check
                   
          if ($do_continue) {            
            continue; //skip further processing for this iteration of the "for" loop
          }
        }  //end product is on special check
        //set up cart audit trail info, keyed to rule prod_id
        $this->vtprd_init_cartAuditTrail($i,$k);

        //does product participate in inPop
        $this->vtprd_test_if_inPop_product($i, $k);       
         
        $this->vtprd_test_if_actionPop_product($i, $k);                                                            


      } //end cart-items 'for' loop
 
    
    }  //end rules 'for' loop
 

      return;   
   }                              
 
        
   public function vtprd_manage_shared_rule_tests($i) { 
      global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
      
    //error_log( print_r(  'vtprd_manage_shared_rule_tests ', true ) ); 
     
      $rule_is_date_valid = vtprd_rule_date_validity_test($i);
      if (!$rule_is_date_valid) {
         $vtprd_rules_set[$i]->rule_status = 'dateInvalid';  //temp chg of rule_status for this execution only
         $vtprd_rules_set[$i]->rule_processing_status = 'Cart Transaction does not fall within date boundaries set for the rule.';
      }

      //IP is immediately available, check against Lifetime limits
      //  check against all rules
  
      if ($vtprd_setup_options['use_lifetime_max_limits'] == 'yes')  {
        //populate rule with purchaser history
        vtprd_get_rule_purchaser_history($i);        
        $rule_has_reached_lifetime_limit = vtprd_rule_lifetime_validity_test($i);
        if ($rule_has_reached_lifetime_limit) {
           $vtprd_rules_set[$i]->rule_status = 'lifetimeMaxInvalid';  //temp chg of rule_status for this execution only
           $vtprd_rules_set[$i]->rule_processing_status = 'Rule has already reached lifetime max for IP purchases.';
        }
      }  
          
     //v1.1.0.9 begin - coupon_activated_discount for later use in parent-cart-validation during remove_coupon, as needed
     //  if coupon removed, this promotes the re-run of the discount at the same time. (re-use of session var, similar situation from v1.1.0.8)    
      //don't run if 'no'
      if ($vtprd_rules_set[$i]->cumulativeCouponPricing == 'no') {
         
         //cumulativeCouponNo for later use in parent-cart-validation during add/remove_coupon, as needed
         //  if coupon removed, this promotes the re-run of the discount at the same time.
         //  This session var set to true will cause a re-process of the cart on Cart and checkout pages ONLY        
        if(!isset($_SESSION)){
          session_start();
          header("Cache-Control: no-cache");
          header("Pragma: no-cache");
        }
        $_SESSION['cumulativeCouponNo'] = true;
        
   //error_log( print_r(  'set cumulativeCouponNo=  true', true ) );        
//        $coupon_cnt_without_deals_coupon = sizeof($vtprd_info['coupon_codes_array']);
   //error_log( print_r(  'only_for_this_coupon_name= ' .$vtprd_rules_set[$i]->only_for_this_coupon_name , true ) ); 
   //error_log( print_r(  '$coupon_cnt= ' .$coupon_cnt_without_deals_coupon, true ) ); 
   //error_log( print_r(  'coupon_codes_array= ' , true ) ); 
   //error_log( var_export($vtprd_info['coupon_codes_array'], true ) );          
        
        $sizeof_coupon_codes_array = (sizeof($vtprd_info['coupon_codes_array'])) ; 
        if ( ($vtprd_rules_set[$i]->only_for_this_coupon_name > ' ')  &&
             ($sizeof_coupon_codes_array == 1) &&
             (in_array($vtprd_rules_set[$i]->only_for_this_coupon_name, $vtprd_info['coupon_codes_array'] )) ) {
          //activated by coupon and 1coupon found, so all ok
          $all_good = true;
        } else {
           //coupons array is **without** deals coupon
           if ($sizeof_coupon_codes_array > 0) {
             $vtprd_rules_set[$i]->rule_status = 'cumulativeCouponPricingNo';  //temp chg of rule_status for this execution only
             $vtprd_rules_set[$i]->rule_processing_status = 'Coupon presented, rule switch says do not run.';         
            }        
        }                     
      }
      //v1.1.0.9 end     
   
   } 
   
  // ****************  
  // inPop TESTS
  // ****************     
        
   public function vtprd_test_if_inPop_product($i, $k) { 
      global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
      
    //error_log( print_r(  'vtprd_test_if_inPop_product ', true ) ); 
       
      /*  v1.0.5 
      ADDTIONAL RULE CRITERIA FILTER - optional, default = TRUE   (useful to add additional checks on a specific rule)
      
      all data needed accessible through global statement, eg global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
        Rule ID = $vtprd_rules_set[$i]->post_id
       filter can check for specific rule_id, and apply criteria.
         if failed additional criteria check, return FALSE, so that the rule is not executed 
      To Execute, sample:
        add_filter('vtprd_additional_inpop_include_criteria', 'your function name', 10, 3);
        $i = ruleset occurrence ($vtprd_rules_set[$i])
        $k = cart occurence  ($vtprd_cart->cart_items[$k])
        
      LOOK FOR 'process_additional_inpop_include_criteria'  example at the bottom of the document... 
      */
      switch( $vtprd_rules_set[$i]->inPop ) {  
           case 'wholeStore':                                                                                      
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_inpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                
                //load whole cart into inPop
                $this->vtprd_load_inPop_found_list($i, $k);                              
            break;
          case 'cart':  
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_inpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                                                                                                              
                //load whole cart into inPop               
                $this->vtprd_load_inPop_found_list($i, $k);                              
            break;
          case 'groups':
                                 
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_inpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                
              //test if product belongs in rule inPop
              if ( $this->vtprd_is_product_in_inPop_group($i, $k) ) {
                $this->vtprd_load_inPop_found_list($i, $k);                        
              } else {
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product not found in group';
              }
            break;
          case 'vargroup':   
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_inpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                              
              switch( true) {  
                case (in_array($vtprd_cart->cart_items[$k]->product_id, $vtprd_rules_set[$i]->var_in_checked )):
                    //product name on cart is the owning product name.  to get the variation name, get the post title, load it into the cart...                  
                    //$this->vtprd_load_inPop_found_list($i, $k);  //v1.1 moved
                    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Variation found in checked list';
                  break;
                /*MWNTEST AJAX
                        //for later ajaxVariations pricing
                case  ( ($vtprd_info['current_processing_request'] == 'display') && 
                        ($vtprd_rules_set[$i]->rule_execution_type == 'display') && 
                       // ($vtprd_cart->cart_items[$k]->this_is_a_parent_product_with_variations == 'yes') && 
                        ($vtprd_cart->cart_items[$k]->product_id  == $vtprd_rules_set[$i]->inPop_varProdID ) ) :
                    //product name on cart is the owning product name.  to get the variation name, get the post title, load it into the cart...                  
                    $this->vtprd_load_inPop_found_list($i, $k);
                    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Variation Parent found for Variation list';
                  break;
                  */
                default:  
                    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Variation not found in checked list';
                  break 2; //v1.1 break out of both case structures as well
              }
              
              //v1.1 begin
              if ( sizeof($vtprd_rules_set[$i]->role_in_checked) > 0 ) {
                if($this->vtprd_is_role_in_inPop_list_check($i, $k) ) {
                  $this->vtprd_load_inPop_found_list($i, $k); //both var and role found
                } 
              } else {
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'No Roles checked for rule';
                $this->vtprd_load_inPop_found_list($i, $k); //var found and no role checked
              }
              //v1.1 end
              
            break;
          case 'single':
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_inpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                              
              //one product to rule them all
              if ($vtprd_cart->cart_items[$k]->product_id == $vtprd_rules_set[$i]->inPop_singleProdID) {
                //$this->vtprd_load_inPop_found_list($i, $k);  //v1.1 moved below
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Single product found';
              } else {
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Single product not found';
                break; //v1.1 stop here
              }
                            
              //v1.1 begin
              if ( sizeof($vtprd_rules_set[$i]->role_in_checked) > 0 ) {
                if($this->vtprd_is_role_in_inPop_list_check($i, $k) ) {
                  $this->vtprd_load_inPop_found_list($i, $k); //both product and role found
                } 
              } else {
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'No Roles checked for rule';
                $this->vtprd_load_inPop_found_list($i, $k); //product found and no role checked
              }
              //v1.1 end
              
            break;
        } 
    } 


   // **************** 
  // actionPop TESTS        
  // **************** 
           
   public function vtprd_test_if_actionPop_product($i, $k) { 
   
      global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
      
    //error_log( print_r(  'vtprd_test_if_actionPop_product ', true ) );      
      /*  v1.0.5 
      ADDTIONAL RULE CRITERIA FILTER - optional, default = TRUE   (useful to add additional checks on a specific rule)
      
      all data needed accessible through global statement, eg global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
        Rule ID = $vtprd_rules_set[$i]->post_id
       filter can check for specific rule_id, and apply criteria.
         if failed additional criteria check, return FALSE, so that the rule is not executed 
      To Execute, sample:
        add_filter('vtprd_additional_actionpop_include_criteria', 'your function name', 10, 3);
        $i = ruleset occurrence ($vtprd_rules_set[$i])
        $k = cart occurence  ($vtprd_cart->cart_items[$k])
      */
      switch( $vtprd_rules_set[$i]->actionPop ) {  
          case 'sameAsInPop':
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_actionpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Actionpop Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                                                
                //if current product in inpop products array...
                if ( in_array($vtprd_cart->cart_items[$k]->product_id, $vtprd_rules_set[$i]->inPop_prodIds_array) ) {
                  $this->vtprd_load_actionPop_found_list($i, $k);
                } else {
                  $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Product not found in inpop list, so not included on actionPop';
                }
            break;
          case 'wholeStore':
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_actionpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Actionpop Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                                
                $this->vtprd_load_actionPop_found_list($i, $k);
            break;            
          case 'cart':                                                                                      
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_actionpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Actionpop Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                
                //load whole cart into actionPop
                $this->vtprd_load_actionPop_found_list($i, $k);
            break;
          case 'groups':
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_actionpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Actionpop Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                              
              //test if product belongs in rule actionPop
              if ( $this->vtprd_is_product_in_actionPop_group($i, $k) ) {
                $this->vtprd_load_actionPop_found_list($i, $k);                        
              } else {
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Variation not found in group';
              }
            break;
          case 'vargroup':
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_actionpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Actionpop Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                              
              if (in_array($vtprd_cart->cart_items[$k]->product_id, $vtprd_rules_set[$i]->var_out_checked )) {   //if IDS is in previously checked_list                
                //product name on cart is the owning product name.  to get the variation name, get the post title, load it into the cart...                 
                $this->vtprd_load_actionPop_found_list($i, $k);
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Product found in Variation checked list';
              } else {
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Product not found in Variation checked list';
              }
            break;
          case 'single':
                
                //v1.0.5 begin                
                $additional_include_criteria = apply_filters('vtprd_additional_actionpop_include_criteria',TRUE,$i, $k );
                if ($additional_include_criteria == FALSE) {
                   $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Actionpop Product rejected by additional criteria filter';
                   continue;
                }
                //v1.0.5 end
                              
              //one product to rule them all
              if ($vtprd_cart->cart_items[$k]->product_id == $vtprd_rules_set[$i]->actionPop_singleProdID) {
                $this->vtprd_load_actionPop_found_list($i, $k);
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Single product found';
              } else {
                $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Single product not found';
              }              
            break;
        } 
    } 



  public function vtprd_process_cart_for_rules_discounts() {
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info;  
      
    //error_log( print_r(  'vtprd_process_cart_for_rules_discounts ', true ) ); 
        
    //************************************************
    //SECOND PASS - have the inPop, output and rule conditions been met
    //************************************************
    $sizeof_rules_set = sizeof($vtprd_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) {         
      if ( $vtprd_rules_set[$i]->rule_status != 'publish' ) {          
        continue;  //skip the rest of this iteration, but keep the "for" loop going
      }

      //THIS WOULD ONLY BE A MESSAGE REQUEST AT DISPLAY TIME for a single product on a Cart rule      
      if ($vtprd_info['current_processing_request'] == 'display') {  
          if ($vtprd_rules_set[$i]->rule_execution_type == 'cart') {
            $vtprd_info['product_session_info']['product_rule_short_msg_array'][] = $vtprd_cart->cart_items[0]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['rule_short_msg'];
            $vtprd_info['product_session_info']['product_rule_full_msg_array'][]  = $vtprd_cart->cart_items[0]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['rule_full_msg'];
            $vtprd_cart->cart_items[0]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'MessageRequestCompleted';
            $vtprd_cart->cart_items[0]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  'Display Message for Cart rule successfully sent back.';           
            continue;  //skip the rest of this iteration, but keep the "for" loop going
          }
      } 
      
      //**********************
      //v1.1.0.6  begin
      //roll out free candidates if unused following autoadd rule ==>> autoadd rule sorted always to be 1st!!!
      /*
      NO LONGER NECESSARY - ACTIONPOP LOAD IGNORES NON-PURCHASED STUFF IF ***NOT*** IN TEH AUTO-ADD RULE!!!!!!
      If ( ($i == 1) &&
           ($vtprd_rules_set[0]->rule_contains_auto_add_free_product == 'yes') ) {
        // are there unused autoadd candidates?
        // if so, roll them out of the vtprd_cart, exploded as well
        $this->vtprd_maybe_roll_out_auto_inserted_products(0);    
      }
      */
      //v1.1.0.6  end
      //**********************
      

      //no point in continuing of no actionpop to discount for this rule...
      if ( sizeof($vtprd_rules_set[$i]->actionPop_found_list) == 0 ) {
       // $vtprd_rules_set[$i]->rule_requires_cart_action = 'no';
        $vtprd_rules_set[$i]->rule_processing_status = 'No action population products found for this rule.';
        continue;   
      }      
      //reset inPop running totals for each rule iteration
      $vtprd_rules_set[$i]->inPop_group_begin_pointer     = 1; //begin with 1st iteration
      $vtprd_rules_set[$i]->inPop_exploded_group_begin   = 0;
      $vtprd_rules_set[$i]->inPop_exploded_group_end     = 0;

      //reset actionPop running totals => they will aways reflect the inPop, unless using different actionPop
      $vtprd_rules_set[$i]->actionPop_group_begin_pointer     = 1;  //begin with 1st iteration
      $vtprd_rules_set[$i]->actionPop_exploded_group_begin   = 0;  
      $vtprd_rules_set[$i]->actionPop_exploded_group_end     = 0; 

    /* ******************
     PROCESS CART FOR DISCOUNT: group within rule until: info lines done / processing completed / inpop ended
     ********************* */       
      
      //Overriding Control Status Switch Setup
      $vtprd_rules_set[$i]->discount_processing_status = 'inProcess'; // inProcess / completed /  InPopEnd
     // $vtprd_rules_set[$i]->end_of_actionPop_reached = 'no';   

      // ends with sizeof being reached, OR  $vtprd_rules_set[$i]->discount_processing_status == 'yes'
      $sizeof_rule_deal_info = sizeof($vtprd_rules_set[$i]->rule_deal_info);
      for($d=0; $d < $sizeof_rule_deal_info; $d++) {
        switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_repeat_condition'] ) {
            case 'none':     //only applies to 1st rule deal line
                 /* 
                 There can be multiple conditions which are covered by inserting a repeat count = 1.
                 Most often, the rule applies to the entire actionPop.  If that is the case, the 
                 actionPop Loop will run through the whole actionPop in one go, to process all of the 
                 discounts.  This is a hack, as it really should be governed here.                 
                 */
                $buy_repeat_count = 1;
              break;
            case 'unlimited':   //only applies to 1st rule deal line
                $buy_repeat_count = 999999;
              break;
            case 'count':     //can only occur when there's only one rule deal line
                $buy_repeat_count = $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_repeat_count'];
              break;  
        }  
  
        //REPEAT count only augments IF a discount successfully processes...
        for($br=0; $br < $buy_repeat_count; $br++) {
   //error_log( print_r(  'Just above vtprd_repeating_group_discount_cntl, $br= ' . $br, true ) );            
           $this->vtprd_repeating_group_discount_cntl($i, $d, $br );             
           
           if ($vtprd_rules_set[$i]->discount_processing_status != 'inProcess') { 
   //error_log( print_r(  'rule no longer inProcess, broke out of $br loop ' . $br, true ) );              
             break; // exit repeat for loop
           }                     
        } // $buy_repeat_count for loop        
    
        //v1.0.4 begin => lifetime counted by group (= 'all') up count here, once per rule/cart
        if ( ($vtprd_rules_set[$i]->discount_total_amt_for_rule > 0) &&             
             ($vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_type'] == 'quantity') &&
             ($vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_count'] > 0) &&
             ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'all') ) {
          $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts    +=  1; // +1 for each RULE OCCURRENCE usage...
        }
        //v1.0.4 end 
                  
      }  //rule_deal_info for loop
       
      
     /*  THIS IS ONLY NECESSARY IN WPEC, NOT WOO,
         as in woo the adds haven't happened yet - nothing to roll out in this situation...
      
      //***********************************************************
      // If a product was auto inserted for a free discount, but does *not*
      //     receive that discount,
      //   Roll the auto-added product 'UNfree' qty out of the all of the rules actionPop array
      //      AND out of vtprd_cart, removing the product entirely if necessary.
      //***********************************************************
      if ( ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') &&
           (sizeof($vtprd_rules_set[$i]->auto_add_inserted_array) > 0) )  {        
        $this->vtprd_maybe_roll_out_auto_inserted_products($i); 
      }     
      
      */
      
      //***********************************************************
      // If a product has been given a 'Free' discount, it can't get
      //     any further discounts.
      //   Roll the product 'free' qty out of the rest of the rules actionPop array
      //***********************************************************
      if (sizeof($vtprd_rules_set[$i]->free_product_array) > 0) {
        $this->vtprd_roll_free_products_out_of_other_rules($i); 
      }
      
      //v1.0.8.4 begin
      //  used in following rule processing iterations, if cumulativeRulePricing == 'no'
      //v1.0.9.3 added if isset
      if ( (isset ($vtprd_info['applied_value_of_discount_applies_to']) ) &&
         ( ($vtprd_info['applied_value_of_discount_applies_to']  == 'cheapest') ||
           ($vtprd_info['applied_value_of_discount_applies_to']  == 'most_expensive') ||
           ($vtprd_info['applied_value_of_discount_applies_to']  == 'all') ) ) {
         $this->vtprd_mark_products_in_an_all_rule($i);
      } 
      /*
      if ( ($vtprd_info['applied_value_of_discount_applies_to']  == 'cheapest') ||
           ($vtprd_info['applied_value_of_discount_applies_to']  == 'most_expensive') ||
           ($vtprd_info['applied_value_of_discount_applies_to']  == 'all') ) {
         $this->vtprd_mark_products_in_an_all_rule($i);
      } 
      */     
      //v1.0.8.4 begin  

      //v1.1.0.8 begin
      //only activate if coupon presented
      // coupon_activated_discount for later use in parent-cart-validation during remove_coupon, as needed
      if ( ($vtprd_rules_set[$i]->only_for_this_coupon_name > ' ')  &&
           ($vtprd_rules_set[$i]->discount_total_amt_for_rule > 0) ) {
        if(!isset($_SESSION)){
            session_start();
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
          }
          $_SESSION['coupon_activated_discount'] = true;
      }
      //v1.1.0.8 end
         
    }  //ruleset for loop
    return;    
  }

  //$i = rule index, $d = deal index, $br = repeat index
  //***********************************************************
  // Take a Single BUY group all the way through the discount process,
  //     Performed by  REPEAT NUM  within DEAL LINE within RULE
  //***********************************************************
  public function vtprd_repeating_group_discount_cntl($i, $d, $br) {
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework, $vtprd_deal_structure_framework;        

    //error_log( print_r(  'Top Of vtprd_repeating_group_discount_cntl, $br= ' . $br, true ) ); 
            
    //initialize rule_processing_trail(
    $vtprd_rules_set[$i]->rule_processing_trail[] = $vtprd_deal_structure_framework;
    $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupBeingTested';

    // previously determined template key
    $templateKey = $vtprd_rules_set[$i]->rule_template; 
   
    //if buy_amt_type is active and there is a buy_amt count...
    //***********************************************************
    //THIS SETS THE SIZE OF THE BUY exploded GROUP "WINDOW"
    //***********************************************************
    // Initialize the amt qty as needed
    if ($vtprd_template_structures_framework[$templateKey]['buy_amt_type'] > ' ' ) { 
      if ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] == 'none' ) ||  
           ($vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] == 'one' ) ) {
         $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] = 'quantity';
         $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count'] = 1;
         if ($vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to']  <= ' ') {
           $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to']  = 'all';
         }
      }
    } else {
       $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] = 'quantity';
       $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count'] = 1;
       $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to']  = 'all';
    }    
    
    // INPOP_EXPLODED_GROUP_BEGIN setup
    if ($br == 0) { //is this the 1st time through the buy repeat?  
      $vtprd_rules_set[$i]->inPop_exploded_group_begin = 0;
    } else {    //if 2nd-nth time      
       switch ($vtprd_rules_set[$i]->discountAppliesWhere) {
        case 'allActionPop':        //process all actionPop in one go , 'allActionPop'
        case 'inCurrentInPopOnly':  //treats inpop group as a unit, so we get the next inpop group unit                    
        case 'nextInActionPop':     //FOR all 3 values, add 1 to end            
            $vtprd_rules_set[$i]->inPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_end;// + 1;
          break;
        case 'nextInInPop':   //we're bouncing between inpop and actionpop, so use actionPop end + 1 here:
            $vtprd_rules_set[$i]->inPop_exploded_group_begin = $vtprd_rules_set[$i]->actionPop_exploded_group_end;// + 1;
          break;     
      }
 
    }

    //*************************************************************
    //1st pass through data, set the begin/end pointers, 
    // verify 'buy' conditions met
    //*************************************************************
    $this->vtprd_set_buy_group_end($i, $d, $br );     //vtprd_buy_amt_process   
    
    //if buy amt process failed, exit
    if ($vtprd_rules_set[$i]->rule_processing_status == 'cartGroupFailedTest') {
      //if buy criteria not met, discount processing for rule is done
      $vtprd_rules_set[$i]->discount_processing_status = 'InPopEnd';
   //error_log( print_r(  'reached INPOPEND, $br= ' . $br, true ) );       
      return;
    } 

    //***************
    //ACTION area
    //***************
    switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_repeat_condition'] ) {
      case 'none':     //only one rule deal line
          $action_repeat_count = 1;
        break;
      case 'unlimited':   //only one rule deal line
          $action_repeat_count = 999999;
        break;
      case 'count':     //only one rule deal line
          $action_repeat_count = $vtprd_rules_set[$i]->rule_deal_info[$d]['action_repeat_count'];
        break;  
    } 
    
    for($ar=0; $ar < $action_repeat_count; $ar++) {
       $this->vtprd_process_actionPop_and_discount($i, $d, $br, $ar );                 
       if ($vtprd_rules_set[$i]->discount_processing_status != 'inProcess')  {         
         break; //break out of  for loop
       }                             
    } // end $action_repeat_count for loop  
                                                                
  }
 
  public function vtprd_process_actionPop_and_discount($i, $d, $br, $ar ) {      
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework;        
      
    //error_log( print_r(  'vtprd_process_actionPop_and_discount  $i= ' .$i. ' $d= ' .$d.  ' $br= ' .$br. ' $ar= ' .$ar, true ) ); 
                     
    //v1.1.0.6 begin
    //If 2nd to nth repeat where INPOP has been blessed, then add more free candidates!
    if ( ($br > 0) ||
         ($ar > 0) ) {
         
    //error_log( print_r(  'just above exec of vtprd_add_free_item_candidate, $br= ' . $br, true ) ); 
   //error_log( print_r(  'just above exec of vtprd_add_free_item_candidate, $ar= ' . $ar, true ) ); 
   //error_log( print_r(  'just above exec of vtprd_add_free_item_candidate, $inPop_exploded_group_end= ' .$vtprd_rules_set[$i]->inPop_exploded_group_end , true ) ); 
//$sizeof_inPop_exploded_found_list = sizeof($vtprd_rules_set[$i]->inPop_exploded_found_list); 
   //error_log( print_r(  'just above exec of vtprd_add_free_item_candidate, $sizeof_inPop_exploded_found_list= ' .$sizeof_inPop_exploded_found_list , true ) );      
      
      //2nd => nth
      if ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') {    
        $this->vtprd_add_free_item_candidate($i); //v1.1.0.6 if $br or $ar > 0, add to exploded!
      }
    }
    //v1.1.0.6 end
    
    $templateKey = $vtprd_rules_set[$i]->rule_template;

    if ($vtprd_template_structures_framework[$templateKey]['action_amt_type'] > ' ' ) { 
      if ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'none' ) ||  
          ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'one' ) ) {
        $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] = 'quantity';
        $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'] = 1;
        if ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to'] <= ' ') {
           $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to']  = 'all';
        }
      }
    } else {
        $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] = 'quantity';
        $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'] = 1;
        $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to']  = 'all';
    }

   //ACTIONPOP_EXPLODED_GROUP BEGN AND END  SETUP
   switch( $vtprd_rules_set[$i]->discountAppliesWhere  ) {     // 'allActionPop' / 'inCurrentInPopOnly'  / 'nextInInPop' / 'nextInActionPop' / 'inActionPop' /
      case 'allActionPop':
          //process all actionPop in one go
          $vtprd_rules_set[$i]->actionPop_exploded_group_begin = 0;
          $vtprd_rules_set[$i]->actionPop_exploded_group_end   = sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list);
        break;
      case 'inCurrentInPopOnly':
         //v1.0.8.1 begin  -  refactored
          /*
          if ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'zero' ) {  //means we are acting on the already-found 'buy' unit
            $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_end - 1;   //end - 1 gets the nth, as well as the direct hit...       
          } else {          
            //always the same as inPop pointers
            $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_begin;
          }
        //$vtprd_rules_set[$i]->actionPop_exploded_group_end   = $vtprd_rules_set[$i]->inPop_exploded_group_end;   //v1.0.3 
         $vtprd_rules_set[$i]->actionPop_exploded_group_end   = sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list);    //v1.0.3
          */

          if ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'zero' ) {  //means we are acting on the already-found 'buy' unit
            $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_end - 1;   //end - 1 gets the nth, as well as the direct hit...
            $vtprd_rules_set[$i]->actionPop_exploded_group_end   = $vtprd_rules_set[$i]->inPop_exploded_group_end;         
          } else {
            if ($ar > 0) { //if 2nd - nth actionPop repeat, use the previous actionPop group end to begin the next group
              $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->actionPop_exploded_group_end;
            } else {
              //always the same as inPop pointers at beginning
              $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_begin;                   
            } 
   
            //SETS action amt "window" for the actionPop_exploded_group
            $this->vtprd_set_action_group_end($i, $d, $ar );  //vtprd_action_amt_process 
          }
          //v1.0.8.1 end              
          
        break;  
      case 'nextInInPop':   
          if ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'zero' ) {  //means we are acting on the already-found 'buy' unit
            $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_end - 1;   //end - 1 gets the nth, as well as the direct hit...
            $vtprd_rules_set[$i]->actionPop_exploded_group_end   = $vtprd_rules_set[$i]->inPop_exploded_group_end;         
          } else {
            if ($ar > 0) { //if 2nd - nth actionPop repeat, use the previous actionPop group end to begin the next group
              $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->actionPop_exploded_group_end;
            } else {
              $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_end;// + 1;                   
            } 
   
            //SETS action amt "window" for the actionPop_exploded_group
            $this->vtprd_set_action_group_end($i, $d, $ar );  //vtprd_action_amt_process 
          }
          
          //v1.0.8.7 begin
          // capture overflow...  >= since we're comparing occurrence with size
          $sizeOf_actionPop_exploded_found_list = sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list);
          if ($vtprd_rules_set[$i]->actionPop_exploded_group_begin >= $sizeOf_actionPop_exploded_found_list ) {
             $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
   //error_log( print_r('cartGroupFailedTest 001 ', true ) );              
             break;
          }
      
          //v1.0.8.7 end
          
/*
//echo 'group begin= ' . $ss . 'group begin ProdID= ' .$ProdID .'<br>';
        error_log( print_r(  'SizeOf exploded list= ' . $sizeOf_actionPop_exploded_found_list, true ) );
                
$ss = $vtprd_rules_set[$i]->actionPop_exploded_group_begin;
$ProdID = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$ss]['prod_id'];
//echo 'group begin= ' . $ss . 'group begin ProdID= ' .$ProdID .'<br>';
        error_log( print_r(  'group begin= ' . $ss . ' group begin ProdID= ' .$ProdID, true ) );



$ss = $vtprd_rules_set[$i]->actionPop_exploded_group_end;
$ProdID = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$ss]['prod_id'];
//echo 'group end = ' . $ss . 'group begin ProdID= ' .$ProdID .'<br>';
      error_log( print_r(  'group end= ' . $ss . ' group end ProdID= ' .$ProdID, true ) );
          //here for test  MWN
          
*/          
          
        break;  
      case 'nextInActionPop':         
          //first time actionPop_exploded_group_end arrives here = 0...
          if (($br > 0) ||    //if 2nd to nth buy repeat or actionpop repeat, , use the previous actionPop group end to begin the next group
              ($ar > 0)) { 
            $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->actionPop_exploded_group_end;// + 1;
   //error_log( print_r(  'at nextInActionPop,  actionPop_exploded_group_begin= ' . $vtprd_rules_set[$i]->actionPop_exploded_group_begin, true ) );             
          } 
          // first time through,  $vtprd_rules_set[$i]->actionPop_exploded_group_begin = 0;
            
          //SETS action amt "window" for the actionPop_exploded_group
          $this->vtprd_set_action_group_end($i, $d, $ar );  //vtprd_action_amt_process      
   //error_log( print_r(  'after vtprd_set_action_group_end,  actionPop_exploded_group_end= ' . $vtprd_rules_set[$i]->actionPop_exploded_group_end, true ) );           
        break;   
    } 
    
    //only possible if  vtprd_set_action_group_end  executed
    if ($vtprd_rules_set[$i]->rule_processing_status == 'cartGroupFailedTest') {
      //THIS PATH can either end processing for the rule, or just this iteration of actionPop processing, based on settings in set_action_group...    

   //error_log( print_r(  'Inpop end reached ' , true ) );      
      
      $vtprd_rules_set[$i]->discount_processing_status = 'InPopEnd';
   //error_log( print_r(  'discount_processing_status = InPopEnd #001 br/ar = ' . $br . '  ' . $ar, true ) ); 
      return;
    }         

    //************************************************
    //************************************************
    //     PROCESS DISCOUNTS                             
    //************************************************
    //************************************************
    /*
     Do we treat the actionPop as a group or as individuals ?
        Requires group analysis:
          *least expensive
          *most expensive
          *forThePriceOf units
          *forThePriceOf currency        
        Can be applied to the group or individually (each/all)
          *currency discount
          *percentage discount
        Can only be applied to individual products
          *free
          *fixed price                        
    */
    
    switch( true ) {
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'forThePriceOf_Units') :
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'forThePriceOf_Currency') :
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'cheapest') :    //can only be 'each'
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'most_expensive') :   //can only be 'each'
        
        //v1.0.7.2 begin
          //reset the action group pointers to be = to buy group pointers, so actionpop doesn't count whole group at once...  so whatever group count the buy group as set, we do here.
          if ($vtprd_rules_set[$i]->discountAppliesWhere == 'inCurrentInPopOnly') {  //'inCurrentInPopOnly' = 'discount this one'
              $vtprd_rules_set[$i]->actionPop_exploded_group_begin = $vtprd_rules_set[$i]->inPop_exploded_group_begin; 
              $vtprd_rules_set[$i]->actionPop_exploded_group_end   = $vtprd_rules_set[$i]->inPop_exploded_group_end;
          }
          $this->vtprd_apply_discount_as_a_group($i, $d, $ar );       
        break;
        //v1.0.7.2 end  
              
      case ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'all') && ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'currency') ):
      case ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'all') && ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'percent') ):  //v1.0.7.4   floating point fix...
          $this->vtprd_apply_discount_as_a_group($i, $d, $ar );       
        break;
      
      case ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'free')       && ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'each') ):   //can only be 'each'
      case ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'fixedPrice') && ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'each') ):   //can only be 'each'  
      case ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'currency')   && ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'each') ):
      case ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'percent')    && ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'each') ):    //v1.0.7.4
          $this->vtprd_apply_discount_to_each_product($i, $d, $ar );       
        break;
    } 


    $vtprd_info['applied_value_of_discount_applies_to']  =  $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'];   //v1.0.8.4  store value for processing

    $sizeof_actionpop_list = sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list); //v 1.0.3
    
    //v1.1.0.6  begin
    // revamped the fallthrough logic to handle 'infitely repeatable'
    if ( ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') &&
         ($vtprd_rules_set[$i]->actionPop_exploded_group_end >= $sizeof_actionpop_list) )  {
      //emulate having the future additonal free item which will be added next iteration, if $br or $ar have "room" left...
      $sizeof_actionpop_list++;
   //error_log( print_r(  'sizeof actionpop increased, sizeof/br/ar= ' . $sizeof_actionpop_list . '  ' . $br . '  ' . $ar, true ) );      
    }
    //v1.1.0.6 END
    
    
    if ( ($vtprd_rules_set[$i]->actionPop_exploded_group_end >= $sizeof_actionpop_list ) || 
         ($ar >= ($sizeof_actionpop_list) ) ||  //v1.0.3 exit if infinite repeat  
         ($vtprd_rules_set[$i]->end_of_actionPop_reached == 'yes') ) {
         
   //error_log( print_r(  'discount_processing_status = InPopEnd #002 br/ar= ' . $br . '  ' . $ar, true ) );
   //error_log( print_r(  '$vtprd_rules_set[$i]->actionPop_exploded_group_end / sizeof = ' . $vtprd_rules_set[$i]->actionPop_exploded_group_end . '  '. $sizeof_actionpop_list, true ) ); 
   //error_log( print_r(  '$ar= , $sizeof_actionpop_list = ' . $ar . '  '. $sizeof_actionpop_list, true ) );
   //error_log( print_r(  '$vtprd_rules_set[$i]->end_of_actionPop_reached = ' . $vtprd_rules_set[$i]->end_of_actionPop_reached, true ) );  
       
       $vtprd_rules_set[$i]->discount_processing_status = 'InPopEnd';

    } else {
      switch ($vtprd_rules_set[$i]->discountAppliesWhere)  {
        case 'allActionPop':
           $vtprd_rules_set[$i]->discount_processing_status = 'InPopEnd'; //all done - process all actionPop in one go 
   //error_log( print_r(  'discount_processing_status = InPopEnd #002A br/ar= ' . $br . '  ' . $ar, true ) );             
          break;
        case 'inCurrentInPopOnly':              
        case 'nextInInPop':       
        case 'nextInActionPop':
            $vtprd_rules_set[$i]->actionPop_repeat_activity_completed = 'yes';  //action completed, then allow the repeat to control the discount action
          break;          
      }    
    }
             
  } // end  vtprd_process_actionPop_and_discount

 
  public function vtprd_apply_discount_to_each_product($i, $d, $ar ) {  
     global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework;        
      
    //error_log( print_r(  'vtprd_apply_discount_to_each_product $i= ' .$i. ' $d= ' .$d. ' $ar= ' .$ar, true ) ); 
 
     //if we're doing action nth processing, only the LAST product in the list gets the discount.
     if ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'nthQuantity') {
       $each_product_group_begin = $vtprd_rules_set[$i]->actionPop_exploded_group_end - 1;
     } else {
       $each_product_group_begin = $vtprd_rules_set[$i]->actionPop_exploded_group_begin;
     }
          
     for( $s=$each_product_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
        $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] = $this->vtprd_compute_each_discount($i, $d, $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price']);
        $curr_prod_array = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s];
        $curr_prod_array['exploded_group_occurrence'] = $s; 
        $this->vtprd_upd_cart_discount($i, $d, $curr_prod_array);
        //just in case...
        if ($s >= sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list)) {
   //error_log( print_r(  'discount_processing_status = InPopEnd #003 br/ar' . $br . '  ' . $ar, true ) );         
          $vtprd_rules_set[$i]->discount_processing_status = 'InPopEnd';
          return;
        }  
     } 
    //at this point we may have processed all of actionPop in one go, so we set the end switch
     
     return; 
  }
 
  public function vtprd_apply_discount_as_a_group($i, $d, $ar ) {   
     global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework; 
      
    //error_log( print_r(  'vtprd_apply_discount_as_a_group ', true ) ); 
             
    $prod_discount = 0;    
    switch( true ) {
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'forThePriceOf_Units') :
         // buy 5 ( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count'] ) 
         // get 5   ( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count']; )
         // FOR THE PRICE OF           
         // 4 ( $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_for_the_price_of_count'] )
         
         //add unit prices together
         $cart_group_total_price = 0;
         for ( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $cart_group_total_price += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price'];
         }      
       if ($vtprd_rules_set[$i]->rule_template == 'C-forThePriceOf-inCart') {  //buy-x-action-forThePriceOf-same-group-discount
           $forThePriceOf_Divisor = $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count'];
        } else {
           $forThePriceOf_Divisor = $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'];
        }

        //divide by total by number of units = average price
        $cart_group_avg_price = $cart_group_total_price / $forThePriceOf_Divisor;

        //multiply average price * # of forthepriceof units = new group price
        $new_total_price = $cart_group_avg_price * $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'];

        $total_savings = $cart_group_total_price - $new_total_price;

        //per unit savings = new total / group unit count => by Buy group or Action Group
        //$per_unit_savings = $total_savings / $forThePriceOf_Divisor;

        //compute remainder
        //$per_unit_savings_2decimals = bcdiv($total_savings , $forThePriceOf_Divisor , 2);
        $per_unit_savings_2decimals = round( ($total_savings / $forThePriceOf_Divisor) , 2); 
            
        $running_total =  $per_unit_savings_2decimals * $forThePriceOf_Divisor;

        //$remainder = $total_savings - $running_total;
        $remainder = round($total_savings - $running_total, 2); //v1.0.7.4   floating point...
        
        if ($remainder <> 0) {      //v1.0.5.1 changed > 0 to <>0 ==>> pick up positive or negative rounding error
          $add_a_penny_to_first = $remainder;
        } else {
          $add_a_penny_to_first = 0;
        }

       
        //apply the per unit savings to each unit       
        for ( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] = $per_unit_savings_2decimals;
            
            //if first occurrence, add in penny if remainder calc produced one
            if ($s == $vtprd_rules_set[$i]->actionPop_exploded_group_begin) {
               $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] += $add_a_penny_to_first;
            }
            
            $curr_prod_array = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s];
            $curr_prod_array['exploded_group_occurrence'] = $s;
            $this->vtprd_upd_cart_discount($i, $d, $curr_prod_array);
         } 

        break;
      
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'forThePriceOf_Currency') :

         // buy 5 ( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count'] ) 
         // get 5   ( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count']; )
         // FOR THE PRICE OF           
         // 4 ( $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_for_the_price_of_count'] )
         
         //add unit prices together
         $cart_group_total_price = 0;
         for ( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $cart_group_total_price += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price'];
         }      

       if ($vtprd_rules_set[$i]->rule_template == 'C-forThePriceOf-inCart') {  //buy-x-action-forThePriceOf-same-group-discount
           $forThePriceOf_Divisor = $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count'];
        } else {
           $forThePriceOf_Divisor = $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'];
        }

        $cart_group_new_fixed_price = $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'];
        
        $total_savings = $cart_group_total_price - $cart_group_new_fixed_price;

        //compute remainder
        //$per_unit_savings_2decimals = bcdiv($total_savings , $forThePriceOf_Divisor , 2);
        $per_unit_savings_2decimals = round( ($total_savings / $forThePriceOf_Divisor) , 2); 
            
        $running_total =  $per_unit_savings_2decimals * $forThePriceOf_Divisor;
        
      //$remainder = $total_savings - $running_total;
        $remainder = round($total_savings - $running_total, 2); //v1.0.7.4   floating point...
        
        if ($remainder <> 0) {      //v1.0.5.1 changed > 0 to <>0 ==>> pick up positive or negative rounding error
          $add_a_penny_to_first = $remainder;
        } else {
          $add_a_penny_to_first = 0;
        }
       
        //apply the per unit savings to each unit       
        for ( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] = $per_unit_savings_2decimals;
            
            //if first occurrence, add in penny if remainder calc produced one
            if ($s == $vtprd_rules_set[$i]->actionPop_exploded_group_begin) {
               $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] += $add_a_penny_to_first;
            }
            
            $curr_prod_array = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s];
            $curr_prod_array['exploded_group_occurrence'] = $s;
            $this->vtprd_upd_cart_discount($i, $d, $curr_prod_array);
         } 

        break;
        
        
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'cheapest') :
         $cheapest_array = array();
         //create candidate array
         for( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['exploded_group_occurrence'] = $s;
            $cheapest_array [] = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s];           
         }
         //http://stackoverflow.com/questions/7839198/array-multisort-with-natural-sort
         //http://isambard.com.au/blog/2009/07/03/sorting-a-php-multi-column-array/
         //sort group by prod_unit_price (relative column3), cheapest 1stt
         $prod_unit_price = array();
         foreach ($cheapest_array as $key => $row) {
            $prod_unit_price[$key] = $row['prod_unit_price'];
         } 
         array_multisort($prod_unit_price, SORT_ASC, SORT_NUMERIC, $cheapest_array);
         
         //apply discount        
         $curr_prod_array = $cheapest_array[0];
         $curr_prod_array['prod_discount_amt'] = $this->vtprd_compute_each_discount($i, $d, $cheapest_array[0]['prod_unit_price'] );
         $this->vtprd_upd_cart_discount($i, $d, $curr_prod_array);
 
        break;
      
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'most_expensive') :
         $mostExpensive_array = array();
         
         //create candidate array
         for( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['exploded_group_occurrence'] = $s;
            $mostExpensive_array [] = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s];
         }
         
         //sort group by prod_unit_price , most expensive 1st
         $prod_unit_price = array();
         foreach ($mostExpensive_array as $key => $row) {
            $prod_unit_price[$key] = $row['prod_unit_price'];
         } 
         array_multisort($prod_unit_price, SORT_DESC, SORT_NUMERIC, $mostExpensive_array);
         
         //apply discount
         $curr_prod_array = $mostExpensive_array[0];
         $curr_prod_array['prod_discount_amt'] = $this->vtprd_compute_each_discount($i, $d, $mostExpensive_array[0]['prod_unit_price'] );
         $this->vtprd_upd_cart_discount($i, $d, $curr_prod_array);
         
        break;
        
      //$$ value off of a group
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'currency') :  //only 'ALL'
         
         //add unit prices together
         $cart_group_total_price = 0;
         for( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $cart_group_total_price += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price'];
         }      
        $unit_count = $vtprd_rules_set[$i]->actionPop_exploded_group_end - $vtprd_rules_set[$i]->actionPop_exploded_group_begin;
       
        //per unit savings = new total / group unit count
        

        //compute remainder
        //$per_unit_savings_2decimals = bcdiv($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'] , $unit_count , 2);
        $per_unit_savings_2decimals = round( ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'] / $unit_count ) , 2);     
     
        $running_total =  $per_unit_savings_2decimals * $unit_count;

        //$remainder = $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'] - $running_total;
        $remainder = round($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'] - $running_total, 2);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
        
        //if ($remainder > 0) {
        if ($remainder != 0) {      //v1.0.8.1  allow for negative remainder!
          $add_a_penny_to_first = $remainder;
        } else {
          $add_a_penny_to_first = 0;
        }
    
        //apply the per unit savings to each unit
        for( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] = $per_unit_savings_2decimals;
            
            //if first occurrence, add in penny if remainder calc produced one
            if ($s == $vtprd_rules_set[$i]->actionPop_exploded_group_begin) {
               $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] += $add_a_penny_to_first;
            }
                      
            $curr_prod_array = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s];
            $curr_prod_array['exploded_group_occurrence'] = $s;
            $this->vtprd_upd_cart_discount($i, $d, $curr_prod_array);
         } 

        break;
         
      //v1.0.7.4 begin
      //*******************************************
      //% value off of a group
      //  added to handle a price decimal ending in 5, which otherwise will produce a rounding-based error.
      //  rounding errors are now handled within each product sub-group, so that the fix will be reflected in the appropriate bucket.
      //*******************************************
      //--------------------------------------------
      //v1.0.7.6 entire case structure reworked
      //--------------------------------------------
      case ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   == 'percent') :     
         //Applying a % discount to a group is often different from applying it singly, due to rounding issues.  This routine repairs that
         //  by comparing the total of the individually discounted items against the discount total of the same group, and adding any remainder to the last item discounted

         //*******************************************
         //add unit prices together, per product (so any group-level remainder goes with the correct product!)
         //******************************************* 
         
         $cart_group_total = array();
  
         $s = $vtprd_rules_set[$i]->actionPop_exploded_group_begin;
         $current_product_id = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_id'];
         
         $current_unit_count = 0;
         $current_total_price = 0;
         $current_unit_price = 0; 
         $grand_total_exploded_group = 0;
         $running_grand_total = 0;
     
         //pre-process action group for remainder info
         for( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) {
            
            if ($current_product_id == $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_id'])  {
               
               //add to current totals
               $current_unit_count++;
               $current_total_price += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price'];
               $current_unit_price = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price'];
          
            } else {
               //insert the totals of the previous product id     
               $cart_group_total[] = array(
                   'product_id'  => $current_product_id,
                   'unit_count'  => $current_unit_count,
                   'unit_price'  => $current_unit_price,
                   'total_price' => $current_total_price,
                   'total_discount' => 0,
                   'total_remainder' => 0,
                   'product_discount' => 0,
                   'product_discount_remainder' => 0  
                    ); 
               
               //initialize the current group     
               $current_product_id = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_id'];
               $current_unit_count = 1;
               $current_total_price = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price'];
               $current_unit_price = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price']; 
                                                     
            }

            //handle last in list
            if (  ($s + 1) == $vtprd_rules_set[$i]->actionPop_exploded_group_end ) {
               $cart_group_total[] = array(
                   'product_id'  => $current_product_id,
                   'unit_count'  => $current_unit_count,
                   'unit_price'  => $current_unit_price,
                   'total_price' => $current_total_price,
                   'total_discount' => 0,
                   'total_remainder' => 0,
                   'product_discount' => 0,
                   'product_discount_remainder' => 0   
                    );            
            }

            $grand_total_exploded_group += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_unit_price']; 
            
           
         }

         $percent_off = $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'] / 100;

         //*******************
         //compute group discounts, by product
         //*******************
         $sizeof_cart_group_total = sizeof ($cart_group_total);
         
         for( $c=0; $c < $sizeof_cart_group_total; $c++) {
            //****************************************************
            //Get the total discount amount for the whole group, used to calculate final remainder later
            //****************************************************
        //  $discount_applied_to_total = bcmul($cart_group_total[$c]['total_price'], $percent_off , 2);
            $discount_applied_to_total = round($cart_group_total[$c]['total_price'] * $percent_off , 2); //v1.0.7.6

            $cart_group_total[$c]['total_discount']  =  $discount_applied_to_total; 
            $cart_group_total[$c]['total_remainder'] =  0;
            
            
            //****************************************************
            //Get the Unit Price discount
            //****************************************************            
        //  $discounted_per_unit = (bcmul($cart_group_total[$c]['unit_price'], $percent_off , 2));
            $discounted_per_unit = (round($cart_group_total[$c]['unit_price'] * $percent_off , 2));  //v1.0.7.6
            
            $discount_applied_to_unit_times_count = $discounted_per_unit * $cart_group_total[$c]['unit_count'];
            
            $unit_price_discount_difference = $discount_applied_to_total - $discount_applied_to_unit_times_count;

            $discounted_per_unit_round = (round ($cart_group_total[$c]['unit_price'] * $percent_off , 2));


            $cart_group_total[$c]['product_discount'] = $discounted_per_unit;
            $cart_group_total[$c]['product_discount_remainder'] = $unit_price_discount_difference;
            
            //keep track of grand total
          //  $running_grand_total += ($temp_product_total_discount + $unit_price_discount_difference);           
             
         }
         
         //See if there is remainder AFTER all of the product groups are computed
       //$grand_total_exploded_group_discount = bcmul($grand_total_exploded_group, $percent_off , 2); 
         $grand_total_exploded_group_discount = round($grand_total_exploded_group * $percent_off , 2);
         $grand_total_remainder = round($grand_total_exploded_group_discount - $running_grand_total, 2);

        $current_product_id = '';
        $current_unit_count = 0;
        $current_total_discount = 0;
        
        //*******************  
        //apply discount to each item - add in **group** remainder to last
        //*******************
        for( $s=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $s < $vtprd_rules_set[$i]->actionPop_exploded_group_end; $s++) { 

            //*******************
            // track unit count for this product
            //*******************  
            if ($current_product_id == $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_id'])  {
              $current_unit_count++; 
            } else {
              $current_product_id = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_id'];
              $current_unit_count     = 1;
            }   
            
            //*******************
            // add in group remainder, as needed
            //*******************                      
            for( $c=0; $c < $sizeof_cart_group_total; $c++) {               
               
               if ($cart_group_total[$c]['product_id']  ==  $current_product_id ) {
                  
                  $this_prod_discount_amt = $cart_group_total[$c]['product_discount'];
                  
                  if ($cart_group_total[$c]['unit_count']  ==  $current_unit_count ){  //are we on last unit in product group?                  
                      $this_prod_discount_amt += $cart_group_total[$c]['product_discount_remainder']; 
                      /*  only do this by product!!
                      //if we're on the last product, last unit - add in grand_total_remainder
                      if ( ($c + 1) == $sizeof_cart_group_total) {
                         $this_prod_discount_amt += $grand_total_remainder;
                      }
                     */                   
                  }

                  $c = $sizeof_cart_group_total; //exit stage left
               }
            }
            
            //*******************
            // update discount
            //*******************                
            $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s]['prod_discount_amt'] = $this_prod_discount_amt;
            $curr_prod_array = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$s];
            $curr_prod_array['exploded_group_occurrence'] = $s;
            $this->vtprd_upd_cart_discount($i, $d, $curr_prod_array);                 
        }

      break;
      //v1.0.7.4 end
        
    }
    
    return;           
  }
 
 /*  --------------------------
 This routine creates a single exploded product's discount.  It also checks that discount against
 individual limits.  It also checks if this exploded product discount 
 exceeds the product's cumulative quantity discount.
    -------------------------- */
  public function vtprd_upd_cart_discount($i, $d, $curr_prod_array) {   
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework;  
      
     //error_log( print_r(  'vtprd_upd_cart_discount AT TOP, $i= ' .$i. ' $d= ' .$d, true ) ); 
     //error_log( print_r(  '$curr_prod_array AT TOP= ', true ) );
     //error_log( var_export($curr_prod_array, true ) );
 
    $k = $curr_prod_array['prod_id_cart_occurrence'];
    $rule_id = $vtprd_rules_set[$i]->post_id; 
    
    $product_id = $vtprd_cart->cart_items[$k]->product_id; //v1.1.1.2 

    if ($curr_prod_array['prod_discount_amt'] == 0) {
      //v1.1.0.6 begin -  don't skip this if there is a zero priced product on an auto insert rule...
            
      $current_auto_add_array = $this->vtprd_get_current_auto_add_array();
        
      if ( ($vtprd_info['current_processing_request']  ==  'cart') &&
           ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product  ==  'yes') &&
           //v1.1.1.2 begin
           //($vtprd_cart->cart_items[$k]->product_id  ==  $current_auto_add_array['free_product_id']) &&
           ($vtprd_cart->cart_items[$k]->product_auto_insert_rule_id  ==  $vtprd_rules_set[$i]->post_id) &&
           (isset ($current_auto_add_array[$product_id]) ) &&
           //v1.1.1.2 end
           ($vtprd_cart->cart_items[$k]->unit_price  ==  0) ) {
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'Zero price auto add product';
        $vtprd_cart->cart_items[$k]->zero_price_auto_add_free_item = 'yes'; //MARK FOR PRINTING FUNCTIONS
        $vtprd_cart->cart_has_zero_price_auto_add_free_item = 'yes'; //MARK FOR PRINTING FUNCTIONS
      } else {
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No discount for this rule';
        return;
      }
      //v1.1.0.6 end
    }
      
    //just in case discount for this rule already applied to this product iteration....
    //mark exploded list product as already processed for this rule
    $occurrence = $curr_prod_array['exploded_group_occurrence'];       
    if (($curr_prod_array['prod_discount_applied'] == 'yes') ||
        ($vtprd_rules_set[$i]->actionPop_exploded_found_list[$occurrence]['prod_discount_applied'] == 'yes')) {
      $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'Discount already applied, can"t reapply';
      //exit stage left, can't apply discount for same rule to same product....
      return;
    }
 

    //*********************************************************************
    //CHECK THE MANY DIFFERENT MAX LIMITS BEFORE UPDATING THE DISCOUNT TO THE ARRAY
    //********************************************************************* 
 
    //v1.0.8.4 begin
    if ( ($vtprd_rules_set[$i]->cumulativeRulePricing == 'no') &&
         ($vtprd_cart->cart_items[$k]->product_already_in_an_all_rule == 'yes') ) {
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - counted as part of an "all" rule group from previous discount, no more allowed';
        return;     
    }
    //v1.0.8.4 begin
      
    if ( isset( $vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id] ) ) {
      if ( (sizeof ($vtprd_cart->cart_items[$k]->yousave_by_rule_info) > 1 ) &&   //only 1 allowed in this case...
           ($vtprd_rules_set[$i]->cumulativeRulePricing == 'no') ) {
         //1 discount rule already applied discount, no more allowed;
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - 1 discount rule already applied discount, no more allowed';
         return;  
      }
      if ( $vtprd_setup_options['discount_floor_pct_per_single_item'] > 0 ) {
        if ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']   != 'free') {
           if ( ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['yousave_pct'] >= $vtprd_setup_options['discount_floor_pct_per_single_item']) ||
                ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['rule_max_amt_msg'] > ' ') ) {
              //yousave percent max alread reached in a previous discount!!!!!!  Do Nothing
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Discount floor percentage max reached, ' .$vtprd_setup_options['discount_floor_pct_msg']; //floor percentage maxed out;            
              return;
           }
        }
      } 

      //v1.0.7.4 begin
      //CHECK if catalog discount already applied , from v1.0.7.4 in free version
      if ($vtprd_info['current_processing_request'] == 'cart') { 
         if ( ($vtprd_cart->cart_items[$k]->unit_price < $vtprd_cart->cart_items[$k]->save_orig_unit_price) &&
              ($vtprd_rules_set[$i]->cumulativeRulePricing == 'no') ) {
            //A Catalog or other external discount previously applied, no 
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - CATALOG discount rule already applied discount, no more allowed';
            return;           
         }
      }      
      //v1.0.7.4 end
            
      switch( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_type'] ) {
        case 'none':
            $do_nothing;
          break;
        case 'percent':
           if ( ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['yousave_pct'] >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count']) ||
                ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['rule_max_amt_msg'] > ' ') ) {          
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Rule Max Percent Previously Reached.'; //floor percentage maxed out;                      
              return;
            }
          break;
        case 'quantity':       
           if ( ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['discount_applies_to_qty'] >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count']) ||
                ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['rule_max_amt_msg'] > ' ') ) {          
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Rule Max Qty Previously Reached.'; //floor percentage maxed out;                      
              return;
            }
          break;        
        case 'currency': 
           if ( ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['yousave_amt'] >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count']) ||
                ($vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['rule_max_amt_msg'] > ' ') ) {          
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Rule Max $$ Value Previously Reached.'; //floor percentage maxed out;                      
              return;
            }      
          break;
      }
      
       
      switch( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_type'] ) {
        case 'none':
            $do_nothing;
          break;
        case 'percent':
           if ( $vtprd_rules_set[$i]->discount_total_pct >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_count']) {          
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Rule Cumulative Max Percent Previously Reached.'; //floor percentage maxed out;                      
              return;
            }
          break;
        case 'quantity':
           if ( $vtprd_rules_set[$i]->discount_total_qty >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_count']) {          
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'rejected';
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Rule Cumulative Max Qty Previously Reached.'; //floor percentage maxed out;                      
              return;
            }
          break;        
        case 'currency':    
           if ( $vtprd_rules_set[$i]->discount_total_amt >= $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_count'])  {          
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Rule Cumulative Max $$ Value Previously Reached.'; //floor percentage maxed out;                      
              return;
            }      
          break;
      }      
 
      $yousave_for_this_rule_id_already_exists = 'yes';

    } else {      
      if ( (sizeof($vtprd_cart->cart_items[$k]->yousave_by_rule_info) > 0 ) &&
           ($vtprd_rules_set[$i]->cumulativeRulePricing == 'no') ) {
         //1 discount rule already applied discount, no more allowed
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Discount for this another rule already applied to this Product, multiple rule discounts not allowed.';         
         return;  
      }
      
      $yousave_for_this_rule_id_already_exists = 'no';
      
    }
    
    
    //*****************************************
    //find current product's yousave percent, altered as needed below
    //*****************************************
    
    if ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']  == 'percent') {
      $yousave_pct = $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count']; 
    } else {
      //compute yousave_pct_at_upd_begin
      $computed_pct =  $curr_prod_array['prod_discount_amt'] /  $curr_prod_array['prod_unit_price'] ;
      //$computed_pct_2decimals = bcdiv($curr_prod_array['prod_discount_amt'] , $curr_prod_array['prod_unit_price'] , 2);
      $computed_pct_2decimals = round( ($curr_prod_array['prod_discount_amt'] / $curr_prod_array['prod_unit_price'] ) , 2);
                
      //$remainder = $computed_pct - $computed_pct_2decimals;
      $remainder = round($computed_pct - $computed_pct_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
                  
      if ($remainder > 0.005) {
        //v1.0.7.4 begin
        $increment = round($remainder, 2); //round the rounding error to 2 decimal points!  floating point
        if ($increment < .01) {
          $increment = .01;
        }
        ////v1.0.7.4 end
        $yousave_pct = ($computed_pct_2decimals + $increment) * 100;  //v1.0.7.4   floating point
      } else {
        $yousave_pct = $computed_pct_2decimals * 100;
      }
    }

 
    $max_msg = '';
    $discount_status = '';
    
    //compute current discount_totals for limits testing
    $discount_total_qty_for_rule = $vtprd_rules_set[$i]->discount_total_qty_for_rule + 1;
    $discount_total_amt_for_rule = $vtprd_rules_set[$i]->discount_total_amt_for_rule + $curr_prod_array['prod_discount_amt'] ;
    //$discount_total_unit_price_for_rule will be the unit qty * db_unit_price already, as this routine is done 1 by 1...
    $discount_total_unit_price_for_rule =  $vtprd_rules_set[$i]->discount_total_unit_price_for_rule + $curr_prod_array['prod_unit_price'] ;
    //yousave pct whole number  = total discount amount / (orig unit price * number of units discounted)
    $discount_total_pct_for_rule = ($discount_total_amt_for_rule / $discount_total_unit_price_for_rule) * 100 ;  //in round #s
     
    //adjust yousave_amt and yousave_pct as needed based on limits
    switch( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_type']  ) {  //var on the 1st iteration only
      case 'none':
          $do_nothing;
        break;
      case 'percent':           
          if ($discount_total_pct_for_rule > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count']) {
            
             // % = floor minus rule % totaled in previous iteration
            $yousave_pct = $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count'] - $vtprd_rules_set[$i]->discount_total_pct_for_rule; 
            
            //*********************************************************************
            //reduce discount amount to max allowed by rule percentage
            //*********************************************************************
          //$discount_2decimals = bcmul(($yousave_pct / 100) , $curr_prod_array['prod_unit_price'] , 2);
            $discount_2decimals = round(($yousave_pct / 100) * $curr_prod_array['prod_unit_price'] , 2); //v1.0.7.6
          
            //compute rounding
            $temp_discount = ($yousave_pct / 100) * $curr_prod_array['prod_unit_price'];
         
            //$rounding = $temp_discount - $discount_2decimals;
            $rounding = round($temp_discount - $discount_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
                     
            if ($rounding > 0.005) {
              //v1.0.7.4 begin
              $increment = round($rounding, 2); //round the rounding error to 2 decimal points!  floating point
              if ($increment < .01) {
                $increment = .01;
              }
              ////v1.0.7.4 end              
              $discount = $discount_2decimals + $increment;   //v1.0.7.4  floating point
            }  else {
              $discount = $discount_2decimals;
            } 
                     
            $curr_prod_array['prod_discount_amt']  = $discount;
            $max_msg = $vtprd_rules_set[$i]->discount_rule_max_amt_msg;
 
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 
            'Discount reduced due to max rule percent overrun.';         
                        
          }
 
        break;      
      case 'quantity':
          if ($discount_total_qty_for_rule > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count']) {
             //we've reached the max allowed by this rule, as we only process 1 at a time, exit
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Discount Rule Max Qty already reached, discount skipped';               
             return;
          }
        break;
      case 'currency':
          if ($discount_total_amt_for_rule > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count']) {
            //reduce discount to max...
            $reduction_amt = $discount_total_amt_for_rule - $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count'];

            $curr_prod_array['prod_discount_amt']  = $curr_prod_array['prod_discount_amt'] - $reduction_amt;
            
            //v1.0.9.3 begin
            if ($curr_prod_array['prod_discount_amt'] > $curr_prod_array['prod_unit_price']) {
              $curr_prod_array['prod_discount_amt'] = $curr_prod_array['prod_unit_price'];
            }
            //v1.0.9.3 end
            
            $max_msg = $vtprd_rules_set[$i]->discount_rule_max_amt_msg;
             
            $yousave_pct_temp = $curr_prod_array['prod_discount_amt'] / $curr_prod_array['prod_unit_price'];
            
            // $yousave_pct = $yousave_amt / $curr_prod_array['prod_unit_price'] * 100;        
            //compute remainder
            //$yousave_pct_2decimals = bcdiv($curr_prod_array['prod_discount_amt'] , $curr_prod_array['prod_unit_price'] , 2);
            
            $yousave_pct_2decimals = round( ($curr_prod_array['prod_discount_amt'] / $curr_prod_array['prod_unit_price'] ) , 2);
                              
          //$remainder = $yousave_pct_temp - $yousave_pct_2decimals;
            $remainder = round($yousave_pct_temp - $yousave_pct_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
                        
            if ($remainder > 0.005) {
              //v1.0.7.4 begin
              $increment = round($remainder, 2); //round the rounding error to 2 decimal points!  floating point
              if ($increment < .01) {
                $increment = .01;
              }
              ////v1.0.7.4 end              
              $yousave_pct = ($yousave_pct_2decimals + $increment) * 100;  //v1.0.7.4  PHP floating point 
            } else {
              $yousave_pct = $yousave_pct_2decimals * 100;
            }
          }
        break;
    }

    //Test yousave for product across All Rules applied to the Product
    $yousave_product_total_amt = $vtprd_cart->cart_items[$k]->yousave_total_amt +  $curr_prod_array['prod_discount_amt'] ;
    $yousave_product_total_qty = $vtprd_cart->cart_items[$k]->yousave_total_qty + 1;
    //  yousave_total_unit_price is a rolling full total of unit price already
    $yousave_total_unit_price = $vtprd_cart->cart_items[$k]->yousave_total_unit_price + $curr_prod_array['prod_unit_price'];  
    //yousave pct whole number = (total discount amount / (orig unit price * number of units discounted))
    $yousave_pct_prod_temp = $yousave_product_total_amt / $yousave_total_unit_price;
    //$yousave_pct_prod_2decimals = bcdiv($yousave_product_total_amt , $yousave_total_unit_price , 2);
    $yousave_pct_prod_2decimals = round( ($yousave_product_total_amt / $yousave_total_unit_price ) , 2);
       
  //$remainder = $yousave_pct_prod_temp - $yousave_pct_prod_2decimals;
    $remainder = round($yousave_pct_prod_temp - $yousave_pct_prod_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
                    
    if ($remainder > 0.005) {
      //v1.0.7.4 begin
      $increment = round($remainder, 2); //round the rounding error to 2 decimal points!  floating point
      if ($increment < .01) {
        $increment = .01;
      }
      ////v1.0.7.4 end
      $yousave_product_total_pct = ($yousave_pct_prod_2decimals + $increment) * 100;   //v1.0.7.4  PHP floating point 
    } else {
      $yousave_product_total_pct = $yousave_pct_prod_2decimals * 100;
    }
    $refigure_yousave_product_totals = 'no';

    //if amts have been massaged, recheck vs discount_floor_percentage
    if ($max_msg > ' ') {
      if ( $vtprd_setup_options['discount_floor_pct_per_single_item'] > 0 ) {

        if ( $yousave_product_total_pct > $vtprd_setup_options['discount_floor_pct_per_single_item']) {
          //reduce discount amount to max allowed by max floor discount percentage
          //    compute the allowed remainder percentage
          // % = floor minus product % totaled *before now*
          $yousave_pct = $vtprd_setup_options['discount_floor_pct_per_single_item'] - $vtprd_cart->cart_items[$k]->yousave_total_pct;
          
          $percent_off = $yousave_pct / 100;         
          //compute rounding
        //$discount_2decimals = bcmul($curr_prod_array['prod_unit_price'] , $percent_off , 2);
          $discount_2decimals = round($curr_prod_array['prod_unit_price'] * $percent_off , 2);  //v1.0.7.6
          
          $temp_discount = $curr_prod_array['prod_unit_price'] * $percent_off;
          
          //$rounding = $temp_discount - $discount_2decimals;
          $rounding = round($temp_discount - $discount_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
                             
          if ($rounding > 0.005) {
            //v1.0.7.4 begin
            $increment = round($rounding, 2); //round the rounding error to 2 decimal points!  floating point
            if ($increment < .01) {
              $increment = .01;
            }
            ////v1.0.7.4 end            
            $curr_prod_array['prod_discount_amt'] = $discount_2decimals + $increment;   //v1.0.7.4  PHP floating point
          }  else {
            $curr_prod_array['prod_discount_amt'] = $discount_2decimals;
          }                   
          $refigure_yousave_product_totals = 'yes';
          //$curr_prod_array['prod_discount_amt']  = ($yousave_pct / 100) * $curr_prod_array['prod_unit_price'];
        } 
      }         
    }
    
        
    //adjust yousave_amt and yousave_pct as needed based on limits
    switch( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_type']  ) {  //var on the 1st iteration only
      case 'none':
          $do_nothing;
        break;
      case 'percent':           
          if ($yousave_product_total_pct > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_count']) {
            
             // % = floor minus rule % totaled *before now*
            $yousave_pct = $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_max_amt_count'] - $vtprd_cart->cart_items[$k]->yousave_total_pct;
            
            //*********************************************************************
            //reduce discount amount to max allowed by rule percentage
            //*********************************************************************
          //$discount_2decimals = bcmul(($yousave_pct / 100) , $curr_prod_array['prod_unit_price'] , 2);
            $discount_2decimals = round(($yousave_pct / 100) * $curr_prod_array['prod_unit_price'] , 2); //v1.0.7.6
         
            //compute rounding
            $temp_discount = ($yousave_pct / 100) * $curr_prod_array['prod_unit_price'];

          //$rounding = $temp_discount - $discount_2decimals;
            $rounding = round($temp_discount - $discount_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
         
            if ($rounding > 0.005) {
               //v1.0.7.4 begin
              $increment = round($rounding, 2); //round the rounding error to 2 decimal points!  floating point
              if ($increment < .01) {
                $increment = .01;
              }
              ////v1.0.7.4 end             
              $discount = $discount_2decimals + $increment;    //v1.0.7.4  PHP floating point 
            }  else {
              $discount = $discount_2decimals;
            } 
          } 
 
        break;       
      case 'quantity':
          if ($yousave_product_total_qty > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_count']) {
             //we've reached the max allowed by this rule, as we only process 1 at a time, exit
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 'No Discount - Discount Rule Max Qty already reached, discount skipped';               
             return;
          }
        break;
      case 'currency':
          if ($yousave_product_total_amt > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_count']) {
            //reduce discount to max...
            $reduction_amt = $yousave_product_total_amt - $vtprd_rules_set[$i]->rule_deal_info[0]['discount_rule_cum_max_amt_count'];

            $curr_prod_array['prod_discount_amt']  = $curr_prod_array['prod_discount_amt'] - $reduction_amt;
            
            //v1.0.9.3 begin
            if ($curr_prod_array['prod_discount_amt'] > $curr_prod_array['prod_unit_price']) {
              $curr_prod_array['prod_discount_amt'] = $curr_prod_array['prod_unit_price'];
            }
            //v1.0.9.3 end
                        
            $max_msg = $vtprd_rules_set[$i]->discount_rule_cum_max_amt_msg;
             
            $yousave_pct_temp = $curr_prod_array['prod_discount_amt'] / $curr_prod_array['prod_unit_price'];
            
            // $yousave_pct = $yousave_amt / $curr_prod_array['prod_unit_price'] * 100;        
            //compute remainder
            //$yousave_pct_2decimals = bcdiv($curr_prod_array['prod_discount_amt'] , $curr_prod_array['prod_unit_price'] , 2);
            $yousave_pct_2decimals = round( ($curr_prod_array['prod_discount_amt'] / $curr_prod_array['prod_unit_price'] ) , 2);
                           
          //$remainder = $yousave_pct_temp - $yousave_pct_2decimals;
            $remainder = round($yousave_pct_temp - $yousave_pct_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
             
            if ($remainder > 0.005) {
              //v1.0.7.4 begin
              $increment = round($remainder, 2); //round the rounding error to 2 decimal points!  floating point
              if ($increment < .01) {
                $increment = .01;
              }
              ////v1.0.7.4 end
              $yousave_pct = ($yousave_pct_2decimals + $increment) * 100;     //v1.0.7.4  PHP floating point 
            } else {
              $yousave_pct = $yousave_pct_2decimals * 100;
            }
            $refigure_yousave_product_totals = 'yes';
          }
        break;
    }

    //*************************************
    // PURCHASE HISTORY LIFETIME LIMIT
    //*************************************   
        
    //adjust yousave_amt and yousave_pct as needed based on limits
    switch( $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_type']  ) {  //var on the 1st iteration only
      case 'none':
        break;
      
      case 'quantity':
          //v1.0.4 begin => lifetime counted by group done elsewhere
          if ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] == 'all') {
            break;
          }
          //v1.0.4 end
          
        //if ( ($yousave_product_total_qty +  <= instead of this, we're actually counting home many times the RULE was used, not the total qty it was applied to...  
          if ( (1 + $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts ) > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_count']) {
             //we've reached the max allowed by this rule, as we only process 1 at a time, exit
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = 
              'No Discount - Discount Rule Lifetime Max Qty already reached, discount skipped.  Prev purch hist= ' .$vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts ;               
             return;
          }
        break;
        
      case 'currency':
          if ( ($yousave_product_total_amt + $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts ) > $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_count']) {
            
            //Only store error msg at checkout validation time
            if ($vtprd_info['checkout_validation_in_process'] == 'yes') {                 
              //REMOVE any line breaks, etc, which would cause a JS error !!
              $tempMsg = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $vtprd_setup_options['lifetime_purchase_button_error_msg']);
              $vtprd_cart->error_messages[] = $tempMsg; 
            }            
            
            //reduce discount to max...
            $reduction_amt = ($yousave_product_total_amt + $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts ) - $vtprd_rules_set[$i]->rule_deal_info[0]['discount_lifetime_max_amt_count'];

            $curr_prod_array['prod_discount_amt']  = $curr_prod_array['prod_discount_amt'] - $reduction_amt;
            
            //v1.0.9.3 begin
            if ($curr_prod_array['prod_discount_amt'] > $curr_prod_array['prod_unit_price']) {
              $curr_prod_array['prod_discount_amt'] = $curr_prod_array['prod_unit_price'];
            }
            //v1.0.9.3 end
                        
            $max_msg = $vtprd_rules_set[$i]->discount_lifetime_max_amt_msg;
             
            $yousave_pct_temp = $curr_prod_array['prod_discount_amt'] / $curr_prod_array['prod_unit_price'];
            
            // $yousave_pct = $yousave_amt / $curr_prod_array['prod_unit_price'] * 100;        
            //compute remainder
            //$yousave_pct_2decimals = bcdiv($curr_prod_array['prod_discount_amt'] , $curr_prod_array['prod_unit_price'] , 2);
            $yousave_pct_2decimals = round( ($curr_prod_array['prod_discount_amt'] / $curr_prod_array['prod_unit_price'] ) , 2);
                 
            //$remainder = $yousave_pct_temp - $yousave_pct_2decimals;
            $remainder = round($yousave_pct_temp - $yousave_pct_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
                
             
            if ($remainder > 0.005) {
              //v1.0.7.4 begin
              $increment = round($remainder, 2); //round the rounding error to 2 decimal points!  floating point
              if ($increment < .01) {
                $increment = .01;
              }
              ////v1.0.7.4 end
              $yousave_pct = ($yousave_pct_2decimals + $increment) * 100;     //v1.0.7.4  PHP floating point 
            } else {
              $yousave_pct = $yousave_pct_2decimals * 100;
            }
            $refigure_yousave_product_totals = 'yes';
          }
        break;
    }
    
    //EXIT if Sale Price already lower than Discount
    if ( ($vtprd_cart->cart_items[$k]->product_is_on_special == 'yes') &&
         ($vtprd_rules_set[$i]->cumulativeSalePricing == 'replaceSalePrice' ) )  {
      //Replacement of Sale Price is requested, but only happens if Discount is GREATER THAN sale price
      $discounted_price = ($curr_prod_array['prod_unit_price'] - $curr_prod_array['prod_discount_amt'] ) ;
      If ($vtprd_cart->cart_items[$k]->db_unit_price_special < $discounted_price ) {
        $vtprd_cart->cart_items[$k]->unit_price     = $vtprd_cart->cart_items[$k]->db_unit_price_special;
        $vtprd_cart->cart_items[$k]->db_unit_price  = $vtprd_cart->cart_items[$k]->db_unit_price_special;
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'rejected';
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  'No Discount - Sale Price Less than Discounted price';
        return;      
      }
   }
  
    //*********************************************************************
    //eND MAX LIMITS CHECKING
    //*********************************************************************
           
      
    //*************************************
    // Add Discount Totals into the Array
    //*************************************       
    if ($yousave_for_this_rule_id_already_exists == 'yes') { 
       $vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['yousave_amt']     +=  $curr_prod_array['prod_discount_amt'] ;
    //cumulative percentage
       $vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['discount_applies_to_qty']++; 
       $vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id]['rule_max_amt_msg'] =  $max_msg;
    }  else {
       $vtprd_cart->cart_items[$k]->yousave_by_rule_info[$rule_id] =  array(
           'ruleset_occurrence'    => $i, 
           'discount_amt_type'   => '',
           'discount_amt_count'   => 0,
           'discount_for_the_price_of_count'  => '', 
           'discount_applies_to_qty'  => 1,         
           'yousave_amt'       => $curr_prod_array['prod_discount_amt'] ,
           'yousave_pct'       => $yousave_pct ,
           'rule_max_amt_msg'  => $max_msg,
           'rule_execution_type' =>  $vtprd_rules_set[$i]->rule_execution_type, //used when sending purchase EMAIL!!       
           'rule_short_msg'    => $vtprd_rules_set[$i]->discount_product_short_msg,
           'rule_full_msg'     => $vtprd_rules_set[$i]->discount_product_full_msg
           //used at cart discount display time => if coupon used, does this discount apply?
           //  ---> pick this up directly from the ruleset occurrence at application time
           //'cumulativeCouponPricingAllowed' => $vtprd_rules_set[$i]->cumulativeCouponPricingAllowed  
          );
        
        //******************************************
        //for later ajaxVariations pricing    - BEGIN
        //******************************************        
        if ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type'] == 'percent') {
          $pricing_rule_percent_discount  = $yousave_pct;
          $pricing_rule_currency_discount = 0;
        } else {
          $pricing_rule_percent_discount  = 0;
          $pricing_rule_currency_discount = $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'];        
        }
        $vtprd_cart->cart_items[$k]->pricing_by_rule_array[] =  array(  
            'pricing_rule_id' => $rule_id, 
            'pricing_rule_applies_to_variations_array' => $vtprd_rules_set[$i]->var_in_checked , //' ' or var list array
            'pricing_rule_percent_discount'  => $pricing_rule_percent_discount,
            'pricing_rule_currency_discount' => $pricing_rule_currency_discount 
          );
        //  ajaxVariations pricing - END
           
    }
    //recompute the discount totals for use in next iteration
    $vtprd_rules_set[$i]->discount_total_qty_for_rule = $vtprd_rules_set[$i]->discount_total_qty_for_rule + 1;
    $vtprd_rules_set[$i]->discount_total_amt_for_rule = $vtprd_rules_set[$i]->discount_total_amt_for_rule + $curr_prod_array['prod_discount_amt'] ;
    //$discount_total_unit_price_for_rule will be the unit qty * db_unit_price already, as this routine is done 1 by 1...
    $vtprd_rules_set[$i]->discount_total_unit_price_for_rule =  $vtprd_rules_set[$i]->discount_total_unit_price_for_rule + $curr_prod_array['prod_db_unit_price'] ;
    //yousave pct whole number  = total discount amount / (orig unit price * number of units discounted)
    $vtprd_rules_set[$i]->discount_total_pct_for_rule = ($discount_total_amt_for_rule / $discount_total_unit_price_for_rule) * 100 ;

    //REFIGURE the product totals, if there was a reduction above...
    if ($refigure_yousave_product_totals == 'yes') {
      $yousave_product_total_amt = $vtprd_cart->cart_items[$k]->yousave_total_amt +  $curr_prod_array['prod_discount_amt'] ;
      $yousave_product_total_qty = $vtprd_cart->cart_items[$k]->yousave_total_qty + 1;
      //  yousave_total_unit_price is a rolling full total of unit price already
      $yousave_total_unit_price = $vtprd_cart->cart_items[$k]->yousave_total_unit_price + $curr_prod_array['prod_unit_price'];  
      //yousave pct whole number = (total discount amount / (orig unit price * number of units discounted))
      $yousave_pct_prod_temp = $yousave_product_total_amt / $yousave_total_unit_price;
      //$yousave_pct_prod_2decimals = bcdiv($yousave_product_total_amt , $yousave_total_unit_price , 2);
      $yousave_pct_prod_2decimals = round( ($yousave_product_total_amt / $yousave_total_unit_price ) , 2);  
         
      //$remainder = $yousave_pct_prod_temp - $yousave_pct_prod_2decimals;
      $remainder = round($yousave_pct_prod_temp - $yousave_pct_prod_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!

      if ($remainder > 0.005) {
        //v1.0.7.4 begin
        $increment = round($remainder, 2); //round the rounding error to 2 decimal points!  floating point
        if ($increment < .01) {
          $increment = .01;
        }
        //v1.0.7.4 end     
        $yousave_product_total_pct = ($yousave_pct_prod_2decimals + $increment) * 100;  //v1.0.7.4   floating point
      } else {
        $yousave_product_total_pct = $yousave_pct_prod_2decimals * 100;
      } 
    }      
    $vtprd_cart->cart_items[$k]->yousave_total_amt = $yousave_product_total_amt; 
    $vtprd_cart->cart_items[$k]->yousave_total_qty = $yousave_product_total_qty; 
    $vtprd_cart->cart_items[$k]->yousave_total_pct = $yousave_product_total_pct ;
    $vtprd_cart->cart_items[$k]->yousave_total_unit_price = $yousave_total_unit_price;
    
    //keep track of historical discount totals 
     //instead of $yousave_product_total_qty;, we're actually counting home many times the RULE was used, not the total qty it was applied to... 
    
    //v1.0.4 begin => lifetime counted by group (= 'all') added only after group processing for rule is complete
    if ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_applies_to'] != 'all') {
      $vtprd_rules_set[$i]->purch_hist_rule_row_qty_total_plus_discounts    +=  1; // +1 for each RULE OCCURRENCE usage...
    }
    //v1.0.4 end    
        
    $vtprd_rules_set[$i]->purch_hist_rule_row_price_total_plus_discounts  +=  $curr_prod_array['prod_discount_amt'];
    
    //used in lifetime limits
    $vtprd_rules_set[$i]->actionPop_rule_yousave_amt  +=  $curr_prod_array['prod_discount_amt'];
    $vtprd_rules_set[$i]->actionPop_rule_yousave_qty  +=  1;  //$yousave_product_total_qty;  not qty, but iterations of USAGE!
    
    //$vtprd_cart->cart_items[$k]->discount_price    = ($vtprd_cart->cart_items[$k]->db_unit_price * $vtprd_cart->cart_items[$k]->quantity) - $yousave_product_total_amt ;  
    $vtprd_cart->cart_items[$k]->discount_price    = ( $curr_prod_array['prod_unit_price'] * $vtprd_cart->cart_items[$k]->quantity) - $yousave_product_total_amt ; 
    
    //v1.1.1 begin
    if ($vtprd_cart->cart_items[$k]->discount_price > 0) {
      $vtprd_cart->cart_items[$k]->discount_unit_price  =  round( $vtprd_cart->cart_items[$k]->discount_price / $vtprd_cart->cart_items[$k]->quantity , 2); 
    } else {
      $vtprd_cart->cart_items[$k]->discount_unit_price  =  '';    
    }  
    //v1.1.1 end
        
    $vtprd_rules_set[$i]->discount_applied == 'yes';
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'applied';
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] = __('Discount Applied', 'vtprd');
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_amt'] = $curr_prod_array['prod_discount_amt'];
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_pct'] = $yousave_pct;
    
    //                         *******************
    //if discount has applied, update rule totals after recalc to pick up most current total price info 
    //                         *******************  
    
    
    //add in total saved to yousave_total_amt for PRODUCT
   
    if ($curr_prod_array['prod_discount_amt'] > 0) {  

      //v1.0.5.3 begin 
      //  the vtprd_cart unit price and discounts all reflect the TAX STATE of 'woocommerce_prices_include_tax'      
		   global $woocommerce;
       $product_id = $vtprd_cart->cart_items[$k]->product_id;
       switch( true ) {
          case ( get_option( 'woocommerce_calc_taxes' ) == 'no' ):
          case ( $woocommerce->customer->is_vat_exempt() ):  
             $prod_discount_amt_excl_tax  =  $curr_prod_array['prod_discount_amt'];
             $prod_discount_amt_incl_tax  =  $curr_prod_array['prod_discount_amt'];
            break; 
          case ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ): 
             $prod_discount_amt_excl_tax  =  vtprd_get_price_excluding_tax($product_id, $curr_prod_array['prod_discount_amt']);
             $prod_discount_amt_incl_tax  =  $curr_prod_array['prod_discount_amt'];
            break; 
          case ( get_option( 'woocommerce_prices_include_tax' ) == 'no' ): 
             $prod_discount_amt_excl_tax  =  $curr_prod_array['prod_discount_amt'];
             $prod_discount_amt_incl_tax  =  vtprd_get_price_including_tax($product_id, $curr_prod_array['prod_discount_amt']);
            break;              
		   }
       //THIS is where the cart SAVE TOTALS are stored!!             
       $vtprd_cart->yousave_cart_total_amt_excl_tax      += $prod_discount_amt_excl_tax;
       $vtprd_cart->yousave_cart_total_amt_incl_tax      += $prod_discount_amt_incl_tax;
        //v1.0.5.3 end    
         
                                
      $vtprd_rules_set[$i]->discount_total_qty += 1;     
      $vtprd_rules_set[$i]->discount_total_amt += $curr_prod_array['prod_discount_amt'];
      $vtprd_cart->yousave_cart_total_qty      += 1;
      $vtprd_cart->yousave_cart_total_amt      += $curr_prod_array['prod_discount_amt'];        
    }    

    //mark exploded list product as already processed for this rule
    $vtprd_rules_set[$i]->actionPop_exploded_found_list[$occurrence]['prod_discount_applied'] = 'yes';


    //**********************************************
    //  if this product is free, add the product qty to the tracking bucket
    //**********************************************    
    if ($curr_prod_array['prod_discount_amt'] == $vtprd_cart->cart_items[$k]->unit_price) {  
      $key =  $vtprd_cart->cart_items[$k]->product_id;
      if (isset($vtprd_rules_set[$i]->free_product_array[$key])) {
         $vtprd_rules_set[$i]->free_product_array[$key]++;
      } else {
         $vtprd_rules_set[$i]->free_product_array[$key] = 1;
      }
    }
    
    //********************
    //v1.1.1.2 begin - reworked for multiple free 
    //v1.1.0.6 begin
    //  SINGLE UNIT - mark free candidate as free, increment/decrement counters
    $current_auto_add_array = $this->vtprd_get_current_auto_add_array(); 
    $free_product_id = $vtprd_cart->cart_items[$k]->product_id; //v1.1.1.2 

//    if ( ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') && 
//         ($vtprd_cart->cart_items[$k]->product_auto_insert_state == 'candidate') ) {
    if ( ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') &&
         ($vtprd_cart->cart_items[$k]->product_auto_insert_rule_id  ==  $vtprd_rules_set[$i]->post_id) &&
         //($vtprd_cart->cart_items[$k]->product_id == $current_auto_add_array['free_product_id']) ){
         (isset($current_auto_add_array[$free_product_id])) ) {
       
     
      $vtprd_cart->cart_items[$k]->product_auto_insert_state = 'free';
      $current_auto_add_array[$free_product_id]['free_qty'] ++;
      $current_auto_add_array[$free_product_id]['candidate_qty'] --;
      
      $current_auto_add_array[$free_product_id]['current_qty'] = 
        ($current_auto_add_array[$free_product_id]['purchased_qty'] + $current_auto_add_array[$free_product_id]['free_qty']); //v1.1.1.2

       
      //error_log( print_r(  '$current_auto_add_array at ADD TO FREE_QTY time = ', true ) );
      //error_log( var_export($current_auto_add_array, true ) ); 
        
      $_SESSION['current_auto_add_array'] = serialize($current_auto_add_array);
    }
    //v1.1.0.6 end
    //v1.1.1.2 end
    //********************
    
    return;
 }
 
 
  public function vtprd_compute_each_discount($i, $d, $prod_unit_price ) {   
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework;    
       
     //error_log( print_r(  'vtprd_compute_each_discount ', true ) ); 
        
     //$vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price']
    switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_type']  ) {            
      case 'free':
          $discount = $prod_unit_price;
        break;
      case 'fixedPrice':
          $discount = $prod_unit_price - $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'];                               
        break;
      case 'percent':
          $percent_off = $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'] / 100;   
        //$discount_2decimals = bcmul($prod_unit_price , $percent_off , 2);
          $discount_2decimals = round($prod_unit_price * $percent_off , 2); //v1.0.7.6
                          
          //compute rounding
          $temp_discount = $prod_unit_price * $percent_off;
          
          //$rounding = $temp_discount - $discount_2decimals;
          $rounding = round($temp_discount - $discount_2decimals, 4);   //v1.0.7.4  PHP floating point error fix - limit to 4 places right of the decimal!!
                  
          if ($rounding > 0.005) {
            //v1.0.7.4 begin
            $increment = round($rounding, 2); //round the rounding error to 2 decimal points!  floating point
            if ($increment < .01) {
              $increment = .01;
            }
            ////v1.0.7.4 end          
            $discount = $discount_2decimals + $increment;   //v1.0.7.4  PHP floating
          }  else {
            $discount = $discount_2decimals;
          }             
        break;              
      case 'currency': 
          $discount = $vtprd_rules_set[$i]->rule_deal_info[$d]['discount_amt_count'];
                      
          //v1.0.9.3 begin
          if ($discount > $prod_unit_price) {
            $discount = $prod_unit_price;
          }
          //v1.0.9.3 end 
              
        break;
    }
    return $discount;
  }
 
  public function vtprd_set_buy_group_end($i, $d, $r ) { 
    global $post, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework;  
      
     //error_log( print_r(  'vtprd_set_buy_group_end i= ' .$i.  ' $d= ' .$d.  ' $r= ' .$r , true ) ); 
        
    //mwn change
    /* 
    can only set begin here: 
    * if qty, end is buy_amt_count
    * if currency, end is only known if currency value of buy_amt_count is reached
    * if nth, end is a multiple of ($r + 1) * buy_amt_count        
    */
    $templateKey = $vtprd_rules_set[$i]->rule_template;    
      
    $for_loop_current_prod_id = '';
    $for_loop_unit_count = 0;
    $for_loop_price_total = 0;
    $for_loop_elapsed_count = 0;

    if ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] == 'quantity') || 
         ($vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] == 'currency') ) {

      $sizeof_inPop_exploded_found_list = sizeof($vtprd_rules_set[$i]->inPop_exploded_found_list);  
      
      for($e=$vtprd_rules_set[$i]->inPop_exploded_group_begin; $e < $sizeof_inPop_exploded_found_list; $e++) {
        $for_loop_elapsed_count++;        
        switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] ) {
          
          case 'quantity':
                $temp_end = $vtprd_rules_set[$i]->inPop_exploded_group_begin + $for_loop_elapsed_count;
               // $temp_end = $vtprd_rules_set[$i]->inPop_exploded_group_end + $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count'] ;  
               // if ( $temp_end > sizeof($vtprd_rules_set[$i]->inPop_exploded_found_list) ) {  //v1.1.0.6 
                if ( $temp_end > $sizeof_inPop_exploded_found_list ) {  //v1.1.0.6 
                   $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                   $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining qty in cart to fulfill buy amt qty';
    //error_log( print_r('cartGroupFailedTest 002 ', true ) );                   
                   return;
                }              
               switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to'] ) {             
                  case 'each':
                       //check if new product in list...
                       if ($for_loop_current_prod_id != $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_id'] ) {
                          //if new product, reset all tracking fields
                          $for_loop_current_prod_id = $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_id'];
                          $for_loop_unit_count = 1;
                          $for_loop_price_total = $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price'];
                       } else {
                          $for_loop_unit_count++;
                          $for_loop_price_total += $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price'];
                       }
                    break;               
                  case 'all':
                      $for_loop_unit_count++;
                      $for_loop_price_total += $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price']; 
                    break;           
               } //end switch  $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to']                
               if ($for_loop_unit_count == $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count']) {
                  //Set group_end here.  use $e + 1 since we may have reset the for_loop_unit_count during processing
                  
                  $vtprd_rules_set[$i]->inPop_exploded_group_end = $vtprd_rules_set[$i]->inPop_exploded_group_begin + $for_loop_elapsed_count; 
                                                      
                  if ($vtprd_template_structures_framework[$templateKey]['buy_amt_mod'] > ' ' ) {
                     switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod'] ) {
                         case 'none':
                           break;  
                         case 'minCurrency':                           
                              if ($for_loop_price_total < $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod_count']) { // < is an error, value should be >= 
                                $failed_test_total++;
                                $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                                $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill minimum buy amt qty';
     //error_log( print_r('cartGroupFailedTest 003 ', true ) );  
                                return; //v1.1.0.6                                                               
                              }
                           break;
                         case 'maxCurrency':
                              if ($for_loop_price_total > $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod_count']) { // > is an error, value should be <= 
                                $failed_test_total++;
                                $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                                $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill maximum buy amt qty';
    //error_log( print_r('cartGroupFailedTest 004 ', true ) );
                                return; //v1.1.0.6    
                              }                              
                           break;                                            
                     } //end switch 
                   }                                   
                  $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Buy amt Qty test completed';
                  return; // done, passed the test, both begin and end set...
               }  //end if         
            break;
         
          case 'currency':
                $temp_end = $vtprd_rules_set[$i]->inPop_exploded_group_begin + $for_loop_elapsed_count;  
                //if ( $temp_end > sizeof($vtprd_rules_set[$i]->inPop_exploded_found_list) ) {    //v1.1.0.6
                if ( $temp_end > $sizeof_inPop_exploded_found_list ) {    //v1.1.0.6  
                   $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                    $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill buy amt qty';
    //error_log( print_r('cartGroupFailedTest 005 ', true ) );                     
                   return;
                }             
               switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to'] ) {             
                  case 'each':
                       //check if new product in list...
                       if ($for_loop_current_prod_id != $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_id'] ) {
                          //if new product, reset all tracking fields
                          $for_loop_current_prod_id = $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_id'];
                          $for_loop_unit_count = 1;
                          $for_loop_price_total = $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price'];
                       } else {
                          $for_loop_unit_count++;
                          $for_loop_price_total += $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price'];
                       }
                    break;               
                  case 'all':
                      $for_loop_unit_count++;
                      $for_loop_price_total += $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price']; 
                    break;           
               } //end switch  $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to']
               
               if ($for_loop_price_total >= $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count']) {
                  //Set group_end here.  use $e + 1 since we may have reset the for_loop_unit_count during processing
                  
                  $vtprd_rules_set[$i]->inPop_exploded_group_end = $vtprd_rules_set[$i]->inPop_exploded_group_begin + $for_loop_elapsed_count;
                                
                  if ($vtprd_template_structures_framework[$templateKey]['buy_amt_mod'] > ' ' ) {
                    switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod'] ) {
                       case 'none':
                         break;
                       case 'minCurrency':                           
                            if ($for_loop_price_total < $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod_count']) { // < is an error, value should be >= 
                              $failed_test_total++;
                              $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                              $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill minimum buy amt mod count';
    //error_log( print_r('cartGroupFailedTest 006 ', true ) ); 
                              return; //v1.1.0.6
                            }
                         break;
                       case 'maxCurrency':
                            if ($for_loop_price_total > $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod_count']) { // > is an error, value should be <= 
                              $failed_test_total++;
                              $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                              $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill maximum buy amt mod count';
    //error_log( print_r('cartGroupFailedTest 007 ', true ) ); 
                              return; //v1.1.0.6
                            }                              
                         break;                                              
                    } //end switch                                    
                  }
                  $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Buy amt $$ test completed';
                  return; // done, passed the test, both begin and end set...
               }  //end if  
           break;
                       
        }  //end switch  vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_type'] 
      } //end for loop
      
      //if loop reached end of list... 
      //if ($e >= sizeof($vtprd_rules_set[$i]->inPop_exploded_found_list) ) { //v1.1.0.6 
      if ($e >= $sizeof_inPop_exploded_found_list ) { //v1.1.0.6 
        $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
        $vtprd_rules_set[$i]->rule_processing_msgs[] = 'reached end of inPop_exploded_found_list';
    //error_log( print_r('cartGroupFailedTest 008 $e= ' .$e, true ) ); 
    //error_log( print_r('$sizeof_inPop_exploded_found_list= ' .$sizeof_inPop_exploded_found_list, true ) );         
        return;
      }
    } else {//end if 'quantity' or 'currency'
      
      //'nthQuantity' path
      $end_of_nth_test = 'no';         
      //Must do 'for' loop, as exploded list may cross product boundaries and if 'each' the count must be reset...
      for($e=$vtprd_rules_set[$i]->inPop_exploded_group_begin; $end_of_nth_test == 'no'; $e++) {
          $for_loop_elapsed_count++;       
          $temp_end = $vtprd_rules_set[$i]->inPop_exploded_group_begin + $for_loop_elapsed_count;  
          //if ( $temp_end > sizeof($vtprd_rules_set[$i]->inPop_exploded_found_list) ) { //v1.1.0.6 
          if ( $temp_end > $sizeof_inPop_exploded_found_list ) { //v1.1.0.6 
             $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
             $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining buy qty for nth';
    //error_log( print_r('cartGroupFailedTest 009 ', true ) );              
             return;
          }
          
          switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to'] ) {             
            case 'each':
                 //check if new product in list...
                 if ($for_loop_current_prod_id != $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_id'] ) {
                    //if new product, reset all tracking fields
                    $for_loop_current_prod_id = $vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_id'];
                    $for_loop_unit_count = 1;                
                 } else {
                    $for_loop_unit_count++;                  
                 }
              break;               
            case 'all':
                $for_loop_unit_count++;               
              break;           
          } //end switch  $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_applies_to']        
               
          if ($for_loop_unit_count == $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_count']) {
            //Set group_end here.  use $e + 1 since we may have reset the for_loop_unit_count during processing
            $vtprd_rules_set[$i]->inPop_exploded_group_end = $vtprd_rules_set[$i]->inPop_exploded_group_begin + $for_loop_elapsed_count;                               
            if ($vtprd_template_structures_framework[$templateKey]['buy_amt_mod'] > ' ' ) {
              switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod'] ) {
                 case 'none':
                    break;
                 case 'minCurrency':                           
                      if ($vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price'] < $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod_count']) { // < is an error, value should be >= 
                        $failed_test_total++;
                        $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                        $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining minimum buy $$ for nth';
    //error_log( print_r('cartGroupFailedTest 010 ', true ) ); 
                      }
                   break;
                 case 'maxCurrency':
                      if ($vtprd_rules_set[$i]->inPop_exploded_found_list[$e]['prod_unit_price'] > $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod_count']) { // > is an error, value should be <= 
                        $failed_test_total++;
                        $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                        $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining maximum buy $$ for nth';
    //error_log( print_r('cartGroupFailedTest 011 ', true ) );                         
                      }                              
                   break;                                              
              } //end switch                                    
            }
            $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Buy amt Qty Nth test completed';
            return; // done, passed the test, both begin and end set...            
          }  //end if
          //if ($e >= sizeof($vtprd_rules_set[$i]->inPop_exploded_found_list) ) { //v1.1.0.6 
          if ( $e >= $sizeof_inPop_exploded_found_list ) {  //v1.1.0.6      
            $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
            $vtprd_rules_set[$i]->rule_processing_msgs[] = 'End of inPop reached during Nth processing';
    //error_log( print_r('cartGroupFailedTest 012 ', true ) );
            $end_of_nth_test = 'yes';
            return;
          }         
      } // end for loop $end_of_nth_test
        
    } //end if
 
    return;
 }
 
     //if action_amt_type is active and there is a action_amt count...
    //***********************************************************
    //THIS SETS THE SIZE OF THE BUY exploded GROUP "WINDOW"
    //***********************************************************
 
  public function vtprd_set_action_group_end($i, $d, $ar ) { 
    global $post, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework;     
      
     //error_log( print_r(  'vtprd_set_action_group_end ', true ) ); 
 
    /*
    DETERMINE THE BEGIN AND END OF ACTIONPOP PROCESSING "WINDOW"
    
    1st time, group_end set to 0, end may be set but will be overwritten here
              group_begin remains at 0, since its an OCCURRENCE begin
    2nd-Nth,  group_begin set to previous end + 1
              group_end set to a computed value.  If the required action group size is not reached or end of actionPop reached, 
                  the setup/edit fails.     
    */

    //error_log( print_r('TOP OF vtprd_set_action_group_end, actionPop_exploded_group_begin =  ' .$vtprd_rules_set[$i]->actionPop_exploded_group_begin , true ) );  
    
    $templateKey = $vtprd_rules_set[$i]->rule_template;
    
    $for_loop_current_prod_id = ''; //v1.0.8.7
    $for_loop_unit_count = 0;
    $for_loop_price_total = 0;
    $for_loop_elapsed_count = 0;

    if ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'quantity') || ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'currency') ) {
      $sizeof_actionPop_exploded_found_list = sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list);
      for($e=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $e < $sizeof_actionPop_exploded_found_list; $e++) {
        $for_loop_elapsed_count++;
        switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] ) {
          
          case 'quantity':         
                $temp_end = $vtprd_rules_set[$i]->actionPop_exploded_group_end + $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'] ;  
                if ( $temp_end > sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list) ) {
                   $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                   $vtprd_rules_set[$i]->end_of_actionPop_reached = 'yes';
                   $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining qty in cart to fulfill action amt qty';
    //error_log( print_r('cartGroupFailedTest 013 ', true ) );                   
                   return;
                }               
               switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to'] ) {             
                  case 'each':
                       //check if new product in list...
                       if ($for_loop_current_prod_id != $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_id'] ) {
                          //if new product, reset all tracking fields
                          $for_loop_current_prod_id = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_id'];
                          $for_loop_unit_count = 1;
                          $for_loop_price_total = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price'];                     
                       } else {
                          $for_loop_unit_count++;
                          $for_loop_price_total += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price'];
                       }
                    break;               
                  case 'all':
                      $for_loop_unit_count++;
                      $for_loop_price_total += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price'];                   
                    break;           
               } //end switch  $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to']            
               if ($for_loop_unit_count == $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count']) {
                  $vtprd_rules_set[$i]->actionPop_exploded_group_end = $vtprd_rules_set[$i]->actionPop_exploded_group_begin + $for_loop_elapsed_count;                     
    //error_log( print_r('actionPop_exploded_group_end reset here = ' .$vtprd_rules_set[$i]->actionPop_exploded_group_end, true ) );                  
                  if ($vtprd_template_structures_framework[$templateKey]['action_amt_mod'] > ' ' ) {
                     switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod'] ) {
                         case 'none':
                           break;  
                         case 'minCurrency':                           
                              if ($for_loop_price_total < $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod_count']) { // < is an error, value should be >= 
                                $failed_test_total++;
                                $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                                $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill minimum action amt qty'; 
    //error_log( print_r('cartGroupFailedTest 014 ', true ) );                                                                                                                               
                              }
                           break;
                         case 'maxCurrency':
                              if ($for_loop_price_total > $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod_count']) { // > is an error, value should be <= 
                                $failed_test_total++;
                                $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                                $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill maximum action amt qty'; 
    //error_log( print_r('cartGroupFailedTest 015 ', true ) );                                                               
                              }                              
                           break;                                            
                     } //end switch 
                   }                                                   
                  $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Action amt Qty test completed';
                  return; // done, passed the test, both begin and end set...
               }  //end if         
          break;
         
          case 'currency':          
               switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to'] ) {             
                  case 'each':
                       //check if new product in list...
                       if ($for_loop_current_prod_id != $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_id'] ) {
                          //if new product, reset all tracking fields
                          $for_loop_current_prod_id = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_id'];
                          $for_loop_unit_count = 1;
                          $for_loop_price_total = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price'];
                       } else {
                          $for_loop_unit_count++;
                          $for_loop_price_total += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price'];
                       }
                    break;               
                  case 'all':
                      $for_loop_unit_count++;
                      $for_loop_price_total += $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price']; 
                    break;           
               } //end switch  $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to']
               
               if ($for_loop_price_total >= $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count']) {
                  
                  $vtprd_rules_set[$i]->actionPop_exploded_group_end = $vtprd_rules_set[$i]->actionPop_exploded_group_begin + $for_loop_elapsed_count;                     
    //error_log( print_r('actionPop_exploded_group_end reset here2 = ' .$vtprd_rules_set[$i]->actionPop_exploded_group_end, true ) );                                     
                  if ($vtprd_template_structures_framework[$templateKey]['action_amt_mod'] > ' ' ) {
                    switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod'] ) {
                       case 'none':
                         break;
                       case 'minCurrency':                           
                            if ($for_loop_price_total < $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod_count']) { // < is an error, value should be >= 
                              $failed_test_total++;
                              $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                              $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill minimum action amt mod count';
    //error_log( print_r('cartGroupFailedTest 016 ', true ) );                              
                            }
                         break;
                       case 'maxCurrency':
                            if ($for_loop_price_total > $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod_count']) { // > is an error, value should be <= 
                              $failed_test_total++;
                              $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                              $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining $$ in cart to fulfill maximum action amt mod count';
    //error_log( print_r('cartGroupFailedTest 017 ', true ) );                                                            
                            }                              
                         break;                                              
                    } //end switch                                    
                  }
                  $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Action amt $$ test completed';
                  return; // done, passed the test, both begin and end set...
               }  //end if  
          break;
                       
        }  //end switch  vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] 
      } //end for loop
            
      //if loop dropout + reached end of list...
      if ($e >= sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list) ) {
        $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
        $vtprd_rules_set[$i]->rule_processing_msgs[] = 'reached end of actionPop_exploded_found_list';
     //error_log( print_r('cartGroupFailedTest 018; $e=  ' .$e, true ) );  
     
     //error_log( print_r('$sizeof_actionPop_exploded_found_list = ' .$sizeof_actionPop_exploded_found_list, true ) );       
        return;
      }
    } else {//end if 'quanity' or 'currency'
      
      //'nthQuantity' path
      $end_of_nth_test = 'no';
      for($e=$vtprd_rules_set[$i]->actionPop_exploded_group_begin; $end_of_nth_test == 'no'; $e++) {
         $for_loop_elapsed_count++;
         $temp_end = $vtprd_rules_set[$i]->actionPop_exploded_group_begin + $for_loop_elapsed_count;  
          if ( $temp_end > sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list) ) {
             $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
             $vtprd_rules_set[$i]->end_of_actionPop_reached = 'yes';
             $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining action qty for nth';
    //error_log( print_r('cartGroupFailedTest 019 ', true ) );             
             return;
          }
          
          switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to'] ) {             
            case 'each':
                 //check if new product in list...
                 if ($for_loop_current_prod_id != $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_id'] ) {
                    //if new product, reset all tracking fields
                    $for_loop_current_prod_id = $vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_id'];
                    $for_loop_unit_count = 1;
                 } else {
                    $for_loop_unit_count++;
                 }
              break;               
            case 'all':
                $for_loop_unit_count++;
              break;           
          } //end switch  $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_applies_to']        
               
          if ($for_loop_unit_count == $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count']) {
            $vtprd_rules_set[$i]->actionPop_exploded_group_end = $vtprd_rules_set[$i]->actionPop_exploded_group_begin + $for_loop_elapsed_count; 
    //error_log( print_r('actionPop_exploded_group_end reset here3 = ' .$vtprd_rules_set[$i]->actionPop_exploded_group_end, true ) );                                 
            if ($vtprd_template_structures_framework[$templateKey]['action_amt_mod'] > ' ' ) {
              switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod'] ) {
                 case 'none':
                    break;
                 case 'minCurrency':                           
                      if ($vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price'] < $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod_count']) { // < is an error, value should be >= 
                        $failed_test_total++;
                        $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                        $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining minimum action $$ for nth';
    //error_log( print_r('cartGroupFailedTest 020 ', true ) );                        
                      }
                   break;
                 case 'maxCurrency':
                      if ($vtprd_rules_set[$i]->actionPop_exploded_found_list[$e]['prod_unit_price'] > $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_mod_count']) { // > is an error, value should be <= 
                        $failed_test_total++;
                        $vtprd_rules_set[$i]->rule_processing_status = 'cartGroupFailedTest';
                        $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Insufficient remaining maximum action $$ for nth';
    //error_log( print_r('cartGroupFailedTest 021 ', true ) );
                      }                              
                   break;                                              
              } //end switch                                    
            }
            $vtprd_rules_set[$i]->rule_processing_msgs[] = 'Action amt Qty Nth test completed';
            return; // done, passed the test, both begin and end set...
          }  //end if        
      } // end for loop $end_of_nth_test
        
    } //end if
    
   return;
 }
 
 /*
 This process treats all of the products/quantities in the cart as a running total.  For each sub-group in the cart, derived from applying the buy_amt_count,
 the group valuation is computed.  if it doesn't fulfill the buy_amt_mod requirements, that part of the cart fails this test (for this rule). 
 */ 
  public function vtprd_buy_amt_mod_all_process($i,$d, $failed_test_total) { 
    global $post, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_template_structures_framework;    
       
     //error_log( print_r(  'vtprd_buy_amt_mod_all_process ', true ) ); 
     
    //walk through the cart imits 1 by 1 until inPop_running_unit_group_begin_pointer reached

    //preset to 'fail', on success it is switched to 'pass' in the routine
    //$vtprd_rules_set[$i]->buy_amt_process_status = 'fail';
    $current_group_pointer = 0;
    $buy_amt_mod_count_elapsed = 0;
    $buy_amt_mod_count_currency_total = 0;

    $sizeof_inPop_found_list = sizeof($vtprd_rules_set[$i]->inPop_found_list);
    for($k=0; $k < $sizeof_inPop_found_list; $k++) {      
    //add this product's unit count to the current_group_pointer
    //   until unit_counter_begin reached or end of unit count 
      for($z=0; $z < $vtprd_rules_set[$i]->inPop_found_list[$k]['prod_qty']; $z++) {
         //this augments the $current_group_pointer until it equals the begin pointer, then stops
         //  from this point on, it's the gateway to the rest of the routine.
         if ($current_group_pointer < $vtprd_rules_set[$i]->inPop_group_begin_pointer) { 
            $current_group_pointer++;
         }         
         if ($current_group_pointer == $vtprd_rules_set[$i]->inPop_group_begin_pointer) {
            //used to track the correct starting point
            $buy_amt_mod_count_elapsed++;
            //total up the unit costs until ...
            $buy_amt_mod_count_currency_total +=  $vtprd_rules_set[$i]->inPop_found_list[$k]['prod_unit_price'];  
            
            //if currency threshhold reached...., test and exit
            if ($buy_amt_mod_count_currency_total >= $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod_count']  ) {            
              switch( $vtprd_rules_set[$i]->rule_deal_info[$d]['buy_amt_mod'] ) {
                case 'minCurrency':
                    
                  break;
                case 'maxCurrency':
                  break;
              }
              
              //increment the begin pointer to the end of current group +1 
              $vtprd_rules_set[$i]->inPop_group_begin_pointer = $vtprd_rules_set[$i]->inPop_group_begin_pointer + $buy_amt_mod_count_elapsed + 1 ;
              break 2;  //break out of both for loops and return...
            }
         }
      }
    }                        
     
    return;
 }

/*
  NO LONGER USED!!!!!!!!!!!!!
    
  public function vtprd_test_actionPop_conditions($i) {
    global $post, $vtprd_setup_options, $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info;         
    
           
    //Test PRODCAT list for min requirements   
    $prodcat_out_checked_min_requirement_total_count = 0; 
    $prodcat_out_checked_min_requirement_value_reached_count = 0;
    
    $sizeof_prodcat_out_checked_info = sizeof($vtprd_rules_set[$i]->prodcat_out_checked_info);
    for($k=0; $k < $sizeof_prodcat_out_checked_info; $k++) {
        if ($vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value > 0) {
          $prodcat_out_checked_min_requirement_total_count++; //track min requirements, increment
          if ($vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value_reached == 'yes')  {           
                $prodcat_out_checked_min_requirement_value_reached_count++; //track all min requirements, increment
          }                
        }
    }
    
    if ( $vtprd_rules_set[$i]->prodcat_out_checked_min_requirement == "one"  ) {
      if ( $prodcat_out_checked_min_requirement_value_reached_count == 0 ) {
        //min requirement not met, exit
        $vtprd_info['actionPop_conditions_met'] = 'no';
        return;
      }    
    } else {  //all required
      if ($prodcat_out_checked_min_requirement_value_reached_count <> $prodcat_out_checked_min_requirement_total_count) {
        //min requirement not met, exit
        $vtprd_info['actionPop_conditions_met'] = 'no';
        return;
      } 
    }

      
    //Test RULECAT list for min requirements   
    $rulecat_out_checked_min_requirement_total_count = 0; 
    $rulecat_out_checked_min_requirement_value_reached_count = 0;
    
    $sizeof_rulecat_out_checked_info = sizeof($vtprd_rules_set[$i]->rulecat_out_checked_info); 
    for($k=0; $k < $sizeof_rulecat_out_checked_info; $k++) {
        if ($vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value > 0) {
          $rulecat_out_checked_min_requirement_total_count++; //track min requirements, increment
          if ($vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value_reached == 'yes')  {           
                $rulecat_out_checked_min_requirement_value_reached_count++; //track all min requirements, increment
          }                
        }
    }
    
    if ( $vtprd_rules_set[$i]->rulecat_out_checked_min_requirement == "one"  ) {
      if ( $rulecat_out_checked_min_requirement_value_reached_count == 0 ) {
        //min requirement not met, exit
        $vtprd_info['actionPop_conditions_met'] = 'no';
        return;
      }    
    } else {  //all required
      if ($rulecat_out_checked_min_requirement_value_reached_count <> $rulecat_out_checked_min_requirement_total_count) {
        //min requirement not met, exit
        $vtprd_info['actionPop_conditions_met'] = 'no';
        return;
      } 
    }                                 
   
      
    $vtprd_info['actionPop_conditions_met'] = 'yes';
    return;  
         
  }
 */ 
        
   public function vtprd_is_product_in_inPop_group($i, $k) { 
      global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
      
     //error_log( print_r(  'vtprd_is_product_in_inPop_group ', true ) ); 
       
      /* at this point, the checked list produced at rule store time could be out of sync with the db, as the cats/roles originally selected to be
      *  part of this rule could have been deleted.  this won't affect these loops, as the deleted cats/roles will simply not be in the 
      *  'get_object_terms' list. */

      $vtprd_is_role_in_list  = $this->vtprd_is_role_in_inPop_list_check ($i, $k);
      $vtprd_are_cats_in_list = $this->vtprd_are_cats_in_inPop_list_check ($i, $k);
            
      if ( $vtprd_rules_set[$i]->role_and_or_in == 'and' ) {
         $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = '"And" is required';
         if ($vtprd_is_role_in_list && $vtprd_are_cats_in_list) {            
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product in combined Categories list';
            return true;
         } else {
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product Not in combined Categories list, product rejected';
            return false;
         }
      }
      //otherwise this is an 'or' situation, and any participation = 'true'
      if ($vtprd_is_role_in_list || $vtprd_are_cats_in_list) {
         $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product in combined Categories or Role list';
         return true;
      }      
      $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product Not in combined Categories or Role list';
      return false;
   } 
  
    public function vtprd_is_role_in_inPop_list_check($i,$k) {
    	global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;     
      
     //error_log( print_r(  'vtprd_is_role_in_inPop_list_check ', true ) ); 
 
      $userRole = vtprd_get_current_user_role();
      $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['userRole'] = $userRole;
      
      if ( sizeof($vtprd_rules_set[$i]->role_in_checked) > 0 ) {
        if (in_array($userRole, $vtprd_rules_set[$i]->role_in_checked )) {   //if role is in previously checked_list
              if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){ 
                    error_log( print_r( 'In:: vtprd_is_role_in_inPop_list_check($i,$k)', true ) );
                    error_log( print_r( 'current user role= ' .$userRole, true ) );
                    error_log( print_r( 'rule id= ' .$vtprd_rules_set[$i]->post_id, true ) );
                    error_log( print_r( '$i= ' .$i, true ) );
                    error_log( print_r( '$k= ' .$k, true ) );
                    error_log( print_r( 'role_in_checked', true ) );
                    error_log( var_export($vtprd_rules_set[$i]->role_in_checked, true ) );                
              }
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_role_found'] = 'yes';    
          return true;                                
        } else {
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Role Not in checked role list';
        } 
      } else {
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'No Roles checked for rule';
      }
      $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_role_found'] = 'no';
      return false;
    }
    
    public function vtprd_are_cats_in_inPop_list_check($i,$k) {
    	global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;     
      
     //error_log( print_r(  'vtprd_are_cats_in_inPop_list_check ', true ) ); 
 
      //Test PRODCAT
      if ( ( sizeof($vtprd_cart->cart_items[$k]->prod_cat_list) > 0 ) && ( sizeof($vtprd_rules_set[$i]->prodcat_in_checked) > 0 ) ){   
        //$vtprd_cart->cart_items[$k]->prod_cat_list = wp_get_object_terms( $vtprd_cart->cart_items[$k]->product_id, $vtprd_info['parent_plugin_taxonomy'] );
        if ( array_intersect($vtprd_rules_set[$i]->prodcat_in_checked, $vtprd_cart->cart_items[$k]->prod_cat_list ) ) {   //if any in array1 are in array 2
            
            //LOAD RULE PRODCAT values, if found
            foreach ($vtprd_cart->cart_items[$k]->prod_cat_list as $term_id) {
               if ( (isset($vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value)) &&  //v1.1.0.6
                    ($vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value > 0) ) {  //if the individ cat has a min value, add to running total
                  if ($vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value_selector == 'quantity' ) {
                    $vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->quantity;
                  } else {
                   $vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->total_price;
                  }
                  if ($vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value_total == $vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value) {
                    $vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->value_reached = 'yes';
                  }
                  $vtprd_rules_set[$i]->prodcat_in_checked_info[$term_id]->cart_product_info [] =  array (
                       'prod_id' => $vtprd_cart->cart_items[$k]->product_id ,
                       'prod_id_cart_occurrence' => $k ) ;    
               } //end if                                  
            } //end foreach
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Prodcat found in prodcat checked list';
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_prod_cat_found'] = 'yes';
            return true;                                                  
        } else {
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Prodcat not found in prodcat checked list';
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_prod_cat_found'] = 'no';
        }
      }  else {
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'No Prodcats exist for product, or no prodcat checked list in rule';
      }
      
      //Test RULECAT 
      if ( ( sizeof($vtprd_cart->cart_items[$k]->rule_cat_list) > 0 ) && ( sizeof($vtprd_rules_set[$i]->rulecat_in_checked) > 0 ) ) {
       // $vtprd_cart->cart_items[$k]->rule_cat_list = wp_get_object_terms( $vtprd_cart->cart_items[$k]->product_id, $vtprd_info['rulecat_taxonomy'] );
        if ( array_intersect($vtprd_rules_set[$i]->rulecat_in_checked, $vtprd_cart->cart_items[$k]->rule_cat_list ) ) {   //if any in array1 are in array 2
                                            
             //LOAD RULERULECAT values, if found
            foreach ($vtprd_cart->cart_items[$k]->rule_cat_list as $term_id) {
               if ($vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->value > 0) {  //if the individ cat has a min value, add to running total
                  if ($vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->value_selector == 'quantity' ) {
                    $vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->quantity;
                  } else {
                   $vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->total_price;
                  }
                  if ($vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->value_total == $vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->value) {
                    $vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->value_reached = 'yes';
                  }
                  $vtprd_rules_set[$i]->rulecat_in_checked_info[$term_id]->cart_product_info [] =  array (
                       'prod_id' => $vtprd_cart->cart_items[$k]->product_id ,
                       'prod_id_cart_occurrence' => $k ) ;    
               } //end if                                  
            } //end foreach
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Rulecat not found in rulecat checked list';
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_rule_cat_found'] = 'yes';
            return true;
        } else {
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Rulecat not found in rulecat checked list';
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_rule_cat_found'] = 'no';
        }
      }  else {
        $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'No Rulecats exist for product, or no rulecat checked list in rule';
      }
      
      return false;
    }    


         
   public function vtprd_is_product_in_actionPop_group($i,$k) { 
      global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
      
     //error_log( print_r(  'vtprd_is_product_in_actionPop_group ', true ) ); 
 
      /* at this point, the checked list produced at rule store time could be out of sync with the db, as the cats/roles originally selected to be
      *  part of this rule could have been deleted.  this won't affect these loops, as the deleted cats/roles will simply not be in the 
      *  'get_object_terms' list. */

      $vtprd_is_role_in_list  = $this->vtprd_is_role_in_actionPop_list_check ($i, $k);
      $vtprd_are_cats_in_list = $this->vtprd_are_cats_in_actionPop_list_check ($i, $k);
            
      if ( $vtprd_rules_set[$i]->role_and_or_out == 'and' ) {
         $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_and_required'] =  'yes';
         if ($vtprd_is_role_in_list && $vtprd_are_cats_in_list) {
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['product_in_actionPop'] = 'yes';
            return true;
         } else {
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['product_in_actionPop'] = 'no';
            return false;
         }
      }
      //otherwise this is an 'or' situation, and any participation = 'true'
      if ($vtprd_is_role_in_list || $vtprd_are_cats_in_list) {
         $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['product_in_actionPop'] = 'yes';
         return true;
      }
      
      $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['product_in_actionPop'] = 'no';
      return false;
   } 
  
    public function vtprd_is_role_in_actionPop_list_check($i,$k) {
    	global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options; 
      
     //error_log( print_r(  'vtprd_is_role_in_actionPop_list_check ', true ) ); 
           
      if ( sizeof($vtprd_rules_set[$i]->role_out_checked) > 0 ) {
            if (in_array(vtprd_get_current_user_role(), $vtprd_rules_set[$i]->role_out_checked )) {   //if role is in previously checked_list
                  if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){ 
                    error_log( print_r( 'In:: vtprd_is_role_in_actionPop_list_check($i,$k)', true ) );
                    error_log( print_r( 'current user role= ' .$userRole, true ) );
                    error_log( print_r( 'rule id= ' .$vtprd_rules_set[$i]->post_id, true ) );
                    error_log( print_r( '$i= ' .$i, true ) );
                    error_log( print_r( '$k= ' .$k, true ) );
                    error_log( print_r( 'role_out_checked', true ) );
                    error_log( var_export($vtprd_rules_set[$i]->role_out_checked, true ) );                    
                  }
              $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_role_found'] =  'yes';
              return true;                                
            } 
      }
      $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_role_found'] =  'no'; 
      return false;
    }
    
    public function vtprd_are_cats_in_actionPop_list_check($i,$k) {
    	global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;     
      
     //error_log( print_r(  'vtprd_are_cats_in_actionPop_list_check ', true ) ); 
       
      //Test PRODCAT
      if ( ( sizeof($vtprd_cart->cart_items[$k]->prod_cat_list) > 0 ) && ( sizeof($vtprd_rules_set[$i]->prodcat_out_checked) > 0 ) ){   
        //$vtprd_cart->cart_items[$k]->prod_cat_list = wp_get_object_terms( $vtprd_cart->cart_items[$k]->product_id, $vtprd_info['parent_plugin_taxonomy'] );
        if ( array_intersect($vtprd_rules_set[$i]->prodcat_out_checked, $vtprd_cart->cart_items[$k]->prod_cat_list ) ) {   //if any in array1 are in array 2
            
            //LOAD RULE PRODCAT values, if found
            foreach ($vtprd_cart->cart_items[$k]->prod_cat_list as $term_id) {
               if ($vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value > 0) {  //if the individ cat has a min value, add to running total
                  if ($vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value_selector == 'quantity' ) {
                    $vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->quantity;
                  } else {
                   $vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->total_price;
                  }
                  if ($vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value_total == $vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value) {
                    $vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->value_reached = 'yes';
                  }
                  $vtprd_rules_set[$i]->prodcat_out_checked_info[$term_id]->cart_product_info [] =  array (
                       'prod_id' => $vtprd_cart->cart_items[$k]->product_id ,
                       'prod_id_cart_occurrence' => $k ) ;    
               } //end if                                  
            } //end foreach
            
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_prod_cat_found'] =  'yes';
            return true;                                                  
        }  else {
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_prod_cat_found'] =  'no';
        }
      }
      
      //Test RULECAT 
      if ( ( sizeof($vtprd_cart->cart_items[$k]->rule_cat_list) > 0 ) && ( sizeof($vtprd_rules_set[$i]->rulecat_out_checked) > 0 ) ) {
       // $vtprd_cart->cart_items[$k]->rule_cat_list = wp_get_object_terms( $vtprd_cart->cart_items[$k]->product_id, $vtprd_info['rulecat_taxonomy'] );
        if ( array_intersect($vtprd_rules_set[$i]->rulecat_out_checked, $vtprd_cart->cart_items[$k]->rule_cat_list ) ) {   //if any in array1 are in array 2
            
            //LOAD RULE RULECAT values, if found
            foreach ($vtprd_cart->cart_items[$k]->rule_cat_list as $term_id) {
               if ($vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value > 0) {  //if the individ cat has a min value, add to running total
                  if ($vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value_selector == 'quantity' ) {
                    $vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->quantity;
                  } else {
                   $vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value_total += $vtprd_cart->cart_items[$k]->total_price;
                  }
                  if ($vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value_total == $vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value) {
                    $vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->value_reached = 'yes';
                  }
                  $vtprd_rules_set[$i]->rulecat_out_checked_info[$term_id]->cart_product_info [] =  array (
                       'prod_id' => $vtprd_cart->cart_items[$k]->product_id ,
                       'prod_id_cart_occurrence' => $k ) ;    
               } //end if                                  
            } //end foreach
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_rule_cat_found'] =  'yes';
            return true;
        } else {
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_rule_cat_found'] =  'no';
        }
      }
      return false;
    }    

      
    public function vtprd_list_out_product_names($i) {
      
     //error_log( print_r(  'vtprd_list_out_product_names ', true ) ); 
       
      $prodnames;
    	global $vtprd_rules_set;     
    	for($p=0; $p < sizeof($vtprd_rules_set[$i]->errProds_names); $p++) {
          $prodnames .= __(' "', 'vtprd');
          $prodnames .= $vtprd_rules_set[$i]->errProds_names[$p];
          $prodnames .= __('"  ', 'vtprd');
      } 
    	return $prodnames;
    }
      
   public function vtprd_load_inPop_found_list($i,$k) {
    	global $vtprd_cart, $vtprd_rules_set, $vtprd_info;
      
     //error_log( print_r(  'vtprd_load_inPop_found_list ', true ) ); 
 
      //******************************************
      //****  CHECK for PRODUCT EXCLUSIONS 
      //******************************************   
      //  prod_rule_include_only_list EXCLUDES every rule NOT on the list
      if (sizeof($vtprd_cart->cart_items[$k]->prod_rule_include_only_list) > 0) {  
        if ( in_array($vtprd_rules_set[$i]->post_id, $vtprd_cart->cart_items[$k]->prod_rule_include_only_list) ) {
          //v1.0.5.4
          $do_nothing;
          //continue;
        } else {
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  __('rule NOT on inPop prod_rule_include_only_list for product ', 'vtprd'); 
          return;
        } 
      }    
      //  prod_rule_exclusion_list EXCLUDES every rule On the list or 'all'
      if (sizeof($vtprd_cart->cart_items[$k]->prod_rule_exclusion_list) > 0) {     
        if ( ($vtprd_cart->cart_items[$k]->prod_rule_exclusion_list[0] == 'all') ||
             (in_array($vtprd_rules_set[$i]->post_id, $vtprd_cart->cart_items[$k]->prod_rule_exclusion_list)) ) {               
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  __('rule on inPop prod_rule_exclusion_list for product ', 'vtprd');         
          return;
        } 
      }   
      //END product exclusions check
       

      //***********************
      //v1.1.0.6 begin
      //v1.1.1.2 reworked for multiples
      //***********************
      //reduce qty of candidate/free items to just what was purchased, if free items in cart
      
      $current_auto_add_array = $this->vtprd_get_current_auto_add_array();

      $computed_qty          =  $vtprd_cart->cart_items[$k]->quantity; //initialize the value for fallthrough
      $computed_total_price  =  $vtprd_cart->cart_items[$k]->total_price; //initialize the value for fallthrough

      //v1.1.1.2 begin - reworked for multiple free
      $free_product_id = $vtprd_cart->cart_items[$k]->product_id; //v1.1.1.2 

/*
 error_log( print_r(  'vtprd_load_inPop_found_list, just above the auto add logic', true ) ); 
 error_log( print_r(  'rule_contains_auto_add_free_product= ' .$vtprd_rules_set[$i]->rule_contains_auto_add_free_product , true ) );
 error_log( print_r(  'product_auto_insert_rule_id= ' .$vtprd_cart->cart_items[$k]->product_auto_insert_rule_id , true ) );
 error_log( print_r(  'post_id= ' .$vtprd_rules_set[$i]->post_id , true ) );
 error_log( print_r(  '$current_auto_add_array', true ) );
 error_log( var_export($current_auto_add_array, true ) ); 
*/
      
      if ( ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') &&
           ($vtprd_cart->cart_items[$k]->product_auto_insert_rule_id  ==  $vtprd_rules_set[$i]->post_id) &&
           (isset($current_auto_add_array[$free_product_id])) &&
           ($vtprd_cart->cart_items[$k]->quantity > $current_auto_add_array[$free_product_id]['purchased_qty']) ) {        
          if ($current_auto_add_array[$free_product_id]['purchased_qty'] == 0) {  //item is purely free, none purchased ==> not in inpop atall!
            //rolling out would leave nothing left, so exit!
            return;  
          } else {
      //error_log( print_r(  'vtprd_load_inPop_found_list, $computed_qty altered!! ', true ) );          
            //free stuff must be ONLY available in actionPop!!!
            $computed_qty         =  $current_auto_add_array[$free_product_id]['purchased_qty'];
            $computed_total_price =  $vtprd_cart->cart_items[$k]->unit_price * $computed_qty;
          }   
      } 

     //error_log( print_r(  'vtprd_load_inPop_found_list, $computed_qty= ' .$computed_qty , true ) ); 
     //error_log( print_r(  'vtprd_load_inPop_found_list, $computed_total_price= ' .$computed_total_price , true ) );

      //v1.1.1.2 end
      
      //v1.1.0.6 end
      //***********************
       
       
     // $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['at_least_one_inPop_product_found_in_rule']  = 'yes';

      $vtprd_rules_set[$i]->inPop_found_list[] = array('prod_id' => $vtprd_cart->cart_items[$k]->product_id,
                                                       'prod_name' => $vtprd_cart->cart_items[$k]->product_name,
                                                     //'prod_qty' => $vtprd_cart->cart_items[$k]->quantity,  //v1.1.0.6
                                                     //'prod_running_qty' => $vtprd_cart->cart_items[$k]->quantity,  //v1.1.0.6 
                                                       'prod_qty' => $computed_qty, //v1.1.0.6                                                                                                         
                                                       'prod_running_qty' => $computed_qty, //v1.1.0.6
                                                       'prod_unit_price' => $vtprd_cart->cart_items[$k]->unit_price,
                                                       'prod_db_unit_price' => $vtprd_cart->cart_items[$k]->db_unit_price, 
                                                       'prod_total_price' => $computed_total_price,  //v1.1.1.2
                                                       'prod_running_total_price' => $computed_total_price,  //v1.1.1.2
                                                       'prod_cat_list' => $vtprd_cart->cart_items[$k]->prod_cat_list,
                                                       'rule_cat_list' => $vtprd_cart->cart_items[$k]->rule_cat_list,
                                                       'prod_id_cart_occurrence' => $k, //used to mark product in cart if failed a rule 
                                                       'product_variation_key' =>  $vtprd_cart->cart_items[$k]->product_variation_key //v1.0.8.6                                   
                                                      );
     $vtprd_rules_set[$i]->inPop_qty_total   += $computed_qty; //v1.1.0.6 
     $vtprd_rules_set[$i]->inPop_total_price += ($computed_qty *  $vtprd_cart->cart_items[$k]->unit_price); //v1.1.0.6
     $vtprd_rules_set[$i]->inPop_running_qty_total   += $computed_qty; //v1.1.0.6 
     $vtprd_rules_set[$i]->inPop_running_total_price += ($computed_qty *  $vtprd_cart->cart_items[$k]->unit_price); //v1.1.0.6

     if ($vtprd_rules_set[$i]->rule_execution_type == 'display') {
        $vtprd_cart->cart_items[$k]->product_in_rule_allowing_display = 'yes';     
     }
     
    //*****************************************************************************
    //EXPLODE out the cart into individual unit quantity lines for DISCOUNT processing
    //*****************************************************************************
    //for($e=0; $e < $vtprd_cart->cart_items[$k]->quantity; $e++) { //v1.1.0.6 
    for($e=0; $e < $computed_qty; $e++) { //v1.1.0.6             
      $vtprd_rules_set[$i]->inPop_exploded_found_list[] = array(
                                                       'prod_id' => $vtprd_cart->cart_items[$k]->product_id,
                                                       'prod_name' => $vtprd_cart->cart_items[$k]->product_name,
                                                       'prod_qty' => 1,
                                                       'prod_unit_price' => $vtprd_cart->cart_items[$k]->unit_price,
                                                       'prod_db_unit_price' => $vtprd_cart->cart_items[$k]->db_unit_price, 
                                                       'prod_db_unit_price_list' => $vtprd_cart->cart_items[$k]->db_unit_price_list,
                                                       'prod_db_unit_price_special' => $vtprd_cart->cart_items[$k]->db_unit_price_special,
                                                       'prod_id_cart_occurrence' => $k, //used to mark product in cart if failed a rule
                                                       'exploded_group_occurrence' => $e,
                                                       'prod_discount_amt'  => 0,
                                                       'prod_discount_applied'  => '',
                                                       'product_variation_key' =>  $vtprd_cart->cart_items[$k]->product_variation_key //v1.0.8.6
                                                      );          
  //    $vtprd_rules_set[$i]->inPop_exploded_group_occurrence++;
      $vtprd_rules_set[$i]->inPop_exploded_group_occurrence = $e;
    } //end explode
    
    $vtprd_rules_set[$i]->inPop_prodIds_array [] = $vtprd_cart->cart_items[$k]->product_id; //used only when searching for sameAsInpop
      
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['inPop_participation_msgs'][] = 'Product participates in buy population';              
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['rule_short_msg'] = $vtprd_rules_set[$i]->discount_product_short_msg;
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['rule_full_msg']  = $vtprd_rules_set[$i]->discount_product_full_msg;    
  }
    
        
   public function vtprd_load_actionPop_found_list($i,$k) {
    	global $vtprd_cart, $vtprd_rules_set, $vtprd_info;
      
     //error_log( print_r(  'vtprd_load_actionPop_found_list ', true ) ); 
 
      //******************************************
      //****  CHECK for PRODUCT EXCLUSIONS 
      //******************************************     
      
      //  prod_rule_include_only_list EXCLUDES every rule NOT on the list
      if (sizeof($vtprd_cart->cart_items[$k]->prod_rule_include_only_list) > 0) {
        if ( !in_array($vtprd_rules_set[$i]->post_id, $vtprd_cart->cart_items[$k]->prod_rule_include_only_list) ) {
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'rejected';
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  __('rule NOT on actionPop prod_rule_include_only_list for product ', 'vtprd');
          return;
        } 
      }
      
      //  prod_rule_exclusion_list EXCLUDES every rule On the list or 'all'
      if (sizeof($vtprd_cart->cart_items[$k]->prod_rule_exclusion_list) > 0) {
        if ( ($vtprd_cart->cart_items[$k]->prod_rule_exclusion_list[0] == 'all') ||
             (in_array($vtprd_rules_set[$i]->post_id, $vtprd_cart->cart_items[$k]->prod_rule_exclusion_list)) ) {
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'rejected';
          $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  __('rule on actionPop prod_rule_exclusion_list for product ', 'vtprd');
          return;
        } 
      } 
      
      
      //***********************
      //v1.1.0.6 begin
      //v1.1.1.2 reworked for multiples
      //***********************
      //reduce qty of candidate/free items to just what was purchased **IF NOT ON --matching-- AUTO ADD RULE
      
      $current_auto_add_array = $this->vtprd_get_current_auto_add_array();
      
      $computed_qty          =  $vtprd_cart->cart_items[$k]->quantity; //initialize the value for fallthrough
      $computed_total_price  =  $vtprd_cart->cart_items[$k]->total_price; //initialize the value for fallthrough      


      $free_product_id = $vtprd_cart->cart_items[$k]->product_id; //v1.1.1.2 

      //if this product has an auto_add AND auto add has been applied
      if ( ($vtprd_info['ruleset_contains_auto_add_free_product'] == 'yes') &&
           (isset($current_auto_add_array[$free_product_id]))  &&
           ($vtprd_cart->cart_items[$k]->quantity > $current_auto_add_array[$free_product_id]['purchased_qty']) ) {
         //are we on the correct auto add rule? 
         if ($current_auto_add_array[$free_product_id]['rule_id'] == $vtprd_rules_set[$i]->post_id) {
            //pass, friend - continue processing with free items added
            $all_good = true;
         }  else {
            if ($current_auto_add_array[$free_product_id]['purchased_qty'] == 0) {  //item is purely free, none purchased!
              //rolling out would leave nothing left, so exit!
              return;  
            } else {            
              //roll out free items added, by replacing qty with original purchased qty!!
              $computed_qty         =  $current_auto_add_array[$free_product_id]['purchased_qty'];
              $computed_total_price =  $vtprd_cart->cart_items[$k]->unit_price *  $computed_qty;
            }
         }   
      }
      //v1.1.1.2 end 
      
      /* Marking no longer used.  Now use purchased_qty + free_qty
      if ( ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') {
        if ( ($current_auto_add_array['candidate_qty'] + $current_auto_add_array['free_qty'])   == $vtprd_cart->cart_items[$k]->quantity) { 
          $mark_auto_insert_after_occurrence = 'all';
        } else {
          $mark_auto_insert_after_occurrence = $current_auto_add_array['purchased_qty'] - 1;
        }
      }
      */      
         
      //v1.1.0.6 end
      //***********************
                               
      //END product exclusions check
      
      $prod_unit_price = $vtprd_cart->cart_items[$k]->unit_price;
      //Skip if item already on sale and switch = no
      if ($vtprd_cart->cart_items[$k]->product_is_on_special == 'yes')  {
          if ( $vtprd_rules_set[$i]->cumulativeSalePricing == 'no') { 
            //product already on sale, can't apply further discount
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_status'] = 'rejected';
            $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['discount_msgs'][] =  'No Discount - product already on sale, can"t apply further discount - discount in addition to sale pricing not allowed';
            return;
          } else {
            //overwrite the sale price with the original unit price when applying IN PLACE OF the sale price
            $prod_unit_price = $vtprd_cart->cart_items[$k]->db_unit_price;
          }         
     }

      $vtprd_cart->at_least_one_rule_actionPop_product_found = 'yes'; //mark rule for further processing
  
      $vtprd_rules_set[$i]->actionPop_found_list[] = array('prod_id' => $vtprd_cart->cart_items[$k]->product_id,
                                                       'prod_name' => $vtprd_cart->cart_items[$k]->product_name,
                                                     //'prod_qty' => $vtprd_cart->cart_items[$k]->quantity,  //v1.1.0.6 
                                                     //'prod_running_qty' => $vtprd_cart->cart_items[$k]->quantity,  //v1.1.0.6  
                                                       'prod_qty' => $computed_qty,  //v1.1.0.6  
                                                       'prod_running_qty' => $computed_qty,  //v1.1.0.6 
                                                       'prod_unit_price' => $prod_unit_price,
                                                       'prod_db_unit_price' => $vtprd_cart->cart_items[$k]->db_unit_price,
                                                       'prod_total_price' => $computed_total_price, //v1.1.1.2
                                                       'prod_running_total_price' => $computed_total_price, //v1.1.1.2
                                                       'prod_cat_list' => $vtprd_cart->cart_items[$k]->prod_cat_list,
                                                       'rule_cat_list' => $vtprd_cart->cart_items[$k]->rule_cat_list,
                                                       'prod_id_cart_occurrence' => $k, //used to access product in later processing
                                                       'product_variation_key' =>  $vtprd_cart->cart_items[$k]->product_variation_key //v1.0.8.6
                                                      );

     $vtprd_rules_set[$i]->actionPop_qty_total   += $computed_qty; //v1.1.0.6 
     $vtprd_rules_set[$i]->actionPop_total_price += ($computed_qty *  $vtprd_cart->cart_items[$k]->unit_price); //v1.1.0.6
     $vtprd_rules_set[$i]->actionPop_running_qty_total   += $computed_qty; //v1.1.0.6 
     $vtprd_rules_set[$i]->actionPop_running_total_price += ($computed_qty *  $vtprd_cart->cart_items[$k]->unit_price); //v1.1.0.6
          
     $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['rule_short_msg'] = $vtprd_rules_set[$i]->discount_product_short_msg;
     $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['rule_full_msg']  = $vtprd_rules_set[$i]->discount_product_full_msg; 
          
     if ($vtprd_rules_set[$i]->rule_execution_type == 'display') {
        $vtprd_cart->cart_items[$k]->product_in_rule_allowing_display = 'yes';     
     }

              
    //*****************************************************************************
    //EXPLODE out the cart into individual unit quantity lines for DISCOUNT processing
    //*****************************************************************************
    //for($e=0; $e < $vtprd_cart->cart_items[$k]->quantity; $e++) {  //v1.1.0.6 
    for($e=0; $e < $computed_qty; $e++) {  //v1.1.0.6 
          
      /*  Marking no longer used.  Now use purchased_qty + free_qty
      //v1.1.0.6  begin
      // mark the latter part of the qty group, if some have been purchased
      if ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product == 'yes') {
        if (($mark_auto_insert_after_occurrence == 'all') ||
             ($e > $mark_auto_insert_after_occurrence))  {
          $product_free_auto_insert_candidate = 'yes';
        } else {
          $product_free_auto_insert_candidate = 'no';
        }
      } else {
        $product_free_auto_insert_candidate = ' ';
      }
      //v1.1.0.6 end
      */
      
      $vtprd_rules_set[$i]->actionPop_exploded_found_list[] = array('prod_id' => $vtprd_cart->cart_items[$k]->product_id,
                                                       'prod_name' => $vtprd_cart->cart_items[$k]->product_name,
                                                       'prod_qty' => 1,
                                                       'prod_unit_price' => $vtprd_cart->cart_items[$k]->unit_price,
                                                       'prod_db_unit_price' => $vtprd_cart->cart_items[$k]->db_unit_price, 
                                                       'prod_db_unit_price_list' => $vtprd_cart->cart_items[$k]->db_unit_price_list,
                                                       'prod_db_unit_price_special' => $vtprd_cart->cart_items[$k]->db_unit_price_special,
                                                       'prod_id_cart_occurrence' => $k, //used to mark product in cart if failed a rule
                                                       'exploded_group_occurrence' => $e,
                                                       'prod_discount_amt'  => 0,
                                                       'prod_discount_applied'  => '',
                                                       'product_variation_key' =>  $vtprd_cart->cart_items[$k]->product_variation_key //v1.0.8.6
                              //no longer used                         'product_free_auto_insert_candidate' =>  $product_free_auto_insert_candidate //v1.1.0.6
                                                      );          
                                                      
   //   $vtprd_rules_set[$i]->actionPop_exploded_group_occurrence++;
      $vtprd_rules_set[$i]->actionPop_exploded_group_occurrence = $e;
    } //end explode
  
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id]['actionPop_participation_msgs'][] = 'Product participates in action population';
  
   }
 
      
  public function vtprd_init_recursive_work_elements($i){ 
    global $vtprd_rules_set;
    $vtprd_rules_set[$i]->errProds_qty = 0 ;
    $vtprd_rules_set[$i]->errProds_total_price = 0 ;
    $vtprd_rules_set[$i]->errProds_ids = array() ;
    $vtprd_rules_set[$i]->errProds_names = array() ;    
  }
  public function vtprd_init_cat_work_elements($i){ 
    global $vtprd_rules_set;
    $vtprd_rules_set[$i]->errProds_cat_names = array() ;             
  }     


   public  function vtprd_sort_rules_set_for_cart() {
    global $vtprd_cart, $vtprd_rules_set;
      
     //error_log( print_r(  'vtprd_sort_rules_set_for_cart ', true ) ); 
 
    //***********************************************************
    //DELETE ALL "DISPLAY" RULES from the array for this iteration, leaving only the 'cart' rules
    //***********************************************************
     if ( sizeof($vtprd_rules_set) > 0) {    
        foreach ($vtprd_rules_set as $key => $rule )  {
           if ($rule->rule_execution_type == 'display') {
              unset( $vtprd_rules_set[$key]);           
           }  
           //v1.0.9.3 begin
           if ( $rule->rule_status != 'publish' ) { 
             unset( $vtprd_rules_set[$key]);
           }
           //v1.0.9.3 end               
        } 
                
        //reknit the array to get rid of any holes
        $vtprd_rules_set = array_values($vtprd_rules_set);  
     }    

     //****
     //SORT  if any rules are left...
     //****
     if ( sizeof($vtprd_rules_set) > 1) {
        $this->vtprd_sort_rules_set(); 
     } 
     
     return;
  }


   public  function vtprd_sort_rules_set_for_display() {
     global $vtprd_cart, $vtprd_rules_set;

      //***********************************************************
      //DELETE ALL "CART" RULES from the array for this iteration, leaving only the 'display' rules
      //***********************************************************     
     if ( sizeof($vtprd_rules_set) > 0) {         
        foreach ($vtprd_rules_set as $key => $rule )  {
           if ($rule->rule_execution_type == 'cart') {
              unset( $vtprd_rules_set[$key]);           
           }      
        } 
                
        //reknit the array to get rid of any holes
        $vtprd_rules_set = array_values($vtprd_rules_set);  
     }
     
     //****
     //SORT   if any rules are left...
     //****
     if ( sizeof($vtprd_rules_set) > 1) {
        $this->vtprd_sort_rules_set(); 
     }
    
    return;
  }

   public  function vtprd_sort_rules_set() {
     global $vtprd_cart, $vtprd_rules_set;

      //http://stackoverflow.com/questions/3232965/sort-multidimensional-array-by-multiple-keys
      // excellent example here:   http://cybernet-computing.com/news/blog/php-sort-array-multiple-fields
      $rule_execution_type = array();
      $rule_contains_free_product = array();
      $ruleApplicationPriority_num = array();
      
      $sizeof_rules_set = sizeof($vtprd_rules_set);
      for($i=0; $i < $sizeof_rules_set; $i++) { 
      //  $rule_execution_type[]          =  $vtprd_rules_set[$i]->rule_execution_type;
        $rule_contains_free_product[]   =  $vtprd_rules_set[$i]->rule_contains_free_product;
        $ruleApplicationPriority_num[]  =  $vtprd_rules_set[$i]->ruleApplicationPriority_num;
      }
      array_multisort(
      //  $rule_execution_type, SORT_DESC, //display / cart  
               
        $rule_contains_free_product, SORT_DESC,   // yes / no / [blank]]
        $ruleApplicationPriority_num, SORT_ASC,     // 0 => on up
        
			  $vtprd_rules_set  //applies all the sort parameters to the object in question
      ); 
    
    return;
  }
  
  
   public  function vtprd_init_cartAuditTrail($i,$k) {
    global $vtprd_cart, $vtprd_rules_set;  
    $vtprd_cart->cart_items[$k]->cartAuditTrail[$vtprd_rules_set[$i]->post_id] = array(  
          'ruleset_occurrence'          => $i,
          'inPop'                       => $vtprd_rules_set[$i]->inPop, 
          'inPop_prod_cat_found'        => '' ,   
          'inPop_rule_cat_found'        => '' ,
          'inPop_and_required'          => '' ,  
          'userRole'            				=> '' ,
          'inPop_role_found'            => '' ,  
          'inPop_single_found'          => '' , 
          'inPop_variation_found'       => '' ,
          'at_least_one_inPop_product_found_in_rule' => '' ,          
          'product_in_inPop'            => '' ,  
          
          'actionPop'                   => $vtprd_rules_set[$i]->actionPop,   
          'actionPop_prod_cat_found'    => '' ,  
          'actionPop_rule_cat_found'    => '' ,
          'actionPop_and_required'      => '' ,  
          'actionPop_role_found'        => '' , 
          'actionPop_single_found'      => '' ,  
          'actionPop_variation_found'   => '' ,
          'product_in_actionPop'        => '' ,
                      
          'rule_priority'               => '',    // y/n
          
          'discount_status'             => '',
          'discount_msgs'               => array(),
          'discount_amt'                => '',
          'discount_pct'                => '',
          
          // if 'product_in_actionPop' == yes, messages are filled in
          'rule_short_msg'              => '' ,
          'rule_full_msg'               => ''       
    ); 
 
    return;   
  }
                                       

 
  //***********************************************************
/*
  // v1.1.0.6  REFACTORED 
NO LONGER NECESSARY - ACTIONPOP LOAD IGNORES NON-PURCHASED STUFF IF ***NOT*** IN TEH AUTO-ADD RULE!!!!!!
  // If a product was auto inserted for a free discount, but does *not*
  //     receive that discount,
  //   Roll the auto-added product 'UNfree' qty out of the all of the rules actionPop array
  //      AND out of vtprd_cart, removing the product entirely if necessary.
  //***********************************************************    
   public  function vtprd_maybe_roll_out_auto_inserted_products($i) {
		global $vtprd_cart, $vtprd_rules_set, $vtprd_info, $vtprd_setup_options, $vtprd_rule;     

    
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    } 
    
    
     //if no array, nothing done!
     if (isset($_SESSION['current_auto_add_array'])) {
       $current_auto_add_array = unserialize($_SESSION['current_auto_add_array']);
     } else {
       return:
     }
     
     //no rollouts if all candidates became free!
     if ($current_auto_add_array['current_qty'] == ($current_auto_add_array['purchased_qty'] == 0) {
       return;
     }

    //compute qty to be removed, if any: subtract free qty from auto added qty
    $remove_auto_add_qty = $current_auto_add_array['candidate_qty'] - $current_auto_add_array['free_qty'];
    
    //***************************************************************
    //remove the remainder $remove_auto_add_qty from ALL **actionpop lists **
    //***************************************************************
    $sizeof_ruleset = sizeof($vtprd_rules_set);
    for($rule=0; $rule < $sizeof_ruleset; $rule++) {

      $delete_qty = $remove_auto_add_qty;
      foreach ($vtprd_rules_set[$rule]->actionPop_exploded_found_list as $actionPop_key => $actionPop_exploded_found_list )  {
         if ($actionPop_exploded_found_list['prod_id'] == $current_auto_add_array['free_product_id']) {            
            //as each row has a quantity of 1, unset is the way to go....
            //from  http://stackoverflow.com/questions/2304570/how-to-delete-object-from-array-inside-foreach-loop
            unset( $vtprd_rules_set[$rule]->actionPop_exploded_found_list[$actionPop_key]);                       
            $delete_qty -= 1;
         }         
         if ($delete_qty == 0) {
           break;
         }
      } //end "for" loop unsetting the free product
      
      //if any unsets were done, need to re-knit the array so that there are no gaps...
      //    from    http://stackoverflow.com/questions/1748006/what-is-the-best-way-to-delete-array-item-in-php/1748132#1748132
      //            $a = array_values($a);
      if ($delete_qty != $remove_auto_add_qty) {          
        $vtprd_rules_set[$rule]->actionPop_exploded_found_list = array_values($vtprd_rules_set[$rule]->actionPop_exploded_found_list);
      }
    
    } //end "for"  rule loop
    
    //***************************************************************
    //remove the $remove_auto_add_qty from **$vtprd_cart** !! 
    //***************************************************************
    $removed_row_qty = 0;
    foreach($vtprd_cart->cart_items as $key => $cart_item) {      
      if ($cart_item->product_id != $current_auto_add_array['free_product_id']) {
        continue;
      }
            
      $cart_item->quantity -= $remove_auto_add_qty;
      
      if ($cart_item->quantity <= 0) {
        unset($vtprd_cart->cart_items[$key]);
        $removed_row_qty++;
        //**************************************************
      }  else  {
        $cart_item->total_price = $cart_item->quantity * $cart_item->unit_price;      
      }
      
      //once the product_id has been processed, we're all done...
      break;
      
    }  //end foreach
        
    if ($removed_row_qty > 0) {          
      //re-knit the array as needed
      $vtprd_cart->cart_items = array_values($vtprd_cart->cart_items);
    } 

       
    return;
  }  
*/
                                       
  
  //***********************************************************
  // If a product(s) has been given a 'Free' discount, it can't get
  //     any further discounts.
  //   Roll the product 'free' qty out of the rest of the rules actionPop arrays
  //      so that they can't be found when searching for other discounts
  //***********************************************************     
   public  function vtprd_roll_free_products_out_of_other_rules($i) {
		global $vtprd_cart, $vtprd_rules_set, $vtprd_info, $vtprd_setup_options, $vtprd_rule;     

    $sizeof_ruleset = sizeof($vtprd_rules_set);
    
    //for this rule's free_product_array, roll out these products from all other rules...
    foreach($vtprd_rules_set[$i]->free_product_array as $free_product_key => $free_qty) {  
      
      for($rule=0; $rule < $sizeof_ruleset; $rule++) {

        //skip if we're on the rule initiating the free product array logic
        if ( ($vtprd_rules_set[$rule]->post_id == $vtprd_rules_set[$i]->post_id) ||      //1.0.5.1
             ($vtprd_rules_set[$rule]->rule_status != 'publish') ) {                     //1.0.5.1 added in != 'publish' test
          continue; 
        }
        
        //delete as many of the product from the actionpop array as there are free qty
        $delete_qty = $free_qty;
        foreach ($vtprd_rules_set[$rule]->actionPop_exploded_found_list as $actionPop_key => $actionPop_exploded_found_list )  {
           if ($actionPop_exploded_found_list['prod_id'] == $free_product_key) {
              
              //as each row has a quantity of 1, unset is the way to go....
              //from  http://stackoverflow.com/questions/2304570/how-to-delete-object-from-array-inside-foreach-loop
              unset( $vtprd_rules_set[$rule]->actionPop_exploded_found_list[$actionPop_key]);           
              
              $delete_qty -= 1;
           }
           
           if ($delete_qty == 0) {
             break;
           }
           
        } //end "for" loop unsetting the free product
        
        //if any unsets were done, need to re-knit the array so that there are no gaps...
        //    from    http://stackoverflow.com/questions/1748006/what-is-the-best-way-to-delete-array-item-in-php/1748132#1748132
        //            $a = array_values($a);
        if ($delete_qty < $free_qty) {          
          $vtprd_rules_set[$rule]->actionPop_exploded_found_list = array_values($vtprd_rules_set[$rule]->actionPop_exploded_found_list);
        }
      
      } //end "for"  rule loop
      
    } //end foreach free product
    
    return;
  }  



  /*******************************************************  
  //v1.1.0.6 REFACTORED!!!  
  //v1.1.1.2 reworked to handle multiple autoadds  
  ******************************************************** */
	public function vtprd_pre_process_cart_for_autoAdds(){
  
     //error_log( print_r(' ', true ) );
     //error_log( print_r('vtprd_pre_process_cart_for_autoAdds', true ) );

    global $post, $wpdb, $woocommerce, $vtprd_cart, $vtprd_cart_item, $vtprd_setup_options, $vtprd_rules_set, $vtprd_info; //v1.1.1.2 moved here      
  
    //v1.1.0.9 begin
    if ($vtprd_info['ruleset_contains_auto_add_free_product'] != 'yes') {  //v1.1.1.2 
    //if (get_option('vtprd_ruleset_contains_auto_add_free_product') != 'yes') {
      return;
    }
    //v1.1.0.9 end
    
    
    //******************************
    //get auto add for free products session variable
    //******************************
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }      

    if ($vtprd_info['current_processing_request'] != 'cart') {
      return;
    }   
  
    //******************************
    //GET PREVIOUS 'Current' ARRAY
    //  'Previous' array loaded from 'Current' at the end of the last iteration processing, 
    //    in function vtprd_post_process_cart_for_autoAdds
    //  initialize Current array
    //******************************
    $previous_auto_add_array = $this->vtprd_get_previous_auto_add_array(); 

     //error_log( print_r(  'pre-process at top, $previous_auto_add_array=', true ) );
     //error_log( var_export($previous_auto_add_array, true ) );   
     //error_log( print_r(  'Pre-Process $vtprd_cart items before UNSET', true ) );
     //error_log( var_export($vtprd_cart->cart_items, true ) );              
  
 //$woocommerce_cart_contents = $woocommerce->cart->get_cart();
    //error_log( print_r(  'Pre-Process WOO CART before unset for comparison', true ) );
    //error_log( var_export($woocommerce_cart_contents, true ) ); 
    
    //**********************************
    //ROLL OUT PREVIOUS free qty from VTPRD-CART
    //***********************************
    //  what's left is the previous purchased qty
    /*
    Three possible scenarios:
    1. inpop same as actionpop ==>> "buy 2 get 1 free"
    2. inpop includes actionpop ==>> "buy any computer item, get a free mouse"
    3. inpop excludes actionpop ==>> "buy a computer, get a free mouse"
    
    If current contents >= previous contents, just subtract out the previous free stuff.
    
    If current contents < previous contents, we don't know 
    
    
    
    */
    //********************
    //v1.1.1.2 begin - reworked for multiple free 
    // added foreach, changed update to apply to the forearch row
    // put updates at bottom after foreach
    //********************  
    $update_previous = false; //v1.1.1.2
    $unset_row_array = array(); //v1.1.1.2 
    foreach ( $previous_auto_add_array as $free_product_id => $previous_auto_add_array_row ) {
    
      //error_log( print_r(  'pre-process $previous_auto_add_array_row, key= ' .$free_product_id, true ) );
      //error_log( var_export($previous_auto_add_array_row, true ) );    
    
      if ($previous_auto_add_array_row['free_qty'] > 0)  {
          $remove_item = false;
          $free_product_occurrence = false;
          $free_product_found = false;    
          $sizeof_cart_items = sizeof($vtprd_cart->cart_items);
          for($c=0; $c < $sizeof_cart_items; $c++) {  
             if ($vtprd_cart->cart_items[$c]->product_id == $previous_auto_add_array_row['free_product_id']) {
               $free_product_found = true;
               $free_product_occurrence = $c;
               break; //breaks out of this for loop
             }
          } 
  
          if ($free_product_found) {
   
              switch(true) { 
                case ($vtprd_cart->cart_items[$free_product_occurrence]->quantity == $previous_auto_add_array_row['current_qty']):
                     //carry on, nothing changed from prev...
      //error_log( print_r('$free_product_found 001 ', true ) );                   
                  break; 
                case ($vtprd_cart->cart_items[$free_product_occurrence]->quantity > $previous_auto_add_array_row['current_qty']):
                    //if cart qty > previous, user has purchased, so the difference is added to previous purchase 
                     $additonal_purch = $vtprd_cart->cart_items[$free_product_occurrence]->quantity - $previous_auto_add_array_row['current_qty'];  
                     $previous_auto_add_array_row['purchased_qty'] += $additonal_purch;
                     //save updated previous array for post process
                     $update_previous = true;
      //error_log( print_r('$free_product_found 002 ', true ) );   
                  break;
                case ($vtprd_cart->cart_items[$free_product_occurrence]->quantity < $previous_auto_add_array_row['current_qty']):
                     //if cart qty < previous, user has removed purchases, so the difference is subtracted from previous purchase 
                     $subtract_from_purch = $previous_auto_add_array_row['current_qty'] - $vtprd_cart->cart_items[$free_product_occurrence]->quantity;
                    
      //error_log( print_r('$free_product_found 003 ', true ) );                     
                     //subtract from free and purchased equally@@@@@!!!!!!!!!!!!
                     
                     
                     if ($previous_auto_add_array_row['purchased_qty'] <= $subtract_from_purch) {
                       $previous_auto_add_array_row['purchased_qty'] = 0;
      //error_log( print_r('$free_product_found 003a ', true ) );                        
                       //treat all as new adds
                       //$previous_auto_add_array = $this->vtprd_init_previous_auto_add_array();
                     } else {
                       $previous_auto_add_array_row['purchased_qty'] -= $subtract_from_purch;
      //error_log( print_r('$free_product_found 003b ', true ) );                                         
                     } 
                       
                     //only if the FREE rule is ***sameAsInPop***, do we allow the entered quantity in this case to stand as the purchased qty!
                     //  clearing out prev free_qty means that quantity will be ignored below in the subtraction.
                     if ( ($vtprd_cart->cart_items[$free_product_occurrence]->quantity <= $previous_auto_add_array_row['free_qty']) &&
                          ($previous_auto_add_array_row['free_rule_actionPop'] == 'sameAsInPop') ) {
                        $previous_auto_add_array_row['free_qty'] = 0;
      //error_log( print_r('$free_product_found 003c ', true ) );                            
                     }          
                              
                     //save updated previous array for post process
                     $update_previous = true;
                  break;
              } //end switch
              
              //always subtract out the previous free qty
              $vtprd_cart->cart_items[$free_product_occurrence]->quantity -= $previous_auto_add_array_row['free_qty'];
            
     
          //error_log( print_r(  'AFTER SUBTRACTING previous free_qty from current quantity= ' .$vtprd_cart->cart_items[$free_product_occurrence]->quantity, true ) ); 
             
              if ($vtprd_cart->cart_items[$free_product_occurrence]->quantity <= 0) {
                //this product was entirely auto-added due to external trigger rules, or the qty has been reduced below the auto adds from prev..
                //  delete it! and it will be re-added as necessary in the coming processing
                
                //**************************************************
  
       //error_log( print_r(  'Pre-Process cart item unset, key= ' .$free_product_occurrence, true ) );              
                
                 //this UNSETS the CART item
                  unset($vtprd_cart->cart_items[$free_product_occurrence]);
                 $vtprd_cart->cart_items = array_values($vtprd_cart->cart_items);
                 
              //error_log( print_r(  '$vtprd_cart items after UNSET', true ) );
              //error_log( var_export($vtprd_cart->cart_items, true ) );        
              } 
          
          } else {
            //no purchases, and free items (if any) deleted by customer.         
            //$previous_auto_add_array_row['purchased_qty'] = 0;
            //$previous_auto_add_array_row['free_qty'] = 0;

            $unset_row_array[] = $free_product_id;
            $update_previous = true;
          }
       } 
      //*********************************  
      // end ROLL OUT of previous stuff...
      //*********************************      
      
      if ($previous_auto_add_array_row['purchased_qty'] > 0) {
        $purchased_qty = $previous_auto_add_array_row['purchased_qty'];
      //error_log( print_r(  '$purchased_qty 001  from previous = ' .$purchased_qty, true ) );
      }  else  {  
        $purchased_qty = 0;
      //error_log( print_r(  '$purchased_qty 002  set to zero = ' .$purchased_qty, true ) );       
      }
    
    } //end foreach 
    
    
    if ($update_previous) {
        
        switch( true ) {
          case ( sizeof($unset_row_array) == sizeof(previous_auto_add_array) ) :              
              $previous_auto_add_array = array();                  
            break;        
          case ( sizeof($unset_row_array) > 0)  :              
              foreach ( $unset_row_array as $iteration => $unset_key ) { 
                 unset($previous_auto_add_array[$unset_key]);
              }   
              $previous_auto_add_array = array_values($previous_auto_add_array);                 
            break;             
        }
        
        //update array
        $_SESSION['previous_auto_add_array'] = serialize($previous_auto_add_array); 
        $vtprd_info['previous_auto_add_array'] = $previous_auto_add_array; //$vtprd_info['previous_auto_add_array'] used when session variable disappears due to age                                   
    }
               
    //v1.1.1.2 end
    //********************  
    
    
    
      
    /* NO - need the previous array in post_process for potential cleanout!!
    OLD PREVIOUS is kept around for this test:
        case ($previous_auto_add_array['free_qty']  > 0)
            if it gets here, then use free_qty to clear out that amount from woo_cart...
    //then clean out previous array
    $this->vtprd_maybe_remove_previous_auto_add_array();
    */
    
    $current_auto_add_array = array();
   
    $sizeof_rules_set = sizeof($vtprd_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) {                                                               

      // HAVE to do this for all rules, to get a complete picture  ??????????????????????????????????????
      //ONLY execute AUTO ADDs!
      if ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product != 'yes') {
         continue;  //skip out of this for loop iteration
      }
      
      //skip **existing** invalid rules
      if ( $vtprd_rules_set[$i]->rule_status != 'publish' ) { 
        continue;  //skip out of this for loop iteration
      } 
    
      $this->vtprd_manage_shared_rule_tests($i); 


      //skip rules **now** invalid     
      if ( $vtprd_rules_set[$i]->rule_status != 'publish' ) { 
          continue;  //skip out of this for loop iteration
      } 

      //overwrite actionpop var-out and single if same as inpop and autoadd on ...
      //this gets reset later in main processing...
      if ($vtprd_rules_set[$i]->actionPop == 'sameAsInPop')  {                                                  
         $vtprd_rules_set[$i]->actionPop                =   $vtprd_rules_set[$i]->inPop;
         $vtprd_rules_set[$i]->var_out_checked          =   $vtprd_rules_set[$i]->var_in_checked;  
         $vtprd_rules_set[$i]->actionPop_singleProdID   =   $vtprd_rules_set[$i]->inPop_singleProdID;    
      }
 
      //set up Free product prod_id
      switch( $vtprd_rules_set[$i]->actionPop ) { 
        case 'vargroup':     //only applies to 1st rule deal line
             $free_product_id = $vtprd_rules_set[$i]->var_out_checked[0]; //there can be only one entry in the array  
          break; 
        case 'single':
             $free_product_id = $vtprd_rules_set[$i]->actionPop_singleProdID;    
          break;
        default:
             return;
          break;
      }

      //v1.1.1 begin    
      $_product    =  get_product( $free_product_id );
      if ( !$_product  ) {
        $vtprd_rules_set[$i]->rule_status = 'freeProductNotFound'; 
        continue;  //skip out of this for loop iteration
      } 
      //v1.1.1 end
      

      $d = 0;                              
      if ($vtprd_rules_set[$i]->rule_deal_info[$d]['discount_auto_add_free_product'] == 'yes') {

        switch( true ) { 
          
          case ( ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'none' ) ||  
                 ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'one' ) ) :
               $action_amt_count = 1;
             break;
          
          case ( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'quantity' ):   
               $action_amt_count = $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'];
             break;          
          
          case ( $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_type'] == 'currency' ):   
               //$_product    =  get_product( $free_product_id );  //v1.1.1 moved above
               $unit_price  =  get_option( 'woocommerce_tax_display_cart' ) == 'excl' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price();                                   
               
               $action_amt_count = round ($vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'] / $unit_price);
               
               $action_amt_count_not_rounded = $vtprd_rules_set[$i]->rule_deal_info[$d]['action_amt_count'] / $unit_price;
               If ($action_amt_count_not_rounded > $action_amt_count) {  //there is a remainder, add another unit to cover it...
                  $action_amt_count++;
               }
               
             break;                                            
        }

        $free_product_status = false;
        
        $sizeof_cart_items = sizeof($vtprd_cart->cart_items);
        for($c=0; $c < $sizeof_cart_items; $c++) {  
           if ($vtprd_cart->cart_items[$c]->product_id == $free_product_id) {
     //error_log( print_r(  'free productg id FOUND' , true ) );           
             $free_product_status = 'found';
             break; //breaks out of this for loop
           }
        }  
       
     //error_log( print_r( 'add/upd free item ', true ) );  

        if ($free_product_status == 'found') {
     //error_log( print_r(  'free product id FOUND processing, purchased qty 003 = ' .$vtprd_cart->cart_items[$c]->quantity , true ) ); 
          //updates to both inpop and inpop exploded...
          $purchased_qty = $vtprd_cart->cart_items[$c]->quantity;
          //ADD to $vtprd_cart
          $vtprd_cart->cart_items[$c]->quantity += $action_amt_count; 
          $vtprd_cart->cart_items[$c]->total_price  =  $vtprd_cart->cart_items[$c]->quantity  * $vtprd_cart->cart_items[$c]->unit_price;
          $vtprd_cart->cart_items[$c]->product_auto_insert_state = 'candidate';

          //v1.1.1.2 begin
          //MARK the inserted item to be used ONLY for this rule 
          //      (update edit only allows unique product across all free rules in ruleset)
          $vtprd_cart->cart_items[$c]->product_auto_insert_rule_id = $vtprd_rules_set[$i]->post_id;
          //v1.1.1.2 end  
                  
        } else { 
     //error_log( print_r(  'run add_to_vtprd_cart, qty= ' .$action_amt_count , true ) );         
          $price_add_to_total = $this->vtprd_auto_add_to_vtprd_cart($free_product_id, $action_amt_count, $i);
          $purchased_qty = 0; //v1.1.1.2   
        }
       
        
        //**************************
        //v1.1.1.2 begin - reworked for multiple free 
        // moved all of this into the 'for' loop, as it now occurs multiple times
        //**************************
        $current_auto_add_array_row = $this->vtprd_init_auto_add_array_row();      
      //error_log( print_r(  '$purchased_qty 004 = ' .$purchased_qty, true ) );
        $current_auto_add_array_row['free_product_id']              =  $free_product_id;
        $current_auto_add_array_row['free_product_add_action_cnt']  =  $action_amt_count;
        $current_auto_add_array_row['rule_id']                      =  $vtprd_rules_set[$i]->post_id;
        $current_auto_add_array_row['current_qty']                  =  $vtprd_cart->cart_items[$c]->quantity;
        $current_auto_add_array_row['purchased_qty']                =  $purchased_qty;
        $current_auto_add_array_row['candidate_qty']                =  $action_amt_count;
        $current_auto_add_array_row['free_qty']                     =  0;
        $current_auto_add_array_row['variations_parameter']         =  $vtprd_rules_set[$i]->var_out_product_variations_parameter;
        
        if ( ($vtprd_rules_set[$i]->actionPop == 'sameAsInPop')  ||                                                  
            (($vtprd_rules_set[$i]->actionPop                ==   $vtprd_rules_set[$i]->inPop) &&
             ($vtprd_rules_set[$i]->var_out_checked          ==   $vtprd_rules_set[$i]->var_in_checked) &&  
             ($vtprd_rules_set[$i]->actionPop_singleProdID   ==   $vtprd_rules_set[$i]->inPop_singleProdID)) ) {  
           $current_auto_add_array_row['free_rule_actionPop'] = 'sameAsInPop';  
        }
        
        $current_auto_add_array[$free_product_id] = $current_auto_add_array_row;
                   
      }

      //v1.1.1.2 removed this line - need whole loop to run, to allow multiples
      //break; //break out of 'for' loop - once single rule with auto-add processed, no further processing necessary
      
    }   
     
    //v1.1.1.2 end

    //populate the SESSION, SERIALIZE AND STORE!!


    //  MAKE SURE that the autoadd product is LAST - 
    //    that way we can add to the exploded array easily later on
    $this->vtprd_sort_vtprd_cart_autoAdd_last();
    
    $_SESSION['current_auto_add_array'] = serialize($current_auto_add_array);

     //error_log( print_r('pre-process at BOTTOM, $previous_auto_add_array=', true ) );
     //error_log( var_export($previous_auto_add_array, true ) );
     //error_log( print_r('pre-process at BOTTOM, $current_auto_add_array=', true ) );
     //error_log( var_export($current_auto_add_array, true ) );
 
    return;
    
  } //end  vtprd_pre_process_cart_for_autoAdds


  
  //++++++++++++++++++++++++++++++
  //v1.1.0.6 Commented out
  //++++++++++++++++++++++++++++++
  /*  ***************************************************************************************
   AUTO add - test here if inpop criteria reached for auto add rule
    InPop has JUST been loaded, and THIS rule is an auto-add rule,
            (a) is the product already in cart somewhere - 
                if not ADD it right now...
            (b) If SO, will its quantity suffice or should it be increased
        (if the auto add switch is on, free products are always auto-added, 
                regardless of whether that product is already in the cart...)
       ***************************************************************************************                    
  */
  //$i = rule index, $d = deal index, $k = product index
/*
  public function vtprd_maybe_auto_add_to_vtprd_cart($i, $d, $free_product_id) {
  
error_log( print_r(  'Entry vtprd_maybe_auto_add_to_vtprd_cart', true ) );
error_log( print_r(  '$i = ' .$i, true ) );
error_log( print_r(  '$d = ' .$d, true ) );


    $purchased_qty = 0;
    $sizeof_cart_items = sizeof($vtprd_cart->cart_items);
    for($c=0; $c < $sizeof_cart_items; $c++) {  
       if ($vtprd_cart->cart_items[$c]->product_id == $free_product_id) {
         $free_product_status = 'found';
         break; //breaks out of this for loop
       }
    }  
   
    //UPD existing inpop qty  or  ADD to Cart and ActionPop
error_log( print_r(  'FROM apply-rules.php  function vtprd_maybe_auto_add_to_vtprd_cart', true ) );
error_log( print_r(  '$free_product_id= ' .$free_product_id, true ) );
error_log( print_r(  '$free_product_status=  ' .$free_product_status, true ) );


    //free_product_qty is ALWAYS 1!!!!
    if ($free_product_status == 'found') {
      //updates to both inpop and inpop exploded...
      $purchased_qty = $vtprd_cart->cart_items[$c]->quantity;
      //ADD to $vtprd_cart
      $vtprd_cart->cart_items[$c]->quantity++; 
      $vtprd_cart->cart_items[$c]->total_price  +=  $vtprd_cart->cart_items[$c]->unit_price
      $vtprd_cart->cart_items[$c]->product_auto_insert_state = 'candidate';
      
error_log( print_r(  'vtprd_maybe_auto_add_to_vtprd_cart  001', true ) );
    } else { 
      $price_add_to_total = $this->vtprd_auto_add_to_vtprd_cart($free_product_id, 1, $i);    
error_log( print_r(  'vtprd_maybe_auto_add_to_vtprd_cart  002', true ) );
    }


  
    return $purchased_qty;
     
  }
*/

  //******************************
  //insert/delete free stuff as warranted...
  //v1.1.0.6  refactored
  //v1.1.1.2 refactored for multiple auto adds 
  //******************************	
	public function vtprd_post_process_cart_for_autoAdds(){ 

      global $post, $wpdb, $woocommerce, $vtprd_cart, $vtprd_rules_set, $vtprd_cart_item, $vtprd_info; //v1.1.1.2 moved here
       
     //error_log( print_r(' ', true ) );
     //error_log( print_r(  'TOP OF vtprd_post_process_cart_for_autoAdds ', true ) );      
      
  /* v1.1.1.2
  New approach:
    
    loop woocommerce cart
      hit current_auto_add_array by product key
      upd to reflect auto adds
      count number of updated products
      
    if number of updated products = count of free products occurrences in array
      all done
    else
      Loop the current_auto_add_array by product
        access the woocommerce cart by key
          if there, no add
          if *not* there, add new product to cart
    
    housekeeping...
  
  */
      //v1.1.0.9 begin
      if ($vtprd_info['ruleset_contains_auto_add_free_product'] != 'yes') {  //v1.1.1.2 
      //if (get_option('vtprd_ruleset_contains_auto_add_free_product') != 'yes') {
        return;
      }
      //v1.1.0.9 end
  

      //******************************
      //get auto add session variables
      if(!isset($_SESSION)){
        session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
      }  



      //v1.1.1.2 moved here
      $previous_auto_add_array = $this->vtprd_get_previous_auto_add_array();          
      $current_auto_add_array  = $this->vtprd_get_current_auto_add_array();

      //v1.1.1.2 new edit...
      if ( (sizeof($previous_auto_add_array) == 0) &&
           (sizeof($current_auto_add_array)  == 0) ) {
         return; 
      }        

      //******************************
      //prevents recursive processing during auto add execution of add_to_cart!
      //v1.1.0.6 placed at top of routine
      //******************************
      //  otherwise there would be an endless loop via both add_to_cart and set_quantity  ...
      $_SESSION['auto_add_in_progress'] = 'yes';
      //add_in_progress switch will be overriden in 10 seconds using the timestamp (also shut off at bottom of this routine)
      $_SESSION['auto_add_in_progress_timestamp'] = time();
      //******************************


      //only roll out previous stuff, if NO current stuff to add
      if ( (sizeof($previous_auto_add_array) > 0) &&
           (sizeof($current_auto_add_array)  == 0) ) {
         $this->vtprd_maybe_roll_out_prev_auto_insert_from_woo_cart($previous_auto_add_array, 'all');
         $this->vtprd_maybe_remove_previous_auto_add_array();
         $this->vtprd_turn_off_auto_add_in_progress();          
         return; 
      }


      
     //error_log( print_r(  '$current_auto_add_array=', true ) );
     //error_log( var_export($current_auto_add_array, true ) );
     //error_log( print_r(  '$previous_auto_add_array=', true ) );
     //error_log( var_export($previous_auto_add_array, true ) );
     //error_log( print_r(  '$vtprd_rules_set', true ) );
     //error_log( var_export($vtprd_rules_set, true ) );
     //error_log( print_r(  '$vtprd_cart', true ) );
     //error_log( var_export($vtprd_cart, true ) ); 
         

      global $post, $wpdb, $woocommerce, $vtprd_cart, $vtprd_rules_set, $vtprd_cart_item, $vtprd_info;
      
      //$woocommerce_free_product_processed = false; //v1.1.1.2 moved
      $woocommerce_cart_updated = false;
     
      //********************************
      // if the item is ALREADY IN THE CART
      // Process updates to the Cart
      //********************************
      
      $free_product_already_in_cart_cnt = 0;
      
      $current_auto_add_array_items_processed = array(); //v1.1.1.2 track all the upds as they happen...
      
      $woocommerce_cart_contents = $woocommerce->cart->get_cart(); 
      foreach($woocommerce_cart_contents as $key => $cart_item) {

        $woocommerce_free_product_processed = false; //v1.1.1.2 moved here
        
        if ($cart_item['variation_id'] > ' ') {      
            $cart_product_id    = $cart_item['variation_id'];
        } else { 
            $cart_product_id    = $cart_item['product_id']; 
        }    
             
     //error_log( print_r(  'update_parent_cart_for_autoAdds  003  IN $woocommerce_cart_contents, PROD ID= '  .$cart_product_id , true ) );
        
        //if this ID not getting a free item, skip
        if (isset($current_auto_add_array[$cart_product_id] )) {
          
          $free_product_already_in_cart_cnt ++;
          
          //GET THE ROW
          $current_auto_add_array_row = $current_auto_add_array[$cart_product_id];
          
          //adjust current_qty to be what is needed!
          $current_auto_add_array_row['current_qty'] = ($current_auto_add_array_row['purchased_qty'] + $current_auto_add_array_row['free_qty']);          
          //v1.1.1.2 
          /*  moved above $current_total_quantity replaced with $current_auto_add_array_row['current_qty']
          $current_total_quantity =  $current_auto_add_array['purchased_qty'] +
                                     $current_auto_add_array['free_qty'];        
          */
          if ($cart_item['quantity'] != $current_auto_add_array_row['current_qty']) { 
                   
      //error_log( print_r(  'update_parent_cart_for_autoAdds  004', true ) );
  
             if ($current_auto_add_array_row['current_qty'] <= 0) { //(nothing purchased, no adds)
             
      //error_log( print_r(  'update_parent_cart_for_autoAdds  004a', true ) );  
  // COMMENT OUT SET QTY
                $woocommerce->cart->set_quantity($key,0,false); //set_quantity = 0 ==> delete the product
  
             } else {
      //error_log( print_r(  'update_parent_cart_for_autoAdds  005', true ) );
  
             // COMMENT OUT SET QTY
                //$woocommerce->cart->set_quantity($key,$current_total_quantity,false); //false = don't refresh totals
                $woocommerce->cart->set_quantity($key,$current_auto_add_array_row['current_qty'],false); //false = don't refresh totals
  
             }
             
             $woocommerce_cart_updated = true;
             
          }
      //error_log( print_r(  '$woocommerce_free_product_processed = true', true ) );         
          $woocommerce_free_product_processed = true; 
          
          //update the current array row in the parent array
          $current_auto_add_array[$cart_product_id] = $current_auto_add_array_row;
          
          $current_auto_add_array_items_processed[] = $cart_product_id; //mark key as processed
          
       } else {
          //if no current free stuff, but there is previous free stuff, roll it out for *this key*
          if ( (isset($previous_auto_add_array[$cart_product_id] )) &&
               ($previous_auto_add_array[$cart_product_id]['free_qty']  > 0) ) {
            $this->vtprd_maybe_roll_out_prev_auto_insert_from_woo_cart($previous_auto_add_array, 'single', $cart_product_id );
            $woocommerce_cart_updated = true;
          }
       }
       
        //********************************
        //Process Auto Add of new Products - If any current free products were not processed above, then they are added here.
        //********************************
        //  for WOO, see http://docs.woothemes.com/document/automatically-add-product-to-cart-on-visit/
        if ( (!$woocommerce_free_product_processed) &&  //so free item wasn't already in the cart
             (isset($current_auto_add_array[$cart_product_id])) ) {  
  
      //error_log( print_r(  '$vtprd_rules_set', true ) );
      //error_log( var_export($vtprd_rules_set, true ) ); 
   
           /* -------------------------------------- 
            add to cart logic from woocommerce-functions.php  function woocommerce_add_to_cart_action
           -------------------------------------- */
                 
            //$current_auto_add_array_row GOTTEN AB
            $qty = $current_auto_add_array_row['free_qty'];
            
            //add_to_cart( $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = array() ) {
            //add product to cart 
            
      //error_log( print_r(  'update_parent_cart_for_autoAdds  006 $qty = ' .$qty, true ) );
            
            if ( ( is_array($current_auto_add_array_row['variations_parameter']) ) &&         //v1.0.5.6
                 (   sizeof($current_auto_add_array_row['variations_parameter']) > 0 ) ) {      //v1.0.5.6
      //error_log( print_r(  'update_parent_cart_for_autoAdds  007', true ) );
  //wp_die( __('<strong>Looks like</strong>', 'vtmin'), __('VT', 'vtmin'), array('back_link' => true));  
               /*
                   $current_free_product_row['variations_parameter'] = $vtprd_rule->var_out_product_variations_parameter = array(
                     'parent_product_id'    => $product_id,
                     'variation_product_id' => $variation_id,
                     'variations_array'     => $variations
                    );
                    
                 Sample add_to_cart param array for variation product:               
                    $product_id = 738
                    $quantity = 1
                    $variation_id = 754
                    $variations= 
                      Array
                      (
                          [pa_colors2] => purple
                          [pa_size2] => xxlg
                      )    
               */
               $variation_id        =  $current_auto_add_array_row['free_product_id'];
               $parent_product_id   =  $current_auto_add_array_row['variations_parameter']['parent_product_id'];
               $variations_array    =  $current_auto_add_array_row['variations_parameter']['variations_array'];
   
                //COMMENT ADD TO CART             
               $woocommerce->cart->add_to_cart($parent_product_id, $qty, $variation_id, $variations_array );
  
      //error_log( print_r(  'update_parent_cart_for_autoAdds  008', true ) );
            }  else {
      //error_log( print_r(  'update_parent_cart_for_autoAdds  009', true ) );
  
                //COMMENT ADD TO CART
               $woocommerce->cart->add_to_cart($current_auto_add_array_row['free_product_id'], $qty);
  
  
  
            }                                                                       
            
             //$current_free_product_row['variations_parameter']
      //error_log( print_r(  'update_parent_cart_for_autoAdds  0010', true ) );
            $woocommerce_cart_updated = true;
             
            $current_auto_add_array_items_processed[] = $cart_product_id; //mark key as processed       
          }   
          
     
       
       
      } //end foreach         

 
        
         
      //v1.1.1.2 cleanup ==>>changed to a foreach
      //********************************
      //Process Auto Add of new Products - If any current free products were not processed above, then they are added here.
      foreach($current_auto_add_array as $key => $current_auto_add_array_row ) {  

          //ONLY process those keys not yet processed!
          if ( in_array($key, $current_auto_add_array_items_processed) ) {
              continue;  //skip if already processed!!
          }

          $qty = $current_auto_add_array_row['free_qty'];

    //error_log( print_r(  'update_parent_cart_for_autoAdds  106 $qty = ' .$qty, true ) );
          
          if ( ( is_array($current_auto_add_array_row['variations_parameter']) ) &&         //v1.0.5.6
               (   sizeof($current_auto_add_array_row['variations_parameter']) > 0 ) ) {      //v1.0.5.6
 
    //error_log( print_r(  'update_parent_cart_for_autoAdds  107', true ) );


             $variation_id        =  $current_auto_add_array_row['free_product_id'];
             $parent_product_id   =  $current_auto_add_array_row['variations_parameter']['parent_product_id'];
             $variations_array    =  $current_auto_add_array_row['variations_parameter']['variations_array'];
 
              //COMMENT ADD TO CART             
             $woocommerce->cart->add_to_cart($parent_product_id, $qty, $variation_id, $variations_array );

    //error_log( print_r(  'update_parent_cart_for_autoAdds  108', true ) );
          
          }  else {
          
    //error_log( print_r(  'update_parent_cart_for_autoAdds  109', true ) );

              //COMMENT ADD TO CART
             $woocommerce->cart->add_to_cart($current_auto_add_array_row['free_product_id'], $qty);

          }                                                                       
          
           //$current_free_product_row['variations_parameter']
    //error_log( print_r(  'update_parent_cart_for_autoAdds  110', true ) );
          $woocommerce_cart_updated = true;        
        } //end add new free item foreach   
 
 
 
 
 
     //error_log( print_r(  'update_parent_cart_for_autoAdds  0011', true ) );             

      
      //after updates are completed, calculate the subtotal + cleanup
      if ($woocommerce_cart_updated) {
     //error_log( print_r(  'update_parent_cart_for_autoAdds  0013', true ) );  
      
        //v1.0.9.3 - mark call as internal only - 
        //	accessed in parent-cart-validation/ function vtprd_maybe_before_calculate_totals
        $_SESSION['internal_call_for_calculate_totals'] = true;   
          
        $woocommerce->cart->calculate_totals(); 
        
     //error_log( print_r(  'update_parent_cart_for_autoAdds  0014', true ) );  
                
      }   

     //error_log( print_r(  'update_parent_cart_for_autoAdds  0015', true ) );



     //v1.1.1.2 roll out unused free candidate entries from $current so they don't muck things up on next round
     $unset_cnt = 0;
     foreach ( $current_auto_add_array as $key => $current_auto_add_array_row ) { 
       if ( ($current_auto_add_array_row['purchased_qty'] == 0)  &&
            ($current_auto_add_array_row['free_qty'] == 0) ) {
          unset($current_auto_add_array[$key]);
          $unset_cnt++;     
       } else {
          //reset candidate_qty for next cycle
          $current_auto_add_array_row['candidate_qty'] = 0;
       }
       
     }   
     if ($unset_cnt > 0) {  //re-knit the array
        $current_auto_add_array = array_values($current_auto_add_array);
     }
     //end roll out
       
     
     //MOVE 'CURRENT' TO PREVIOUS, CLEAR OUT 'CURRENT'
     //refresh the 'previous' array with the 'current' array, for the next iteration...
     $_SESSION['previous_auto_add_array'] = serialize($current_auto_add_array);
     $vtprd_info['previous_auto_add_array'] = $current_auto_add_array; //$vtprd_info['previous_auto_add_array'] used when session variable disappears due to age


        
      //***************
      //v1.1.1.1 begin
      //***************
      //IF auto add of *new product* (not of one already in the cart), vtprd_cart HAS NO cart_item_key .
      //  cart_item_key is NOW used as the key for all loops in cart-validation
      //  Find and fill in value
     if ($woocommerce_free_product_processed || 
         $woocommerce_cart_updated) {
            
        $woocommerce_cart_contents = $woocommerce->cart->get_cart();

        $sizeof_cart_items = sizeof($vtprd_cart->cart_items);
        for($k=0; $k < $sizeof_cart_items; $k++) {  
      
          if ($vtprd_cart->cart_items[$k]->cart_item_key == null) { //only if the product was auto-added
           
            foreach($woocommerce_cart_contents as $key => $cart_item) {
  
              if ($cart_item['variation_id'] > ' ') {      
                  $cart_product_id    = $cart_item['variation_id'];
              } else { 
                  $cart_product_id    = $cart_item['product_id']; 
              }   
                          
              if ($vtprd_cart->cart_items[$k]->product_id == $cart_product_id) {
              
                $vtprd_cart->cart_items[$k]->cart_item_key = $key;
              }
              
            } //end inner foreach
          
          }
        
        }  //end outer for
      }
      //v1.1.1.1 end
      //***************
      
      
     //clear out the current variable for use in next iteration, as needed
     
     
     //error_log( print_r(  'update_parent_cart_for_autoAdds  0016', true ) );
 //echo '$vtprd_cart= <pre>'.print_r($vtprd_cart, true).'</pre>' ; 
 //echo '$vtprd_rules_set= <pre>'.print_r($vtprd_rules_set, true).'</pre>' ;
 //echo '$woocommerce= <pre>'.print_r($woocommerce, true).'</pre>' ;  
//wp_die( __('<strong>Looks like</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WP', 'vtmin'), array('back_link' => true));
   
     $this->vtprd_maybe_remove_current_auto_add_array();
     $this->vtprd_turn_off_auto_add_in_progress();

     //error_log( print_r(  'AT END OF vtprd_post_process_cart_for_autoAdds  ', true ) );
     //error_log( print_r(  '$previous_auto_add_array NOW=', true ) );
 $previous_auto_add_array = $this->vtprd_get_previous_auto_add_array(); 
 
     //error_log( var_export($previous_auto_add_array, true ) );
     //error_log( print_r(  ' ', true ) );
     //error_log( print_r(  '$vtprd_cart at end of post_process', true ) );
     //error_log( var_export($vtprd_cart, true ) ); 
//$woocommerce_cart_contents = $woocommerce->cart->get_cart();
     //error_log( print_r(  'WOO CART at end of post_process', true ) );
     //error_log( var_export($woocommerce_cart_contents, true ) );        
     return;    
  }  //end vtprd_post_process_cart_for_autoAdds  
  
     
        
   //**********************************
   //v1.1.0.6 new function 
   //  Only 1 ITERATION of auto add free candidate COUNT is added during pre_processing
   //   all others are added HERE, as needed, based on the $br and $ar REPEATS
   //v1.1.1.2 begin - reworked for multiple free 
   //  $current_auto_add_array for this product created in pre_processing
   //**********************************       
   public function vtprd_add_free_item_candidate($i) {
    	global $vtprd_cart, $vtprd_rules_set, $vtprd_info;
      
     //error_log( print_r(  'vtprd_add_free_item_candidate ', true ) ); 
       
      $current_auto_add_array = $this->vtprd_get_current_auto_add_array();
 
      
      //v1.1.1.2 ADDED to identify prod id
      //set up Free product prod_id

      //overwrite actionpop var-out and single if same as inpop and autoadd on ...
      //this gets reset later in main processing...
      if ($vtprd_rules_set[$i]->actionPop == 'sameAsInPop')  {                                                  
         $vtprd_rules_set[$i]->actionPop                =   $vtprd_rules_set[$i]->inPop;
         $vtprd_rules_set[$i]->var_out_checked          =   $vtprd_rules_set[$i]->var_in_checked;  
         $vtprd_rules_set[$i]->actionPop_singleProdID   =   $vtprd_rules_set[$i]->inPop_singleProdID;    
      }
       
      switch( $vtprd_rules_set[$i]->actionPop ) { 
        case 'vargroup':     //only applies to 1st rule deal line
             $free_product_id = $vtprd_rules_set[$i]->var_out_checked[0]; //there can be only one entry in the array  
          break; 
        case 'single':
             $free_product_id = $vtprd_rules_set[$i]->actionPop_singleProdID;    
          break;
      }

     //error_log( print_r(  'add_free_item_candidate AT TOP $free_product_id= ' .$free_product_id, true ) );  
      
      $current_auto_add_array_row = $current_auto_add_array[$free_product_id];
     //error_log( print_r(  '$vtprd_rules_set at vtprd_add_free_item_candidate $i= ' .$i, true ) );
     //error_log( var_export($vtprd_rules_set[$i], true ) );                 
      //actionPop_found_list
      //$last_iteration_key = ( sizeof ($vtprd_rules_set[$i]->actionPop_found_list) - 1);
      
      $actionPop_key_array = array_keys($vtprd_rules_set[$i]->actionPop_found_list);
      $last_iteration_key =  end($actionPop_key_array);
      
      
      //error_log( print_r(  'vtprd_add_free_item_candidate $last_iteration_key= ' .$last_iteration_key , true ) );    
      //error_log( print_r(  'prod_qty before add = ' .$vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_qty'] , true ) );  
      $vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_qty'] += $current_auto_add_array_row['free_product_add_action_cnt'];
     //error_log( print_r(  'prod_qty after add = ' .$vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_qty'] , true ) );
      $vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_running_qty'] +=$current_auto_add_array_row['free_product_add_action_cnt'];
      $vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_total_price'] += 
          ($current_auto_add_array_row['free_product_add_action_cnt'] * $vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_unit_price']);
      

      $vtprd_rules_set[$i]->actionPop_qty_total   += $current_auto_add_array_row['free_product_add_action_cnt'];
      $vtprd_rules_set[$i]->actionPop_total_price += 
          ($current_auto_add_array_row['free_product_add_action_cnt'] * $vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_unit_price']);
      $vtprd_rules_set[$i]->actionPop_running_qty_total   += $current_auto_add_array_row['free_product_add_action_cnt'];
      $vtprd_rules_set[$i]->actionPop_running_total_price += 
          ($current_auto_add_array_row['free_product_add_action_cnt'] * $vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_unit_price']);
     
      //****************************************
      //increase qty on $vtprd_cart->cart_item
      $k = $vtprd_rules_set[$i]->actionPop_found_list[$last_iteration_key]['prod_id_cart_occurrence'];
      $vtprd_cart->cart_items[$k]->quantity    +=  $current_auto_add_array_row['free_product_add_action_cnt'];
      $vtprd_cart->cart_items[$k]->total_price += ($current_auto_add_array_row['free_product_add_action_cnt'] * $vtprd_cart->cart_items[$k]->unit_price);
      $current_auto_add_array_row['current_qty']    =  $vtprd_cart->cart_items[$k]->quantity;
     
     
      //actionPop_exploded_found_list
      
      $next_actionPop_iteration = end($vtprd_rules_set[$i]->actionPop_exploded_found_list);
      $next_actionPop_iteration['prod_discount_amt'] = 0;
      $next_actionPop_iteration['prod_discount_applied'] = '';  
          
      for($cnt=0; $cnt < $current_auto_add_array_row['free_product_add_action_cnt']; $cnt++){
        $vtprd_rules_set[$i]->actionPop_exploded_found_list[] = $next_actionPop_iteration;
     //error_log( print_r('vtprd_add_free_item_candidate added iteration ', true ) );        
    //no longer used    $vtprd_rules_set[$i]->actionPop_exploded_found_list[$last_iteration_key]->product_free_auto_insert_candidate = 'yes';
      }
      
      if (sizeof($vtprd_rules_set[$i]->actionPop_exploded_found_list) == $vtprd_rules_set[$i]->actionPop_exploded_group_end) {
         $vtprd_rules_set[$i]->actionPop_exploded_group_end++;
     //error_log( print_r('actionPop_exploded_group_end reset here4 = ' .$vtprd_rules_set[$i]->actionPop_exploded_group_end, true ) );           
      }
      
      //put ROW back into the array
      $current_auto_add_array[$free_product_id] = $current_auto_add_array_row;
 
        
      //error_log( print_r(  '$current_auto_add_array at add_free_item_candidate time , key= ' .$free_product_id , true ) );
      //error_log( var_export($current_auto_add_array, true ) ); 
    
      $_SESSION['current_auto_add_array'] = serialize($current_auto_add_array);

      return; 
  }                                                       
     
        
   //**********************************
   //v1.1.0.6 new function
   //v1.1.1.2 begin - reworked for multiple free  
   /*
    now acceepts  $previous_auto_add_array,$all_or_single,$single_key
    if 'all', process whole $previous_auto_add_array
    if 'single' only process supplied $single_key
   */
   //**********************************                                 
   public function vtprd_maybe_roll_out_prev_auto_insert_from_woo_cart($previous_auto_add_array, $all_or_single, $single_key=none) {      
      global $woocommerce;
      
     //error_log( print_r(  'vtprd_maybe_roll_out_prev_auto_insert_from_woo_cart ', true ) ); 
 
      /*
      if ($previous_auto_add_array['free_qty']  <= 0) {
        return;
      }
      */
      $cart_updated = false; //v1.1.1.2
      $woocommerce_cart_contents = $woocommerce->cart->get_cart(); 
      foreach($woocommerce_cart_contents as $key => $cart_item) {

        if ($cart_item['variation_id'] > ' ') {      
            $cart_product_id    = $cart_item['variation_id'];
        } else { 
            $cart_product_id    = $cart_item['product_id']; 
        } 
        
        if ($all_or_single == 'single') { 
            if ($single_key != $cart_product_id) {
              continue;  //skip if not = to supplied single key
            }
        }      
             
        //if ($previous_auto_add_array['free_product_id'] == $cart_product_id) { 
        if (isset($previous_auto_add_array[$cart_product_id] )) { 

         $previous_auto_add_array_row = $previous_auto_add_array[$cart_product_id];
 
         //SKIP this product if no free qty
         if ($previous_auto_add_array_row['free_qty']  <= 0) {
            continue;
         }
                  
         $current_total_quantity =  ($cart_item['quantity'] - $previous_auto_add_array_row['free_qty']); 

         if ($current_total_quantity <= 0) {

            $woocommerce->cart->set_quantity($key,0,false); //set_quantity = 0 ==> delete the product

         } else {

            $woocommerce->cart->set_quantity($key,$current_total_quantity,false); //false = don't refresh totals

         }
         
         $cart_updated = true; //v1.1.1.2

        //v1.1.1.2  need to keep running for multiples
        //break; //break out of for each

       } 
        
      } //end foreach  
      
      //v1.1.1.2 new
      if ($cart_updated) {
              //v1.0.9.3 - mark call as internal only - 
        //	accessed in parent-cart-validation/ function vtprd_maybe_before_calculate_totals
        $_SESSION['internal_call_for_calculate_totals'] = true;   
          
        $woocommerce->cart->calculate_totals();
      }  
      
      return;
   } 
       
              
   //**********************************
   //v1.1.0.6 new function 
   //**********************************
   public function vtprd_turn_off_auto_add_in_progress() { 
     $contents = $_SESSION['auto_add_in_progress'];
     unset( $_SESSION['auto_add_in_progress'], $contents );
     $contents = $_SESSION['auto_add_in_progress_timestamp'];
     unset( $_SESSION['auto_add_in_progress_timestamp'], $contents );
   } 
        
   //**********************************
   //v1.1.0.6 new function
   //v1.1.1.2 begin - reworked for multiple free 
   //**********************************
   public function vtprd_maybe_remove_previous_auto_add_array() { 
     global $vtprd_info;
     //clear out the previous variable for use in next iteration, as needed
     $previous_auto_add_array = array();
     $_SESSION['previous_auto_add_array'] = serialize($previous_auto_add_array);
   } 
        
   //**********************************
   //v1.1.0.6 new function 
   //**********************************
   public function vtprd_get_previous_auto_add_array() { 
      global $vtprd_info;
      
     //error_log( print_r(  'vtprd_get_previous_auto_add_array ', true ) ); 
       
      if (isset($_SESSION['previous_auto_add_array']))  {
         $previous_auto_add_array = unserialize($_SESSION['previous_auto_add_array']);
      } else {
         //session var may have expired due to age.  If in vtprd_info, use that version!
         //****************************************
         // IF BOTH are gone, Free stuff DISAPPEARS and becomes a purchase.
         //****************************************
         if ( is_array($vtprd_info['previous_auto_add_array']) ) {
            $previous_auto_add_array = $vtprd_info['previous_auto_add_array'];
         } else {
            $previous_auto_add_array = array(); //v1.1.1.2
         }         
      }
      
      return $previous_auto_add_array; 
   }  
 
 /* v1.1.1.2  removed, no longer in use          
   //**********************************
   //v1.1.0.6 new function 
   //**********************************
   public function vtprd_init_previous_auto_add_array() { 
       $previous_auto_add_array = array (       
          'free_product_id' => '',
          'free_product_add_action_cnt' => '', 
          'free_product_in_inPop' => '',
          'free_rule_actionPop' => '',      
          'rule_id' => '',
          'current_qty' => '', 
          'purchased_qty' => '',
          'candidate_qty' => '',
          'free_qty' => '',
          'variations_parameter' => ''                  
      );
      
      return $previous_auto_add_array; 
   }     
 */   
               
   //**********************************
   //v1.1.0.6 new function 
   //**********************************
   public function vtprd_get_current_auto_add_array() { 
      
     //error_log( print_r(  'vtprd_get_current_auto_add_array ', true ) ); 
    
      if (isset($_SESSION['current_auto_add_array']))  {
         $current_auto_add_array = unserialize($_SESSION['current_auto_add_array']);
      } else {
         //v1.1.1.2 begin  -  handle multiples!
         //$current_auto_add_array = $this->vtprd_init_auto_add_array_row();
         $current_auto_add_array = array();
         //v1.1.1.2 end
      }
      
      return $current_auto_add_array; 
   }    
           
   //**********************************
   //v1.1.1.2 begin - reworked for multiple free 
   //v1.1.0.6 new function 
   //**********************************
   public function vtprd_init_auto_add_array_row() { 

       $auto_add_array_row = array (       
          'free_product_id' => '',
          'free_product_add_action_cnt' => '', 
          'free_product_in_inPop' => '',
          'free_rule_actionPop' => '',         
          'rule_id' => '',
          'current_qty' => '', 
          'purchased_qty' => '',
          'candidate_qty' => '',
          'free_qty' => '',
          'variations_parameter' => ''                  
      );
      
      return $auto_add_array_row; 
   }     
    
   //**********************************
   //v1.1.0.6 new function 
   //v1.1.1.2 begin - reworked for multiple free 
   //**********************************
   public function vtprd_maybe_remove_current_auto_add_array() { 
     //clear out the current variable for use in next iteration, as needed
     $current_auto_add_array = array();
     $_SESSION['current_auto_add_array'] = serialize($current_auto_add_array);
   }    
        
/* //v1.1.1.2  no longer used
   //**********************************
   //test for auto-add pre-process
   //**********************************
   public function vtprd_maybe_product_in_inPop($i, $k) { 
      global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;

      //init variable
      $in_inPop;
      switch( $vtprd_rules_set[$i]->inPop ) {  
          case 'wholeStore':                                                                                      
          case 'cart':                                                                                                  
                $this->vtprd_load_inPop_exploded_for_autoAdd($i, $k); 
                $in_inPop = 'yes';                              
            break;
          case 'groups':
              //test if product belongs in rule inPop
              if ( $this->vtprd_is_product_in_inPop_group($i, $k) ) {
                $this->vtprd_load_inPop_exploded_for_autoAdd($i, $k);
                $in_inPop = 'yes';                        
              } 
            break;
          case 'vargroup':   
              //is this the variation?
              if (in_array($vtprd_cart->cart_items[$k]->product_id, $vtprd_rules_set[$i]->var_in_checked )) {
                $this->vtprd_load_inPop_exploded_for_autoAdd($i, $k);
                $in_inPop = 'yes';
              }
            break;
          case 'single':
              //one product to rule them all
              if ($vtprd_cart->cart_items[$k]->product_id == $vtprd_rules_set[$i]->inPop_singleProdID) {
                $this->vtprd_load_inPop_exploded_for_autoAdd($i, $k);
                $in_inPop = 'yes';
              } 
            break;
        } 
      return $in_inPop;
   }
*/
  
        
   //**********************************
   //v1.1.0.6 new function
   //put potentially free product at end of cart!!
   //**********************************
   public function vtprd_sort_vtprd_cart_autoAdd_last() { 
      global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
      
     //error_log( print_r(  'vtprd_sort_vtprd_cart_autoAdd_last ', true ) ); 
       
      //v1.1.0.9 begin handle empty cart
      if (!isset($vtprd_cart->cart_items)) {
        return;
      }
      //v1.1.0.9 end
 
      $temp_vtprd_cart_items = array();
      $hold_cart_items = array(); //v1.1.0.9  can be MORE THAN 1!!
      foreach($vtprd_cart->cart_items as $key => $cart_item) {
         if ($cart_item->product_auto_insert_state != 'candidate') {
            $temp_vtprd_cart_items[] = $cart_item;
         } else {
            $hold_cart_items[] = $cart_item;  //v1.1.0.9
         }
      }
      
      //put potentially free product at end of cart!!
      //v1.1.0.9 begin
      foreach ($hold_cart_items as $key => $cart_item )  {
        $temp_vtprd_cart_items[] = $cart_item;
      }
       //v1.1.0.9 end
            
      //overwrite with sorted array
      $vtprd_cart->cart_items = $temp_vtprd_cart_items;
      
      return;
   }

/* //v1.1.1.2  no longer used
   //test for auto-add pre-process
   public function vtprd_load_inPop_exploded_for_autoAdd($i, $k) {
      global $vtprd_rules_set, $vtprd_cart;
      //******************************************
      //****  CHECK for PRODUCT EXCLUSIONS 
      //******************************************   
      //  prod_rule_include_only_list EXCLUDES every rule NOT on the list
      if (sizeof($vtprd_cart->cart_items[$k]->prod_rule_include_only_list) > 0) {  
        if ( in_array($vtprd_rules_set[$i]->post_id, $vtprd_cart->cart_items[$k]->prod_rule_include_only_list) ) {
          //v1.0.5.4
          $do_nothing;
          //continue;
        } else {
          return;
        } 
      }    
      //  prod_rule_exclusion_list EXCLUDES every rule On the list or 'all'
      if (sizeof($vtprd_cart->cart_items[$k]->prod_rule_exclusion_list) > 0) {     
        if ( ($vtprd_cart->cart_items[$k]->prod_rule_exclusion_list[0] == 'all') ||
             (in_array($vtprd_rules_set[$i]->post_id, $vtprd_cart->cart_items[$k]->prod_rule_exclusion_list)) ) {               
          return;
        } 
      }  
    //*****************************************************************************
    //EXPLODE out the cart into individual unit quantity lines for DISCOUNT processing
    //*****************************************************************************
    for($e=0; $e < $vtprd_cart->cart_items[$k]->quantity; $e++) {            
      $vtprd_rules_set[$i]->inPop_exploded_found_list[] = array(
                                                       'prod_id' => $vtprd_cart->cart_items[$k]->product_id,
                                                       'prod_name' => $vtprd_cart->cart_items[$k]->product_name,
                                                       'prod_qty' => 1,
                                                       'prod_unit_price' => $vtprd_cart->cart_items[$k]->unit_price,
                                                       'prod_db_unit_price' => $vtprd_cart->cart_items[$k]->db_unit_price, 
                                                       'prod_db_unit_price_list' => $vtprd_cart->cart_items[$k]->db_unit_price_list,
                                                       'prod_db_unit_price_special' => $vtprd_cart->cart_items[$k]->db_unit_price_special,
                                                       'prod_id_cart_occurrence' => $k, //used to mark product in cart if failed a rule
                                                       'exploded_group_occurrence' => $e,
                                                       'prod_discount_amt'  => 0,
                                                       'prod_discount_applied'  => ''
                                                      );          
  //    $vtprd_rules_set[$i]->inPop_exploded_group_occurrence++;
      $vtprd_rules_set[$i]->inPop_exploded_group_occurrence = $e;
    } //end explode         
  } 
 */
       
  //************
  //AUTO ADD to vtprd-cart, only used for free items... 
  //************
	public function vtprd_auto_add_to_vtprd_cart($free_product_id, $free_product_to_be_added_qty, $i) {
      global $post, $wpdb, $woocommerce, $vtprd_cart, $vtprd_cart_item, $vtprd_info, $vtprd_rules_set; 
     //error_log( print_r(  'TOP OF  auto_add_to_vtprd_cart' , true ) );  
      $vtprd_cart_item                = new VTPRD_Cart_Item;
                  
      // $free_product_id will be the var id if it's a variation

      //use get_post to strip out single quotes if there... nothing else works  //v1.0.5.6
      //$post = get_post($free_product_id);         //v1.0.5.6 
      //$vtprd_cart_item->product_id = $post->ID;   //v1.0.5.6
      $vtprd_cart_item->product_id  =  $free_product_id;   //v1.1.0.6 

      $vtprd_cart_item->quantity    =  $free_product_to_be_added_qty;

/*   
       **This structure is needed for the add-to-cart woocommerce function...   
          [var_out_product_variations_parameter] => Array
            (
                [parent_product_id] => 738
                [variation_product_id] => 742
                [variations_array] => Array
                    (
                        [pa_colors2] => white
                        [pa_size2] => lg
                    )
            )     
*/      
  
      if ( (sizeof ($vtprd_rules_set[$i]->var_out_product_variations_parameter) > 0) && 
           (get_post_field('post_parent', $free_product_id) ) ) {     //variations have a Parent!! 
     //error_log( print_r(  'Adding Variation' , true ) );  
          // get variation names to string onto parent title
          foreach($vtprd_rules_set[$i]->var_out_product_variations_parameter['variations_array'] as $key => $value) {          
            $varLabels .= $value . '&nbsp;';           
          }

          $vtprd_cart_item->variation_array      = $vtprd_rules_set[$i]->var_out_product_variations_parameter['variations_array'];                  
          if ($vtprd_rules_set[$i]->actionPop_varProdID_name > ' ') {
            $vtprd_cart_item->parent_product_name  = $vtprd_rules_set[$i]->actionPop_varProdID_name;
          }  else {
            $vtprd_cart_item->parent_product_name  = $vtprd_rules_set[$i]->inPop_varProdID_name;
          }
          
          //remove  ' (Variations)' from parent_product_name
          $vtprd_cart_item->parent_product_name = str_replace( ' (Variations)', '', $vtprd_cart_item->parent_product_name  ) ; 
       //   $vtprd_cart_item->parent_product_name = sanitize_title( str_replace( ' (Variations)', '', $vtprd_cart_item->parent_product_name  ) );         
          
          $vtprd_cart_item->product_name = $vtprd_cart_item->parent_product_name . '&nbsp;' . $varLabels ;

      }  else {
     //error_log( print_r(  'Adding Regular Product' , true ) );        
         //v1.0.5.6 begin
         //$post = get_post($free_product_id); 
         //$vtprd_cart_item->product_name = $post->post_name;
         if ($vtprd_rules_set[$i]->actionPop_singleProdID_name > ' '  ) {
            $vtprd_cart_item->product_name = $vtprd_rules_set[$i]->actionPop_singleProdID_name;
         } else {
            $vtprd_cart_item->product_name = $vtprd_rules_set[$i]->inPop_singleProdID_name;
         }
         //v1.0.5.6  end
      }


      $_product = get_product( $free_product_id );
                                        //get_option( 'woocommerce_tax_display_cart' ) == 'excl' || $woocommerce->customer->is_vat_exempt()
      $vtprd_cart_item->unit_price     =  get_option( 'woocommerce_tax_display_cart' ) == 'excl' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price();
      
      
      $vtprd_cart_item->db_unit_price  =  $vtprd_cart_item->unit_price; 

      $vtprd_cart_item->db_unit_price_list     =  get_post_meta( $free_product_id, '_regular_price', true ); //v1.1.0.6  replaced $product_id
      $vtprd_cart_item->db_unit_price_special  =  get_post_meta( $free_product_id, '_sale_price', true );   //v1.1.0.6  replaced $product_id       
      
    
      
      //always recalculate based on the db price, to redo any discounts previously applied
      $vtprd_cart_item->total_price   = $vtprd_cart_item->quantity * $vtprd_cart_item->unit_price;
   
   // now done ABOVE   
  //    $vtprd_cart->cart_original_total_amt += $vtprd_cart_item->total_price;
      
      $vtprd_cart_item->product_auto_insert_state = 'candidate'; //v1.1.0.6

      //v1.1.1.2 begin
      //MARK the inserted item to be used ONLY for this rule
      $vtprd_cart_item->product_auto_insert_rule_id = $vtprd_rules_set[$i]->post_id;
      //v1.1.1.2 end

      
      //********************************
      //add cart_item to cart array 
      //********************************
      $vtprd_cart->cart_items[]       = $vtprd_cart_item;

      //return $vtprd_cart_item->total_price; //v1.1.0.6
      return;      
  }

 
 
   //*******************************************************
   //v1.0.8.4 new function
   //  used in following rule processing iterations, if cumulativeRulePricing == 'no'
   //*******************************************************
   public  function vtprd_mark_products_in_an_all_rule($i) {
		  global $vtprd_cart, $vtprd_rules_set, $vtprd_info, $vtprd_setup_options, $vtprd_rule; 
      
      $sizeof_cart_items = sizeof($vtprd_cart->cart_items);
      $sizeof_actionPop_found_list = sizeof($vtprd_rules_set[$i]->actionPop_found_list);
      
      for($a=0; $a < $sizeof_actionPop_found_list; $a++) {            
          for($k=0; $k < $sizeof_cart_items; $k++) { 
             if ($vtprd_cart->cart_items[$k]->product_id == $vtprd_rules_set[$i]->actionPop_found_list[$a]['prod_id']) {
                $vtprd_cart->cart_items[$k]->product_already_in_an_all_rule = 'yes'; 
             }
          }
      }
   }      
  
  
/*
ADDTIONAL RULE CRITERIA FILTER - Execution example

add_filter('vtprd_additional_inpop_include_criteria', 'process_additional_inpop_include_criteria', 10, 3);

function process_additional_inpop_include_criteria ($return_status, $i, $k) {
  global $vtprd_cart, $vtprd_rules_set, $vtprd_rule, $vtprd_info, $vtprd_setup_options;
  $return_status = TRUE;
  
  //$vtprd_rules_set[$i]->post_id = Rule ID
  //$vtprd_cart is the cart contents ==> look at  core/vtprd-cart-classes.php  for cart contents structure
  //   and check this document for examples of how to access the cart data items.
  
  
  switch( $vtprd_rules_set[$i]->post_id ) { 
     //ONLY test those ids for which additional criteria is needed
     case '001':    //rule id 001
         //  **do add-on-criteria test
         //  *if failed test,
             $return_status = FALSE;                      
        break;
     case '002':    etc
                 
        break;        
  }
  return $return_status;
}

*/
   
} //end class
