<?php
/**
 * SCREETS © 2013
 *
 * Administration functions
 *
 */

add_action( 'admin_menu', 'sc_chat_admin_menu', 9 );


/**
 * Install the plugin on activation
 *
 * @access public
 * @return void
 */
function sc_chat_activate() {
	global $wpdb;
	
	// Get options
	$opts = sc_chat_get_options();
	
	// Create chat lines
	$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "chat_lines` (
		  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `author` varchar(32) DEFAULT NULL,
		  `gravatar` varchar(32) DEFAULT NULL,
		  `receiver_ID` varchar(32) NOT NULL DEFAULT 'OP',
		  `chat_line` varchar(700) NOT NULL,
		  `chat_date` int(10) NOT NULL,
		  PRIMARY KEY (`ID`),
		  KEY `chat_date` (`chat_date`)
		) DEFAULT CHARSET=utf8;"
	);
				
				
	// Create chat logs table
	$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "chat_logs` (
		  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `visitor_ID` int(10) unsigned NOT NULL,
		  `chat_date` int(10) NOT NULL,
		  `sender` varchar(32) DEFAULT NULL,
		  `sender_email` varchar(64) DEFAULT NULL,
		  `chat_line` varchar(700) NOT NULL,
		  PRIMARY KEY (`ID`),
		  KEY `chat_date` (`chat_date`)
		) DEFAULT CHARSET=utf8;"
	);
	

	// Create chat online table
	$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "chat_online` (
		  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `visitor_ID` int(10) unsigned DEFAULT NULL,
		  `name` varchar(32) DEFAULT NULL,
		  `email` varchar(64) DEFAULT NULL,
		  `gravatar` varchar(32) DEFAULT NULL,
		  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `ip_address` int(11) unsigned DEFAULT NULL,
		  `user_agent` varchar(120) DEFAULT NULL,
		  `type` tinyint(1) NOT NULL DEFAULT '1',
		  `status` tinyint(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`ID`),
		  UNIQUE KEY `name` (`name`),
		  KEY `last_activity` (`last_activity`),
		  KEY `status` (`status`)
		) DEFAULT CHARSET=utf8;"
	);

	// Create chat visitors table
	$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "chat_visitors` (
		  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(32) DEFAULT NULL,
		  `email` varchar(64) DEFAULT NULL,
		  `gravatar` varchar(32) DEFAULT NULL,
		  `ip_address` int(11) unsigned DEFAULT NULL,
		  `user_agent` varchar(120) DEFAULT NULL,
		  PRIMARY KEY (`ID`),
		  UNIQUE KEY `gravatar` (`gravatar`)
		) DEFAULT CHARSET=utf8;"
	);
	
	/**
	 * Update administration role
	 */
	$admin_role = get_role( 'administrator' );
	$admin_role->add_cap( 'chat_with_users' ); 
	
	/** 
	 * Update operator role
	 */
	sc_chat_update_op_role( 'editor' );
	
}

/**
 * Create / Update operator role
 *
 * @access public
 * @return void
*/
function sc_chat_update_op_role( $role ) {
	
	// First clean rol
	remove_role( 'sc_chat_op' );

	// Create operator role
	$op_role = add_role( 'sc_chat_op', __( 'Chat Operator', 'sc_chat' ) );

	// Add common operator capability
	$op_role->add_cap( 'chat_with_users' );
	
	switch( $role ) {
		/**
		 * N/A
		 */
		case 'none':
			
			$op_role->add_cap( 'read' );

			break;
			
		/**
		 * Editor
		 */
		case 'editor':

			// Get editor role
			$editor = get_role('editor' );


			// Add editor caps to chat operator
			foreach( $editor->capabilities as $custom_role => $v ) {
				$op_role->add_cap( $custom_role );
			}

			break;
			
		/**
		 * Author
		 */
		case 'author':
			
			// Get author role
			$author = get_role('author' );

			// Add editor caps to chat operator
			foreach( $author->capabilities as $custom_role => $v )
				$op_role->add_cap( $custom_role );

			break;
			
			
		/**
		 * Contributor
		 */
		case 'contributor':
			
			// Get author role
			$contributor = get_role('contributor' );

			// Add editor caps to chat operator
			foreach( $contributor->capabilities as $custom_role => $v )
				$op_role->add_cap( $custom_role );

			break;
			
	}
}


/**
 * Render Chat Console Template
 *
 * @access public
 * @return void
*/
function sc_chat_console_template() {
	
	require SC_CHAT_PLUGIN_PATH . '/core/templates/chat_console.php';
	
}


/**
 * Setup the Admin menu in WordPress
 *
 * @access public
 * @return void
 */
