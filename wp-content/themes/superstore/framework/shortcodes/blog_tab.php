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

	class WPO_Shortcode_Blog_Tab extends WPO_Shortcode_Base{

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
				'icon'	 => 'blog-tab',
				'title' => $this->l( 'Blog Tabs' ),
				'desc'  => $this->l( 'Display Banner Image Or Ads Banner' ),
				'name'  => $this->name
			);

			return $button;
		}

		public function getOptions( ){
		    $this->options[] = array(
		        'label' 	=> __('Category Parent', TEXTDOMAIN),
		        'id' 		=> 'category',
		        'type' 		=> 'category_parent',
		        'explain'	=> __( 'Put Your Image Link Here', TEXTDOMAIN ),
		        'default'	=> '',
		        'hint'		=> ''
		        );

		    $this->options[] = array(
		        'label' 	=> __('Count Item', TEXTDOMAIN),
		        'id' 		=> 'count',
		        'type' 		=> 'text',
		        'explain'	=> '',
		        'default'	=> '',
		        'hint'		=> '',
	        );

	        $this->options[] = array(
		        'label' 	=> __('Addition Class', TEXTDOMAIN),
		        'id' 		=> 'class',
		        'type' 		=> 'text',
		        'explain'	=> __( 'Using to make own style', TEXTDOMAIN ),
		        'default'	=> '',
		        'hint'		=> '',
	        );
	        $this->options[] = array(
	        	'label' 	=> '',
		        'id' 		=> 'id',
		        'type' 		=> 'hidden_id',
		        'default'	=> '',
		        'hint'		=> '',
	        );
		}
	}
?>