<?php

if(!function_exists('wpo_create_type_sliders')){
    function wpo_create_type_sliders(){
        $labels = array(
            'name' => __( 'Sliders', TEXTDOMAIN ),
            'singular_name' => __( 'Slider', TEXTDOMAIN),
            'add_new' => __( 'Add New Slider', TEXTDOMAIN ),
            'add_new_item' => __( 'Add New Slider', TEXTDOMAIN ),
            'edit_item' => __( 'Edit Slider', TEXTDOMAIN ),
            'new_item' => __( 'New Slider', TEXTDOMAIN ),
            'view_item' => __( 'View Slider', TEXTDOMAIN ),
            'search_items' => __( 'Search Slider', TEXTDOMAIN ),
            'not_found' => __( 'No Slider found', TEXTDOMAIN ),
            'not_found_in_trash' => __( 'No Slider found in Trash', TEXTDOMAIN ),
            'parent_item_colon' => __( 'Parent Slider:', TEXTDOMAIN ),
            'menu_name' => __( 'Sliders', TEXTDOMAIN )
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'description' => 'List Slider',
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies' => array('slider_group' ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_nav_menus' => false,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'has_archive' => true,
            'query_var' => true,
            'can_export' => true,
            'rewrite' => true,
            'capability_type' => 'post'
        );
        register_post_type( 'sliders', $args );


        $labels = array(
            'name' => __( 'Slider groups', TEXTDOMAIN ),
            'singular_name' => __( 'Slider group', TEXTDOMAIN ),
            'search_items' =>  __( 'Search Slider groups',TEXTDOMAIN ),
            'all_items' => __( 'All Slider groups',TEXTDOMAIN ),
            'parent_item' => __( 'Parent Slider group',TEXTDOMAIN ),
            'parent_item_colon' => __( 'Parent Slider group:',TEXTDOMAIN ),
            'edit_item' => __( 'Edit Slider group',TEXTDOMAIN ),
            'update_item' => __( 'Update Slider group',TEXTDOMAIN ),
            'add_new_item' => __( 'Add New Slider group',TEXTDOMAIN ),
            'new_item_name' => __( 'New Slider group',TEXTDOMAIN ),
            'menu_name' => __( 'Slider groups',TEXTDOMAIN ),
        );

        register_taxonomy('slider_group',array('sliders'), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'slider_group' ),
            'show_in_nav_menus'=>false
        ));
    }
    add_action( 'init','wpo_create_type_sliders' );
}