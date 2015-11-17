<?php 
	function wooexim_save_woo_products(){
		$products = array();
		$products = $_REQUEST['products'];
		//echo "<pre>"; print_r($products);
		if( empty ( $products ) )
			update_option( 'wooexim_selected_products', '' );
		else
			update_option( 'wooexim_selected_products', $products );
		die;
	}
	
	function wooexim_save_woo_category(){
		$categories = array();
		$categories = $_REQUEST['categories'];
		//echo "<pre>"; print_r($categories);
		if( empty ( $categories ) )
			update_option( 'wooexim_selected_categories', '' );
		else
			update_option( 'wooexim_selected_categories', $categories );
		die;
	}
?>