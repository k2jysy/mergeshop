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

add_shortcode('code','wpo_shortcode_code');
function wpo_shortcode_code($atts,$content=null){

}

add_shortcode('clearfix', 'wpo_shortcode_clearfix');
function wpo_shortcode_clearfix($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => '',
    ), $atts);
    $html ='';
    $html .='
        <div class="clearfix"></div>
    ';
    return $html;
}

add_shortcode('one_full', 'wpo_shortcode_one_full');
function wpo_shortcode_one_full($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-12'
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-12 '.$atts['class'].'">';
            $html.= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('one_half', 'wpo_shortcode_one_half');
function wpo_shortcode_one_half($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-6',
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-6 '.$atts['class'].'">
            '.do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('one_third', 'wpo_shortcode_one_third');
function wpo_shortcode_one_third($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-6',
        'title' => '',
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-4 '.$atts['class'].'">';
            $html .= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('two_third', 'wpo_shortcode_two_third');
function wpo_shortcode_two_third($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-6'
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-8 '.$atts['class'].'">';
            $html .= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('one_fourth', 'wpo_shortcode_one_fourth');
function wpo_shortcode_one_fourth($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-6'
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-3 '.$atts['class'].'">';
            $html.= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('three_fourth', 'wpo_shortcode_three_fourth');
function wpo_shortcode_three_fourth($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-9'
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-9 '.$atts['class'].'">';
            $html.= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('one_sixth', 'wpo_shortcode_one_sixth');
function wpo_shortcode_one_sixth($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-6'
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-2 '.$atts['class'].'">';
            $html .= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('seven_twelve', 'wpo_shortcode_seven_twelve');
function wpo_shortcode_seven_twelve($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-6'
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-7 '.$atts['class'].'">';
            $html.= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('five_twelve', 'wpo_shortcode_five_twelve');
function wpo_shortcode_five_twelve($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'class' => 'col-sm-6'
    ), $atts);
    $html ='';
    $html .='
        <div class="col-md-5 '.$atts['class'].'">';
            $html.= do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('panel_group', 'wpo_shortcode_panel_group');
function wpo_shortcode_panel_group($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'id' => '',
        'sub_title' => '',
        'class' => '',
        'animate' => '',
    ), $atts);
    $html ='';
    $html .='
        <div class="panel-group" id="'.$atts['id'].'">
            '.do_shortcode($content).'
        </div>
    ';
    return $html;
    
}
add_shortcode('panel', 'wpo_shortcode_panel');
function wpo_shortcode_panel($atts, $content=null){
    static $counter = 1;
    $atts = shortcode_atts(
        array(
        'id_parent' => '',
        'in' => '',
        'title' => '',
        'type' => 'default',
        'animate' => '',
    ), $atts);
    if($atts['in']=="true"){
        $in = 'in';
    }else{
        $in = '';
    }
    $html ='';
    $html .='
        <div class="panel panel-'.$atts['type'].'">
            <div class="panel-heading">
                <h4 class="panel-title">
                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#'.$atts['id_parent'].'" href="#collapsepanel'.$counter.'">'.$atts['title'].'</a>
                </h4>
            </div>
            <div id="collapsepanel'.$counter.'" class="panel-collapse collapse '.$in.'">
                <div class="panel-body">
                <div>'.do_shortcode($content).'</div>
                </div>
            </div>
        </div>
    ';
    $counter++;
    return $html;
    
}

add_shortcode('accordion', 'wpo_shortcode_accordion');
function wpo_shortcode_accordion($atts, $content=null){
    $atts = shortcode_atts(
        array(
        'id' => '',
        'sub_title' => '',
        'class' => '',
        'animate' => '',
    ), $atts);
    $html ='';
    $html .='
        <div class="accordion" id="'.$atts['id'].'">
            '.do_shortcode($content).'
        </div>
    ';
    return $html;

}

add_shortcode('accordion_item', 'wpo_shortcode_accordion_item');
function wpo_shortcode_accordion_item($atts, $content=null){
    static $counter = 1;
    $atts = shortcode_atts(
        array(
        'id_parent' => '',
        'effect' => '',
        'in' => '',
        'title' => '',
        'animate' => '',
    ), $atts);
    if($atts['in']=="true"){
        $in = 'in';
    }else{
        $in = '';
    }
    $html ='';
    $html .='
        <div class="accordion-group">
            <div class="accordion-heading">
                 <a class="accordion-toggle" data-toggle="collapse" data-parent="#'.$atts['id_parent'].'" href="#collapse'.$counter.'"><em class="icon-plus icon-fixed-width"></em>'.$atts['title'].'</a>
            </div>
            <div id="collapse'.$counter.'" class="accordion-body collapse '.$in.'">
                <div class="accordion-inner">
                <div>'.do_shortcode($content).'</div>
                </div>
            </div>
        </div>
    ';
    $counter++;
    return $html;

}

add_shortcode('button','wpo_shortcode_button');
 function wpo_shortcode_button($atts, $content = null){
     $atts = shortcode_atts(
        array(
        'type'=>'default',
        'size'=>'',
        'disable'=>'',
        'block'=>'',
        'link'=>'#',
    ), $atts);
    if($atts['disable']=='true'){
        $disable = 'disable';
    }else{
        $disable = '';
    }
    if($atts['block']=='true'){
        $block = 'block';
    }else{
        $block = '';
    }
    $html ='';
    $html .='
        <a class="btn '.$disable.' btn-'.$atts['type'].' btn-'.$atts['block'].' btn-'.$atts['size'].'" href="'.$atts['link'].'">'.do_shortcode($content).'</a>
    ';
    return $html;
}

add_shortcode('tabs', 'wpo_shortcode_tabs_group');
function wpo_shortcode_tabs_group($atts, $content = null ) {
    global $tabs_divs;
    extract(shortcode_atts(array(
        'style' => '',
    ), $atts));
    $tabs_divs = '';

    $output = '<div class="tabbable tabs-'.$style.'"><ul class="nav nav-tabs"';
    $output.='>'.do_shortcode($content).'</ul>';
    $output.= '<div class="tab-content">'.$tabs_divs.'</div></div>';

    return $output;
}

add_shortcode('tab', 'wpo_shortcode_tab');
function wpo_shortcode_tab($atts, $content = null) {
    global $tabs_divs;

    extract(shortcode_atts(array(
        'id' => '',
        'title' => '',
        'active' => '',
    ), $atts));

    if(empty($id))
        $id = 'side-tab'.rand(100,999);

    $output = '
        <li class="'.$active.'">
            <a href="#'.$id.'" data-toggle="tab">'.$title.'</a>
        </li>
    ';

    $tabs_divs.= '<div class="tab-pane '.$active.'" id="'.$id.'">'.do_shortcode($content).'</div>';

    return $output;
}

add_shortcode('alert_box', 'wpo_shortcode_alert_box');
function wpo_shortcode_alert_box($atts, $content = null){
    extract(shortcode_atts(array(  
        'type' => '',
    ), $atts));
    $html ='';
    $html .='
         <div class="alert alert-'.$type.'">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          '.do_shortcode($content).'
        </div>
    ';
    return $html;
}

add_shortcode('icon','wpo_shortcode_icon');
 function wpo_shortcode_icon($atts, $content = null){
     $atts = shortcode_atts(
        array(
        'icon_name'=>''
    ), $atts);
    $html = '';
    $html .='
        <i class="fa '.$atts['icon_name'].'"></i>
    ';
    return $html;
 }