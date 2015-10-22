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
if(!function_exists('wpo_related_post')){
    function wpo_related_post($relate_count=-1,$posttype='post'){
    	$tag_ids = array();
    	if(get_the_tags()){
    		foreach (get_the_tags() as $key => $value) {
    			$tag_ids[] = $value->term_id;
    		}

    		$args = array(
    			'tag__in'=>$tag_ids,
    			'posts_per_page'=>$relate_count,
    			'post__not_in'=>array(get_the_ID()),
    			'orderby' =>'rand',
    			'post_type'=>$posttype
    		);

    		$relates = new WP_Query( $args );

    		if(is_file(FRAMEWORK_TEMPLATES.'related_post.php')){
    			include(FRAMEWORK_TEMPLATES.'related_post.php');
    		}
    	}
    }
}

if(!function_exists('wpo_getAgo')){
    function wpo_getAgo($timestamp){
    	// return $timestamp;
    	$timestamp = strtotime($timestamp);
    	$difference = time() - $timestamp;

        if ($difference < 60) {
            return $difference.__(" seconds ago",TEXTDOMAIN);
        } else {
            $difference = round($difference / 60);
        }

        if ($difference < 60) {
            return $difference.__(" minutes ago",TEXTDOMAIN);
        } else {
            $difference = round($difference / 60);
        }

        if ($difference < 24) {
            return $difference.__(" hours ago",TEXTDOMAIN);
        }
        else {
            $difference = round($difference / 24);
        }

        if ($difference < 7) {
            return $difference.__(" days ago",TEXTDOMAIN);
        } else {
            $difference = round($difference / 7);
            return $difference.__(" weeks ago",TEXTDOMAIN);
        }
    }
}

if(!function_exists('wpo_excerpt')){
    //Custom Excerpt Function
    function wpo_excerpt($limit,$afterlimit='[...]') {
        if(get_the_excerpt() != ''){
    	   $excerpt = explode(' ', strip_tags(get_the_excerpt()), $limit);
        }else{
            $excerpt = explode(' ', strip_tags(get_the_content( )), $limit);
        }
    	if (count($excerpt)>=$limit) {
    		array_pop($excerpt);
    		$excerpt = implode(" ",$excerpt).' '.$afterlimit;
    	} else {
    		$excerpt = implode(" ",$excerpt);
    	}
    	$excerpt = preg_replace('`[[^]]*]`','',$excerpt);
    	return strip_shortcodes( $excerpt );
    }
}

if(!function_exists('wpo_theme_comment')){
    function wpo_theme_comment($comment, $args, $depth){
    	if(is_file(FRAMEWORK_TEMPLATES.'list_comments.php')){
    		require (FRAMEWORK_TEMPLATES.'list_comments.php');
    	}
    }
}

if(!function_exists('wpo_thumbnail_url')){
    //Get thumbnail url
    function wpo_thumbnail_url($size){
        global $post;
        //$url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()),$size );
        if($size==''){
            $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
             return $url;
        }else{
            $url = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), $size);
             return $url[0];
        }
    }
}

if(!function_exists('wpo_get_pageconfig')){
    // page Configuration
    function wpo_get_pageconfig(){
    	global $wp_query;
    	$pageconfig = get_post_meta($wp_query->get_queried_object_id(),'wpo_pageconfig',true);
    	$lt = (!empty($pageconfig['page_layout']) ||$pageconfig['page_layout']!= '' )?$pageconfig['page_layout']:'0-1-0';
    	$maincontent = array();
    	$config = array();
    	$config['breadcrumb']=$pageconfig['breadcrumb'];
    	$config['right-sidebar']['widget']=$pageconfig['right_sidebar'];
    	$config['left-sidebar']['widget']=$pageconfig['left_sidebar'];
    	$config['showtitle']= $pageconfig['showtitle'];
    	$obj = new WPO_Template();
    	$config = $obj->configLayout($lt,$config);
    	return $config;
    }
}


if(!function_exists('wpo_renderMegamenuVertical')){
    //Megamenu
    function wpo_renderMegamenuVertical($menuid='main-menu'){
        $nav_menu = ( of_get_option('magemenu-menu-vertical','') !='' ) ? wp_get_nav_menu_object( of_get_option('magemenu-menu-vertical','') ) : false;
        if(!$nav_menu) return false;
        $args = array(  'menu' => $nav_menu,
                        'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
                        'menu_class' => 'nav navbar-nav megamenu',
                        'fallback_cb' => '',
                        'menu_id' => $menuid,
                        'walker' => new Wpo_Megamenu_Vertical());
        wp_nav_menu($args);
    }
}


if(!function_exists('wpo_renderMegamenu')){
    //Megamenu
    function wpo_renderMegamenu($menuid='main-menu'){
    	$nav_menu = ( of_get_option('magemenu-menu','') !='' ) ? wp_get_nav_menu_object( of_get_option('magemenu-menu','') ) : false;
        $args = array(  'menu' => $nav_menu,
                        'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
                        'menu_class' => 'nav navbar-nav megamenu',
                        'fallback_cb' => '',
                        'menu_id' => $menuid,
                        'walker' => new Wpo_Megamenu());
        wp_nav_menu($args);
    }
}

if(!function_exists('wpo_renderButtonToggle')){
    function wpo_renderButtonToggle($class=''){
    ?>
    	<a href="javascript:;"
            data-target=".navbar-collapse"
            data-pos="left" data-effect="<?php echo of_get_option('magemenu-offcanvas-effect','off-canvas-effect-1'); ?>"
            data-nav="#wpo-off-canvas"
            class="navbar-toggle off-canvas-toggle <?php echo $class; ?>">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
        </a>
    <?php
    }
}

