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
 * @website  http:/wpopal.com
 * @support  http://wpopal.com
 */

get_header( of_get_option('header','') );

if(is_single()){
	$config = $wpo->configLayout(of_get_option('woocommerce-single-layout','0-1-0'));
	wc_get_template( 'single-product.php' , array( 'config'=>$config ) );
}else{
	$config = $wpo->configLayout(of_get_option('woocommerce-archive-layout','0-1-0'));
	wc_get_template( 'archive-product.php' , array( 'config' => $config ) );
}

get_footer( of_get_option('footer','') );