<?php
if (!defined('ABSPATH'))
    die("Can't load this file directly");

if (!function_exists('displayProduct_shorcode')) {
    function displayProduct_shorcode($atts, $content = null) {
        extract(shortcode_atts(array(
            'id' => 0,

            
            'trimwords'=>20,

            'title' => 'show',
            'excerpt' => 'show',
            'image' => 'show',
            'price' => 'show',
            'button' => 'show',
            'star' => 'show',
            'sku' => 'show',
            'metacategory' => 'show',
            'metatag' => 'show',
            'featured' => 'show',
            'outofstock' => 'show',
            'sale' => 'show',
            'link' => 'show',
            
            'arrowanddot' => 1,
            'arrowstyle'=>  1,
            'arrowposition'=>  'topRight',
            'dpanimatehover' => 'disable',
            'dpanimatehover_productname' => 'fadeInDown',
            'dpanimatehover_star' => 'rotateInLeft',
            'dpanimatehover_price' => 'rotateInRight',
           //'skin' => 'default',
            'font'=>'Droid+Sans',
            'tablebackground'=> '#ffffff',
            'tableheadbackground'=> '#DBDADA',
            'tableheadtextcolor'=>'#ffffff',
            'tablerowhovercolor'=> '#fafafa',
            'backgroundcolor'=> '#fefefe',
            'bordercolor'=> '#fc5b5b',
            'productnamecolor'=> '#444444',
            'productnamehovercolor'=> '#A88F5C',
            'pricecolor'=> '#444444',
            'textcolor'=> '#444444',
            'linkcolor'=> '#fc5b5b',
            'linkhovercolor'=>'#A88F5C',
            'buttoncolor'=> '#fc5b5b',
            'buttonhovercolor'=> '#444444',
            'featuredcolor'=>'#ffd347',
            'salecolor'=>'#fc5b5b',
            'addtocartbutton'=>'default',
            'addtocarturl'=> '',
            'addtocarttext'=> '',
            'grid'=>'image,title,price,button',
            'list'=>'image,title,price,button',
            'box'=>'image,title,price,button',
            'table'=>'image,title,price,button',
            'carousel'=>'image,title,price,button',
            'carousel_grid'=>'image,title,price,button',
        ), $atts));
        $result = '';
        wp_reset_postdata();
        wp_reset_query();

        /*
         * Get Meta From ID
         */
        if($id){
            /*
             * Get Metadata by ID
             */
            $dp_sortElement = get_post_meta($id, 'dp_sort-element', true);
            $type           = get_post_meta($id, 'dp_select_template', true);
            $dp_title_s     = get_post_meta($id,'dp_title',true);
            $dp_image_s     = get_post_meta($id,'dp_image',true);
            $dp_excerpt_s   = get_post_meta($id,'dp_excerpt',true);
            $dp_price_s     = get_post_meta($id,'dp_price',true);
            $dp_option_s    = get_post_meta($id,'dp_option',true);
            $dp_addtocartbutton_s   = get_post_meta($id,'dp_addtocartbutton',true);
            $dp_customfield_s       = get_post_meta($id,'dp_customfield',true);
            $dp_variable_s  = get_post_meta($id,'dp_variable',true);

            /*
             * Unserialize
             */
            $dp_title   = unserialize($dp_title_s);
            $dp_image   = unserialize($dp_image_s);
            $dp_excerpt = unserialize($dp_excerpt_s);
            $dp_price   = unserialize($dp_price_s);
            $dp_addtocartbutton = unserialize($dp_addtocartbutton_s);
            $dp_customfield     = unserialize($dp_customfield_s);
            $dp_option  = unserialize($dp_option_s);


            /*
             * Set Up Value
             */
            $type= $type?$type:'grid';
            $filter= empty($dp_option['filter_select'])?'':$dp_option['filter_select'];
            if($dp_option['filter_condition']=='filterproduct'){
                $filter=$dp_option['filter_select'];
            }else{
                $filter= '';
            }
            if($dp_option['category_condition']=='customCategory'){
                $category= $dp_option['category_select'];
            }else{
                $category= 'all';
            }
            if($dp_option['tag_condition']=='customTag'){
                $tag= $dp_option['tag_select'];
            }else{
                $tag= 'all';
            }
            if($dp_option['shippingClass_condition']=='customShippingClass'){
                $shippingClass= $dp_option['shippingClass_select'];
            }else{
                $shippingClass= 'all';
            }
            $sort       =$dp_option['sort']? $dp_option['sort'] :'default';
            $perpage    =$dp_option['perpage']? $dp_option['perpage'] :20;
            $columns    =$dp_option['column']? $dp_option['column'] :3;
            $frontsorter=$dp_option['frontsorter']? $dp_option['frontsorter'] :'default';
            $pagination =$dp_option['pagination']? $dp_option['pagination'] :'default';
            $quickview  =$dp_option['quickview']? $dp_option['quickview'] :'default';
            
            $sortElement=str_replace('displayProduct-', "", $dp_sortElement);
            

        }else{
            echo 'Please add new display product template.';
        }



        global $woocommerce;
        $filter = array($filter);
        do_action('displayProductSkin',$type,$columns); // no column
        do_action('displayProductFont', $font); // no font
        do_action('displayProductFontPrint', $font); // no font
        
        // Set Up WooCommerce Query Product
        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } else if ( get_query_var('page') ) {
            $paged = get_query_var('page');
        } else{
            $paged = 1;
        }
        
        if($_GET['perpage']!="default"&&!empty($_GET['perpage'])){
            $perpage = $_GET['perpage'];
        }else{
            $perpage=$perpage;
        }
        if($_GET['orderby']!="default"&&!empty($_GET['orderby'])){
            $sort=$_GET['orderby'];
        }
        if($_GET['dp_search']&&!empty($_GET['dp_search'])){
            $dp_search=$_GET['dp_search'];
        }
        /* -------------------------
         * Sale Product
         * ------------------------- */
        if (in_array("sales", $filter)) {
            $product_ids_on_sale = wc_get_product_ids_on_sale();
            $product_ids_on_sale[] = 0;
        }

        /* -------------------------
         * Query Product 
         * ------------------------- */
        $query_args = array(
            'posts_per_page' => $perpage,
            'paged' => $paged,
            'post_status' => 'publish',
            'post_type' => 'product',
            'ignore_sticky_posts' => 1,
            's'=>$dp_search,
            'post__in' => $product_ids_on_sale
        );

        $query_args['meta_query'] = array();
        $query_args['meta_query'] = $woocommerce->query->get_meta_query();
        
        if($_GET['dppage']&&!empty($_GET['dppage'])){
            global $wp_query;
            $query_args['product_cat']=get_query_var('product_cat');
            $query_args['product_tag']=get_query_var('product_tag');
        }else{
            /* -------Default Shortcode -------*/
            
            /* -------------------------
             * In Stock Product 
             * ------------------------- */
            if (in_array("instock", $filter)) {
                $query_args['meta_query'][] =
                        array(
                            'key' => '_manage_stock',
                            'value' => "yes",
                            'compare' => '=',
                );
                $query_args['meta_query'][] = array(
                    'key' => '_stock_status',
                    'value' => "instock",
                    'compare' => '=',
                        )
                ;
            }
             /* -------------------------
              * Out Stock Product 
              * ------------------------- */
               if (in_array("outofstock", $filter)) {
                   $query_args['meta_query'][] =
                           array(
                               'key' => '_manage_stock',
                               'value' => "yes",
                               'compare' => '=',
                   );
                   $query_args['meta_query'][] = array(
                       'key' => '_stock_status',
                       'value' => "outofstock",
                       'compare' => '=',
                   );
               }
               
               /* -------------------------
                * Category 
                * ------------------------- */
               if ($dp_option['category_condition']=='customCategory') {
                   $category_query = array(
                       array(
                           'taxonomy' => 'product_cat',
                           'terms' => $category,
                           'field' => 'slug',
                           'operator' => 'IN'
                       )
                   );
                   $query_args['tax_query'][] = $category_query;
               }
               //$category='auto';
               if($category==='auto'){
                    $term = get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
                    $category_query = array(
                       array(
                           'taxonomy' => 'product_cat',
                           'terms' => $term->name,
                           'field' => 'slug',
                           'operator' => 'IN'
                       )
                   );
                   $query_args['tax_query'][] = $category_query;
               }
               
               /* -------------------------
                * Tag 
                * ------------------------- */
               if ($dp_option['tag_condition']=='customTag') {
                   $tag_query = array(
                       array(
                           'taxonomy' => 'product_tag',
                           'terms' => $tag,
                           'field' => 'slug',
                           'operator' => 'IN'
                       )
                   );
                   $query_args['tax_query'][] = $tag_query;
               }
               
               /* -------------------------
                * Shipping Class 
                * ------------------------- */
               if ($dp_option['shippingClass_condition']=='customShippingClass') {
                   $shippingClass_query = array(
                       array(
                           'taxonomy' => 'product_shipping_class',
                           'terms' => $shippingClass,
                           'field' => 'slug',
                           'operator' => 'IN'
                       )
                   );
                   $query_args['tax_query'][] = $shippingClass_query;
               }
               
               /* -------------------------
                * Featured Product 
                * ------------------------- */
               if (in_array("featured", $filter)) {
                   $query_args['meta_query'][] = array(
                       'key' => '_featured',
                       'value' => 'yes'
                   );
               }
               $query_args['tax_query'][]=array('relation' => 'AND');
               
        }//if check dppage
        
        /* -------------------------
         * Top Rated Product 
         * ------------------------- */
        if (in_array("toprate", $filter)) {
            add_filter('posts_clauses', array($woocommerce->query, 'order_by_rating_post_clauses'));
        }

        switch ($sort){
            case 'default':
                 $ordering_args = $woocommerce->query->get_catalog_ordering_args($orderby, $order);
                 $query_args['orderby'] = $ordering_args['orderby'];
                 $query_args['order'] =$ordering_args['order'];
                break;
            case 'popularity':
                add_filter('posts_clauses', array($woocommerce->query, 'order_by_rating_post_clauses'));
                break;
            case 'newness':
                $query_args['orderby'] = 'date';
		$query_args['order'] = 'desc';
		$query_args['meta_key'] = '';
                break;
            case 'oldest':
                $query_args['orderby'] = 'date';
		$query_args['order'] = 'asc';
		$query_args['meta_key'] = '';
                break;
            case 'nameaz':
                $query_args['orderby'] = 'title';
		$query_args['order'] = 'asc';
		$query_args['meta_key'] = '';
                break;
            case 'nameza':
                $query_args['orderby'] = 'title';
		$query_args['order'] = 'desc';
		$query_args['meta_key'] = '';
                break;
            case 'lowhigh':
                $query_args['meta_key'] = '_price';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'asc'; 
                break;
            case 'highlow':
                $query_args['meta_key'] = '_price';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'desc'; 
                break;
            case 'skulowhigh':
                $query_args['meta_key'] = '_sku';
                $query_args['orderby'] = 'meta_value';
                $query_args['order'] = 'asc'; 
                break;
            case 'skuhighlow':
                $query_args['meta_key'] = '_sku';
                $query_args['orderby'] = 'meta_value';
                $query_args['order'] = 'desc'; 
                break;
            case 'stocklowhigh':
                $query_args['meta_key'] = '_stock';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'asc'; 
                break;
            case 'stockhighlow':
                $query_args['meta_key'] = '_stock';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'desc'; 
                break;
            case 'random':
                $query_args['orderby'] = 'rand';
		$query_args['order'] = '';
		$query_args['meta_key'] = '';
                break;
            default:
                break;
            
        }

        /* -------------------------
         * WP Query 
         * ------------------------- */
        $r = new WP_Query($query_args);
        $display_id=rand();
        $result.='<div id="displayProduct-'.$display_id.'" class="displayProduct-shortcode displayProduct-Container">';
         do_action('woocommerce_before_shop_loop');
        if ($r->have_posts()) {
            if($frontsorter=='default') {
                include(plugin_dir_path(__FILE__) . '/templates/sorter.php');
            }
            //Switch Template
            if ($type) {
                include(plugin_dir_path(__FILE__) . '/templates/' . $type . '.php');
            } else {
                include(plugin_dir_path(__FILE__) . '/templates/grid.php');
            }
        } else {
            include(plugin_dir_path(__FILE__) . '/templates/404.php');
        }

        wp_reset_query();
        if ($pagination == 'default' && $type != 'carousel' && $type != 'carouselGrid'):
            if ($r->max_num_pages >= 1) {

                $result.='<nav class="woocommerce-pagination">';
                $result.= paginate_links(apply_filters('woocommerce_pagination_args', array(
                    'base' => str_replace(99999, '%#%', html_entity_decode(get_pagenum_link(99999))),
                    'current' => max(1, $paged),
                    'total' => $r->max_num_pages,
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                    'type' => 'list',
                    'end_size' => 3,
                    'mid_size' => 3
                )));
            
                $result.='</nav>';
            }
        endif;

        $result.='</div>';
        $color_function='dp_'.$type.'_color_css';
        if(!function_exists($color_function)){
            require( plugin_dir_path(__FILE__) . 'assets/css/' . $type . '/' . $type . '-color.php' );
        }
        $result.=$color_function('displayProduct-'.$display_id,$tablebackground,$tableheadbackground,$tableheadtextcolor,$tablerowhovercolor,$bordercolor,$dp_title['color'],$dp_title['HoverColor'],$dp_price['color'],$dp_excerpt 
                ['color'],$linkcolor,$linkhovercolor,$dp_addtocartbutton['color'],$dp_addtocartbutton['hovercolor'],$backgroundcolor,$featuredcolor,$salecolor);
        
        if($type == 'boxCarousel' || $type == 'gridCarousel') {
            if(!function_exists('carouselCustom')){
                require( plugin_dir_path(__FILE__) . 'plugin/owl-carousel/carousel-custom.php' );
                wp_enqueue_style('dp-frontend-owlcarousel', DP_URL . 'plugin/owl-carousel/owl.carousel.css');
                wp_enqueue_style('dp-frontend-owltheme', DP_URL . 'plugin/owl-carousel/owl.theme.css');
                wp_enqueue_script('dp-frontend-owlcarousel-js', DP_URL . 'plugin/owl-carousel/owl.carousel.js');
            }
            $result.=carouselCustom($display_id,$columns,$arrowanddot);
        }
        $result=preg_replace('/^\s+|\n|\r|\s+$/m', '', $result);

        return $result;
    }
    add_shortcode('displayProduct', 'displayProduct_shorcode');    
}
/*
if (!function_exists('db_get_first_image')) {
    function db_get_first_image($w = '', $h = '') {
        global $post, $posts;
        $first_img = '';
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        $first_img = $matches [1] [0];

        if (empty($first_img)) { //Defines a default image
            $first_img = "/images/default.jpg";
        }
        if ($w && $h) {
            $style = 'style="max-width:' . $w . 'px;max-height: ' . $h . 'px; text-align:center"';
        } else {
            $style = 'style="max-width:100%;width:100%;height:auto; text-align:center"';
        }
        return '<img src="' . $first_img . '" ' . $style . ' />';
    }
}*/

