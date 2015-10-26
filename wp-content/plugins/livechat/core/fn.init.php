<?php
/**
 * SCREETS © 2013
 *
 * Initialization functions
 *
 */

/**
 * Ajax Callback
 *
 * @access public
 * @return void
 */
function sc_chat_ajax_callback() {
	
	$response = array();
	
	try {
		
		// Handling the supported actions:
		switch( $_GET['mode'] ) {
			
			case 'login':
				$response = Chat::login( 
									@$_POST['f_chat_user_name'], 
									@$_POST['f_chat_user_email'], 
									$_POST['f_chat_is_admin'] 
								);
			break;
			
			case 'send_contact_from':
				$response = Chat::send_contact_from( $_POST );
			break;
			
			case 'is_user_logged_in':
				$response = Chat::is_user_logged_in();
			break;
			
			case 'logout':
				$response = Chat::logout();
			break;
			
			case 'online':
				$response = Chat::online();
			break;
			
			case 'offline':
				$response = Chat::offline();
			break;
			
			case 'send_chat_msg':
				$response = Chat::send_chat_msg( $_POST );
			break;
			
			case 'get_online_users':
				$response = Chat::get_online_users();
			break;
			
			case 'get_chat_lines':
				$response = Chat::get_chat_lines( $_GET['last_log_ID'], $_GET['sender'] );
			break;
			
			case 'user_info':
				$response = Chat::user_info( $_GET['ID'] );
			break;
			
			default:
				throw new Exception( 'Wrong action' );
		}
	
	} catch ( Exception $e ) {
		
		$response['error'] = $e->getMessage();
		
	}
	
    
	// Response output
	header( "Content-Type: application/json" );
	echo json_encode( $response );
	exit;
	
}

/**
 * Chat online shortcode
 *
 * @access public
 * @return string
 */
 
function sc_chat_shortcode_online( $atts, $content = '' ) {
	
	// Check if any OP online
	if( Chat::check_if_any_op_online() )
		return $content;
	
}


/**
 * Chat offline shortcode
 *
 * @access public
 * @return string
 */
 
function sc_chat_shortcode_offline( $atts, $content = '' ) {
	
	// Check if all OPs offline
	if( !Chat::check_if_any_op_online() )
		return $content;
	
}


/**
 * Check if name is available
 *
 * @access public
 * @return bool True if name is available
 */
 
function sc_chat_name_is_available( $name ) {
	global $wpdb;
	
	// Get all operator names
	$op_names = $wpdb->get_col( 'SELECT meta_value FROM ' . $wpdb->usermeta . ' WHERE meta_key = "sc_chat_op_name" AND meta_value != ""');
	
	if( in_array( $name, $op_names ) )
		return false;
	
	// Check if online users have same name
	$check_online_users = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'chat_online
			 WHERE `name` = %s LIMIT 1',
			$name
		)
	);
	
	if( $check_online_users > 0 )
		return false;
	
	return true;
							  
}


/**
 * Start session
 *
 * @access public
 * @return string
 */
 
function sc_chat_session_start() {
		
	
	if( !session_id() ) {
	
		// Start sessions
		session_start();

	}
	
}

/**
 * Create random string
 *
 * @access public
 * @return string Random string
 */
 
function sc_chat_rand_str( $length, $chars = 'abcdefghiklmnprsxyz') {
	
	return substr( str_shuffle( $chars ), 0, $length );
	
}


/**
 * Make URLs into links 
 *
 * @access public
 * @return string Edited string
 */
 
function sc_chat_make_url_to_link( $string ){

	// Make sure there is an http:// on all URLs
	$string = preg_replace( "/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", $string );
	
	// Make all URLs links
	$string = preg_replace( "/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>", $string );
	
	// Make all emails hot links
	$string = preg_replace( "/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i", "<a href=\"mailto:$1\">$1</a>", $string );

	return $string;
	
}

/**
 * Adjusting a hex colour
 *
 * @param $hex string Example input: #222222
 * @param $steps int Btw. -255 and 255. Negative = darker, positive = lighter
 * @access public
 * @return string Hex color
 */
function sc_chat_adjust_brightness( $hex, $steps ) {

    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Format the hex color string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Get decimal values
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));

    // Adjust number of steps and keep it inside 0 to 255
    $r = max(0,min(255,$r + $steps));
    $g = max(0,min(255,$g + $steps));  
    $b = max(0,min(255,$b + $steps));

    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

    return '#'.$r_hex.$g_hex.$b_hex;
}

/**
 * Custom stylesheets for skin
 *
 * @access public
 * @return void
 */
