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
if(!class_exists('WPO_Widget')){
abstract class WPO_Widget extends WP_Widget{

	protected $widgetName='';
	/**
	 * this method check overriding layout path in current template
	 */
	public function renderLayout($layout='default' ){
		$output='';
		$tpl = FRAMEWORK_TEMPLATES .'widgets/'.$this->widgetName.'/'.$layout.'.php';
		$tpl_default = FRAMEWORK_WIDGETS .$this->widgetName.'/tpl/'.$layout.'.php';
		if(  is_file($tpl) ){
			return $tpl;
		}else if( is_file($tpl_default) ){
			return $tpl_default;
		}
	}

	public function selectLayout(){
		$tml_default 	= glob(FRAMEWORK_WIDGETS .$this->widgetName.'/tpl/*.php');
		$tml_new 		= glob(FRAMEWORK_TEMPLATES .'widgets/'.$this->widgetName.'/*.php');
		$layout = array_merge($tml_default,$tml_new);
		foreach ($layout as $key => $value) {
			$layout[$key] = basename($value,'.php');
		}
		$layout = array_unique($layout);
		return $layout;
	}

}
}