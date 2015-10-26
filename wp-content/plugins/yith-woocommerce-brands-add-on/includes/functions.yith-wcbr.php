<?php
/**
 * Utility functions
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Brands
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCBR' ) ) {
	exit;
} // Exit if accessed directly

if( ! function_exists( 'yith_wcbr_get_template' ) ){
	/**
	 * Get template for Brands plugin
	 *
	 * @param $filename string Template name (with or without extension)
	 * @param $args mixed Array of params to use in the template
	 * @param $section string Subdirectory where to search
	 */
	function yith_wcbr_get_template( $filename, $args = array(), $section = '' ){
		$ext = strpos( $filename, '.php' ) === false ? '.php' : '';

		$template_name      = $section . '/' . $filename . $ext;
		$template_path      = WC()->template_path() . 'yith-wcbr/';
		$default_path       = YITH_WCBR_DIR . 'templates/';

		if( defined( 'YITH_WCBR_PREMIUM' ) ){
			$premium_template   = str_replace( '.php', '-premium.php', $template_name );
			$located_premium    = wc_locate_template( $premium_template, $template_path, $default_path );
			$template_name      = file_exists( $located_premium ) ?  $premium_template : $template_name;
		}

		wc_get_template( $template_name, $args, $template_path, $default_path );
	}
}

if( ! function_exists( 'yith_wcbr_add_slider_post_class' ) ){
	/**
	 * Add classes to posts for sliders
	 *
	 * @param $classes mixed Array of available class
	 *
	 * @return mixed Filtered array of classes
	 * @since 1.0.0
	 */
	function yith_wcbr_add_slider_post_class( $classes ){
		$classes[] = 'swiper-slide';

		return $classes;
	}
}