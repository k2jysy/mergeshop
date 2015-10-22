<?php
$output = $title = $tab_id = $tabicon = '';
$default = $this->predefined_atts;
$default['tabicon']='';
extract(shortcode_atts($default, $atts));

global $wpo_tab_item;
$wpo_tab_item[] = array('tab-id'=>$tab_id,'title'=>$title,'tabicon'=>$tabicon,'content'=>wpb_js_remove_wpautop($content));