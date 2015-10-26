

<div id="Chat_console">
	
	<div class="sc-chat-notification"></div>

	<!-- Error Message -->
	<div id="SC_chat_error"></div>
	
	<!-- Title -->
	<div class="sc-chat-title">
		<h1><?php _e( 'Chat Console', 'sc_chat' ); ?></h1>
	</div>
	
	<!-- Primary Buttons -->
	<div class="sc-chat-primary-btns">
		<!-- Online Button -->
		<a href="javascript:void(0)" class="sc-chat-login-btn button button-large"><i class="sc-icon-connecting"></i> <?php _e( 'Connecting', 'sc_chat' ); ?>...</a>
		
		<!-- Log out -->
		<a href="javascript:void(0)" class="sc-chat-btn-logout button button-large"><?php _e( 'Logout from Chat', 'sc_chat' ); ?></a>
	</div>
	
	<div class="clear"></div>
	
	
	<!-- Online People -->
	<div id="People_list">
		
		<h2><?php _e( 'Online Users', 'sc_chat' ); ?></h2>
		
		<div class="sc-chat-users"></div>
		
	</div>
	
	<!-- Conversations -->
	<div id="Conversations">
		<div class="inner">
		
			<!-- Wall -->
			<div class="sc-chat-wall">
				<ul class="sc-chat-tabs">
					<li class="console-tab"><a href="#Console"><?php _e( 'Welcome', 'sc_chat' ); ?></a></li>
				</ul>
				
				<div class="sc-chat-tab-contents">
					<div id="Console" class="active sc-chat-tab-content">&nbsp;</div>
				</div>
			</div>
			
		</div>
	</div>
	
	<div class="clear"></div>
	
</div>