<?php
/**
 * SCREETS-SESSION © 2013
 * Chat Line
 *
 */

 
class Chat_line extends Chat_base {
	
	protected $chat_line 		= '';
	protected $author 			= '';
	protected $gravatar 		= '';
	protected $email 			= '';
	protected $receiver_ID 	= 'OP';
	protected $visitor_ID	 	= 0;
	
	
	/**
	 * Save message to current chat line
	 *
	 * @access public
	 * @return int Chat line ID
	 */
	public function save() {
		global $wpdb;
		
		// Prepare chat message data
		$data = array(
			'author'		=> $this->author,
			'gravatar'		=> $this->gravatar,
			'receiver_ID'	=> $this->receiver_ID,
			'chat_line'		=> $this->chat_line,
			'chat_date'		=> current_time( 'timestamp', 1 )
		);
		
		// Insert chat line
		$wpdb->insert( $wpdb->prefix . 'chat_lines', $data, array( '%s', '%s', '%s', '%s', '%d' ) );
		
		// Save chat line insert id
		$insert_id = $wpdb->insert_id;
		
		/*
		 * Save Chat log
		 */
		$data = null;
		// Prepare data from back-end (console)
		if( current_user_can('chat_with_users') and !empty( $this->visitor_ID ) ) {
			$data = array(
				'visitor_ID'	=> $this->visitor_ID,
				'chat_date' 	=> current_time( 'timestamp', 1 ),
				'sender'		=> $this->author,
				'sender_email'	=> $this->email,
				'chat_line'		=> $this->chat_line,
			);
		}
		// Prepare data from front-end
		elseif( !empty( $_SESSION['sc_chat']['chat_visitor_ID'] )) {
			$data = array(
				'visitor_ID'	=> $_SESSION['sc_chat']['chat_visitor_ID'],
				'chat_date' 	=> current_time( 'timestamp', 1 ),
				'sender'		=> null,
				'sender_email'	=> null,
				'chat_line'		=> $this->chat_line
			);
		}
			
		// Save chat log
		if( !empty( $data ) )
			$wpdb->insert( $wpdb->prefix . 'chat_logs', $data, array( '%s', '%d', '%s', '%s' ) );
		
		
		return $insert_id;

	}
}
?>