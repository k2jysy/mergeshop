<?php

	class WPO_Shortcode_Tab extends WPO_Shortcode_Base{

		public function __construct( ){

			// add hook to convert shortcode to html.
			 $this->name = str_replace( 'wpo_shortcode_','',strtolower( __CLASS__ ) );
 		 	 $this->key = 'wpo_'.$this->name;
			 add_shortcode( $this->key  ,  array($this,'render') );
			 $this->excludedMegamenu = true;
		}

		public function getButton( $data=null ){
			return array(
				'icon'	=> 'tab',
				'title' => $this->l( 'Tab List' ),
				'desc'  => $this->l( 'Display Tab List Supported By Bootstrap 3' ),
				'name'  => $this->name
			);
		}

		public function getOptions( ){

		}

		public function getAttrs( $attrs=array() ){

		}

		/**
		 *
		 */
		public function render( $atts, $content="" ){

		}
	}


?>