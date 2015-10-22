<?php

	class WPO_Shortcode_Image extends WPO_Shortcode_Base{

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
				'icon'	 => 'image',
				'title' => $this->l( 'Single Image' ),
				'desc'  => $this->l( 'Display Banner Image Or Ads Banner' ),
				'name'  => $this->name
			);

			return $button;
		}

		public function getOptions( ){
		    $this->options[] = array(
		        'label' 	=> __('Image Link', TEXTDOMAIN),
		        'id' 		=> 'link',
		        'type' 		=> 'editor',
		        'explain'	=> __( 'Put Your Image Link Here', TEXTDOMAIN ),
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
		}

		public function render( $attrs, $content="" ){
			return '<div>'.$attrs['style'].'</div>';
		}
	}
?>