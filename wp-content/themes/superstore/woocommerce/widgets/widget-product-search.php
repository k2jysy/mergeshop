<?php
/**
 * Product Search Widget
 *
 * @author 		WooThemes
 * @category 	Widgets
 * @package 	WooCommerce/Widgets
 * @version 	2.1.0
 * @extends 	WC_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPO_Widget_Product_Search extends WC_Widget_Product_Search {
	
	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title = $instance['title'];
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		get_product_search_form();

		echo $after_widget;
	}
}

register_widget( 'WPO_Widget_Product_Search' );