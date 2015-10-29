<?php
if (!defined('ABSPATH'))
    die("Can't load this file directly");

//Style
$result.='<div id="displayProduct" class="product_box dp-section dp-group woocommerce">';
// Define Variable
$item = 0;
$enable = explode(',', $sortElement);
while ($r->have_posts()) {
    $r->the_post();
    global $product;
    $first = '';
        $link_start = '<a href="' . get_permalink() . '" title="' . get_the_title() . '">';
        $link_end = '</a>';
        $link_thumb_start = '<a href="' . get_permalink() . '"  title="' . get_the_title() . '" class="dp-product-image">';
    if ($item % $columns == 0) {
        $first = 'firstcol';
    }

    $result.='<div class="dp-col dp-col_1_of_' . $columns . ' ' . $first . '">';
    $result.=dp_get_sale_flash();
    $result.='<div class="he-wrap tpl4">';
    $result.='<div class="he-view">';
    $result.='<div class="bg">';
    $result.='<div class="a0" data-animate="' . $dpanimatehover . '"></div>';
    $result.='</div>'; //bg
    $result.='<div class="content">';
    if (in_array( 'title',$enable)) {
        $result.='<h2 class="product-name info-title a2" data-animate="' . $dpanimatehover_productname . '">' . $link_start . get_the_title() . $link_end . '</h2>';
    }
    if (in_array( 'excerpt',$enable)) {
        $result.= '<p class="dp-grid-excerpt excerpt a2" data-animate="' . $dpanimatehover_star . '">' . dp_excerpt_max_charlength($dp_excerpt['lenght']) . '</p>';
    }
    if (in_array( 'content',$enable)) {
        $result.='<p class="dp-grid-excerpt excerpt a2" data-animate="' . $dpanimatehover_star . '">' . wp_trim_words( get_the_content(), $dp_excerpt['lenght'] ) . '</p>';
    }
    if (in_array( 'star',$enable)) {
        $result.='<div class="dp-box-rating star a2" data-animate="' . $dpanimatehover_star . '">' . $product->get_rating_html() . '</div>';
    }
    if(in_array( 'quickview',$enable)){
            $result.='<span class="dpquickview dp_quickview_button" data-id="'.  get_the_ID().'"><img src="'.DP_DIR.'/assets/images/quickview.png"></span>';
        }
    if (in_array( 'price',$enable)) {
        $result.='<div class="dp-box-price price a2" data-animate="' . $dpanimatehover_price . '">' . $product->get_price_html() . '</div>';
    }
    if (in_array( 'button',$enable)) {
        $result.='<div class="dp-box-button more a2" data-animate="fadeInUp">';
            switch ($dp_addtocartbutton['type']) {
//                case 'buttonquantity':
//                    ob_start();
//                    woocommerce_template_single_add_to_cart();
//                    $result.= ob_get_contents();
//                    ob_end_clean();
//                    break;
                case 'productDetail':
                    $result.='<div class="dp-grid-button">' . dp_add_to_cart_productdetail(get_permalink(),$addtocarttext) . '</div>';
                    break;
                case 'customButton':
                    $result.='<div class="dp-grid-button">' . dp_add_to_cart_customButton($addtocarturl,$addtocarttext) . '</div>';
                    break;
                case 'customText':
                    $result.='<div class="dp-grid-button">' . dp_add_to_cart_customText($addtocarturl,$addtocarttext) . '</div>';
                    break;
                default:
                    $result.='<div class="dp-grid-button">' . dp_add_to_cart() . '</div>';
                    break;
            }
   
        $result.='</div>';
    }
    $result.='</div>'; //content
    $result.='</div>'; // He View
    if (in_array( 'image',$enable)) {
        //$result.=$link_thumb_start;
        $result.=dp_get_image_box($dp_image);
        // $result.=$link_end;
    }
    $result.='<div style="clear: both;"></div>';
    $result.='</div>'; //he-wrap


    $result.='</div>'; //dp-col
    $item++;
}
$result.='</div>'; //#displayProduct
?>
