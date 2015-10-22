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
if(class_exists('WooCommerce')){
	class WPO_Shortcode_Products extends WPO_Shortcode_Base{

		public function __construct( ){
			// add hook to convert shortcode to html.
			$this->name = str_replace( 'wpo_shortcode_','',strtolower( __CLASS__ ) );
			$this->key = 'wpo_'.$this->name;
			parent::__construct( );
		}

		/**
		 * $data format is object field of megamenu_widget record.
		 */
		public function getButton( $data=null ){
			$button = array(
				'icon'	 => 'image',
				'title' => $this->l( 'Products' ),
				'desc'  => $this->l( 'Display Products' ),
				'name'  => $this->name
			);

			return $button;
		}

		public function getOptions( ){
		    $this->options[] = array(
		        'label' 	=> $this->l('Type'),
		        'id' 		=> 'type',
		        'type' 		=> 'select',
		        'options'   => array(
		        		'top_rate' => 'Top Rate',
		        		'recent_product' => 'Recent Products',
		        		'featured_product' => 'Featured Products',
		        		'best_selling' => 'Best Selling'
		        	)
	        );

	        $this->options[] = array(
		        'label' 	=> $this->l('Columns count'),
		        'id' 		=> 'columns_count',
		        'type' 		=> 'select',
		        'options'   => array('4'=>4, '3'=>3, '2'=>2, '1'=>1)
	        );
	        $this->options[] = array(
				"type" => "text",
				"label" => __("Number of products to show", TEXTDOMAIN),
				"id" => "number",
				"default" => '4'
			);
		}
	}
}