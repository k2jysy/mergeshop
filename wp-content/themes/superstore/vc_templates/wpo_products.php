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
	'number'=>-1,
	'columns_count'=>'4',
	'icon' => '',
	'el_class' => '',
	'type'=>'',
	'skin'=>'carousel'
), $atts ) );
switch ($columns_count) {
	case '6':
		$class_column='col-md-2 col-sm-6';
		break;
	case '4':
		$class_column='col-md-3 col-sm-6';
		break;
	case '3':
		$class_column='col-md-4 col-sm-4';
		break;
	case '2':
		$class_column='col-md-6 col-sm-6';
		break;
	default:
		$class_column='col-md-12 col-sm-12';
		break;
}

if($type=='') return;

global $woocommerce;

$_id = wpo_makeid();
$_count = 1;
$args = array(
	'post_type' => 'product',
	'posts_per_page' => $number,
	'post_status' => 'publish'
);
switch ($type) {
	case 'best_selling':
		$args['meta_key']='total_sales';
		$args['orderby']='meta_value_num';
		$args['ignore_sticky_posts']   = 1;
		$args['meta_query'] = array();
        $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
        $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
		break;
	case 'featured_product':
		$args['ignore_sticky_posts']=1;
		$args['meta_query'] = array();
		$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
		$args['meta_query'][] = array(
                     'key' => '_featured',
                     'value' => 'yes'
                 );
		$query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
		break;
	case 'top_rate':
		add_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
		$args['meta_query'] = array();
        $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
        $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
		break;
	case 'recent_product':
		$args['meta_query'] = array();
        $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
		break;
}

$loop = new WP_Query( $args );

if ( $loop->have_posts() ) : ?>
	<?php $_total = $loop->found_posts; ?>
	<div class="woocommerce<?php echo (($el_class!='')?' '.$el_class:''); ?>">
		<?php wc_get_template( 'widget-products/'.$skin.'.php' , array( 'loop'=>$loop,'columns_count'=>$columns_count,'class_column'=>$class_column,'posts_per_page'=>$number,'two_rows'=>true ) ); ?>
	</div>
<?php endif; ?>

<?php wp_reset_query(); ?>