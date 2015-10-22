<?php
/*
	Plugin Name: googlefonts to 360
	Plugin URI: http://baiye.us/googlefonts-2-360.html
	Description: 将谷歌字体等链接替换成360国内CDN链接,解决google在中国访问时常抽风影响到了google api和一些公共服务.
	Version: 1.0
	Author: 百叶
	Author URI: http://baiye.us
*/


function izt_cdn_callback($buffer) {
	return str_replace('googleapis.com', 'useso.com', $buffer);
}
function izt_buffer_start() {
	ob_start("izt_cdn_callback");
}
function izt_buffer_end() {
	ob_end_flush();
}
add_action('init', 'izt_buffer_start');
add_action('shutdown', 'izt_buffer_end');

?>