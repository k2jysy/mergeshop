<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Brainweb  Team < support@brainweb.vn>
 * @copyright  Copyright (C) 2014 brainweb.vn. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.brainweb.vn
 * @support  http://www.brainweb.vn/support/forum.html
 */

class WPO_Shortcode_Submenu extends WPO_Shortcode_Base{

		public function __construct( ){
			// add hook to convert shortcode to html.
			$this->name = str_replace( 'wpo_shortcode_','',strtolower( __CLASS__ ) );
			$this->key = 'wpo_'.$this->name;

			add_shortcode( $this->key  ,  array($this,'render') );

			parent::__construct( );
		}

		/**
		 * $data format is object field of megamenu_widget record.
		 */
		public function getButton( $data=null ){
			$button = array(
				'icon'	 => 'content',
				'title' => $this->l( 'Sub Menu' ),
				'desc'  => $this->l( '' ),
				'name'  => $this->name
			);
			return $button;
		}

		public function getOptions( ){
		    $this->options[] = array(
		        'label' 	=> __('Title', TEXTDOMAIN),
		        'id' 		=> 'title',
		        'type' 		=> 'input',
		        'explain'	=> __( 'Put Title Here', TEXTDOMAIN ),
		        'default'	=> '',
		        'hint'		=> '',
	        );

	        $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
	        $option = array();
	        foreach ($menus as $menu) {
	        	$option[$menu->term_id]=$menu->name;
	        }

	        $this->options[] = array(
		        'label' 	=> __('Menu', TEXTDOMAIN),
		        'id' 		=> 'menu',
		        'type' 		=> 'select',
		        'explain'	=> __( 'Select Menu', TEXTDOMAIN ),
		        'default'	=> '',
		        'options' 	=> $option,
		        'hint'		=> '',
	        );

	        $this->options[] = array(
		        'label' 	=> __('Addition Class', TEXTDOMAIN),
		        'id' 		=> 'class',
		        'type' 		=> 'input',
		        'explain'	=> __( 'Using to make own style', TEXTDOMAIN ),
		        'default'	=> '',
		        'hint'		=> '',
	        );
		}
	}
?>