if (!function_exists('dp_load_style')) {
    function dp_load_style($type='grid', $columns=4) {
        //global $type;
        if (!is_admin()) {
            wp_enqueue_style('dp-frontend-default', DP_DIR . 'assets/css/default.css');
            if ($type == 'grid' || $type == 'list' || $type == 'box' || $type == 'boxCarousel' || $type == 'gridCarousel') {
                wp_enqueue_style('dp-frontend-col', DP_DIR . 'assets/css/responsivegrid/col.css', array());
                wp_enqueue_style('dp-frontend-' . $type . '-default-style', DP_DIR . 'assets/css/' . $type . '/' . $type . '-default.css');
            }
            if ($type == 'table') {
                wp_enqueue_style('dp-frontend-' . $type . '-default-style', DP_DIR . 'assets/css/' . $type . '/' . $type . '-default.css');
            }
            wp_enqueue_style('dp-frontend-button', DP_DIR .'assets/css/button/button.css');
            wp_enqueue_style('dp-frontend-pagination', DP_DIR . 'assets/css/paginations.css');
            wp_enqueue_style('dp-frontend-sorter', DP_DIR . 'assets/css/sorter.css');

            /* Heover Ex */
            wp_enqueue_style('dp-frontend-hoverex-templates-css', DP_DIR . 'plugin/hoverex/template_assets/templates.css');
            wp_enqueue_style('dp-frontend-hoverex-all-css', DP_DIR . 'plugin/hoverex/hoverex-all.css');
            wp_enqueue_script('dp-frontend-hoverex-all-js', DP_DIR . 'plugin/hoverex/jquery.hoverex.js');
            wp_enqueue_script('dp-frontend-script', DP_DIR . 'assets/js/displayProduct_front.js');
            
            /* deRegister Default */
            wp_dequeue_script( 'wc-add-to-cart-variation' );
            wp_deregister_script( 'wc-add-to-cart-variation' );
            
            wp_register_script('dp-frontend-variation', DP_DIR . 'assets/js/dp-front-variation.js', array( 'jquery' ), '1.0', true);
            wp_enqueue_script('dp-frontend-variation');
            
            wp_localize_script( 'dp-frontend-variation', 'dp_add_to_cart_variation_params', apply_filters( 'wc_add_to_cart_variation_params', array(
			'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
			'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
		) ) );
            
        }
    }
    add_action('displayProductSkin', 'dp_load_style', 10, 2);
    add_action( 'admin_init', 'dp_load_style' ,15);
}
function dp_load_font($font){
    wp_register_style('dpGoogleFonts', 'http://fonts.googleapis.com/css?family='.$font);
    wp_enqueue_style('dpGoogleFonts');
    dp_print_style($font);
}
add_action('displayProductFont', 'dp_load_font', 15, 1);

