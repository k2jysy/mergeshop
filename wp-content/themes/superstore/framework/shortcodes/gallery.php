<?php

	class WPO_Shortcode_Gallery extends WPO_Shortcode_Base{

		public function __construct( ){
			// add hook to convert shortcode to html.
			$this->name = str_replace( 'wpo_shortcode_','',strtolower( __CLASS__ ) );
			$this->key = 'wpo_'.$this->name;
			$this->excludedMegamenu = true;
			add_shortcode( $this->key  ,  array($this,'render') );
			parent::__construct( );
		}

		/**
		 * $data format is object field of megamenu_widget record.
		 */
		public function getButton( $data=null ){
			$button = array(
				'icon'	 => 'gallery',
				'title' => $this->l( 'Images Gallery' ),
				'desc'  => $this->l( 'Display List of Images' ),
				'name'  => $this->name
			);

			return $button;
		}

		public function getOptions( ){
		    $this->options[] = array(
		        'label' 	=> __('Limited Post', TEXTDOMAIN),
		        'id' 		=> 'limit',
		        'type' 		=> 'text',
		        'explain'	=> $this->l( 'Enter Vimeo Link or Youtube Here' ),
		        'default'	=> '6',
		        'hint'		=> '',
	        );

	         $this->options[] = array(
		        'label' 	=> __('Addition Class', TEXTDOMAIN),
		        'id' 		=> 'class',
		        'type' 		=> 'text',
		        'explain'	=> $this->l( 'Using to make own style' ),
		        'default'	=> '',
		        'hint'		=> '',
	        );
		}

		public function render( $attrs, $content="" ){
			return '<div>'.$attrs['style'].'</div>';
		}
	}
?>