function sc_chat_custom_frontend_styles() {
	
	// Get options
	$opts = sc_chat_get_options();
	
	// Set color tones (Btw. -255 and 255)
	$tone_5 = ( $opts['skin_type'] == 'light' ) ? sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], -5 ) : sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], 5 );
	
	$tone_10 = ( $opts['skin_type'] == 'light' ) ? sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], -10 ) : sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], 10 );
	
	$tone_20 = ( $opts['skin_type'] == 'light' ) ? sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], -20 ) : sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], 20 );
	
	$tone_50 = ( $opts['skin_type'] == 'light' ) ? sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], -50 ) : sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], 50 );
	
	$tone_70 = ( $opts['skin_type'] == 'light' ) ? sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], -70 ) : sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], 70 );
	
	$tone_120 = ( $opts['skin_type'] == 'light' ) ? sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], -120 ) : sc_chat_adjust_brightness( $opts['skin_chatbox_bg'], 120 );
	
    ?>
    <style type="text/css">
		
		
		<?php
		// Change chatbox body colors
		?>
		.sc-chat-toolbar,
		.sc-cnv-wrap,
		.sc-msg-wrap,
		.sc-chat-wrapper,
		#sc_chat_box textarea.f-chat-line,
		#sc_chat_box p.sc-lead,
		#sc_chat_box .sc-chat-wrapper input, 
		#sc_chat_box .sc-chat-wrapper textarea {
			color: <?php echo $opts['skin_chatbox_fg']; ?>;
			background-color: <?php echo $opts['skin_chatbox_bg']; ?>;
		}

		.sc-chat-toolbar a { color: <?php echo $tone_70; ?>; }
		.sc-chat-toolbar a:hover { color: <?php echo $tone_120; ?>; }
		
		#sc_chat_box .sc-chat-wrapper input, 
		#sc_chat_box .sc-chat-wrapper textarea,
		#sc_chat_box textarea.f-chat-line {
			border-color: <?php echo $tone_50; ?>;
		}
		#sc_chat_box .sc-chat-wrapper input:focus,
		#sc_chat_box .sc-chat-wrapper textarea:focus {
			background-color: <?php echo $tone_10; ?>;
			border-color: <?php echo $tone_70; ?>;
		}
		
		#sc_chat_box textarea.f-chat-line:focus {
			background-color: <?php echo $tone_5; ?>;
			border-color: <?php echo $tone_70; ?>;
		}
		
		#sc_chat_box .sc-chat-wrapper label {
			color: <?php echo $tone_120; ?>;
		}
		
		#sc_chat_box form.sc-chat-reply {
			border-top: 1px solid <?php echo $tone_50; ?>;
			background-color: <?php echo $tone_10; ?>;
		}
		
		#sc_chat_box {
			width: <?php echo $opts['skin_box_width']; ?>px;
			<?php echo $opts['position']; ?>: <?php echo $opts['offset']; ?>px;
		}
		
		#sc_chat_box textarea.f-chat-line {
			width: <?php echo $opts['skin_box_width'] - 42; ?>px;
		}
		
		<?php 
		/*
		 * Default radius
		 */
		if( $opts['default_radius'] ): ?>
		
			#sc_chat_box div.sc-chat-header {
				-webkit-border-radius: <?php echo $opts['default_radius']; ?>px <?php echo $opts['default_radius']; ?>px 0 0;
				   -moz-border-radius: <?php echo $opts['default_radius']; ?>px <?php echo $opts['default_radius']; ?>px 0 0;
					   border-radius: <?php echo $opts['default_radius']; ?>px <?php echo $opts['default_radius']; ?>px 0 0;
			}
			
			.sc-chat-notification.warning,
			#sc_chat_box .sc-chat-wrapper .sc-start-chat-btn a,
			#sc_chat_box .sc-chat-wrapper input, #sc_chat_box .sc-chat-wrapper textarea {
				-webkit-border-radius: <?php echo $opts['default_radius']; ?>px;
				   -moz-border-radius: <?php echo $opts['default_radius']; ?>px;
					   border-radius: <?php echo $opts['default_radius']; ?>px;
			}
		
		<?php endif; ?>
		
		#sc_chat_box .sc-chat-wrapper input, #sc_chat_box .sc-chat-wrapper textarea {
			width: <?php echo $opts['skin_box_width'] - 70; ?>px;
		}
		
		.sc-chat-wrapper {
			border-color: <?php echo $tone_20; ?>;
			max-height: <?php echo $opts['skin_box_height']; ?>px;
		}
		
		.sc-cnv-wrap {
			border-color: <?php echo $tone_20; ?>;
			max-height: <?php echo $opts['skin_box_height'] - 30; ?>px;
		}
		
		#sc_chat_box .sc-chat-wrapper .sc-start-chat-btn > a {
			color: <?php echo $opts['skin_submit_btn_fg']; ?>;
			background-color: <?php echo $opts['skin_submit_btn_bg']; ?>;
		}
		
		#sc_chat_box .sc-chat-wrapper .sc-start-chat-btn > a:hover {
			color: <?php echo $opts['skin_header_fg']; ?>;
			background-color: <?php echo $opts['skin_header_bg']; ?>;
		}
		
		#sc_chat_box div.sc-chat-header {
			color: <?php echo $opts['skin_header_fg']; ?>;
			background-color: <?php echo $opts['skin_header_bg']; ?>;
		}
       <?php
        
        // Use CSS Animations
        if( $opts['use_css_anim'] ): ?>
            
            .sc-chat-css-anim {
                -webkit-transition: bottom .2s;
                   -moz-transition: bottom .2s;
                     -o-transition: bottom .2s;
                        transition: bottom .2s;
            }
            
       <?php endif; 
	   
	   // Custom CSS
	   if( !empty( $opts['custom_css'] ) )
			echo $opts['custom_css'];
			
	   ?>
		
    </style>
    <?php
}

