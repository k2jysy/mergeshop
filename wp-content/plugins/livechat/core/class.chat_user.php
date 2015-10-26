<?php
/**
 * SCREETS © 2013
 *
 * Chat User Class
 *
 */

 
class Chat_user extends Chat_base {
	
	
	protected $name 		= '';
	protected $email 		= '';
	protected $gravatar 	= '';
	protected $type 		= '';
	protected $ip_address	= '';
	protected $user_agent	= '';
	
	
	/**
	 * Save user (Create new one)
	 *
	 * @access public
	 * @return int|boll User ID, or false
	 */
	public function save() {
		global $wpdb;
		
		/*
		 * Check for operators
		 */
		if( current_user_can('chat_with_users') ) {
		
			// Remove previous OP with the same name
			$wpdb->query(
				$wpdb->prepare(
					'DELETE FROM ' . $wpdb->prefix . 'chat_online WHERE `name` = %s',
					$this->name
				)
			);
		}
		
		
		/*
		 * Check for visitors
		 */
		else {
			
			// Check administrator emails
			$check_admins = get_users('role=administrator&search=' . $this->email . '&fields=ID' );
			
			// Check operator emails
			$check_ops = get_users('role=sc_chat_op&search=' . $this->email . '&fields=ID' );
			
			if( !empty( $check_admins ) or !empty( $check_ops ) ) {
			
				throw new Exception( __('E-mail address has already been used by an operator. Please enter another one', 'sc_chat' ) );
				
				
			}
			
			// Delete the previous VISITOR who has the same email with new visitor
			$wpdb->query(
					$wpdb->prepare(
						'DELETE FROM ' . $wpdb->prefix . 'chat_online
						WHERE `gravatar` = %s AND `type` = 2 LIMIT 1',
						$this->gravatar
					)
			);
			
			// Give user a new name
			if( !sc_chat_name_is_available( $this->name ) )
				$this->name = $this->name . '-' . rand( 0, 1000 );
				
			
			// Create new visitor if not already exists
			$sql =  $wpdb->prepare(
						'INSERT INTO ' . $wpdb->prefix . 'chat_visitors (`name`, `email`, `gravatar`, `ip_address`, `user_agent`)
						VALUES( %s, %s, %s, %s, %s )
						ON DUPLICATE KEY UPDATE `name` = %s, `ip_address` = %d, `user_agent` = %s
						',
						$this->name, 
						$this->email,
						$this->gravatar,
						$this->ip_address,
						$this->user_agent,
						$this->name,
						$this->ip_address,
						$this->user_agent
					);
					
			$wpdb->query( $sql );
		
			$_SESSION['sc_chat']['chat_visitor_ID'] = $wpdb->insert_id;
			
		}
		
		// Prepare user data
		$data = array(
			'visitor_ID'=> ( !empty( $_SESSION['sc_chat']['chat_visitor_ID'] ) ) ? $_SESSION['sc_chat']['chat_visitor_ID'] : null,
			'name'			=> $this->name,
			'email'			=> $this->email,
			'gravatar' 		=> $this->gravatar,
			'ip_address'	=> $this->ip_address,
			'user_agent'	=> $this->user_agent,
			'type'			=> $this->type
		);
		
		// Insert user
		$wpdb->insert( $wpdb->prefix . 'chat_online', $data, array( '%d', '%s', '%s', '%s', '%s', '%s', '%d' ) );
		
		if( !$wpdb->insert_id )
			return false;
			
		return array(
			'user_id' 		=> $wpdb->insert_id,
			'name' 			=> $this->name,
			'email' 		=> $this->email,
			'ip_address' 	=> $this->ip_address,
			'user_agent' 	=> $this->user_agent,
			'gravatar'	 	=> $this->gravatar,
			'type' 			=> $this->type
		);
		
	}
	
	
	/**
	 * Update user
	 *
	 * @access public
	 * @return array
	 */
	public function update() {
		global $wpdb;
		
		// Get visitor ID
		$visitor_ID = ( !empty( $_SESSION['sc_chat']['chat_visitor_ID'] ) ) ? $_SESSION['sc_chat']['chat_visitor_ID'] : null;
		
		// Prepare sql
		$sql =  $wpdb->prepare(
					'INSERT INTO ' . $wpdb->prefix . 'chat_online (visitor_ID, `name`, email, gravatar, `ip_address`, `type` )
					VALUES( %d, %s, %s, %s, %d, %d )
					ON DUPLICATE KEY UPDATE `last_activity` = NOW()
					',
					$visitor_ID, 
					$this->name, 
					$this->email,
					$this->gravatar,
					$this->ip_address,
					$this->type
				);
		
		$wpdb->query( $sql );
			
	}
	
}
?>