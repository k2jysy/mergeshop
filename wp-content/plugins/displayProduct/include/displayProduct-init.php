<?php
//function dp_admin_notices_styles() {
//	$dpCheckpage=get_option("dp_product_shop_page");
//        $dp_needs_pages=get_option("dp_needs_pages");
//        if ( empty( $dpCheckpage )&& $dp_needs_pages==1&&$_GET['page']=='display-product-page' ){
//		wp_enqueue_style( 'displayproduct-activation', plugins_url(  '/assets/css/displayproduct-notice.css', dirname( __FILE__ ) ) );
//		add_action( 'admin_notices', 'dp_admin_install_notices' );
//	}
//}
//add_action( 'admin_print_styles', 'dp_admin_notices_styles' );
//
//
//function dp_admin_install_notices() {
//	// If we have just installed, show a message with the install pages button
//	$dpCheckpage=get_option("dp_product_shop_page");
//        $dp_needs_pages=get_option("dp_needs_pages");
//        if ( empty( $dpCheckpage )&& $dp_needs_pages==1 &&$_GET['page']=='display-product-page'){
//		include( 'displayProduct-notice-install.php' );
//	}
//}

function dp_admin_init() {
	global $pagenow, $typenow, $post;

	ob_start();

	// Install - Add pages button
	if ( ! empty( $_GET['install_dp_pages'] ) ) {
		dp_create_pages();

		// We no longer need to install pages
		delete_option( 'dp_needs_pages' );

		// What's new redirect
		wp_safe_redirect( admin_url( 'admin.php?page=display-product-page' ) );
		exit;

	// Skip button
	} elseif ( ! empty( $_GET['skip_install_dp_pages'] ) ) {

		// We no longer need to install pages
                update_option( 'dp_needs_pages', 0 );
		// What's new redirect
		wp_safe_redirect( admin_url( 'admin.php?page=display-product-page' ) );
		exit;
        }elseif ( ! empty( $_GET['reset_install_dp_pages'] ) ) {
                dp_reset_create_pages();
                update_option("dp_replace_woo_page", 0);
		// We no longer need to install pages
                update_option( 'dp_needs_pages', 0 );
		// What's new redirect
		wp_safe_redirect( admin_url( 'admin.php?page=display-product-page' ) );
		exit;
        }

}

add_action('admin_init', 'dp_admin_init');



