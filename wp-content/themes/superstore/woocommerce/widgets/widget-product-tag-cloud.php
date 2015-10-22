<?php
/**
 * Tag Cloud Widget
 *
 * @author 		WooThemes
 * @category 	Widgets
 * @package 	WooCommerce/Widgets
 * @version 	2.1.0
 * @extends 	WC_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPO_Widget_Product_Tag_Cloud extends WC_Widget_Product_Tag_Cloud {

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$current_taxonomy = $this->_get_current_taxonomy($instance);

		if ( empty( $instance['title'] ) ) {
			$tax   = get_taxonomy( $current_taxonomy );
			$title = apply_filters( 'widget_title', $tax->labels->name, $instance, $this->id_base );
		} else {
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		}

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="tagcloud">';

		wp_tag_cloud( apply_filters( 'woocommerce_product_tag_cloud_widget_args', array( 'taxonomy' => $current_taxonomy ) ) );

		echo "</div>";

		echo $after_widget;
	}
}

register_widget( 'WPO_Widget_Product_Tag_Cloud' );