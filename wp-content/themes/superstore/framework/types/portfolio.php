<?php
 /**
  * $Desc
  *
  * @version    $Id$
  * @package    wpbase
  * @author     Opal  Team <opalwordpressl@gmail.com >
  * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
  * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
  *
  * @website  http://www.wpopal.com
  * @support  http://www.wpopal.com/support/forum.html
  */
if(!function_exists('wpo_create_type_portfolio')){
    function wpo_create_type_portfolio(){
      $labels = array(
          'name' => __( 'Portfolios', TEXTDOMAIN ),
          'singular_name' => __( 'Portfolio', TEXTDOMAIN ),
          'add_new' => __( 'Add New Portfolio', TEXTDOMAIN ),
          'add_new_item' => __( 'Add New Portfolio', TEXTDOMAIN ),
          'edit_item' => __( 'Edit Portfolio', TEXTDOMAIN ),
          'new_item' => __( 'New Portfolio', TEXTDOMAIN ),
          'view_item' => __( 'View Portfolio', TEXTDOMAIN ),
          'search_items' => __( 'Search Portfolios', TEXTDOMAIN ),
          'not_found' => __( 'No Portfolios found', TEXTDOMAIN ),
          'not_found_in_trash' => __( 'No Portfolios found in Trash', TEXTDOMAIN ),
          'parent_item_colon' => __( 'Parent Portfolio:', TEXTDOMAIN ),
          'menu_name' => __( 'WPO Portfolios', TEXTDOMAIN ),
      );

      $args = array(
          'labels' => $labels,
          'hierarchical' => true,
          'description' => 'List Portfolio',
          'supports' => array( 'title', 'editor', 'thumbnail' ),
          'taxonomies' => array( 'Portfolio_category','skills' ),
          'public' => true,
          'show_ui' => true,
          'show_in_menu' => true,
          'menu_position' => 5,
          'menu_icon' => FRAMEWORK_ADMIN_IMAGE_URI.'icon/admin_ico_portfolio.png',
          'show_in_nav_menus' => false,
          'publicly_queryable' => true,
          'exclude_from_search' => false,
          'has_archive' => true,
          'query_var' => true,
          'can_export' => true,
          'rewrite' => true,
          'capability_type' => 'post'
      );
      register_post_type( 'portfolio', $args );

      //Add Port folio Skill
      // Add new taxonomy, make it hierarchical like categories
      //first do the translations part for GUI
      $labels = array(
        'name' => __( 'Skills', TEXTDOMAIN ),
        'singular_name' => __( 'Skill', TEXTDOMAIN ),
        'search_items' =>  __( 'Search Skills',TEXTDOMAIN ),
        'all_items' => __( 'All Skills',TEXTDOMAIN ),
        'parent_item' => __( 'Parent Skill',TEXTDOMAIN ),
        'parent_item_colon' => __( 'Parent Skill:',TEXTDOMAIN ),
        'edit_item' => __( 'Edit Skill',TEXTDOMAIN ),
        'update_item' => __( 'Update Skill',TEXTDOMAIN ),
        'add_new_item' => __( 'Add New Skill',TEXTDOMAIN ),
        'new_item_name' => __( 'New Skill Name',TEXTDOMAIN ),
        'menu_name' => __( 'Skills',TEXTDOMAIN ),
      );
      // Now register the taxonomy
      register_taxonomy('Skills',array('portfolio'),
          array(
              'hierarchical' => true,
              'labels' => $labels,
              'show_ui' => true,
              'show_admin_column' => true,
              'query_var' => true,
              'show_in_nav_menus'=>false,
              'rewrite' => array( 'slug' => 'skills'
          ),
      ));
  }
  add_action( 'init','wpo_create_type_portfolio' );
}