function dp_create_pages() {

    // Shop page
    dp_create_page( esc_sql( _x( 'dp-shop', 'page_slug', DP_TEXTDOMAN ) ), 'dp_product_shop_page', __( 'My Shop', DP_TEXTDOMAN ), '[displayProduct type="grid" excerpt="hide" sku="hide" metacategory="hide" metatag="hide" outofstock="hide"]' );

    // Category page
    dp_create_page( esc_sql( _x( 'dp-category', 'page_slug', DP_TEXTDOMAN ) ), 'dp_product_category_page', __( 'Product Category', DP_TEXTDOMAN ), '[displayProduct type="grid" excerpt="hide" sku="hide" metacategory="hide" metatag="hide" outofstock="hide"]' );

    // Tag page
    dp_create_page( esc_sql( _x( 'dp-tag', 'page_slug', DP_TEXTDOMAN ) ), 'dp_product_tag_page', __( 'Product Tags', DP_TEXTDOMAN ), '[displayProduct type="grid" excerpt="hide" sku="hide" metacategory="hide" metatag="hide" outofstock="hide"]' );

    // Search page
    dp_create_page( esc_sql( _x( 'dp-search', 'page_slug', DP_TEXTDOMAN ) ), 'dp_product_search_page', __( 'Product Search', DP_TEXTDOMAN ), '[displayProduct type="grid" excerpt="hide" sku="hide" metacategory="hide" metatag="hide" outofstock="hide"]' );

}
function dp_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;
	$option_value = get_option( $option );// Interger ex = 36

	if ( ($option_value > 0 && get_post( $option_value )) || $option_value=='disable')
		return;

	$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
	if ( $page_found ) {
		if ( ! $option_value )
			update_option( $option, $page_found );
		return;
	}

	$page_data = array(
        'post_status' 		=> 'publish',
        'post_type' 		=> 'page',
        'post_author' 		=> 1,
        'post_name' 		=> $slug,
        'post_title' 		=> $page_title,
        'post_content' 		=> $page_content,
        'post_parent' 		=> $post_parent,
        'comment_status' 	=> 'closed'
    );
    $page_id = wp_insert_post( $page_data );

    update_option( $option, $page_id );
}
function dp_reset_create_pages() {

    // Shop page
    dp_reset_create_page(  'dp_product_shop_page' );

    // Category page
    dp_reset_create_page(  'dp_product_category_page' );

    // Tag page
    dp_reset_create_page(  'dp_product_tag_page' );

    // Search page
    dp_reset_create_page( 'dp_product_search_page' );

}
function dp_reset_create_page(  $option) {
    global $wpdb;

    update_option( $option, 'disable' );
}
function displayproduct_textdomain($text=NULL){
    
    $strings = array(
        'selectliststyle' => __( '1. Select list style', DP_TEXTDOMAN ),
        'Grid'   => __( 'Grid', DP_TEXTDOMAN ),
        'List'   => __( 'List', DP_TEXTDOMAN ),
        'Table'  => __( 'Table', DP_TEXTDOMAN ),
        'Box'    => __( 'Box', DP_TEXTDOMAN ),
        'Carousel_Box'   => __( 'Carousel Box', DP_TEXTDOMAN ),
        'Carousel_Grid'   => __( 'Carousel Grid', DP_TEXTDOMAN ),
        'DisplayOptions'   => __( '2. Display Options', DP_TEXTDOMAN ),
        'Select_product'   => __( 'Select Product', DP_TEXTDOMAN ),
        'allproduct'   => __( 'All Product', DP_TEXTDOMAN ),
        'filterproduct'   => __( 'Select Product', DP_TEXTDOMAN ),
        'featuredproduct'   => __( 'Featured Product', DP_TEXTDOMAN ),
        'saleproduct'   => __( 'Sales Product', DP_TEXTDOMAN ),
        'instockproduct'   => __( 'In Stock Product', DP_TEXTDOMAN ),
        'outofstockpproduct'   => __( 'Out of Stock Product', DP_TEXTDOMAN ),
        'productcategory'   => __( 'Product Category', DP_TEXTDOMAN ),
        'allcategory'   => __( 'All Category', DP_TEXTDOMAN ),
        'customcategory'   => __( 'Select Category', DP_TEXTDOMAN ),
        'sortbuy'   => __( 'Sort by', DP_TEXTDOMAN ),
        'Default_sorting'   => __( 'Default sorting', DP_TEXTDOMAN ),
        'Sort_by_popularity'   => __( 'Sort by popularity', DP_TEXTDOMAN ),
        'Sort_by_newness'  => __( 'Sort by newness', DP_TEXTDOMAN ),
        'Sort_by_oldest'    => __( 'Sort by oldest', DP_TEXTDOMAN ),
        'Sort_by_Product_title_a_to_z'   => __( 'Sort by Product title a to z', DP_TEXTDOMAN ),
        'Sort_by_Product_title_z_to_a'   => __( 'Sort by Product title z to a', DP_TEXTDOMAN ),
        'Sort_by_Price_low_to_high'   => __( 'Sort by Price low to high', DP_TEXTDOMAN ),
        'Sort_by_Price_high_to_low'   => __( 'Sort by Price high to low', DP_TEXTDOMAN ),
        'Sort_by_SKU_low_to_high'   => __( 'Sort by SKU low to high', DP_TEXTDOMAN ),
        'Sort_by_SKU_high_to_low'   => __( 'Sort by SKU high to low', DP_TEXTDOMAN ),
        'Sort_by_stock_low_to_high'   => __( 'Sort by stock low to high', DP_TEXTDOMAN ),
        'Sort_by_stock_high_to_low'   => __( 'Sort by stock high to low', DP_TEXTDOMAN ),
        'Sort_by_random'   => __( 'Sort by random', DP_TEXTDOMAN ),
        'Columns'   => __( 'Columns', DP_TEXTDOMAN ),
        'Products_displayed_per_page'   => __( 'Products displayed per page', DP_TEXTDOMAN ),
        'Pagination'   => __( 'Pagination', DP_TEXTDOMAN ),
        'Default'   => __( 'Default', DP_TEXTDOMAN ),
        'Disable'   => __( 'Disable', DP_TEXTDOMAN ),
        'Quickview' => __('Quick View',DP_TEXTDOMAN),
        'Trimwords' => __( 'Trim words', DP_TEXTDOMAN ),
        'Show'   => __( 'Show', DP_TEXTDOMAN ),
        'Title'   => __( 'Title', DP_TEXTDOMAN ),
        'Excerpt'  => __( 'Excerpt', DP_TEXTDOMAN ),
        'Image'    => __( 'Image', DP_TEXTDOMAN ),
        'Price'   => __( 'Price', DP_TEXTDOMAN ),
        'Star'   => __( 'Star', DP_TEXTDOMAN ),
        'SKU'   => __( 'SKU', DP_TEXTDOMAN ),
        'Category'   => __( 'Category', DP_TEXTDOMAN ),
        'Tag'   => __( 'Tag', DP_TEXTDOMAN ),
        'Button'   => __( 'Button', DP_TEXTDOMAN ),
        'Featured'   => __( 'Featured', DP_TEXTDOMAN ),
        'Sale'   => __( 'Sale', DP_TEXTDOMAN ),
        'Out_of_Stock'   => __( 'Out of Stock', DP_TEXTDOMAN ),
        'Link_to_Product_Page'   => __( 'Link to Product Page', DP_TEXTDOMAN ),
        'Frontend_Sorter'   => __( 'Frontend Sorter', DP_TEXTDOMAN ),
        'Button_and_Quantity'   => __( 'Button & Quantity', DP_TEXTDOMAN ),
        'Button_default'   => __( 'Button default', DP_TEXTDOMAN ),
        'Button_Quantity'   => __( 'Button & Quantity', DP_TEXTDOMAN ),
        'Product_detail' => __( 'Product Detail', DP_TEXTDOMAN ),
        
        'Custom_Button'   => __( 'Custom Button', DP_TEXTDOMAN ),
        'Custom_Text_Call_for_price'   => __( 'Custom Text: Call for price', DP_TEXTDOMAN ),
        'Button_Custom_URL'   => __( 'Button Custom URL', DP_TEXTDOMAN ),
        'Button_Custom_Text'   => __( 'Button Custom Text', DP_TEXTDOMAN ),
        'quickview' => __( 'Quickview', DP_TEXTDOMAN ),
        'Color'   => __( '3. Color', DP_TEXTDOMAN ),
        'Arrow_Dot'   => __( 'Arrow & Dot', DP_TEXTDOMAN ),
        'Arrow'   => __( 'Arrow', DP_TEXTDOMAN ),
        'Show_pagination_Dot'   => __( 'Show pagination: Dot', DP_TEXTDOMAN ),
        'Arrow_and_Dot'   => __( 'Arrow & Dot', DP_TEXTDOMAN ),
        'Arrow_Style'   => __( 'Arrow Style', DP_TEXTDOMAN ),
        'Arrow_Position'   => __( 'Arrow Position', DP_TEXTDOMAN ),
        'Side_Middle'   => __( 'Side & Middle', DP_TEXTDOMAN ),
        'Top_Right'   => __( 'Top_Right', DP_TEXTDOMAN ),
        'Top_Left' => __( 'Top & Left', DP_TEXTDOMAN ),
        
        'Select_Thumbnail_Hover_Effect' => __( 'Select Thumbnail Hover Effect', DP_TEXTDOMAN ),
        'Select_Hover_Effec_Product_Name'   => __( 'Select Hover Effect(Product Name)', DP_TEXTDOMAN ),
        'Select_Hover_Effect_excerpt_and_star'   => __( 'Select Hover Effect(excerpt and star)', DP_TEXTDOMAN ),
        'Select_Hover_Effect_Price'  => __( 'Select Hover Effect(Price)', DP_TEXTDOMAN ),
        'Table_Background_color'    => __( 'Table Background color', DP_TEXTDOMAN ),
        'Table_Head_Background_color'   => __( 'Table Head Background color', DP_TEXTDOMAN ),
        'Table_Head_Text_color'   => __( 'Table Head Text color', DP_TEXTDOMAN ),
        'Table_Row_hover_color'   => __( 'Table Row hover color', DP_TEXTDOMAN ),
        'Background_color'   => __( 'Background color', DP_TEXTDOMAN ),
        'featuredcolor'   => __( 'Featured color', DP_TEXTDOMAN ),
        'salecolor'   => __( 'Sale color', DP_TEXTDOMAN ),
        'Border_color'   => __( 'Border color', DP_TEXTDOMAN ),
        'Product_name_color'   => __( 'Product name color', DP_TEXTDOMAN ),
        'Product_nam_hover_color'   => __( 'Product name hover color', DP_TEXTDOMAN ),
        'Price_color'   => __( 'Price color', DP_TEXTDOMAN ),
        'Text_color'   => __( 'Text color', DP_TEXTDOMAN ),
        'Link_color'   => __( 'Link color', DP_TEXTDOMAN ),
        'Link_hover_color'   => __( 'Link hover color', DP_TEXTDOMAN ),
        'Button_color'   => __( 'Button color', DP_TEXTDOMAN ),
        'Button_hover_color'   => __( 'Button hover color', DP_TEXTDOMAN ),
        'Select_Font'   => __( 'Select Font', DP_TEXTDOMAN ),
        'Insert_Product_Shortcode'   => __( 'Insert Product Shortcode', DP_TEXTDOMAN ),
        'Edit'=>__('Edit',DP_TEXTDOMAN),
        'customize_layout'=>__('4. Customizing Product Layouts',DP_TEXTDOMAN),
        'Date'=>__('Date',DP_TEXTDOMAN),
        'Author'=>__('Author',DP_TEXTDOMAN),
        'Tags'=>__('Tags',DP_TEXTDOMAN),
        'filter_by_tag'=>__('Filter by tag',DP_TEXTDOMAN),
        'Custom_fields'=>__('Custom Field',DP_TEXTDOMAN),
        'Variable'=>__('Variable',DP_TEXTDOMAN),
        'Date'=>__('Date',DP_TEXTDOMAN),
        'Product_name'=>__('Product Name',DP_TEXTDOMAN),
        'Color'=>__('Color',DP_TEXTDOMAN),
        'HoverColor'=>__('Hover Color',DP_TEXTDOMAN),
        'Meta_group'=>__('Meta Group',DP_TEXTDOMAN),
        'filter_category' =>__('Filter by category',DP_TEXTDOMAN),
        'filter_tags' =>__('Filter by tags',DP_TEXTDOMAN),
        'alltag'=> __('All tag',DP_TEXTDOMAN),
        'customtag'=> __('Custom tag', DP_TEXTDOMAN),
        'width'=> __('Width',DP_TEXTDOMAN),
        'height'=>__('Height',DP_TEXTDOMAN),
        'filter_by_shippingClass' => __('Filter by shipping class',DP_TEXTDOMAN),
        'allshippingclass'=>__('All Shipping Class',DP_TEXTDOMAN),
        'customshippingclass'=>__('Custom Shipping Class',DP_TEXTDOMAN),
        'Crop'=>__('Crop', DP_TEXTDOMAN),
        'ProductShortDescription'=>'Product Short Description'
    );
    if($text){
        return $strings[$text];
    }else{
        return $strings;
    }
}
function dp_the_animation_option_init($select_animate=''){
    $s='selected="selected"';
    $dp_animation='<option value="fadeIn" '.($select_animate=='fadeIn'?$s:"").'>fadeIn</option>
                <option value="fadeInLeft" '.($select_animate=='fadeInLeft'?$s:"").'>fadeInLeft</option>
                <option value="fadeInRight" '.($select_animate=='fadeInRight'?$s:"").'>fadeInRight</option>
                <option value="fadeInUp" '.($select_animate=='fadeInUp'?$s:"").'>fadeInUp</option>
                <option value="fadeInDown" '.($select_animate=='fadeInDown'?$s:"").'>fadeInDown</option>
                <option value="rotateIn" '.($select_animate=='rotateIn'?$s:"").'>rotateIn</option>
                <option value="rotateInLeft" '.($select_animate=='rotateInLeft'?$s:"").'>rotateInLeft</option>
                <option value="rotateInRight" '.($select_animate=='rotateInRight'?$s:"").'>rotateInRight</option>
                <option value="rotateInUp" '.($select_animate=='rotateInUp'?$s:"").'>rotateInUp</option>
                <option value="rotateInDown" '.($select_animate=='rotateInDown'?$s:"").'>rotateInDown</option>
                <option value="bounce" '.($select_animate=='bounce'?$s:"").'>bounce</option>
                <option value="bounceInLeft" '.($select_animate=='bounceInLeft'?$s:"").'>bounceInLeft</option>
                <option value="bounceInRight" '.($select_animate=='bounceInRight'?$s:"").'>bounceInRight</option>
                <option value="bounceInUp" '.($select_animate=='bounceInUp'?$s:"").'>bounceInUp</option>
                <option value="bounceInDown" '.($select_animate=='bounceInDown'?$s:"").'>bounceInDown</option>
                <option value="elasticInLeft" '.($select_animate=='elasticInLeft'?$s:"").'>elasticInLeft</option>
                <option value="elasticInRight" '.($select_animate=='elasticInRight'?$s:"").'>elasticInRight</option>
                <option value="elasticInUp" '.($select_animate=='elasticInUp'?$s:"").'>elasticInUp</option>
                <option value="elasticInDown" '.($select_animate=='elasticInDown'?$s:"").'>elasticInDown</option>
                <option value="zoomIn" '.($select_animate=='zoomIn'?$s:"").'>zoomIn</option>
                <option value="zoomInLeft" '.($select_animate=='zoomInLeft'?$s:"").'>zoomInLeft</option>
                <option value="zoomInRight" '.($select_animate=='zoomInRight'?$s:"").'>zoomInRight</option>
                <option value="zoomInUp" '.($select_animate=='zoomInUp'?$s:"").'>zoomInUp</option>
                <option value="zoomInDown" '.($select_animate=='zoomInDown'?$s:"").'>zoomInDown</option>
                <option value="jellyInLeft" '.($select_animate=='jellyInLeft'?$s:"").'>jellyInLeft</option>
                <option value="jellyInRight" '.($select_animate=='jellyInRight'?$s:"").'>jellyInRight</option>
                <option value="jellyInDown" '.($select_animate=='jellyInDown'?$s:"").'>jellyInDown</option>
                <option value="jellyInUp" '.($select_animate=='jellyInUp'?$s:"").'>jellyInUp</option>
                <option value="flipInLeft" '.($select_animate=='flipInLeft'?$s:"").'>flipInLeft</option>
                <option value="flipInRight" '.($select_animate=='flipInRight'?$s:"").'>flipInRight</option>
                <option value="flipInUp" '.($select_animate=='flipInUp'?$s:"").'>flipInUp</option>
                <option value="flipInDown" '.($select_animate=='flipInDown'?$s:"").'>flipInDown</option>
                <option value="flipInV" '.($select_animate=='flipInV'?$s:"").'>flipInV</option>
                <option value="flipInH" '.($select_animate=='flipInH'?$s:"").'> flipInH</option>
                <option value="pendulum" '.($select_animate=='pendulum'?$s:"").'>pendulum</option>';
    return $dp_animation;
}
?>
