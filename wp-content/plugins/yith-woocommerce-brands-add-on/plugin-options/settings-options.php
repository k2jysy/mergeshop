<?php
/**
 * General settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Brands Add-on
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCBR' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wcbr_general_settings',
	array(
		'settings' => array(

			'general-options' => array(
				'title' => __( 'General', 'yith-wcbr' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wcbr_general_options'
			),

			'general-brand-label' => array(
				'id'        => 'yith_wcbr_brands_label',
				'name'      => __( 'Brand label', 'yith-wcbr' ),
				'type'      => 'text',
				'desc'      => __( 'Label used for "Brand" link', 'yith-wcbr' ),
				'default'   => __( 'Brand:', 'yith-wcbr' ),
				'css'       => 'min-width:300px;'
			),

			'general-options-end' => array(
				'type'  => 'sectionend',
				'id'    => 'yith_wcbr_general_options'
			),
		)
	)
);