function dp_print_style($font){?>
    <style type="text/css">
        .displayProduct-shortcode .product_grid .product-name a, 
        .displayProduct-shortcode .product_grid .product-name a:active, 
        .displayProduct-shortcode .product_grid .product-name a:visited{
            font-family: '<?php echo preg_replace('/\+/', ' ', $font);?>', sans-serif;
        }
    </style>
    <?php
}

function dp_get_image($dp_image,$dpanimatehover) {
    global $product;
    if (has_post_thumbnail()) {
        $result.='<div class="he-wrap tpl1">';
        if($dpanimatehover!='disable'){
            $attachment_ids = $product->get_gallery_attachment_ids();
            if ($attachment_ids) {
                $result.='<div class="he-view">';
                foreach ($attachment_ids as $attachment_id) {
                    
                    $image_gallery=wp_get_attachment_image_src( $attachment_id, 'full' );
                    if ( $image_gallery ) {
                        $result.='<div class="dp-img-wrapper">'; //img-hided
                        $params = array( 'width' =>$dp_image['width'],'height' => $dp_image['height'], 'crop' => $dp_image['crop']);
                        $result.='<img class="a0t" data-animate ="'.$dpanimatehover.'" src="' . bfi_thumb( $image_gallery[0], $params ). '" alt="" />';
                        $result.='</div>';
                        break;
                    }
                }
                $result.='</div>';
            }
        }
        
        $result.='<div class="dp-img-wrapper">';
        $image=wp_get_attachment_image_src( get_post_thumbnail_id( $r->post->ID ), 'full' );
        if ( $image ) {
                $params = array( 'width' =>$dp_image['width'],'height' => $dp_image['height'], 'crop' => $dp_image['crop']);
                $result.='<img class="thumbnail-left" src="' . bfi_thumb( $image[0], $params ). '" alt="" />';
        }
        //$result.=get_the_post_thumbnail($r->post->ID, 'display_product_thumbnail');
        $result.='</div>';
        
        $result.='</div>';
    }else {
        $result.=wc_placeholder_img();
    }
    return $result;
}

