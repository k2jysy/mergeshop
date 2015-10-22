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



if( !defined('FRAMEWORK_PATH') )
	define( 'FRAMEWORK_PATH', get_template_directory() . '/framework/' );
if( !defined('FRAMEWORK_MEGAMENU') )
  define( 'FRAMEWORK_MEGAMENU', FRAMEWORK_PATH.'megamenu/' );
if( !defined('FRAMEWORK_SHORTCODE') )
  define( 'FRAMEWORK_SHORTCODE', FRAMEWORK_PATH.'shortcodes/' );
if( !defined('FRAMEWORK_LANGUAGE') )
  define( 'FRAMEWORK_LANGUAGE', get_template_directory().'/languages' );
if( !defined('FRAMEWORK_WIDGETS') )
  define( 'FRAMEWORK_WIDGETS', FRAMEWORK_PATH.'widgets/' );
if( !defined('FRAMEWORK_SHORTCODE') )
  define( 'FRAMEWORK_SHORTCODE', FRAMEWORK_PATH.'shortcodes/' );
if( !defined('FRAMEWORK_POSTTYPE') )
  define( 'FRAMEWORK_POSTTYPE', FRAMEWORK_PATH.'types/' );
if( !defined('FRAMEWORK_TEMPLATES') )
  define( 'FRAMEWORK_TEMPLATES', get_template_directory().'/templates/' );
if( !defined('FRAMEWORK_WOOCOMMERCE_WIDGETS') )
  define( 'FRAMEWORK_WOOCOMMERCE_WIDGETS', get_template_directory().'/woocommerce/widgets/' );
if( !defined('FRAMEWORK_TEMPLATES_PAGEBUILDER') )
  define( 'FRAMEWORK_TEMPLATES_PAGEBUILDER', get_template_directory().'/vc_templates/' );
if( !defined('FRAMEWORK_ADMIN_TEMPLATE_PATH') )
  define( 'FRAMEWORK_ADMIN_TEMPLATE_PATH', get_template_directory() . '/framework/admin/templates/' );
if( !defined('FRAMEWORK_PLUGINS') )
  define( 'FRAMEWORK_PLUGINS', get_template_directory().'/framework/plugins/' );
if( !defined('FRAMEWORK_XMLPATH') )
  define( 'FRAMEWORK_XMLPATH', get_template_directory().'/sub/customize/' );
if( !defined('FRAMEWORK_CUSTOMZIME_STYLE') )
  define( 'FRAMEWORK_CUSTOMZIME_STYLE', FRAMEWORK_XMLPATH.'assets/' );

define( 'WPO_FRAMEWORK_CUSTOMZIME_STYLE', FRAMEWORK_XMLPATH.'assets/' );

// URI
if( !defined('FRAMEWORK_CUSTOMZIME_STYLE_URI') )
  define( 'FRAMEWORK_CUSTOMZIME_STYLE_URI', get_template_directory_uri().'/sub/customize/assets/' );
if( !defined('FRAMEWORK_ADMIN_STYLE_URI') )
  define( 'FRAMEWORK_ADMIN_STYLE_URI', get_template_directory_uri().'/framework/admin/assets/' );
if( !defined('FRAMEWORK_ADMIN_IMAGE_URI') )
  define( 'FRAMEWORK_ADMIN_IMAGE_URI', FRAMEWORK_ADMIN_STYLE_URI.'images/' );
if( !defined('FRAMEWORK_STYLE_URI') )
  define( 'FRAMEWORK_STYLE_URI', get_template_directory_uri().'/framework/assets/' );

global $wpdb;
define( 'DB_PREFIX', $wpdb->prefix  );
//echo FRAMEWORK_POSTTYPE;
require_once ( FRAMEWORK_PATH . 'functions/functions.php');
require_once ( FRAMEWORK_PATH . 'classes/core/metabox.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/params.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/plugin-activation.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/megamenu-config.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/megamenu.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/megamenu-vertical.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/megamenu-sub.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/megamenu-offcanvas.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/megamenu-widget.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/shortcodebase.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/template.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/options.php' );
require_once ( FRAMEWORK_PATH . 'classes/core/pagebuilder.php' );
require_once ( FRAMEWORK_PATH . 'classes/megamenu-editor.php' );
require_once ( FRAMEWORK_PATH . 'classes/multiple_sidebars.php' );
require_once ( FRAMEWORK_PATH . 'classes/admin.php' );
require_once ( FRAMEWORK_PATH . 'classes/framework.php' );
require_once ( FRAMEWORK_PATH . 'classes/woocommerce.php' );
require_once ( FRAMEWORK_PATH . 'classes/shortcodes.php' );
require_once ( FRAMEWORK_PATH . 'classes/livetheme.php' );
require_once ( FRAMEWORK_PATH . 'classes/widget.php' );


//$WPO_SHORTCODE =   WPO_Shortcodes::getInstance();