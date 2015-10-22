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

	class WPO_Shortcode_GMap extends WPO_Shortcode_Base{

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
				'icon'	 => 'gmap',
				'title' => $this->l( 'Google Map' ),
				'desc'  => $this->l( 'Display Banner Image Or Ads Banner' ),
				'name'  => $this->name
			);

			return $button;
		}

		public function getOptions( ){
		    
		    $this->options[] = array(
		        'label' 	=> __('Count Item', TEXTDOMAIN),
		        'id' 		=> 'count',
		        'type' 		=> 'gmap',
		        'explain'	=> '',
		        'default'	=> '21.0173222,105.78405279999993',
		        'hint'		=> '',
	        );

	         $this->options[] = array(
		        'label' 	=> __('Map Height', TEXTDOMAIN),
		        'id' 		=> 'height',
		        'type' 		=> 'text',
		        'explain'	=> '',
		        'default'	=> '300'
	        );

	        $this->options[] = array(
		        'label' 	=> __('Map type', TEXTDOMAIN),
		        'id' 		=> 'type',
		        'type' 		=> 'select',
		        'explain'	=> '',
		        'options'   => array(
		        					'ROADMAP'=>'roadmap',
		        					'HYBRID'=>'hybrid',
		        					'SATELLITE'=>'satellite',
		        					'TERRAIN'=>'terrain'
		        				),
		        'default'	=> 'ROADMAP'
	        );

	        $this->options[] = array(
		        'label' 	=> __('Map type', TEXTDOMAIN),
		        'id' 		=> 'zoom',
		        'type' 		=> 'select',
		        'explain'	=> '',
		        'options'   => array(
		        					'1'=>'1',
		        					'2'=>'2',
		        					'3'=>'3',
		        					'4'=>'4',
		        					'5'=>'5',
		        					'6'=>'6',
		        					'7'=>'7',
		        					'8'=>'8',
		        					'9'=>'9',
		        					'10'=>'10',
		        					'11'=>'11',
		        					'12'=>'12',
		        					'13'=>'13',
		        					'14'=>'14 Default',
		        					'15'=>'15',
		        					'16'=>'16',
		        					'17'=>'17',
		        					'18'=>'18',
		        					'19'=>'19',
		        					'20'=>'20'
		        				),
		        'default'	=> '14'
	        );
		}
	}
?>