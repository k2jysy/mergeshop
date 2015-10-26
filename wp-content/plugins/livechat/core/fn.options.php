<?php
/**
 * SCREETS © 2013
 *
 * Admin Option functions
 *
 */
	
/**
 * Get current options
 *
 * @access public
 * @return array
 */
function sc_chat_get_options() {
	return get_option( 'sc_chat_opts', sc_chat_get_default_options() );
}


/**
 * Get default theme options
 *
 * @access public
 * @return array
 */
function sc_chat_get_default_options( $args = array() ){

	$opts = array_merge( $GLOBALS['SC_Chat']->default_opts, $args );

	return apply_filters( 'sc_chat_get_default_options', $opts );
}


/**
 * Whitelist options
 *
 * @access public
 * @return void
 */
function sc_chat_register_options() {

	if ( false === sc_chat_get_options() )
		add_option( 'sc_chat_opts', sc_chat_get_default_options() );

	register_setting(
		'sc_chat_opt_group_general', // Option group
		'sc_chat_opts', 	// Option name
		'sc_chat_options_validate' // The sanitization callback
	);
	
	register_setting(
		'sc_chat_opt_group_customize_skin', // Option group
		'sc_chat_opts', 	// Option name
		'sc_chat_options_validate' // The sanitization callback
	);
	register_setting(
		'sc_chat_opt_group_messages', // Option group
		'sc_chat_opts', 	// Option name
		'sc_chat_options_validate' // The sanitization callback
	);

	/** 
	 * SECTION: General
	 **/
	add_settings_section(
		'general', 							// Unique identifier for the settings section
		__('General Settings', 'sc_chat'), 	// Section title
		'__return_false', 					// Section callback (we don't want anything)
		'sc_chat_opts_slug_general'			// Menu slug, used to uniquely identify the page
	);

	/** 
	 * SECTION: Offline messages
	 **/
	add_settings_section(
		'offline_msg', 							
		__('Offline Messages', 'sc_chat'), 		
		'__return_false', 					
		'sc_chat_opts_slug_general'	
	);

	/** 
	 * SECTION: Users & Visitors
	 **/
	add_settings_section(
		'users', 							
		__('Users & Visitors', 'sc_chat'), 		
		'__return_false', 					
		'sc_chat_opts_slug_general'	
	);
	
	/** 
	 * SECTION: Customize Skin 
	 **/
	add_settings_section(
		'customize_skin',
		__('Customize Skin', 'sc_chat'),
		'__return_false',
		'sc_chat_opts_slug_customize_skin'
	);
	
	/** 
	 * SECTION: Colors
	 **/
	add_settings_section(
		'colors',
		__('Colors', 'sc_chat'),
		'__return_false',
		'sc_chat_opts_slug_customize_skin'
	);
	
	/** 
	 * SECTION: Sound
	 **/
	add_settings_section(
		'sound',
		__('Sound', 'sc_chat'),
		'__return_false',
		'sc_chat_opts_slug_customize_skin'
	);

	/** 
	 * SECTION: Messages 
	 **/
	add_settings_section(
		'messages', 
		__('Messages', 'sc_chat'), 
		'__return_false', 
		'sc_chat_opts_slug_customize_messages'
	);

	/** 
	 * SECTION: Forms 
	 **/
	add_settings_section(
		'forms', 
		__('Forms', 'sc_chat'), 
		'__return_false', 
		'sc_chat_opts_slug_customize_messages'
	);

	/** 
	 * SECTION: Advanced 
	 **/
	add_settings_section(
		'advanced', 
		__('Advanced', 'sc_chat'), 
		'__return_false', 
		'sc_chat_opts_slug_general'
	);
	
	
	// Hide chatbox
	add_settings_field( 'sc_chat_display_chatbox', 
						__( 'Display chatbox automatically', 'sc_chat' ), 
						'sc_chat_render_display_chatbox', 
						'sc_chat_opts_slug_general',
						'general'
					  );
					  
	// Hide When Offline
	add_settings_field( 'sc_chat_hide_chat_when_offline', 
						__( 'Hide When Offline', 'sc_chat' ), 
						'sc_chat_render_hide_chat_when_offline', 
						'sc_chat_opts_slug_general',
						'general'
					  );
					  
	// Always show in homepage
	add_settings_field( 'sc_chat_always_show_homepage', 
						__( 'Always show in homepage', 'sc_chat' ), 
						'sc_chat_render_always_show_homepage', 
						'sc_chat_opts_slug_general',
						'general'
					  );
					  
	// Disable in mobile devices
	add_settings_field( 'sc_chat_disable_in_mobile', 
						__( 'Disable in mobile devices', 'sc_chat' ), 
						'sc_chat_render_disable_in_mobile', 
						'sc_chat_opts_slug_general',
						'general'
					  );
					  
	// Where should offline messages go?
	add_settings_field( 'sc_chat_offline_msg_email', 
						__( 'Where should offline messages go?', 'sc_chat' ), 
						'sc_chat_render_offline_msg_email', 
						'sc_chat_opts_slug_general',
						'offline_msg'
					  );

	// Get notifications by email
	add_settings_field( 'sc_chat_get_notifications', 
						__( '通过电子邮件获得通知', 'sc_chat' ), 
						'sc_chat_render_get_notifications', 
						'sc_chat_opts_slug_general',
						'offline_msg'
					  );
	
	// Allowed visitors at one time
	add_settings_field( 'sc_chat_allowed_visitors', 
						__( 'Allowed visitors at one time', 'sc_chat' ), 
						'sc_chat_render_allowed_visitors', 
						'sc_chat_opts_slug_general',
						'users'
					  );
					  
	// Operator additional role
	add_settings_field( 'sc_chat_op_role', 
						__( 'Operator Additional Role', 'sc_chat' ), 
						'sc_chat_render_op_role', 
						'sc_chat_opts_slug_general',
						'users'
					  );
					  
					  
	// Position
	add_settings_field( 'sc_chat_position', 
						__( 'Position', 'sc_chat' ), 
						'sc_chat_render_position', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );
					  
	// Offset
	add_settings_field( 'sc_chat_offset', 
						__( 'Offset', 'sc_chat' ), 
						'sc_chat_render_offset', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );


	// Use CSS3 Animations
	add_settings_field( 'sc_chat_use_css_anim', 
						__( 'CSS Animations', 'sc_chat' ), 
						'sc_chat_render_use_css_anim', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );


	// Ask for name
	add_settings_field( 'sc_chat_ask_name_field', 
						__( 'Ask for name?', 'sc_chat' ), 
						'sc_chat_render_ask_name_field', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );
					  
	// Ask for phone field
	add_settings_field( 'sc_chat_ask_phone_field', 
						__( 'Ask for phone field?', 'sc_chat' ), 
						'sc_chat_render_ask_phone_field', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );
                      
                      
    // Delay
	add_settings_field( 'sc_chat_delay', 
						__( 'Delay', 'sc_chat' ), 
						'sc_chat_render_delay', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );
	
	// Skin Box Width
	add_settings_field( 'sc_chat_skin_box_width', 
						__( 'Width', 'sc_chat' ), 
						'sc_chat_render_skin_box_width', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );

	// Skin Box Height
	add_settings_field( 'sc_chat_skin_box_height', 
						__( 'Height', 'sc_chat' ), 
						'sc_chat_render_skin_box_height', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );

	// Default radius
	add_settings_field( 'sc_chat_default_radius', 
						__( 'Default Radius', 'sc_chat' ), 
						'sc_chat_render_default_radius', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );
					  

	// Load default skin CSS file
	add_settings_field( 'sc_chat_load_skin_css', 
						__( 'Load default skin CSS file', 'sc_chat' ), 
						'sc_chat_render_load_skin_css', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );

	// Compress CSS file
	add_settings_field( 'sc_chat_compress_css', 
						__( 'Compress CSS', 'sc_chat' ), 
						'sc_chat_render_compress_css', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );
	
	// Compress JS file
	add_settings_field( 'sc_chat_compress_js', 
						__( 'Compress JavaScript file', 'sc_chat' ), 
						'sc_chat_render_compress_js', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );
					  
	// Custom CSS
	add_settings_field( 'sc_chat_custom_css', 
						__( 'Custom CSS', 'sc_chat' ), 
						'sc_chat_render_custom_css', 
						'sc_chat_opts_slug_customize_skin',
						'customize_skin'
					  );

	// Skin Type
	add_settings_field( 'sc_chat_skin_type', 
						__( 'Skin Type', 'sc_chat' ), 
						'sc_chat_render_skin_type', 
						'sc_chat_opts_slug_customize_skin',
						'colors'
					  );

	// Skin Chat Box Background
	add_settings_field( 'sc_chat_skin_chatbox_bg', 
						__( 'Chat Box Background', 'sc_chat' ), 
						'sc_chat_render_skin_chatbox_bg', 
						'sc_chat_opts_slug_customize_skin',
						'colors'
					  );
					  
	// Skin Chat Box Foreground
	add_settings_field( 'sc_chat_skin_chatbox_fg', 
						__( 'Chat Box Foreground', 'sc_chat' ), 
						'sc_chat_render_skin_chatbox_fg', 
						'sc_chat_opts_slug_customize_skin',
						'colors'
					  );				  
					  
	// Skin Header Background
	add_settings_field( 'sc_chat_skin_header_bg', 
						__( 'Header Background', 'sc_chat' ), 
						'sc_chat_render_skin_header_bg', 
						'sc_chat_opts_slug_customize_skin',
						'colors'
					  );

	// Skin Header Forefround
	add_settings_field( 'sc_chat_skin_header_fg', 
						__( 'Header Foreground', 'sc_chat' ), 
						'sc_chat_render_skin_header_fg', 
						'sc_chat_opts_slug_customize_skin',
						'colors'
					  );

	// Skin Submit Button Background
	add_settings_field( 'sc_chat_skin_submit_btn_bg', 
						__( 'Submit Button Background', 'sc_chat' ), 
						'sc_chat_render_skin_submit_btn_bg', 
						'sc_chat_opts_slug_customize_skin',
						'colors'
					  );
					  
	// Skin Submit Button Foreground
	add_settings_field( 'sc_chat_skin_submit_btn_fg', 
						__( 'Submit Button Foreground', 'sc_chat' ), 
						'sc_chat_render_skin_submit_btn_fg', 
						'sc_chat_opts_slug_customize_skin',
						'colors'
					  );
					  
	// Skin Mute Sound
	add_settings_field( 'sc_chat_mute_sound', 
						__( 'Mute sound', 'sc_chat' ), 
						'sc_chat_render_mute_sound', 
						'sc_chat_opts_slug_customize_skin',
						'sound'
					  );

	// Before chat header
	add_settings_field( 'sc_chat_before_chat_header', 
						__( 'Before Chat Header', 'sc_chat' ), 
						'sc_chat_render_before_chat_header', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );

					  

	// In chat header
	add_settings_field( 'sc_chat_in_chat_header', 
						__( 'In Chat Header', 'sc_chat' ), 
						'sc_chat_render_in_chat_header', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );

	// Welcome Message (Pre-chat)
	add_settings_field( 'sc_chat_prechat_welcome_msg', 
						__( 'Welcome Message (Pre-chat)', 'sc_chat' ), 
						'sc_chat_render_prechat_welcome_msg', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );
					 // 在线聊天QQ代码
	add_settings_field( 'sc_chat_online_qq', 
						__( '在线聊天QQ代码', 'sc_chat' ), 
						'sc_chat_render_online_qq', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );

	// Welcome Message (During chat)
	add_settings_field( 'sc_chat_welcome_msg', 
						__( 'Welcome Message (During chat)', 'sc_chat' ), 
						'sc_chat_render_welcome_msg', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );

	// Offline Header
	add_settings_field( 'sc_chat_offline_header', 
						__( 'Offline Header', 'sc_chat' ), 
						'sc_chat_render_offline_header', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );

	// Offline Body
	add_settings_field( 'sc_chat_offline_body', 
						__( 'Offline Body', 'sc_chat' ), 
						'sc_chat_render_offline_body', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );
		// QQ聊天
	add_settings_field( 'sc_chat_line_qq', 
						__( '网页聊天，离线时QQ聊天代码', 'sc_chat' ), 
						'sc_chat_render_line_qq', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );			 

	// End chat
	add_settings_field( 'sc_chat_end_chat_field', 
						__( 'End chat', 'sc_chat' ), 
						'sc_chat_render_end_chat_field', 
						'sc_chat_opts_slug_customize_messages',
						'messages'
					  );

	
	// Name field
	add_settings_field( 'sc_chat_name_field', 
						__( 'Name Field', 'sc_chat' ), 
						'sc_chat_render_name_field', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
	// E-mail field
	add_settings_field( 'sc_chat_email_field', 
						__( 'E-mail Field', 'sc_chat' ), 
						'sc_chat_render_email_field', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
	// Phone field
	add_settings_field( 'sc_chat_phone_field', 
						__( 'Phone Field', 'sc_chat' ), 
						'sc_chat_render_phone_field', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
	// Question field
	add_settings_field( 'sc_chat_question_field', 
						__( 'Question Field', 'sc_chat' ), 
						'sc_chat_render_question_field', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
	// Required text
	add_settings_field( 'sc_chat_req_text', 
						__( 'Required text', 'sc_chat' ), 
						'sc_chat_render_req_text', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
	// Chat Button
	add_settings_field( 'sc_chat_chat_btn', 
						__( 'Chat Button', 'sc_chat' ), 
						'sc_chat_render_chat_btn', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
					  
	// Send Button
	add_settings_field( 'sc_chat_send_btn', 
						__( 'Send Button', 'sc_chat' ), 
						'sc_chat_render_send_btn', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
					  
	// Input box placeholder
	add_settings_field( 'sc_chat_input_box_placeholder', 
						__( 'Input Box Placeholder', 'sc_chat' ), 
						'sc_chat_render_input_box_placeholder', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
	// Input Box Message
	add_settings_field( 'sc_chat_input_box_msg', 
						__( 'Input Box Message', 'sc_chat' ), 
						'sc_chat_render_input_box_msg', 
						'sc_chat_opts_slug_customize_messages',
						'forms'
					  );
					  
	// Item Purchase Key
	add_settings_field( 'sc_chat_purchase_key', 
					__( 'Item Purchase Key', 'sc_chat' ), 
					'sc_chat_render_purchase_key', 
					'sc_chat_opts_slug_general',
					'advanced'
				  );
	
}
	
/**
 * Render chat options page
 *
 * @access public
 * @return void
 */
function sc_chat_render_chat_opts() { 
	
	$active_tab = 'general';
	$opts = sc_chat_get_options();
	
	
	// Find active tab
	if( isset( $_GET[ 'tab' ] ) )  
		$active_tab = $_GET[ 'tab' ]; 
?>
	
	<style>
		.sc-page-icon { background:url('<?php echo SC_CHAT_PLUGIN_URL; ?>/assets/img/sc-icon-32.png') no-repeat; }
	</style>
	
    <div class="wrap">
        
		<div class="sc-page-icon icon32"><br/></div>
		<h2><?php _e( 'Chat Options', 'sc_chat' ); ?></h2>

		<?php settings_errors(); ?>
		
		<h2 class="nav-tab-wrapper">  
			<a href="?page=sc_chat_m_chat_opts&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Settings', 'sc_chat' ); ?></a> 
			
			<?php if( scchatvlk($opts['purchase_key']) ): ?>
				<a href="?page=sc_chat_m_chat_opts&tab=customize_skin" class="nav-tab <?php echo $active_tab == 'customize_skin' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Customize Skin', 'sc_chat' ); ?></a>  
				
				<a href="?page=sc_chat_m_chat_opts&tab=messages" class="nav-tab <?php echo $active_tab == 'messages' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Messages', 'sc_chat' ); ?></a>
			<?php endif; ?>
			
		</h2>  
		
		<form method="post" action="options.php" style="position: relative;">
			<input type="hidden" name="active_tab" value="<?php echo $active_tab; ?>" />
			
			<div class="sc_chat_info_box">
				
				<?php if( !scchatvlk($opts['purchase_key']) ): ?>
					<div class="_box">					
						<strong style="color: red;">(!) <?php _e( 'License is not valid', 'sc_chat' ); ?></strong>				
						<p>* 请到 CodeCanyon <a href="http://codecanyon.net/downloads" target="_blank">下载</a> 最新版 <br/>
							* 点击 "Download" 按钮 <br/>
							* 点击 <strong>许可证</strong> 链接
						</p>
					</div>
				<?php endif; ?>
				

				<div class="_box">
					<strong>聊天框没有出现？</strong><br>
					如果你看不到聊天框前台界面，这意味着您的主题没有<a href="http://codex.wordpress.org/Function_Reference/wp_footer" target="_blank">wp_footer()</a> 函数。所以只需在当前主题的footer.php中添加即可。
				</div>
				<div class="_box">
					<strong>使用缓存插件</strong><br>
					如果您使用任何缓存插件，当您更新外观的相关选项，你将要清除缓存。
				</div>

			</div>
			
			
			<?php
				switch( $active_tab ) {
					case 'general':
						settings_fields( 'sc_chat_opt_group_general' );
						do_settings_sections( 'sc_chat_opts_slug_general' );
						break;
						
					case 'customize_skin':
						settings_fields( 'sc_chat_opt_group_customize_skin' );
						do_settings_sections( 'sc_chat_opts_slug_customize_skin' );
						break;
						
					case 'messages':
						settings_fields( 'sc_chat_opt_group_messages' );
						do_settings_sections( 'sc_chat_opts_slug_customize_messages' );
						break;
						
				}
				
				submit_button();
			?>
		</form>
        
    </div>
	
	
<?php } 

/**
 * Render email for offline messages field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_offline_msg_email( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[offline_msg_email]" value="<?php echo $opts['offline_msg_email']; ?>" /> <span class="description"><?php _e( 'Enter e-mail address', 'sc_chat' ); ?></span> <br/>
	
	<small class="description">如果您需要SMTP配置，可以使用 <a href="http://wordpress.org/plugins/wp-smtp/" target="_blank" style="color:#666;">WP SMTP</a> 插件或者其他SMTP插件，否则将可能不能发送邮件！</small>
<?php }


/**
 * Render "get notifications by email" field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_get_notifications( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[get_notifications]" value="1" <?php checked( $opts['get_notifications'], 1 ); ?> />
	

<?php }


/**
 * Render hide when offline field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_hide_chat_when_offline( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[hide_chat_when_offline]" value="1" <?php checked( $opts['hide_chat_when_offline'], 1 ); ?> />
	

<?php }



/**
 * Render always show homepage field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_always_show_homepage( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[always_show_homepage]" value="1" <?php checked( $opts['always_show_homepage'], 1 ); ?> />
	

<?php }

/**
 * Disable in mobile devices
 * 
 * @access public
 * @return void
 */
function sc_chat_render_disable_in_mobile( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[disable_in_mobile]" value="1" <?php checked( $opts['disable_in_mobile'], 1 ); ?> />
	

<?php }

/**
 * Allowed visitors at one time
 * 
 * @access public
 * @return void
 */
function sc_chat_render_allowed_visitors( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="number" name="sc_chat_opts[allowed_visitors]" value="<?php echo $opts['allowed_visitors']; ?>" style="width: 75px;" />
	
	<br/>
	
	<small class="description"><?php _e( 'Other visitors are allowed to fill out the contact form instead of connecting to chat', 'sc_chat' ); ?></small>
	

<?php }

/**
 * Operator role
 * 
 * @access public
 * @return void
 */
function sc_chat_render_op_role( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<select name="sc_chat_opts[op_role]">
		<option value="none" <?php selected( $opts['op_role'], 'none' ); ?>><?php _e('N/A', 'sc_chat' ); ?></option>
		<option value="editor" <?php selected( $opts['op_role'], 'editor' ); ?>><?php _e( '编辑' ); ?></option>
		<option value="author" <?php selected( $opts['op_role'], 'author' ); ?>><?php _e( 'Author' ); ?></option>
		<option value="contributor" <?php selected( $opts['op_role'], 'contributor' ); ?>><?php _e( '投稿者' ); ?></option>
	</select>
	
	<a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table" target="_blank">[?]</a>

<?php }


/**
 * Render hide chatbox field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_display_chatbox( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[display_chatbox]" value="1" <?php checked( $opts['display_chatbox'], 1 ); ?> />
<?php }


/**
 * Render position field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_position( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 
	
	?>
    
    <select name="sc_chat_opts[position]">
		<option value="left" <?php selected( $opts['position'], 'left' ); ?>><?php _e( 'left-bottom', 'sc_chat' ); ?></option>
		<option value="right" <?php selected( $opts['position'], 'right' ); ?>><?php _e( 'right-bottom', 'sc_chat' ); ?></option>
	</select>

<?php }

/**
 * Render use CSS3 animations checkbox
 * 
 * @access public
 * @return void
 */
function sc_chat_render_use_css_anim( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 
	
	?>
    
    <label>
        <input type="checkbox" name="sc_chat_opts[use_css_anim]" value="1" <?php checked( $opts['use_css_anim'], 1 ); ?> />
        
        <?php _e( 'Use CSS Animations', 'sc_chat' ); ?>
    </label>

<?php }

/**
 * Render ask name field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_ask_name_field( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 
	
	?>
    
	<select name="sc_chat_opts[ask_name_field]">
		<option value="1" <?php selected( $opts['ask_name_field'], 1 ); ?>><?php _e( 'Yes', 'sc_chat' ); ?></option>
		<option value="0" <?php selected( $opts['ask_name_field'], 0 ); ?>><?php _e( 'No', 'sc_chat' ); ?></option>
		<option value="2" <?php selected( $opts['ask_name_field'], 2 ); ?>><?php _e( 'Yes, but make it optional', 'sc_chat' ); ?></option>
	</select>
        

<?php }

/**
 * Render ask phone field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_ask_phone_field( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 
	
	?>
    
	<select name="sc_chat_opts[ask_phone_field]">
		<option value="1" <?php selected( $opts['ask_phone_field'], 1 ); ?>><?php _e( 'Yes', 'sc_chat' ); ?></option>
		<option value="0" <?php selected( $opts['ask_phone_field'], 0 ); ?>><?php _e( 'No', 'sc_chat' ); ?></option>
		<option value="2" <?php selected( $opts['ask_phone_field'], 2 ); ?>><?php _e( 'Yes, but make it optional', 'sc_chat' ); ?></option>
	</select>
    
	
	<span class="description"><?php _e( 'Only in contact form', 'sc_chat' ); ?></span>

<?php }


/**
 * Render delay field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_delay( $input ) {

	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[delay]" value="<?php echo $opts['delay']; ?>" style="width: 30px" /> <span class="example"><?php _e( 'sec.', 'sc_chat' ); ?></span>

	

<?php
	
}



/**
 * Render skin box width field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_box_width( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_box_width]" value="<?php echo $opts['skin_box_width']; ?>" style="width: 50px" /> <span class="example">px</span>

	

<?php }


/**
 * Render skin box height field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_box_height( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_box_height]" value="<?php echo $opts['skin_box_height']; ?>" style="width: 50px" /> 
	<span class="example">px</span> &nbsp;&nbsp;
	<span class="description">(max-height)</span>

<?php }


/**
 * Render default radius field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_default_radius( $input ) {
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[default_radius]" value="<?php echo $opts['default_radius']; ?>" style="width: 30px" /> <span class="example">px</span>

	

<?php

}

/**
 * Render offset field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_offset( $input ) {
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[offset]" value="<?php echo $opts['offset']; ?>" style="width: 30px" /> <span class="example">px</span>


<?php

}


/**
 * Render load skin css file field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_load_skin_css( $input ) {
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[load_skin_css]" value="1" <?php checked( $opts['load_skin_css'], 1 ); ?> />
	

<?php

}


/**
 * Render compress CSS field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_compress_css( $input ) {
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[compress_css]" value="1" <?php checked( $opts['compress_css'], 1 ); ?> />
	

<?php

}

/**
 * Render compress JS field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_compress_js( $input ) {
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[compress_js]" value="1" <?php checked( $opts['compress_js'], 1 ); ?> />
	

<?php

}


/**
 * Render custom CSS field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_custom_css( $input ) {
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<textarea name="sc_chat_opts[custom_css]" style="width:100%; max-width:500px;" rows="3"><?php echo $opts['custom_css']; ?></textarea>
	

<?php

}



/**
 * Render skin type field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_type( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	
	<label>
		<input type="radio" name="sc_chat_opts[skin_type]" value="light" <?php checked( $opts['skin_type'], 'light' ); ?> /> <?php _e( 'Light', 'sc_chat' ) ;?>
	</label>
	
	&nbsp;
	
	<label>
		<input type="radio" name="sc_chat_opts[skin_type]" value="dark" <?php checked( $opts['skin_type'], 'dark' ); ?> /> <?php _e( 'Dark', 'sc_chat' ) ;?>
	</label>
	
	<p>
		<small class="description"><?php _e( 'This feature sets tones of custom colors you choiced below', 'sc_chat' ); ?></small>
	</p>
	

<?php }

/**
 * Render skin chat box background field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_chatbox_bg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_chatbox_bg]" value="<?php echo $opts['skin_chatbox_bg']; ?>" class="sc-chat-color-field" style="width: 75px" />
	
	
	
	

<?php }


/**
 * Render skin chat box foreground field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_chatbox_fg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_chatbox_fg]" value="<?php echo $opts['skin_chatbox_fg']; ?>" class="sc-chat-color-field" style="width: 75px" />
	

<?php }


/**
 * Render skin header background field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_header_bg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_header_bg]" value="<?php echo $opts['skin_header_bg']; ?>" class="sc-chat-color-field" style="width: 75px" />

	

<?php }


/**
 * Render skin header foreground field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_header_fg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_header_fg]" value="<?php echo $opts['skin_header_fg']; ?>" class="sc-chat-color-field"  style="width: 75px" />

	

<?php }


/**
 * Render submit button background field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_submit_btn_bg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_submit_btn_bg]" value="<?php echo $opts['skin_submit_btn_bg']; ?>" class="sc-chat-color-field"  style="width: 75px" />

<?php }


/**
 * Render submit button foreground field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_skin_submit_btn_fg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[skin_submit_btn_fg]" value="<?php echo $opts['skin_submit_btn_fg']; ?>" class="sc-chat-color-field"  style="width: 75px" />

<?php 
}

/**
 * Render mute sound field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_mute_sound( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="checkbox" name="sc_chat_opts[sound_package]" value="none" <?php checked( $opts['sound_package'], 'none' ); ?> />

<?php 
}


/**
 * Render before chat header field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_before_chat_header( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[before_chat_header]" value="<?php echo $opts['before_chat_header']; ?>" style="width:250px"/>

<?php }


/**
 * Render in chat header field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_in_chat_header( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[in_chat_header]" value="<?php echo $opts['in_chat_header']; ?>" style="width:250px"/>

<?php }


/**
 * Render welcome message (pre-chat) field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_prechat_welcome_msg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<textarea name="sc_chat_opts[prechat_welcome_msg]" style="width:250px;"><?php echo $opts['prechat_welcome_msg']; ?></textarea>

<?php }


/**
 * 在线QQ聊天
 */
function sc_chat_render_online_qq( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<textarea name="sc_chat_opts[online_qq]" style="width:250px;"><?php echo $opts['online_qq']; ?></textarea>

<?php }

/**
 * Render welcome message (during chat) field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_welcome_msg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<textarea name="sc_chat_opts[welcome_msg]" style="width:250px;"><?php echo $opts['welcome_msg']; ?></textarea>

<?php }


/**
 * Render chat button field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_chat_btn( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[chat_btn]" value="<?php echo $opts['chat_btn']; ?>" style="width:150px"/>

<?php }


/**
 * Render send button field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_send_btn( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[send_btn]" value="<?php echo $opts['send_btn']; ?>" style="width:150px"/>

<?php }


/**
 * Render input box message field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_input_box_msg( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[input_box_msg]" value="<?php echo $opts['input_box_msg']; ?>" style="width:250px"/>

<?php }


/**
 * Render offline header field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_offline_header( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[offline_header]" value="<?php echo $opts['offline_header']; ?>" style="width:250px"/>

<?php }


/**
 * Render offline body field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_offline_body( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<textarea name="sc_chat_opts[offline_body]" rows="4" style="width:250px;"><?php echo $opts['offline_body']; ?></textarea>

<?php }

/**
 * QQ聊天
 * 
 */
function sc_chat_render_line_qq( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<textarea name="sc_chat_opts[line_qq]" rows="4" style="width:250px;"><?php echo $opts['line_qq']; ?></textarea>

<?php }

/**
 * Render end chat field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_end_chat_field( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[end_chat_field]" value="<?php echo $opts['end_chat_field']; ?>" style="width:150px"/>
	
<?php }


/**
 * Render name field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_name_field( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[name_field]" value="<?php echo $opts['name_field']; ?>"style="width:250px;" />

<?php }


/**
 * Render email field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_email_field( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[email_field]" value="<?php echo $opts['email_field']; ?>"style="width:250px;" />

<?php }

/**
 * Render phone field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_phone_field( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[phone_field]" value="<?php echo $opts['phone_field']; ?>"style="width:250px;" />

<?php }


/**
 * Render question field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_question_field( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[question_field]" value="<?php echo $opts['question_field']; ?>"style="width:250px;" />
	
	
<?php }


/**
 * Render required text field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_req_text( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[req_text]" value="<?php echo $opts['req_text']; ?>"style="width:250px;" />

<?php }


/**
 * Render input placeholder field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_input_box_placeholder( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 

	?>
	
	<input type="text" name="sc_chat_opts[input_box_placeholder]" value="<?php echo $opts['input_box_placeholder']; ?>"style="width:250px;" />
	
	
<?php }



/**
 * Render purchase key field
 * 
 * @access public
 * @return void
 */
function sc_chat_render_purchase_key( $input ) { 
	
	// Get options
	$opts = sc_chat_get_options(); 
	
	// Get the domain without subdomain or www
	$host = $_SERVER['HTTP_HOST'];
	preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $domain);
	
	?>
	
	
	
	<?php
	$license = get_option( 'sc_chat_license' );
	
	if( !defined('SC_CHAT_LICENSE_KEY') ) {

		// Check license key
		if( !empty( $license['buyer'] ) ) {
			
			// Display license
			echo '<div><strong><span style="color:green">' . $license['license']  . '</span> - ' . $license['buyer'] . '</strong><br/><small style="color:#ccc">' . $license['purchase_date'] . '</small></div>';
			
		} else {
			
			scchatvlk( $opts['purchase_key'], true );
			
			echo '<div style="color:red"> ' . __( 'License is not valid', 'sc_chat' ) . ' &nbsp; <a href="http://codecanyon.net/downloads" title="' . __( 'It can be found in Licence Certificate under Downloads tab', 'sc_chat' ) . '" target="_blank">[?]</a></div>';
		}

	// License is active
	} else {
		echo '<strong style="color:green">Active</strong>';
	}
	
	// License key
	$_key = (defined('SC_CHAT_LICENSE_KEY')) ? SC_CHAT_LICENSE_KEY : $opts['purchase_key'];

	// License should be shown?
	$input_type = (!defined('SC_CHAT_LICENSE_KEY')) ? 'text' : 'hidden';
	?>
	
	<input type="<?php echo $input_type; ?>" name="sc_chat_opts[purchase_key]" value="<?php echo $_key; ?>" style="width:270px"/>
	

<?php }



/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 * 
 * @access public
 * @return array
 */
function sc_chat_options_validate( $input ) {
	
	// Get default values
	$output = $defaults = sc_chat_get_options();
	
	
	switch( $_POST['active_tab'] ) {
		
		// General settings
		case 'general':
			
			// checkbox
			$output['hide_chat_when_offline'] = ( @$input['hide_chat_when_offline'] == 1) ? 1 : 0;
			$output['display_chatbox'] = (@$input['display_chatbox'] == 1) ? 1 : 0;
			$output['always_show_homepage'] = (@$input['always_show_homepage'] == 1) ? 1 : 0;
			$output['disable_in_mobile'] = (@$input['disable_in_mobile'] == 1) ? 1 : 0;
			$output['get_notifications'] = (@$input['get_notifications'] == 1) ? 1 : 0;
			
			// text
			$output['offline_msg_email'] = trim( $input['offline_msg_email'] );
			$output['purchase_key'] = trim( $input['purchase_key'] );
			
			// int
			$output['allowed_visitors'] = intval( $input['allowed_visitors'] );
			
			// select
			$output['op_role'] = $input['op_role'];
			
			// Update operator role
			sc_chat_update_op_role( $input['op_role'] );
			
			// Validate license key
			scchatvlk( trim( $input['purchase_key'] ), true );
			
			break;
			
		
		// Customize skin
		case 'customize_skin':
			
			// custom CSS
			$output['custom_css'] = trim( $input['custom_css'] );
			
			
			// int
			$output['delay'] = intval( $input['delay'] );
			$output['offset'] = intval( $input['offset'] );
			$output['default_radius'] = intval( $input['default_radius'] );
			$output['skin_box_width'] = intval( $input['skin_box_width'] );
			$output['skin_box_height'] = intval( $input['skin_box_height'] );
			
			// checkbox
			$output['use_css_anim'] = (@$input['use_css_anim'] == 1) ? 1 : 0;
			$output['load_skin_css'] = (@$input['load_skin_css'] == 1) ? 1 : 0;
			$output['compress_css'] = (@$input['compress_css'] == 1) ? 1 : 0;
			$output['compress_js'] = (@$input['compress_js'] == 1) ? 1 : 0;
			$output['sound_package'] = (@$input['sound_package'] == 'none') ? 'none' : 'basic';
			
			// select
			$output['ask_name_field'] = $input['ask_name_field'];
			$output['ask_phone_field'] = $input['ask_phone_field'];
			$output['position'] = $input['position'];
			
			// Color inputs
			$output['skin_chatbox_bg'] = $input['skin_chatbox_bg'];
			$output['skin_chatbox_fg'] = $input['skin_chatbox_fg'];
			$output['skin_header_bg'] = $input['skin_header_bg'];
			$output['skin_header_fg'] = $input['skin_header_fg'];
			$output['skin_submit_btn_bg'] = $input['skin_submit_btn_bg'];
			$output['skin_submit_btn_fg'] = $input['skin_submit_btn_fg'];
			
			// radio
			$output['skin_type'] = $input['skin_type'];
			
			break;
			
		// Messages
		case 'messages':
			
			// text
			$output['before_chat_header'] = trim( $input['before_chat_header'] );
			$output['in_chat_header'] = trim( $input['in_chat_header'] );
			$output['prechat_welcome_msg'] = trim( $input['prechat_welcome_msg'] );
			$output['online_qq'] = trim( $input['online_qq'] );
			$output['welcome_msg'] = trim( $input['welcome_msg'] );
			$output['chat_btn'] = trim( $input['chat_btn'] );
			$output['input_box_msg'] = trim( $input['input_box_msg'] );
			$output['offline_header'] = trim( $input['offline_header'] );
			$output['offline_body'] = trim( $input['offline_body'] );
			$output['line_qq'] = trim( $input['line_qq'] );
			$output['end_chat_field'] = trim( $input['end_chat_field'] );
			$output['name_field'] = trim( $input['name_field'] );
			$output['email_field'] = trim( $input['email_field'] );
			$output['phone_field'] = trim( $input['phone_field'] );
			$output['req_text'] = trim( $input['req_text'] );
			$output['chat_btn'] = trim( $input['chat_btn'] );
			$output['input_box_placeholder'] = trim( $input['input_box_placeholder'] );
			$output['input_box_msg'] = trim( $input['input_box_msg'] );
			$output['question_field'] = trim( $input['question_field'] );
			$output['send_btn'] = trim( $input['send_btn'] );
			
			
			
			break;
		
	}
	
	return apply_filters( 'sc_chat_options_validate', $output, $input, $defaults );
}
?>