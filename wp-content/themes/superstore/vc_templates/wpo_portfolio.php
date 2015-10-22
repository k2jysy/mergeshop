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

extract( shortcode_atts( array(
	'template' => '',
	'count'=>-1,
	'columns_count'=>'4'
), $atts ) );

switch ($columns_count) {
	case '4':
		$class_column='col-sm-3';
		break;
	case '3':
		$class_column='col-sm-4';
		break;
	case '2':
		$class_column='col-sm-6';
		break;
	default:
		$class_column='col-sm-12';
		break;
}

$portfolio_skills = get_terms('Skills',array('orderby'=>'id'));

$args = array(
	'post_type' => 'portfolio',
	'paged' => $paged,
	'posts_per_page'=>$count
);
$portfolio = new WP_Query($args);

?>

<div class="teambox">
	<div class="he-wrap">
       <img src="http://localhost:8888/wordpress/college/wp-content/uploads/2014/01/portfolio-10.png" width="250" height="250" alt="" />
        <div class="he-view">
            <div class="bg">
                <div class="center-bar">
                    <a href="#" class="twitter"></a>
                    <a href="#" class="facebook"></a>
                    <a href="#" class="google"></a>
                </div>
            </div>
        </div>
	</div><!-- end he wrap -->
    <h3>Lara CROFT</h3>
    <p>Duis neque nisi, dapibus sed mattis et quis, rutrum accumsan sed. Suspendisse eu varius nibh. Suspendapibus sed mattis quis.</p>
</div>