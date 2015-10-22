<?php
$output = $el_class = $bg_image = $bg_color = $bg_image_repeat = $font_color = $padding = $margin_bottom = '';
extract(shortcode_atts(array(
    'el_class'        => '',
    'bg_image'        => '',
    'bg_color'        => '',
    'bg_image_repeat' => '',
    'font_color'      => '',
    'padding'         => '',
    'margin_bottom'   => '' ,
    'css'             => '',
    'parallax'        => '0',
    'isfullwidth'     => '',
), $atts));


$is_parallax = ($parallax!='' && $parallax!='0') ? true : false;

$el_class = $this->getExtraClass($el_class);
if($is_parallax){
    $el_class .=' parallax';
    $parallax = ' data-stellar-background-ratio="0.6"';
}

$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_row '.get_row_css_class(), $this->settings['base']);

$style = $this->buildStyle($bg_image, $bg_color, $bg_image_repeat, $font_color, $padding, $margin_bottom);

 
$output='<section class="box wpb-container'.($isfullwidth?'':"").'"><div class="wpb-inner '.$el_class.vc_shortcode_custom_css_class($css, ' ').'"'.$parallax.' '.$style.'>';
    if($is_parallax) $output.='<div class="parallax-inner">';
    $output .= '<div class="'.$css_class.'">';
		$output .= wpb_js_remove_wpautop($content);
    $output .= '</div>'.$this->endBlockComment('row');
    if($is_parallax) $output.='</div>';
$output.='</div></section>';

echo $output;
