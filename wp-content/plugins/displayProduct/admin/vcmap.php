<?php
//if(  function_exists( 'vc_map')){



add_action( 'vc_before_init', 'dp_integrateWithVC' );
function dp_integrateWithVC() {
    global $wpdb;
    $allPageTemplate = array('');
    $rs = $wpdb->get_results( "
        SELECT ID, post_title
        FROM $wpdb->posts
        WHERE post_type = 'dp_template'	AND post_status = 'publish'
        ORDER BY ID DESC"
    );
    foreach ( $rs as $r )
    {
        $allPageTemplate[$r->post_title]=$r->ID;
    }

    vc_map( array(
        "name" => __("Display Product for WooCommerce",'asdfasdf'),
        "base" => "displayProduct",
        "class" => "",
        "category" => __('Content'),
        "icon"=>DP_URL.'/assets/images/logo-36x36.png',
//      'admin_enqueue_js' => array(get_template_directory_uri().'/vc_extend/bartag.js'),
//      'admin_enqueue_css' => array(get_template_directory_uri().'/vc_extend/bartag.css'),
        "params" => array(
            array(
                "type" => "dropdown",
                "holder" => "div",
                "class" => "",
                'admin_label' => true,
                "heading" => __("Select Layout",DP_TEXTDOMAN),
                "param_name" => "id",
                "value" =>$allPageTemplate
            )
        )
    ) );
}