function dp_get_image_box($dp_image) {
    global $product;
    if (has_post_thumbnail()) {
        $result.='<div class="dp-img-wrapper hideableHover">';
        $image=wp_get_attachment_image_src( get_post_thumbnail_id( $r->post->ID ), 'full' );
        if ( $image ) {
                $params = array( 'width' =>$dp_image['width'],'height' => $dp_image['height'], 'crop' => $dp_image['crop']);
                $result.='<img src="' . bfi_thumb( $image[0], $params ). '" alt="" />';
        }
        $result.='</div>';
    } else {
        $result.=wc_placeholder_img();
    }
    return $result;
}

function dp_get_sale_flash() {
    global $product;
    if ($product->is_on_sale()):
        return apply_filters('woocommerce_sale_flash', '<span class="onsale">' . __('Sale!', DP_TEXTDOMAN) . '</span>', $post, $product);
    endif;
}

function dp_add_to_cart() {
    global $product;

    if (!$product->is_in_stock()) :

        return '<a href="' . apply_filters('out_of_stock_add_to_cart_url', get_permalink($product->id)) . '" class="dp-button">' . apply_filters('out_of_stock_add_to_cart_text', __('Read More', DP_TEXTDOMAN)) . '</a>';

    else :
        $link = array(
            'url' => '',
            'label' => '',
            'class' => ''
        );

        $handler = apply_filters('woocommerce_add_to_cart_handler', $product->product_type, $product);

        switch ($handler) {
            case "variable" :
                $link['url'] = apply_filters('variable_add_to_cart_url', get_permalink($product->id));
                $link['label'] = apply_filters('variable_add_to_cart_text', __('Select options', DP_TEXTDOMAN));
                break;
            case "grouped" :
                $link['url'] = apply_filters('grouped_add_to_cart_url', get_permalink($product->id));
                $link['label'] = apply_filters('grouped_add_to_cart_text', __('View options', DP_TEXTDOMAN));
                break;
            case "external" :
                $link['url'] = apply_filters('external_add_to_cart_url', get_permalink($product->id));
                $link['label'] = $product->get_button_text();
                break;
            default :
                if ($product->is_purchasable()) {
                    $link['url'] = apply_filters('add_to_cart_url', esc_url($product->add_to_cart_url()));
                    $link['label'] = apply_filters('add_to_cart_text', __('Add to cart', DP_TEXTDOMAN));
                    $link['class'] = apply_filters('add_to_cart_class', 'add_to_cart_button');
                } else {
                    $link['url'] = apply_filters('not_purchasable_url', get_permalink($product->id));
                    $link['label'] = apply_filters('not_purchasable_text', __('Read More', DP_TEXTDOMAN));
                }
                break;
        }

        return apply_filters('woocommerce_loop_add_to_cart_link', sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="%s dp-button product_type_%s">%s</a>', esc_url($link['url']), esc_attr($product->id), esc_attr($product->get_sku()), esc_attr($link['class']), esc_attr($product->product_type), esc_html($link['label'])), $product, $link);

    endif;
}

function dp_add_to_cart_customButton($addtocarturl,$addtocarttext) {
    if($addtocarttext==''){$addtocarttext='Add to Cart';}
    $customlink='<a href="'.$addtocarturl.'" class="single_add_to_cart_button button alt db_customButton">';
    $customlink_end='</a>';
    return $customlink.$addtocarttext.$customlink_end;
}

function dp_add_to_cart_customText($addtocarturl,$addtocarttext) {
    if($addtocarturl){
        $customlink='<a href="'.$addtocarturl.'" class="db_customText">';
        $customlink_end='</a>';
    }
    if($addtocarttext==''){$addtocarttext='Call for Price';}
    return $customlink.'<div class="db_customtext">'.$addtocarttext.'</div>'.$customlink_end;
}

function dp_add_to_cart_productdetail($addtocarturl,$addtocarttext) {
    if($addtocarttext==''){$addtocarttext='View Product';}
    $customlink='<a href="'.$addtocarturl.'" class="single_add_to_cart_button button alt db_customButton">';
    $customlink_end='</a>';
    return $customlink.$addtocarttext.$customlink_end;
}

if (!function_exists('dp_shortcode_empty_paragraph_fix')):

    function dp_shortcode_empty_paragraph_fix($content) {
        $array = array(
            '<p>[' => '[',
            ']</p>' => ']',
            ']<br />' => ']'
        );
        $content = strtr($content, $array);
        return $content;
    }

endif;
add_filter('the_content', 'dp_shortcode_empty_paragraph_fix');

function print_star_rating() {
    global $wpdb;
    global $post;
    $star = '';
    $count = $wpdb->get_var("
        SELECT COUNT(meta_value) FROM $wpdb->commentmeta
        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
        WHERE meta_key = 'rating'
        AND comment_post_ID = $post->ID
        AND comment_approved = '1'
        AND meta_value > 0
    ");

    $rating = $wpdb->get_var("
        SELECT SUM(meta_value) FROM $wpdb->commentmeta
        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
        WHERE meta_key = 'rating'
        AND comment_post_ID = $post->ID
        AND comment_approved = '1'
    ");

    if ($count > 0) {

        $average = number_format($rating / $count, 2);

        $rating_html = '<div class="star-rating" title="' . sprintf(__('Rated %s out of 5', DP_TEXTDOMAN), $rating) . '">';

        $rating_html .= '<span style="width:' . ( ( $average / 5 ) * 100 ) . '%"><strong class="rating">' . $average . '</strong> ' . __('out of 5', DP_TEXTDOMAN) . '</span>';

        $rating_html .= '</div>';
        return $rating_html;
    }else{
        $rating_html = '<div class="star-rating">';
        $rating_html .='No rating.';
        $rating_html .= '</div>';
        return $rating_html;
    }
}

if(!function_exists('dp_excerpt_max_charlength')){
    function dp_excerpt_max_charlength($charlength) {
            $excerpt = get_the_excerpt();
            $charlength++;

            if ( mb_strlen( $excerpt ) > $charlength ) {
                    $subex = mb_substr( $excerpt, 0, $charlength - 5 );
                    $exwords = explode( ' ', $subex );
                    $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
                    if ( $excut < 0 ) {
                            return mb_substr( $subex, 0, $excut );
                    } else {
                            return $subex;
                    }
                    return '[...]';
            } else {
                    return $excerpt;
            }
    }
}// if Exist
?>