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

	class WPO_Shortcode_Accordion extends WPO_Shortcode_Base{

		public function __construct( ){
			// add hook to convert shortcode to html.
			$this->name = str_replace( 'wpo_shortcode_','',strtolower( __CLASS__ ) );
			$this->key = 'wpo_'.$this->name;
			$this->excludedMegamenu = true;
			parent::__construct( );
		}

		/**
		 * $data format is object field of megamenu_widget record.
		 */
		public function getButton( $data=null ){
			$button = array(
				'icon'	 => 'accordion',
				'title' => $this->l( 'Accoridion List' ),
				'desc'  => $this->l( 'Display Accordion List Supported By Bootstrap 3' ),
				'name'  => $this->name
			);
			return $button;
		}

		public function getOptions( ){
		    $this->options[] = array(
		        'label' 	=> __('Input 1', TEXTDOMAIN),
		        'id' 		=> 'style',
		        'type' 		=> 'radio',
		        'options' 	=> array(
		                        'default'=>'default',
		                        'cyan'   =>'cyan',
		                        'yellow' =>'yellow'
		    ));

		    $this->options[] = array(
		        'label' => __('Input 2', TEXTDOMAIN),
		        'id' => 'skin1',
		        'type' => 'text',
		        'content' 	=>true,
		    );

		    $this->options[] = array(
		        'label' 	=> __('Input 1', TEXTDOMAIN),
		        'id' 		=> 'checked',
		        'type' 		=> 'posttypes'
		    );
		}

		public function render( $attrs, $content="" ){
			return '<div>'.$attrs['style'].'</div>';
		}
	}
?>