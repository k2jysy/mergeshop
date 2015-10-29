<?php
/**
 * Register a Display product post type.
 */
function dp_template_init() {
	$labels = array(
		'name'               => __( 'Display Product Template Editor', DP_TEXTDOMAN ),
		'singular_name'      => __( 'List Templates Editor', DP_TEXTDOMAN ),
		'menu_name'          => __( 'Display Product', DP_TEXTDOMAN ),
		'name_admin_bar'     => __( 'List Templates Editor', DP_TEXTDOMAN ),
		'add_new'            => __( 'Add New', DP_TEXTDOMAN ),
		'add_new_item'       => __( 'Add New List Template', DP_TEXTDOMAN ),
		'new_item'           => __( 'New List Template', DP_TEXTDOMAN ),
		'edit_item'          => __( 'Edit List Template', DP_TEXTDOMAN ),
		'view_item'          => __( 'View List Template', DP_TEXTDOMAN ),
		'all_items'          => __( 'All List Template', DP_TEXTDOMAN ),
		'search_items'       => __( 'Search List Template', DP_TEXTDOMAN ),
		'parent_item_colon'  => __( 'Parent List Template:', DP_TEXTDOMAN ),
		'not_found'          => __( 'No books found.', DP_TEXTDOMAN ),
		'not_found_in_trash' => __( 'No books found in Trash.', DP_TEXTDOMAN )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'dp_template' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 110,
                'menu_icon'          => DP_URL.'/assets/images/logo-16x16.png',
		'supports'           => array( 'title' )
	);

	register_post_type( 'dp_template', $args );
}
add_action( 'init', 'dp_template_init' );

function so_screen_layout_columns( $columns ) {
    $columns['dp_template'] = 1;
    return $columns;
}
add_filter( 'screen_layout_columns', 'so_screen_layout_columns' );

function so_screen_layout_dp_template() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_dp_template', 'so_screen_layout_dp_template' );

function wpse_65075_modify_timeline_menu_icon( $post_type, $args ) {
    // Make sure we're only editing the post type we want
    if ( 'dp_template' != $post_type )
        return;

    if(get_post_type()=='dp_template'||$_GET['post_type']=='dp_template'||$post_type=='dp_template'):
        wp_register_style( 'dp-template-editor', plugin_dir_url(__FILE__) . '../assets/css/template-editor/dp-template-editor.css' );
        wp_enqueue_style( 'dp-template-editor' );
    endif;
}
add_action( 'registered_post_type', 'wpse_65075_modify_timeline_menu_icon', 10, 2 );


// ONLY MOVIE CUSTOM TYPE POSTS
add_filter('manage_dp_template_posts_columns', 'dp_shortcode_head', 10);
add_action('manage_dp_template_posts_custom_column', 'dp_shortcode_content', 10, 2);
add_filter('manage_dp_template_posts_columns', 'dp_columns_remove_date');
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
function dp_shortcode_head($defaults) {
    $defaults['dp_shortcode'] = 'Shortcode';
    return $defaults;
}
function dp_shortcode_content($column_name, $post_ID) {
    if ($column_name == 'dp_shortcode') {
        echo '[displayProduct id="'.$post_ID .'"]';
    }
}
// REMOVE DEFAULT CATEGORY COLUMN
function dp_columns_remove_date($defaults) {
    unset($defaults['date']);
    return $defaults;
}