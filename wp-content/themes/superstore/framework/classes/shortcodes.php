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
if(!class_exists('WPO_Shortcodes')){
class WPO_Shortcodes{

	protected $shortcodes = array();

	public static function getInstance(){
		static $_instance;
		if( !$_instance ){
			$_instance = new WPO_Shortcodes();
		}
		return $_instance;
	}

	public function loadShortcodes(){
		$path = FRAMEWORK_SHORTCODE;
		$files = glob( FRAMEWORK_SHORTCODE.'*.php' );

		foreach( $files as $file ){
			require_once( $file );
			$tmp = str_replace( ".php", "", basename($file) );
			$class = 'WPO_Shortcode_'.ucfirst($tmp);

			if( class_exists($class) ){
				$this->shortcodes[$tmp] = new $class;
			}
		}
	}

	public function getButtons( $filter='menu' ){
		if( !$this->shortcodes ){
			$this->loadShortcodes();
		}
		$output = array();
		foreach( $this->shortcodes as $shortcode  ){

			if( $shortcode->isExcludedMenu() && $filter=='menu' ){
				continue;
			}
			$output[] = $shortcode->getButton();
		}
		return $output;
	}

	public function getShortcode( $type ){
		if(  isset($this->shortcodes[$type]) ){
			return $this->shortcodes[$type];
		}else {
			$file = ( FRAMEWORK_SHORTCODE.$type.'.php' );
			if( file_exists($file) ){

				require_once( $file );
				$class = 'WPO_Shortcode_'.trim(ucfirst($type));
				$tmp = str_replace( ".php", "", basename($file) );
				if( class_exists($class) ){
					$this->shortcodes[$tmp] = new $class;
					return $this->shortcodes[$tmp];
				}
			}
		}
 		return null;
	}

	public function getButtonByType( $type, $data=array() ){

		if( $s=$this->getShortcode($type) ){

			$button =  ($s->getButton( $data ));

			return '
				<div class="wpo-shorcode-button btn btn-default">
					<div class="wpo-icon wpo-icon-content"></div>
					<div class="content">
						<h5 class="shortcode-title">'.$button['title'].'<br>(<span>'.$data->name.'</span>)</h5>
						<em class="desc">'.$button['desc'].'</em>
					</div>
				</div>
			';
		}
		return __( 'No button for this',TEXTDOMAIN );
	}

	public function getLayout( $tpl ){

	}

	public function renderContent($type, $data ){
		if( $s=$this->getShortcode($type) ){
			return $s->render( $data );
		}
		return __( 'No data for this',TEXTDOMAIN );
	}
}
}
?>