function sc_chat_admin_menu() {
	
	$opts = sc_chat_get_options();
	
	/**
	 * Menu for Admins
	 */
	add_menu_page('', __('Chat Console', 'sc_chat'), 'administrator', 'sc_opt_pg_a', 'sc_chat_console_template', SC_CHAT_PLUGIN_URL .'/assets/img/sc-icon-16.png', 40);
	
	// Chat logs
	if( scchatvlk($opts['purchase_key']) ) {
		add_submenu_page( 
			'sc_opt_pg_a', 
			__('Chat Logs', 'sc_chat'),
			__('Logs', 'sc_chat'),
			'administrator',
			'sc_chat_m_chat_logs',
			'sc_chat_render_chat_logs'
		);
	}
	
	// Options
	add_submenu_page( 
		'sc_opt_pg_a', 
		__('Chat Options', 'sc_chat'),
		__('Options', 'sc_chat'),
		'administrator',
		'sc_chat_m_chat_opts',
		'sc_chat_render_chat_opts'
	);
	
	/**
	 * Menu for Operators
	 */
	add_menu_page('', 'Chat Console', 'sc_chat_op', 'sc_opt_pg', 'sc_chat_console_template', SC_CHAT_PLUGIN_URL .'/assets/img/sc-icon-16.png', 41);
	
	// Chat logs
	add_submenu_page( 
		'sc_opt_pg', 
		__('Chat Logs', 'sc_chat'),
		__('Logs', 'sc_chat'),
		'sc_chat_op',
		'sc_chat_m_chat_logs',
		'sc_chat_render_chat_logs'
	);
	

	// Call register options function
	add_action( 'admin_init', 'sc_chat_register_options' );
	
}


/**
 * Get operator name
 *
 * @access public
 * @return string Operator name of user
 */
function sc_chat_get_operator_name( $user_id = null ) {
	
	if( empty( $user_id) )
		$user_id = get_current_user_id();
	
	// Get operator name
	$op_name = get_user_meta( $user_id, 'sc_chat_op_name', true );
	
	// Op name isn't defined yet, create new one for user
	if( empty( $op_name ) ) {
		
		global $current_user;
		
		// Get currently logged user info
		get_currentuserinfo();
		
		$op_name = sanitize_key( $current_user->display_name );
		
		// Update user meta as well (for later usage)
		update_user_meta( $user_id, 'sc_chat_op_name', $op_name );
	}
	
	return $op_name;
}


/**
 * Render chat options metabox
 *
 * @access public
 * @return void
 */
function sc_chat_chat_render_opts_meta( $post ) {
	global $post;
	
	// Get fields
    $f_show_chatbox = get_post_meta( $post->ID, 'sc_chat_opt_show_chatbox', true );  
	
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'sc_chat_opts_nonce' );
	
	// Print Show Chatbox field
	echo '<label for="sc_chat_opt_show_chatbox">';
	
	echo '<input type="checkbox" name="sc_chat_opt_show_chatbox" id="sc_chat_opt_show_chatbox" ' . checked( $f_show_chatbox, 'on', false ) . ' value="on" /> ' . __( 'Always show chatbox here', 'sc_chat' );
	 
	echo '</label>';

}


/**
 * Save chat options
 *
 * @access public
 * @return void
 */
function sc_chat_save_chat_opts( $post_id ) {
	
	$_nonce = '';
	
	// Verify if this is an auto save routine. 
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if( !empty( $_POST['sc_chat_opts_nonce'] ) )
		$_nonce = $_POST['sc_chat_opts_nonce'];
	
	// Verify this came from the our screen and with proper authorization
	if ( !wp_verify_nonce( $_nonce, plugin_basename( __FILE__ ) ) )
		return;
		
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}
	
	// Sanitize user input
	$f_show_chat_box = ( isset($_POST['sc_chat_opt_show_chatbox']) ) ? 'on' : 'off';
	
	// Add / update post metas
	add_post_meta( $post_id, 'sc_chat_opt_show_chatbox', $f_show_chat_box, true ) or update_post_meta( $post_id, 'sc_chat_opt_show_chatbox', $f_show_chat_box );

}


/**
 * Render chat logs page
 *
 * @access public
 * @return void
 */