if(!function_exists('wpo_renderOffcanvas')){
    function wpo_renderOffcanvas(){
    ?>
        <div id="wpo-off-canvas" class="wpo-off-canvas">
            <div class="wpo-off-canvas-body">
                <div class="wpo-off-canvas-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#215;</button>
                </div>
                <nav  class="navbar navbar-offcanvas navbar-static" role="navigation">
                    <?php
                    $args = array(  'menu' => of_get_option('magemenu-menu',''),
                                    'container_class' => 'navbar-collapse',
                                    'menu_class' => 'wpo-menu-top nav navbar-nav',
                                    'fallback_cb' => '',
                                    'menu_id' => 'main-menu-offcanvas',
                                    'walker' => new Wpo_Megamenu_Offcanvas()
                                );
                    wp_nav_menu($args);
                    ?>
                </nav>
            </div>
        </div>
    <?php
    }
}

if(!function_exists('wpo_pagination')){
    //page navegation
    function wpo_pagination($prev = 'Prev', $next = 'Next', $pages='' ,$args=array('class'=>'')) {
        global $wp_query, $wp_rewrite;
        $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
        if($pages==''){
            global $wp_query;
             $pages = $wp_query->max_num_pages;
             if(!$pages)
             {
                 $pages = 1;
             }
        }
        $pagination = array(
            'base' => @add_query_arg('paged','%#%'),
            'format' => '',
            'total' => $pages,
            'current' => $current,
            'prev_text' => __($prev,TEXTDOMAIN),
            'next_text' => __($next,TEXTDOMAIN),
            'type' => 'array'
        );
        if( $wp_rewrite->using_permalinks() )
            $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

        if( !empty($wp_query->query_vars['s']) )
            $pagination['add_args'] = array( 's' => get_query_var( 's' ) );
        if(paginate_links( $pagination )!=''){
        	$paginations = paginate_links( $pagination );
    	    echo '<ul class="pagination '.$args["class"].'">';
    	    	foreach ($paginations as $key => $pg) {
    	    		echo '<li>'.$pg.'</li>';
    	    	}
    	    echo '</ul>';
    	}
    }
}

if(!function_exists('wpo_makeid')){
    function wpo_makeid($length = 5){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

if(!function_exists('wpo_breadcrumb')){
    function wpo_breadcrumb(){
    	if(is_file(FRAMEWORK_TEMPLATES.'breadcrumb.php')){
    		require (FRAMEWORK_TEMPLATES.'breadcrumb.php');
    	}
    }
}

if(!function_exists('wpo_comment_form')){
    function wpo_comment_form($arg,$class='btn-default'){
    	ob_start();
    	comment_form($arg);
    	$form = ob_get_clean();
    	echo str_replace('id="submit"','id="submit" class="btn '.$class.'"', $form);
    }
}
if(!function_exists('wpo_comment_reply_link')){
    function wpo_comment_reply_link($arg,$class='btn btn-default btn-xs'){
    	ob_start();
    	comment_reply_link($arg);
    	$reply = ob_get_clean();
    	echo str_replace('comment-reply-link','comment-reply-link '.$class, $reply);
    }
}

if(!function_exists('wpo_get_post_views')){
    function wpo_get_post_views($postID){
        $count_key = 'wpo_post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
            return 0;
        }
        return $count;
    }
}

if(!function_exists('wpo_string_limit_words')){
    function wpo_string_limit_words($string, $word_limit)
    {
    	$words = explode(' ', $string, ($word_limit + 1));

    	if(count($words) > $word_limit) {
    		array_pop($words);
    	}

    	return implode(' ', $words);
    }
}

if(!function_exists('wpo_share_box')){
    function wpo_share_box( $layout='',$args=array() ){
    	$default = array(
    		'position' => 'top',
    		'animation' => 'true'
    		);
    	$args = wp_parse_args( (array) $args, $default );
    	$template = new WPO_Template();
    	$template->getShareBox($args);
    }
}

if(!function_exists('wpo_is_embed')){
    function wpo_is_embed() {
        $postconfig = get_post_meta(get_the_ID(),'wpo_post',true);
        if(!isset($postconfig['embed']) || $postconfig['embed']=='') return false;
        return true;
    }
}

if(!function_exists('wpo_embed')){
    function wpo_embed() {
    	$postconfig = get_post_meta(get_the_ID(),'wpo_post',true);
        $content='';
        if(isset($postconfig['embed']))
            $content = wp_oembed_get($postconfig['embed']);
    	echo $content;
    }
}

if(!function_exists('wpo_is_link')){
    function wpo_is_link(){
        $postconfig = get_post_meta(get_the_ID(),'wpo_post',true);
        if(!isset($postconfig["link"]) || $postconfig["link"]=='' ) return false;
        return true;
    }
}

if(!function_exists('wpo_is_gallery')){
    function wpo_is_gallery(){
        $galleries = get_post_gallery( get_the_ID(), false );
        if(!isset($galleries['ids']) || $galleries['ids']=='' ) return false;
        return true;
    }
}


if(!function_exists('wpo_gallery')){
    function wpo_gallery($size='full'){
    	$galleries = get_post_gallery( get_the_ID(), false );
    	$img_ids = explode(",",$galleries['ids']);
    	$output = array();
    	foreach ($img_ids as $key => $id){
    		$img_src = wp_get_attachment_image_src($id,$size);
    		$output[] = $img_src[0];
    	}
    	return $output;
    }
}

if ( ! function_exists( 'of_get_option' ) ) {

    function of_get_option( $name, $default = false ) {
        $config = get_option( 'optionsframework' );

        if ( ! isset( $config['id'] ) ) {
            return $default;
        }

        $options = get_option( $config['id'] );

        if ( isset( $options[$name] ) ) {
            return $options[$name];
        }

        return $default;
    }
}