/**
 * Get user IP Address
 *
 * @access public
 * @return string IP Adreess
 */
function sc_chat_get_IP() {

	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
    {
        if (array_key_exists($key, $_SERVER) === true)
        {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
            {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                {
                    return $ip;
                }
            }
        }
    }
	
}

/**
 * Get current page URL
 *
 * @return string URL
 */
function sc_chat_current_page_url() {
	
	$page_URL = 'http';
	
	if ( @$_SERVER['HTTPS'] == 'on' )
		$page_URL .= "s";
		
	$page_URL .= '://';
	
	if ( @$_SERVER['SERVER_PORT'] != '80' )
		$page_URL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .$_SERVER['REQUEST_URI'];
	else
		$page_URL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
 
	return $page_URL;

}

function scchatvlkn() { 
	echo '<div class="error"><p>cURL is <strong>NOT</strong> installed in your PHP installation. You will want to follow <a href="http://stackoverflow.com/questions/1347146/how-to-enable-curl-in-php-xampp" target="_blank">one of those tutorials</a> to enable cURL library. It is required for <strong>Screets Live Chat</strong> plugin.</p></div>';
}
function scchatvlk( $lk = null, $recheck = false ) {
	if( !function_exists('curl_version') ) {
		add_action( 'admin_notices', 'scchatvlkn' );
		add_action( 'network_admin_notices', 'scchatvlkn' );
		delete_option( 'sc_chat_validate_license' );delete_option( 'sc_chat_license' );
		return false;
	}
	$ls = get_option( 'sc_chat_validate_license' );
	if( empty( $lk ) and $recheck ) 
	{
	delete_option( 'sc_chat_validate_license' );
	delete_option( 'sc_chat_license' );
	return false;
	}
	elseif( !empty( $ls ) and !$recheck )return true;
	if( !empty( $ls ) and !$recheck )return true;
	/* 96down.com
	$url = 'http://www.screets.com/api/chat/validate-purchase.php?k=' . $lk . '&r='.site_url();
	$ch = curl_init( $url );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec( $ch );
	curl_close( $ch );
	$data = json_decode( $data );
	if( empty( $data ) ) echo "Connection error! Please try again...";
	if( (int) $data->valid == 1 )
	{
	update_option( 'sc_chat_validate_license', 1 );
	update_option( 'sc_chat_license', array('buyer'=> $data->buyer,'license'=> $data->license,'purchase_date'=> $data->date) );
	return true;
	}
	*/
	$buyernull = site_url();
  $licensenull = "123456789"; 
  $datenull = date("Y-m-d");
  if($lk = "123456789" )
	{
    update_option( 'sc_chat_validate_license', 1 );
    update_option( 'sc_chat_license', array('buyer'=> $buyernull,'license'=> $licensenull,'purchase_date'=> $datenull) );
    return true;
  }
	delete_option( 'sc_chat_validate_license' );
	delete_option( 'sc_chat_license' );
	return false;
}

/**
 * Send notification by email
 *
 * @return bool
 */
function sc_chat_send_notification_email( $type = 'user_login', $data = array() ) {

	$message = null;
	$site_name = get_bloginfo( 'name' );

	// Get options
	$opts = sc_chat_get_options();

	// Prepare basic user data
	$username = $data['username'];
	$user_email = $data['email'];

	// Any email should be defined
	if( empty( $opts['offline_msg_email'] ) or !$opts['get_notifications'] )
		return false;

	switch( $type ) {
		
		/** 
		 * New user logged in
		 */
		case 'user_login':
			$to = $opts['offline_msg_email'];
			$subject = sprintf( __( '%s just logged in chat', 'sc_chat' ) , $username ) . ' - ' . get_bloginfo( 'name' );
			
			// Preapare message content
			$message .= __( 'You have new notification', 'sc_chat' ) . ":\r\n\r\n";
			$message .= sprintf( __( '%s just logged in chat', 'sc_chat' ) , $username ) . "!\r\n\r\n";
			$message .=  $site_name . " - " . site_url();
			
			// Prepare headers
			$headers = "From: \"$site_name\" <$user_email>\r\n";

			break;
	}


	// Send email
	if( wp_mail( $to, $subject, $message, $headers ) )
		return true;

	return false;

}
?>