<?php
/**
 * SCREETS © 2013
 *
 * Prepare chat plugin
 *
 */


// Ajax Requests
add_action( 'wp_ajax_nopriv_sc_chat_ajax_callback', 'sc_chat_ajax_callback' );
add_action( 'wp_ajax_sc_chat_ajax_callback', 'sc_chat_ajax_callback' );

/**
 * Default plugin options
 *
 * DO NOT CHANGE DEFAULT OPTIONS.
 * INSTEAD, USE ADMIN PANEL.
 */

$sc_chat_translations = array(
	'before_chat_header'	=> __( 'Talk to us', 'sc_chat' ),
	'line_qq'	=> __( '<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=82708210&amp;site=qq&amp;menu=yes" target="_blank" rel="nofollow"><img title="给模板兔发QQ" alt="模板兔QQ" src="http://wpa.qq.com/pa?p=2:82708210:41" border="0"></a>', 'sc_chat' ),
	'online_qq'	=> __( '<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=82708210&amp;site=qq&amp;menu=yes" target="_blank" rel="nofollow"><img title="给模板兔发QQ" alt="模板兔QQ" src="http://wpa.qq.com/pa?p=2:82708210:41" border="0"></a>', 'sc_chat' ),
	'in_chat_header'		=> __( 'Now Chatting', 'sc_chat' ),
	'prechat_welcome_msg'	=> __( "Questions? We're here. Send us a message!", 'sc_chat' ),
	'welcome_msg'			=> __( "Questions, issues or concerns? I'd love to help you!", 'sc_chat' ),
	'chat_btn'				=> __( "Start Chat", 'sc_chat' ),
	'send_btn'				=> __( "Send", 'sc_chat' ),
	'input_box_msg'			=> __( "Click ENTER to chat", 'sc_chat' ),
	'input_box_placeholder'	=> __( "Write a reply", 'sc_chat' ),
	'name_field'			=> __( "Your name", 'sc_chat' ),
	'email_field'			=> __( "E-mail", 'sc_chat' ),
	'phone_field'			=> __( "Phone", 'sc_chat' ),
	'question_field'		=> __( 'Got a question?', 'sc_chat' ),
	'req_text'				=> __( "Required", 'sc_chat' ),
	'offline_header'		=> __( 'Contact us', 'sc_chat' ),
	'offline_body'			=> __( "We're not around right now. But you can send us an email and we'll get back to you, asap.", 'sc_chat' ),
	'end_chat_field'		=> __( 'End chat', 'sc_chat' ),
);

$_sc_chat_default_opts = array(
	'use_css_anim'			=> 1,
	'skin_box_width'		=> 300,
	'skin_box_height'		=> 380,
	'skin_type'				=> 'light',
	'skin_chatbox_bg'		=> '#ffffff',
	'skin_chatbox_fg'		=> '#222222',
	'skin_header_bg'		=> '#bf3723',
	'skin_header_fg'		=> '#ffffff',
	'skin_submit_btn_bg'	=> '#3a99d1',
	'skin_submit_btn_fg'	=> '#ffffff',
	'ask_name_field'		=> 1,
	'ask_phone_field'		=> 0,
	'display_chatbox'		=> 1,
	'hide_chat_when_offline'=> 0,
	'disable_in_mobile'		=> 0,
	'always_show_homepage'	=> 0,
	'delay'					=> 2, // sec.
	'default_radius'		=> 4, // px
	'position'				=> 'right', // "left" or "right"
	'offset'				=> 40, // px
	'load_skin_css'			=> 1,
	'compress_css'			=> 1,
	'compress_js'			=> 1,
	'allowed_visitors'		=> 5, // Allowed visitors at one time
	'op_role'				=> 'contributor', // Operator role: 'none', 'editor', 'author', 'contributor'
	'offline_msg_email'		=> '', // Where should offline messages go?
	'get_notifications'		=> 1,
	'purchase_key'			=> '',
	'custom_css'			=> '',
	'sound_package'			=> 'basic' // "none": mute sound
);

$sc_chat_default_opts = array_merge( $_sc_chat_default_opts, $sc_chat_translations );