function sc_chat_render_chat_logs() {
	global $wpdb;
	
	// Create an instance of our package class...
	$logs = new SC_chat_logs();
	
	// Fetch, prepare, sort, and filter our data...
	$logs->prepare_items();
    
	// Get visitor information
	if( !empty( $_REQUEST['visitor_ID'] ) ) {
		$visitor = $wpdb->get_row(
			$wpdb->prepare( 
				'SELECT * FROM ' . $wpdb->prefix . 'chat_visitors 
				WHERE `ID` = %d LIMIT 1',
				$_REQUEST['visitor_ID']
			)
		);
	}
	
	
    ?>
	
	<script type="text/javascript">
		jQuery(document).ready( function() {
			
			jQuery('.wp-list-table .delete a').click( function() {
				ask = confirm('<?php _e( 'Are you sure you want to delete?', 'sc_chat' ); ?>');
				
				if( ask == false )
					return false;
			});
			
		});
	</script>
	<style>
		.sc-page-icon { 
			background:url('<?php echo SC_CHAT_PLUGIN_URL; ?>/assets/img/sc-icon-32.png') no-repeat; 
		}
	</style>
	
    <div class="wrap">
        
		<?php if( !empty( $visitor) or empty( $_REQUEST['action'] ) or isset( $_REQUEST['log_s']) ) { ?>
			<div class="sc-page-icon icon32"><br/></div>
			<h2>
				<?php 
				
				// Chat logs
				if( @$_REQUEST['action'] != 'edit' ) {
					
					_e( 'Chat Logs', 'sc_chat' );
				
				// Add visitor name and email
				} else {
					
					echo '<a href="?page=' . $_REQUEST['page'] .'">' . __( 'Chat Logs', 'sc_chat' ) . '</a>';
					
					echo ': ' . $visitor->name . ' (' . $visitor->email . ')';
					
				}
					
				?>
			
			</h2>
        
			<?php
			/**
			 * Show chat logs list
			 */
			if( @$_GET['action'] != 'edit' ): ?>
			
				<form method="get">
					
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
					
					<?php $logs->display(); ?>
					
				</form>
			
			<?php 
			/**
			 * Show single chat transcript
			 */
			else: 
				// Get log transcipt
				$transcript = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM ' . $wpdb->prefix . 'chat_logs
						WHERE visitor_ID = %d',
						$_REQUEST['visitor_ID']
					)
				);
				
				
				?>
				
				<div class="sc-chat-transcript">
					<ul>
				
				<?php
				$i = 0;
				foreach( $transcript as $log ): ?>
					
					<li>
						
						<!-- Time -->
						<div class="sc-chat-trans-time">
							<?php echo date_i18n( get_option('date_format') .' H:i:s', $log->chat_date ); ?>
						</div>
						
						<!-- Author -->
						<div class="sc-chat-trans-author <?php echo ( !empty( $log->sender ) ) ? 'sc-sender' : 'sc-visitor'; ?>">
							<?php 
							if( !empty( $log->sender ) ) 
								echo '<a href="mailto:'.$log->sender_email.'">' . $log->sender . '</a>';
							else 
								echo '<a href="mailto:'.$visitor->email.'">' . $visitor->name . '</a>';
							?>
						</div>
						
						<!-- Message -->
						<div class="sc-chat-trans-msg">
							<?php echo $log->chat_line; ?>
						</div>
						
					</li>
					
				<?php
					$i++;
				
				endforeach;
				
				echo '</ul></div>';
			
			endif; ?>
		
		<?php } else { ?>
			
			<p>No chat log found for this user.</p>
			
		<?php } ?>
	</div>

<?php

}


/**
 * Get browser info
 *
 * @access public
 * @return array Browser info
 */
function sc_chat_browser_info( $user_agent = null ) {
	
	if( empty( $user_agent ) )
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
	$ub = 'Unknown';
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version = "";

	// First get the platform

	if (preg_match('/linux/i', $user_agent)) {
		$platform = 'Linux';
	}
	elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
		$platform = 'Mac';
	}
	elseif (preg_match('/windows|win32/i', $user_agent)) {
		$platform = 'Windows';
	}

	// Next get the name of the useragent yes seperately and for good reason

	if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
		$bname = 'Internet Explorer';
		$ub = "IE";
	}
	elseif (preg_match('/Firefox/i', $user_agent)) {
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	}
	elseif (preg_match('/Chrome/i', $user_agent)) {
		$bname = 'Google Chrome';
		$ub = "Chrome";
	}
	elseif (preg_match('/Safari/i', $user_agent)) {
		$bname = 'Apple Safari';
		$ub = "Safari";
	}
	elseif (preg_match('/Opera/i', $user_agent)) {
		$bname = 'Opera';
		$ub = "Opera";
	}
	elseif (preg_match('/Netscape/i', $user_agent)) {
		$bname = 'Netscape';
		$ub = "Netscape";
	}

	// Finally get the correct version number

	$known = array(
		'Version',
		$ub,
		'other'
	);
	$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

	if ( !preg_match_all( $pattern, $user_agent, $matches) ) {

		// We have no matching number just continue

	}

	// See how many we have

	$i = count($matches['browser']);

	if ($i != 1) {

		// we will have two since we are not using 'other' argument yet
		// See if version is before or after the name

		if (strripos($user_agent, "Version") < strripos($user_agent, $ub)) {
			$version = @$matches['version'][0];
		}
		else {
			$version = @$matches['version'][1];
		}
	}
	else
		$version = @$matches['version'][0];
	

	// Check if we have a number

	if ($version == null || $version == "")
		$version = "?";

	
	// Make version simpler
	$_version = explode( '.', $version );
	
	$version = $_version[0];

	return array(
		'user_agent' 	=> $user_agent,
		'name' 			=> $ub,
		'version' 		=> $version,
		'platform' 		=> $platform,
		'pattern' 		=> $pattern
	);
	
}
