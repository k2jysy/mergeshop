<?php
/**
 * SCREETS © 2013
 *
 * Basic Skin
 * Author: Screets
 * Author URI: http://screets.com
 *
 */

global $current_user;
get_currentuserinfo();
?>

<div id="sc_chat_box">
	
	<div class="sc-chat-header">
		<span class="sc-chat-header-title">
			<?php 
			
			if( !$this->online_op_num  or $this->online_visitors >= $this->opts['allowed_visitors'] )
				echo $this->opts['offline_header'];
				
			else
				echo $this->opts['before_chat_header']; 
			
			?>
		</span>
		<i></i>
	</div>
	
	<div class="sc-chat-wrapper">
		
		<?php
		/*
		 * Don't allow chat for operators
		 */
		
		if( $this->is_user_op and $this->online_op_num ) : ?>
			
			<p class="sc-lead"><?php _e( 'Operators are not allowed to chat from here', 'sc_chat' ); ?> :)</p>
            
			
		<?php 
		/*
		 * Show contact form if no operators online and check over capacity
		 */
		elseif( !$this->online_op_num or $this->online_visitors >= $this->opts['allowed_visitors'] ) : ?>
			
			<form action="index.php" id="SC_contact_form">
				
				<input type="hidden" name="action" value="sc_chat_ajax_callback" />
				
				<p class="sc-lead"><?php echo $this->opts['offline_body']; ?></p>
                <p class="sc-lead">您还可以选择：<?php echo $this->opts['line_qq']; ?></p>
				<p>如果我QQ没在线，请使用以下方式和我们联系！</p>
				<?php
				/**
				 * Ask for name
				 */
				?>
				
				<label for="f_chat_user_name"><?php echo $this->opts['name_field']; ?> <span class="sc-req">*</span>:</label>
				
				<input type="text" name="f_chat_user_name" id="f_chat_user_name" placeholder="<?php echo $this->opts['name_field']; ?> (<?php echo $this->opts['req_text']; ?>)" value="<?php echo ( is_user_logged_in() ) ? $current_user->display_name : '' ?>" />
				
				<?php
				/**
				 * Ask for email
				 */
				?>
				<label for="f_chat_user_email"><?php echo $this->opts['email_field']; ?> <span class="sc-req">*</span>:</label>
				
				<input type="email" name="f_chat_user_email" id="f_chat_user_email" placeholder="<?php echo $this->opts['email_field']; ?> (<?php echo $this->opts['req_text']; ?>)" value="<?php echo ( is_user_logged_in() ) ? $current_user->user_email : '' ?>" />
				
				
				<?php
				/**
				 * Ask for phone
				 */
				if( $this->opts['ask_phone_field'] ): ?>
				
					<label for="f_chat_user_phone">
						<?php echo $this->opts['phone_field'];
						
						// Required ?
						if( $this->opts['ask_phone_field'] == 1 )
							echo '<span class="sc-req">*</span>';
						?>
					:</label>
					<input type="text" name="f_chat_user_phone" id="f_chat_user_phone" placeholder="<?php echo $this->opts['phone_field']; ?> <?php echo ($this->opts['ask_phone_field'] == 1) ? '(' . $this->opts['req_text'] .')' : ''; ?>" />
				
				<?php endif; ?>
				
				<?php
				/**
				 * Got a question?
				 */
				?>
				<label for="f_chat_user_question"><?php echo $this->opts['question_field']; ?> <span class="sc-req">*</span>:</label>
				
				<textarea name="f_chat_user_question" id="f_chat_user_question" placeholder="<?php echo $this->opts['question_field']; ?> (<?php echo $this->opts['req_text']; ?>)" rows="1"></textarea>
				
				<!-- Notifications -->
				<div class="sc-chat-notification"></div>
				
				<!-- Start Chat Button -->
				<div class="sc-start-chat-btn">
					<a href="javascript:void(0)" id="SC_send_form_btn"><?php echo $this->opts['send_btn']; ?></a>
				</div>
				
			</form>
		
		<?php
		/*
		 * Login form
		 */
		else :
		?>
			<form id="SC_login_form" action="index.php">
				
				<input type="hidden" name="action" value="sc_chat_ajax_callback" />
				<input type="hidden" name="f_chat_is_admin" value="false" />
				
				<p class="sc-lead"><?php echo $this->opts['prechat_welcome_msg']; ?></p>
                <p>1、您可以选择：<?php echo $this->opts['online_qq']; ?></p>
                <p>您还可以通过网页聊天直接和我们联系</p>
				<?php
				/**
				 * Ask for name
				 */
				if( $this->opts['ask_name_field'] ):
					$is_req = ( $this->opts['ask_name_field'] == 1 ) ? true : false;
				?>
				
					<label for="f_chat_user_name"><?php echo $this->opts['name_field']; ?> 
						<?php if( $is_req ): ?>
							<span class="sc-req">*</span>
						<?php endif; ?>	
					:</label>
					
					<input type="text" name="f_chat_user_name" id="f_chat_user_name" placeholder="<?php echo $this->opts['name_field']; ?> <?php echo ( $is_req ) ? '(' . $this->opts['req_text'] . ')' : ''; ?>" value="<?php echo ( is_user_logged_in() ) ? $current_user->display_name : '' ?>"  />
				
				<?php
				endif;
				
				/**
				 * Ask for email
				 */
				?>
				<label for="f_chat_user_email"><?php echo $this->opts['email_field']; ?> <span class="sc-req">*</span>:</label>
				
				<input type="email" name="f_chat_user_email" id="f_chat_user_email" placeholder="<?php echo $this->opts['email_field']; ?> (<?php echo $this->opts['req_text']; ?>)" value="<?php echo ( is_user_logged_in() ) ? $current_user->user_email : '' ?>" />
				
				<!-- Notifications -->
				<div class="sc-chat-notification"></div>
				
				<!-- Start Chat Button -->
				<div class="sc-start-chat-btn">
					<a href="javascript:void(0)" id="SC_start_chat_btn"><?php echo $this->opts['chat_btn'] ?></a>
				</div>
				
			</form>
		
		<?php endif; ?>
	
	</div>
	
	<div id="Conversation">

		
		<div class="sc-cnv-wrap">
			
			<p class="sc-lead"><?php echo $this->opts['welcome_msg'] ?></p>
			
		</div>
		
		
		<!-- Notifications -->
		<div class="sc-chat-notification"></div>
		
		<div class="sc-chat-toolbar">
			<div class="sc-chat-toolbar-btns">
				<a href="javascript:void(0)" class="sc-chat-btn-logout"><?php echo $this->opts['end_chat_field']; ?></a>
			</div>
		</div>
				
		<?php 
		// Reply form
		?>
		<form id="Reply_form" method="post" action="index.php" class="sc-chat-reply">
			<input type="hidden" name="action" value="sc_chat_ajax_callback" />
			
			<textarea name="chat_line" class="f-chat-line" maxlength="255" placeholder="<?php echo $this->opts['input_box_placeholder']; ?>"></textarea>
			
			<small class="sc-chat-note"><?php echo $this->opts['input_box_msg']; ?></small>
		</form>
		
	</div>
	
</div>