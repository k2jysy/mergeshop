<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     WPOpal  Team <wpopal@gmail.com, support@wpopal.com>
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

class WPO_TinyMCE_Buttons {
	function __construct() {
    	add_action( 'init', array(&$this,'init') );
    }
    function init() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;
		if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array(&$this, 'add_plugin') );
			add_filter( 'mce_buttons', array(&$this,'register_button') );
		}
    }
	function add_plugin($plugin_array) {
	   $plugin_array['symple_shortcodes'] = get_stylesheet_directory_uri().'/mce/js/symple_shortcodes_tinymce.js';
	   return $plugin_array;
	}
	function register_button($buttons) {
	   array_push($buttons, "wpo_shortcodes_button");
	   return $buttons;
	}
}
$sympleshortcode = new WPO_TinyMCE_Buttons;