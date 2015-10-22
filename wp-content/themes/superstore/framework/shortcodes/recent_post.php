<?php

	class WPO_Shortcode_Recent_post extends WPO_Shortcode_Base{

		public function __construct( ){
			// add hook to convert shortcode to html.
			$this->name = str_replace( 'wpo_shortcode_','',strtolower( __CLASS__ ) );
			$this->key = 'wpo_'.$this->name;
			add_shortcode( $this->key  ,  array($this,'render') );
			$this->excludedMegamenu = true;
			parent::__construct( );
		}

		/**
		 * $data format is object field of megamenu_widget record.
		 */
		public function getButton( $data=null ){
			$button = array(
				'icon'	 => 'recentpost',
				'title' => $this->l( 'Recent Post' ),
				'desc'  => $this->l( 'Display List of Newest Post' ),
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
		        'label' 	=> __('Display Date', TEXTDOMAIN),
		        'id' 		=> 'enable_date',
		        'type' 		=> 'select',
		        'explain'	=> $this->l( 'Display posted data' ),
		        'default'	=> '0',
		        'options'   => array( 0=>$this->l('No'), 1=>$this->l('Yes') ),
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
		}

		public function render( $attrs, $content="" ){
			return '<div>'.$attrs['style'].'</div>';
		}
	}
?>