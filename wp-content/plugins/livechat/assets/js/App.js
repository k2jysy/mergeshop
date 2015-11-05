/*
 * Screets Chat
 * Application scripts
 *
 * Copyright (c) 2013 Screets
 */

(function ($) {
	
	
	$(document).ready(function () {

		/**
		 * Connect to chat
		 */
		$.sc_chat.init();
		
		
		$(window).unload(function() {
			
			
			// If user closes window, break connection (log out)
			if( sc_chat.is_admin ) {
			
				$.ajax({
					type	: 'POST',
					url		: sc_chat.ajaxurl + '?mode=logout',
					data	: 'action=sc_chat_ajax_callback',
					async	: false
				});
			
			}
			
		});

	});

	$.sc_chat = {

		// Data holds variables for use in the class:

		data : {
			first_time 			: true,
			last_log_ID 		: 0,
			no_activity 		: 0,
			logged_in 			: false,
			sender 				: '',
			win_is_active 		: 0,
			sc_chat_box_visible : false,
			error			 	: false
		},

		// Init binds event listeners and sets up timers:

		init : function () {

			// We use the working variable to prevent
			// multiple form submissions:

			var working = false;

			// Check if user is currently on chat window
			window.onfocus = function () {
				$.sc_chat.data.win_is_active = 1;
			};
			window.onblur = function () {
				$.sc_chat.data.win_is_active = 0;
			};

			/*
			 * OPERATOR AREA
			 */

			if (sc_chat.is_admin == true) {

				// Prepare user data
				var user_data = 'action=sc_chat_ajax_callback&f_chat_user_name=' + sc_chat.username + '&f_chat_user_email=' + sc_chat.email + '&f_chat_is_admin=true';

				// Connect to chat as OP
				$.sc_POST('login', user_data, function (r) {
					working = false;

					if (r.error)
						$.sc_chat.display_error(r.error);
					else
						$.sc_chat.login(r.name, r.gravatar, true);

				});

				/*
				 * Open new tab
				 */

				$(document).on('click', '.sc-chat-users .user', function () {

					// Get receiver ID
					var receiver_ID = $(this).attr('data-receiver-id');
					var visitor_ID = $(this).attr('data-visitor-id');

					// Create new tab for current receiver
					$.sc_chat.open_new_tab(receiver_ID, visitor_ID, true);

					return false;

				});

				/*
				 * Control Tabs
				 */

				$('ul.sc-chat-tabs').each(function () {

					// For each set of tabs, we want to keep track of
					// which tab is active and it's associated content
					var $active,
					$content = $(this).find('li');

					// If no match is found, use the first link as the initial active tab.
					$content.addClass('active');

					// Hide the remaining content
					$content.not($active).each(function () {
						$($(this).attr('href')).hide();
					});

					$(document).on('click', '.sc-chat-tab-content', function () {

						// Remove highlight from tab
						$('ul.sc-chat-tabs li.active').removeClass('new-msg');
						
					});

					/*
					 * Bind the click event handler
					 */

					$(this).on('click', 'li a', function (e) {
					
						// Make other tabs inactive
						$('ul.sc-chat-tabs li').removeClass('active new-msg');
						$('.sc-chat-tab-content').removeClass('active');
						
						
						// Update the variables with the new link and content
						$active = $(this).parent();
						$content = $($(this).attr('href'));

						// Make the tab active.
						$active.addClass('active');
						$content.addClass('active');

						// Scroll to Bottom
						$('.sc-cnv-wrap').scrollTop(10000);

						// Focus textarea
						$content.find('.f-chat-line').focus();

						// Prevent the anchor's default click action
						e.preventDefault();

					});

					/*
					 * Close tab
					 */

					$(this).on('click', 'li .close', function (e) {

						var receiver_ID = $(this).prev().attr('data-receiver-id');

						// Make other tabs inactive
						$('ul.sc-chat-tabs li').removeClass('active new-msg');
						$('.sc-chat-tab-content').removeClass('active');

						// Show console tab
						$('.console-tab').addClass('active');
						$('#Console').addClass('active');

						// Remove current tab
						$('#Tab_' + receiver_ID).remove();
						$('#Receiver_' + receiver_ID).remove();

						// Prevent the anchor's default click action
						e.preventDefault();

					});
				});

				/*
				 * Change OP status
				 */

				$('.sc-chat-login-btn').click(function () {

					var btn = $(this);

					// Get current status
					$.sc_chat.data.current_status = $(this).attr('data-status');

					// Go offline (NOT accepting new chats)
					if ($.sc_chat.data.current_status == 'online') {

						// Go offline
						$.sc_POST('offline', 'action=sc_chat_ajax_callback', function (r) {

							// Hide user directly from list
							$('.user[data-user-type="1"]').fadeOut(1000);

							// Check online users
							$.sc_chat.get_online_users();

							// Update button
							btn.attr('data-status', 'offline').html('<i class="sc-icon-offline"></i> ' + sc_chat.tr_offline);

							// Update current status
							$.sc_chat.data.current_status = 'offline';

							// Update background
							$('#Chat_console').removeClass('sc-online').addClass('sc-offline');

							// Play sound
							$.sc_chat.play_sound('offline');
						});

					}

					// Go online (Accepting new chats)
					else if ($.sc_chat.data.current_status == 'offline') {

						// Go online
						$.sc_POST('online', 'action=sc_chat_ajax_callback', function () {

							// Check online users
							$.sc_chat.get_online_users();

							// Show user again
							$('.user[data-user-type="ME"]').show();

							// Update button
							btn.attr('data-status', 'online').html('<i class="sc-icon-online"></i> ' + sc_chat.tr_online);

							// Update current status
							$.sc_chat.data.current_status = 'online';

							// Update background
							$('#Chat_console').removeClass('sc-offline').addClass('sc-online');

							// Play sound
							$.sc_chat.play_sound('online');
						});

					}

				});
				
				// Refresh user agent
				$(document).on('click', '.sc-chat-refresh', function() {
					
					receiver_ID = $(this).parent().parent().parent().parent().attr('data-receiver-id');
					visitor_ID = $('#User_' + receiver_ID).attr('data-visitor-id');
					
					$.sc_chat.get_user_info( visitor_ID, receiver_ID );
					
				});

			}

			/*
			 * VISITOR AREA
			 */
			if (!sc_chat.is_admin) {

				/**
				 * Contact form
				 */
				$('#SC_send_form_btn').click(function () {
					$('#SC_contact_form').submit();

				});

				// Send contact form
				$('#SC_contact_form').submit(function () {

					// Show sending message
					$('.sc-chat-notification').fadeIn(100).removeClass('error success').html(sc_chat.tr_sending + '...');

					$.sc_POST('send_contact_from', $(this).serialize(), function (r) {
						if (r.error)
							// Show error
							$('.sc-chat-notification').addClass('error').html(r.error);
						else {
							// Successfull sent!
							$('.sc-chat-notification').addClass('success').html(r.message).delay(1500).fadeOut(500);

							// Update chatbox title as well :)
							$('.sc-chat-header-title').html(r.message)
							.delay(4000)
							.queue(function (n) {
								$(this).html(sc_chat.tr_offline_header);

								n();
							});

							// Clean fields
							$('#SC_contact_form input[type="text"], #SC_contact_form textarea').val('');

							// Hide chatbox (delay about 3 sec.)
							setTimeout($.sc_chat.hide_chatbox, 2700);
						}
					});

					return false;

				});

				/**
				 * Login Form
				 */

				$('#SC_start_chat_btn').click(function () {
					$('#SC_login_form').submit();
				});

				// Submit login form by clicking ENTER key
				$('#SC_login_form input').keydown(function (e) {
					
					if (e.keyCode == 13)
						$('#SC_login_form').submit();
					
				});

				// Logging a person in the chat:

				$('#SC_login_form').submit(function () {

					if (working)
						return false;
					
					// Display connecting notification
					$('.sc-chat-notification').removeClass('error success')
						.fadeIn(500)
						.html(sc_chat.tr_connecting + '...' );

					working = true;

					// Using our sc_POST wrapper function
					// (defined in the bottom):
					$.sc_POST('login', $(this).serialize(), function (r) {
						working = false;
						
						if (r.error)
							$.sc_chat.display_error(r.error);
						else
							$.sc_chat.login(r.name, r.gravatar, true);
					});

					return false;

				});

				/*
				 * Show Chat Box (Open / Close)
				 */

				// Animate chat box
				var sc_chat_obj = $.sc_chat;
				if (sc_chat.use_css_anim == 1) {
					setTimeout($.sc_chat.animate_chatbox, (sc_chat.delay * 500)); // CSS anims is waiting a little bit
				} else
					setTimeout($.sc_chat.animate_chatbox, (sc_chat.delay * 1000));

			}

			/**
			 * Submitting a new chat entry:
			 */
			$('body').on('keydown', '.f-chat-line', function(e) {
				
				// Enter new line when user clicked shift + enter
				if (e.keyCode == 13 && !e.shiftKey) {
					
					e.preventDefault();

					// Send form
					$(this).parent().submit();

					// Clean input
					$(this).val('');
					
					// Update textarea height
					$(this).trigger('autosize.resize');
				
				}

			});

			$(document).on('submit', '#Reply_form', function () {

				var receiver_ID = $(this).find('.f-receiver-id').val();
				var chat_line = $.trim( $(this).find('.f-chat-line').val() );
				var form_data = $(this).serialize();
				var f_chat_line = $('.f-chat-line');
				
				if (chat_line.length == 0) {
					return false;
				}

				if (working)
					return false;
				working = true;

				// Assigning a temporary ID to the chat:
				var tempID = 't' + Math.round(Math.random() * 1000000),
				params = {
					ID : tempID,
					author : $.sc_chat.data.name,
					gravatar : $.sc_chat.data.gravatar,
					receiver_ID : receiver_ID,
					chat_line : chat_line.replace(/</g, '&lt;').replace(/>/g, '&gt;')
				};

				// Show ajax loader
				f_chat_line.addClass('sc-chat-sending');

				// Using our add_chat_line method to add the chat
				// to the screen immediately, without waiting for
				// the AJAX request to complete:

				$.sc_chat.add_chat_line($.extend({}, params));

				// Using our sc_POST wrapper method to send the chat
				// via a POST AJAX request:

				$.sc_POST('send_chat_msg', form_data, function (r) {
					working = false;

					$('.f-chat-line').val('').removeClass('sc-chat-sending');
					$('div.chat-' + tempID).remove();

					if (r.error) {
						$.sc_chat.display_error(r.error);

						// Disable textarea
						$('#Reply_form .f-chat-line').attr('disabled', 'disabled').addClass('sc-chat-error');

					} else {

						params['ID'] = r.insert_ID;

						$.sc_chat.add_chat_line($.extend({}, params));
					}

				});

				return false;
			});

			// Logging the user out:

			$(document).on('click', '.sc-chat-btn-logout', function () {

				$.sc_chat.data.logged_in = false;
				
				// Logout user
				$.sc_POST('logout', 'action=sc_chat_ajax_callback', function (r) {
					
					// Go to Dashboard for admin area
					if( sc_chat.is_admin == true ) {
						window.location.href = './';
					
					// Hide chatbox
					} else {

						// Hide login
						$('#Conversation').fadeOut( 300, function() {
							
							// Show login form
							$('.sc-chat-wrapper').fadeIn(300, function() {
								
								// Slide down chat box
								setTimeout( function() {
								
									$.sc_chat.hide_chatbox();
									
								}, 1000);
							});
						
						});
						
						
					}

				});
				
				return false;

			});

			// Checking whether the user is already logged (browser refresh)

			$.sc_GET('is_user_logged_in', 'action=sc_chat_ajax_callback', function (r) {

				var allow_chat = true;

				// Don't allow operators to chat in front-end
				if (!sc_chat.is_admin && sc_chat.is_op == true)
					allow_chat = false;

				if (r.logged && allow_chat)
					$.sc_chat.login(r.user.name, r.user.email, false);

			});

			// Self executing timeout functions
			(function get_online_users_timeout_function() {
				$.sc_chat.get_online_users(get_online_users_timeout_function);
			})();

			(function get_chat_lines_timeout_function() {
				$.sc_chat.get_chat_lines(get_chat_lines_timeout_function);
			})();
			

		},

		// The login method hides displays the
		// user's login data and shows the submit form

		login : function (name, gravatar, display_login) {

			$.sc_chat.data.name = name;
			$.sc_chat.data.gravatar = gravatar;
			
			$.sc_chat.data.logged_in = true;
			
			/*
			 * For back-end
			 */
			if (sc_chat.is_admin == true) {

				// Update status to online
				$('.sc-chat-login-btn').attr('data-status', 'online').html('<i class="sc-icon-online"></i> ' + sc_chat.tr_online);

				// Change background
				$('#Chat_console').addClass('sc-online');

			}

			/*
			 * For front-end
			 */
			else {

				// Open chatbox if logged in
				$.sc_chat.open_chatbox();

				// Hide login form and go conversation
				if (display_login == true) {
					$('.sc-chat-wrapper').fadeOut(function () {

						// Clean previous notifications
						$('.sc-chat-notification').html('').hide();

						$('#Conversation').fadeIn();
						$('.f-chat-line').focus().autosize();

						// Scroll to Bottom
						$('.sc-cnv-wrap').scrollTop(10000);

					});

				}
				// Directly open chatbox without displaying login form
				else {

					// Hide login form
					$('.sc-chat-wrapper').hide();

					if ($.cookie('sc_chat_chatbox_status') == 'on') {
						
						// Show conversation
						if (sc_chat.use_css_anim == 1)
							delay = sc_chat.delay * 500; // CSS anims is waiting a little bit
						else
							delay = sc_chat.delay * 1000;

						setTimeout(function () {
							$('#Conversation').show();

							$('.f-chat-line').autosize();

							// Scroll to Bottom
							$('.sc-cnv-wrap').scrollTop(10000);
							
						}, delay);

						$.sc_chat.data.sc_chat_box_visible = true;
					}

				}

			}

			// It's not first-time anymore
			$.sc_chat.data.first_time = false;

		},

		// The render method generates the HTML markup
		// that is needed by the other methods:

		render : function (template, params) {

			var arr = [];
			switch (template) {
			case 'login_top_bar':
				arr = [
					'<span><img src="', params.gravatar, '" width="23" height="23" />',
					'<span class="name">', params.name,
					'</span><a href="" class="logoutButton rounded">', params.tr_logout, '</a></span>'];
				break;

			case 'chat_line':
				arr = [
					'<div class="sc-msg-wrap chat chat-', params.ID, '" data-user-id="', params.author, '"><div class="sc-chat-time">', params.time, '</div><div class="sc-usr-avatar"><img src="', params.gravatar,
					'" width="38" height="38" onload="this.style.visibility=\'visible\'" />', '</div><div class="sc-msg"><div class="sc-usr-name">', params.author,
					':</div><div class="sc-chat-line">', params.chat_line, '</div></div><div class="clearfix"></div></div>'];
				break;

			case 'user':
				// Find user id first
				if (params.type == 1)
					var user_id = params.ID;
				else
					var user_id = params.visitor_ID;

				arr = [
					'<a id="User_', params.name, '" href="#Receiver_', params.ID, '" class="user" data-receiver-id="', params.name, '" data-visitor-id="', user_id, '" data-user-type="', params.type, '"><img class="avatar" src="',
					params.gravatar, '" onload="this.style.visibility=\'visible\'" /> <div class="username"> <strong>', params.name, '</strong> (', params.email, ')<small>', params.tagline, '</small></div></a>'
				];
				break;

			case 'new_tab_title':
				arr = [
					'<li class="', params.custom_class, '" id="Tab_', params.ID, '"><a href="#Receiver_', params.ID, '" data-receiver-id="', params.ID, '">', params.ID, '</a> <button type="button" class="close">&times;</button></li>'
				];
				break;

			case 'new_tab_content':
				
				// Prepare user agent
				if ( params.user_info )
					var user_agent = params.user_info;
				else
					var user_agent = '<a href="javascript:void(0)" class="sc-chat-refresh">Refresh</a>';
				
				arr = [
					'<div id="Receiver_', params.ID, '" data-receiver-id="', params.ID, '" class="', params.custom_class, ' sc-chat-tab-content"><div class="sc-chat-inner"><div id="SC_cnv_wrap" class="sc-cnv-wrap"><div class="sc-chat-user-agent">', user_agent , '</div></div><div class="sc-chat-tip"></div></div><form id="Reply_form" method="post" action="" class="sc-chat-reply"><input type="hidden" name="action" value="sc_chat_ajax_callback" /><input type="hidden" name="receiver_ID" class="f-receiver-id" value="', params.ID, '" /><input type="hidden" name="visitor_ID" class="f-visitor-id" value="', params.visitor_ID, '" /><textarea name="chat_line" class="f-chat-line" maxlength="700" placeholder="', sc_chat.tr_write_a_reply, '"></textarea></form></div>'
				];

				break;
			}

			// A single array join is faster than
			// multiple concatenations

			return arr.join('');

		},

		// Create new tab

		open_new_tab : function (receiver_ID, visitor_ID, force_focus) {
		
			// Get user type
			user_type = $('.sc-chat-users a[data-visitor-id="' + visitor_ID + '"]').attr('data-user-type');

			// Prepare data
			var data = new Array();
			data['ID'] = receiver_ID;
			data['visitor_ID'] = visitor_ID;
			data['custom_class'] = '';

			if (user_type == '1' || !visitor_ID)
				data['user_info'] = '';
			else
				data['user_info'] = sc_chat.tr_loading + '...';

			// If tab not exists for the user, create new one
			if ($('.sc-chat-tabs li#Tab_' + receiver_ID).length == 0) {

				// Deactivate other tabs, if any conversation not started yet
				if ($('.sc-chat-tabs .console-tab.active').length == 1 || force_focus) {
					$('.sc-chat-tabs li').removeClass('active');
					$('.sc-chat-tab-content').removeClass('active');

					// Activate first conversation tab
					data['custom_class'] = 'active ';
				}

				// Insert new tab title
				$('.sc-chat-tabs').append($.sc_chat.render('new_tab_title', data));

				// Insert new tab content
				$('.sc-chat-tab-contents').append($.sc_chat.render('new_tab_content', data));

				// Focus textarea
				$('#Receiver_' + receiver_ID + ' .f-chat-line').focus();

			}

			// Get user info
			$.sc_chat.get_user_info(visitor_ID, receiver_ID);

			return false;

		},

		get_user_info : function (visitor_ID, receiver_ID) {
			
			// Update receiver_ID
			$.sc_chat.data.receiver_ID = receiver_ID;
			
			// Get user info
			$.sc_GET('user_info', 'action=sc_chat_ajax_callback&ID=' + visitor_ID, function (r) {
				
				// Set user info
				if (r.device_name != 'null') {

					// Prepare user info line
					$.sc_chat.data.user_info = r.device_name + ' ' + r.device_version + ' - ' + r.platform + ', ' + r.ip_address + ' &nbsp; <a href="admin.php?page=sc_chat_m_chat_logs&action=edit&visitor_ID=' + visitor_ID + '" target="_blank">' + sc_chat.tr_chat_logs + '</a>';
											
					$.sc_chat.update_user_info();

				}
			});

		},

		update_user_info : function () {
		
			// Update user info
			$('#Receiver_' + $.sc_chat.data.receiver_ID + ' .sc-chat-user-agent').html($.sc_chat.data.user_info);

		},

		// The add_chat_line method adds a chat entry to the page

		add_chat_line : function (params) {
			
			// Open new tabs for all related conversations
			if (sc_chat.is_admin == true) {

				// User sending message to himself
				if (params.author == sc_chat.username && params.receiver_ID == sc_chat.username)
					$.sc_chat.data.sender = sc_chat.username;

				// Replied message
				else if (params.receiver_ID == sc_chat.username)
					$.sc_chat.data.sender = params.author;

				// Incoming message
				else if (params.author == sc_chat.username)
					$.sc_chat.data.sender = params.receiver_ID;

				// Visitor message
				else if (params.receiver_ID == '__OP__')
					$.sc_chat.data.sender = params.author;
				
				// Open new tab
				if ($.sc_chat.data.sender) {
					
					// Find user ID first
					var user_id = $('#User_' + $.sc_chat.data.sender).attr('data-visitor-id');
					
					$.sc_chat.open_new_tab($.sc_chat.data.sender, user_id);

					$.sc_chat.update_user_info();
				}

				// Highlight tab
				$('#Tab_' + params.author + ':not(.active)').addClass('new-msg');

			}

			// All times are displayed in the user's timezone

			var d = new Date();
			if (params.time) {

				// PHP returns the time in UTC (GMT). We use it to feed the date
				// object and later output it in the user's timezone. JavaScript
				// internally converts it for us.

				d.setUTCHours(params.time.hours, params.time.minutes);
			}

			params.time = (d.getHours() < 10 ? '0' : '') + d.getHours() + ':' +
			(d.getMinutes() < 10 ? '0' : '') + d.getMinutes();

			var markup = $.sc_chat.render('chat_line', params);
			exists = $('.sc-cnv-wrap .chat-' + params.ID);

			if (exists.length) {
				exists.remove();
			}

			if (!$.sc_chat.data.last_log_ID) {

				// If this is the first chat, remove the
				// paragraph saying there aren't any:

				$('.sc-cnv-wrap .sc-lead').remove();

			}

			// Get current conversation content
			if (sc_chat.is_admin == true)
				var current_cnv = $('#Receiver_' + $.sc_chat.data.sender + ' .sc-cnv-wrap');
			else
				var current_cnv = $('.sc-cnv-wrap');

			// If this is NOT a temporary chat:
			if (params.ID.toString().charAt(0) != 't') {

				var previous = current_cnv.find('.chat-' + (+params.ID - 1));

				if (previous.length)
					previous.after(markup);
				else
					current_cnv.append(markup);

			} else {

				current_cnv.append(markup);

			}

			// Scroll to Bottom
			$('.sc-cnv-wrap').scrollTop(100000);

			// Who is the user who sent the last message
			$.sc_chat.data.last_user = params.author;

		},

		// This method requests the latest chats
		// (since last_log_ID), and adds them to the page.

		get_chat_lines : function (callback) {
			
			// Don't request if not necessary
			if (!$.sc_chat.data.logged_in && sc_chat.is_admin == false)  {
				
				// Check again later
				setTimeout(callback, 1000);
				
				return;
				
			}
				
			$.sc_GET('get_chat_lines', {
				last_log_ID : $.sc_chat.data.last_log_ID,
				action : 'sc_chat_ajax_callback',
				sender : $.sc_chat.data.sender

			}, function (r) {

				// Add chat lines one by one
				for (var i = 0; i < r.chats.length; i++) {
					$.sc_chat.add_chat_line(r.chats[i]);
				}

				if (r.chats.length) {
					$.sc_chat.data.no_activity = 0;
					$.sc_chat.data.last_log_ID = r.chats[i - 1].ID;

					// Play sound if user is NOT currently on chat window
					if ($.sc_chat.data.first_time != true && ($.sc_chat.data.win_is_active == 0 || sc_chat.is_admin == true)) {
							
						// If chatbox is closed, don't play sound in front-end
						if( sc_chat.is_admin == true )
							$.sc_chat.play_sound('new_message');

						else if($.cookie('sc_chat_chatbox_status') == 'on')
							$.sc_chat.play_sound('new_message');
					}
						
					
				} else {

					// If no chats were received, increment
					// the no_activity counter.

					$.sc_chat.data.no_activity++;
				}

				// Setting a timeout for the next request,
				// depending on the chat activity:

				var nextRequest = 1000;

				// 2 seconds
				if ($.sc_chat.data.no_activity > 3) {
					nextRequest = 2000;
				}

				if ($.sc_chat.data.no_activity > 10) {
					nextRequest = 5000;
				}

				// 15 seconds
				if ($.sc_chat.data.no_activity > 20) {
					nextRequest = 15000;
				}
				
				
				setTimeout(callback, nextRequest);
			});
		},

		// Requesting a list with all the users.

		get_online_users : function (callback) {
			
			// Don't request if not necessary
			if (!$.sc_chat.data.logged_in && sc_chat.is_admin == false) {
				
				// Check again later
				setTimeout(callback, 3000);
				
				return;
				
			}
			
			
			$.sc_GET('get_online_users', {
				action : 'sc_chat_ajax_callback'
			},
				function (r) {

				if (sc_chat.is_admin == true) {
					var users = [];

					for (var i = 0; i < r.users.length; i++) {
						if (r.users[i]) {
							users.push($.sc_chat.render('user', r.users[i]));
						}
					}

					var message = '';

					// "No one is online"
					if (r.total < 1) {
						message = sc_chat.tr_no_one_online;

						// "1 person online"
					} else if (r.total == 1) {
						message = sc_chat.tr_1_person_online;

						// x people online
					} else
						message = sc_chat.tr_x_people_online.replace('%s', r.total);

					users.push('<p class="count">' + message + '</p>');

					$('#People_list .sc-chat-users').html(users.join(''));

				}

				// Check online users every 15 seconds
				setTimeout(callback, 15000);
			});
		},

		// Animate Chatbox ( for front-end )
		animate_chatbox : function () {
			
			var sc_chat_box_obj = $('#sc_chat_box');
			var sc_chat_header_obj = $('#sc_chat_box .sc-chat-header');

			// Calculate sizes
			var sc_chat_box_h = sc_chat_box_obj.innerHeight();
			var sc_chat_header_h = sc_chat_header_obj.innerHeight();

			// Positining box
			sc_chat_box_obj.css('bottom', '-' + sc_chat_box_h + 'px');

			// Make visible chatbox now
			sc_chat_box_obj.css('visibility', 'visible');

			/*
			 * Use CSS Animations
			 */
			if (sc_chat.use_css_anim == 1) {

				// Initialize chatbox
				sc_chat_box_obj.css('bottom', '-' + (sc_chat_box_h - sc_chat_header_h) + 'px').addClass('sc-chat-animated sc-chat-bounce-in-up');

				
				sc_chat_header_obj.click(function () {
					
					// Clean cookie
					$.removeCookie('sc_chat_chatbox_status');

					// Re-calculate sizes
					sc_chat_box_h = sc_chat_box_obj.innerHeight();
					sc_chat_header_h = sc_chat_header_obj.innerHeight();

					// Open it completely
					if ($.sc_chat.data.sc_chat_box_visible == false) {
						// Re-calculate sizes
						sc_chat_box_h = sc_chat_box_obj.innerHeight();
						sc_chat_header_h = sc_chat_header_obj.innerHeight();

						sc_chat_box_obj.css('bottom', 0).addClass('sc-chat-css-anim');

						setTimeout(function () {
							
							// Focus name input or chat message box
							// Don't focus on iPhones
							if (window.innerWidth > 480) {
								
								if ( $('#f_chat_user_name').length )
									$('#f_chat_user_name, .f-chat-line').focus();
								else
									$('#f_chat_user_email, .f-chat-line').focus();
								
							}
							

						}, 500);
						
						if ( $.sc_chat.data.logged_in == true ) {
							
							$('#Conversation').show();
							
							setTimeout(function () {
								$('.f-chat-line').focus().autosize();
							}, 500);
							
						}
							
						
						// Save into cookie
						$.cookie('sc_chat_chatbox_status', 'on', {
							expires : 1
						});

						$.sc_chat.data.sc_chat_box_visible = true;

					}
					// Hide Chat Box
					else {

						sc_chat_box_obj.css('bottom', '-' + (sc_chat_box_h - sc_chat_header_h) + 'px');

						// Save into cookie
						$.cookie('sc_chat_chatbox_status', 'off', {
							expires : 1
						});

						$.sc_chat.data.sc_chat_box_visible = false;
					}
				});

			}

			/*
			 * Use jQuery Animations
			 */
			else {

				// Initialize chatbox
				sc_chat_box_obj
				.stop().animate({
					bottom : '+=' + sc_chat_header_h
				}, {
					duration : 900,
					easing : 'easeOutBack'
				});

				// Show Chat Box
				sc_chat_header_obj.click(function () {

					// Show chatbox just in case
					$('#Conversation').show();

					// Clean cookie
					$.removeCookie('sc_chat_chatbox_status');

					// Re-calculate sizes
					sc_chat_box_h = sc_chat_box_obj.innerHeight();
					sc_chat_header_h = sc_chat_header_obj.innerHeight();

					// Open it completely
					if ($.sc_chat.data.sc_chat_box_visible == false) {
						sc_chat_box_obj.stop().animate({
							bottom : 0
						}, {
							duration : 200,
							easing : 'easeOutExpo',
							complete : function () {
								
								// Focus name input or chat message box
								// Don't focus on iPhones
								if (window.innerWidth > 480) {
									if ( $('#f_chat_user_name').length )
										$('#f_chat_user_name, .f-chat-line').focus();
									else
										$('#f_chat_user_email, .f-chat-line').focus();
									
								}
							}
						});

						// Save into cookie
						$.cookie('sc_chat_chatbox_status', 'on', {
							expires : 1
						});

						$.sc_chat.data.sc_chat_box_visible = true;

					}
					// Hide Chat Box
					else {

						// Save into cookie
						$.cookie('sc_chat_chatbox_status', 'off', {
							expires : 1
						});

						sc_chat_box_obj.stop().animate({
							bottom : '-' + (sc_chat_box_h - sc_chat_header_h)
						}, {
							duration : 190,
							easing : 'easeOutExpo'
						});

						// Save into cookie
						$.cookie('sc_chat_chatbox_status', 'off', {
							expires : 1
						});

						$.sc_chat.data.sc_chat_box_visible = false;
					}
				});

			}

		},

		// Open chatbox
		open_chatbox : function () {

			var sc_chat_box_obj = $('#sc_chat_box');

			// Activate reply textarea if it isn't
			$('#Reply_form .f-chat-line').removeAttr('disabled').removeClass('sc-chat-error');

			/*
			 * Show chatbox with CSS Animations :)
			 */
			if (sc_chat.use_css_anim == 1) {
				
				sc_chat_box_obj.css('bottom', 0);

			}

			/*
			 * Show chatbox with jQuery Animations
			 */
			else {

				sc_chat_box_obj.stop().animate({
					bottom : 0
				}, {
					duration : 200,
					easing : 'easeOutExpo'
				});

				// Focus name input or chat message box
				$('#f_chat_user_name, .f-chat-line').focus();

			}

		},

		// Hide chatbox
		hide_chatbox : function () {

			var sc_chat_box_obj = $('#sc_chat_box');
			var sc_chat_header_obj = $('#sc_chat_box .sc-chat-header');

			// Re-calculate sizes
			sc_chat_box_h = sc_chat_box_obj.innerHeight();
			sc_chat_header_h = sc_chat_header_obj.innerHeight();

			/*
			 * Hide chatbox with CSS Animations :)
			 */
			if (sc_chat.use_css_anim == 1) {

				sc_chat_box_obj.css('bottom', '-' + (sc_chat_box_h - sc_chat_header_h) + 'px');

			}

			/*
			 * Hide chatbox with jQuery Animations
			 */
			else {
				sc_chat_box_obj.stop().animate({
					bottom : '-' + (sc_chat_box_h - sc_chat_header_h)
				}, {
					duration : 190,
					easing : 'easeOutExpo'
				});
			}

			$.sc_chat.data.sc_chat_box_visible = false;
		},

		
		// Add source into <audio> tag
		add_source : function (elem, path) {
			$('<source>').attr('src', path).appendTo(elem);
		},

		// Play sound
		play_sound : function (sound_name) {
			
			if (sc_chat.sound_package == 'none' )
				return;
				
			var audio = $('<audio />', {
				autoPlay : 'autoplay'
			});

			$.sc_chat.add_source(audio, sc_chat.plugin_url + '/assets/sounds/' + sound_name + '.mp3');
			$.sc_chat.add_source(audio, sc_chat.plugin_url + '/assets/sounds/' + sound_name + '.ogg');
			$.sc_chat.add_source(audio, sc_chat.plugin_url + '/assets/sounds/' + sound_name + '.wav');
			audio.appendTo('body');

		},

		// This method displays an error message:
		display_error : function (msg) {
			
			if ( sc_chat.is_admin == true )
				var $class = 'error';
			else
				var $class = '';
			
			$('.sc-chat-notification').show()
			.html('')
			.delay(500)
			.html('<div class="'+ $class + '">' + msg + '</div>');

		},
		
		// Hide error message:
		hide_error : function() {
			
			$('.sc-chat-notification').hide();
			
		}
		
		
	};

	// Custom GET & POST wrappers:

	$.sc_POST = function (mode, data, callback) {

		$.post(sc_chat.ajaxurl + '?mode=' + mode, data, callback, 'json')
		.fail(function (jqXHR) {
			
			// Error occured
			$.sc_chat.data.error = true;
			
			// Log error
			console.log(jqXHR);
			
			// Show error message
			$.sc_chat.display_error( sc_chat.tr_connecting + '...' );
			
			
			return false;
		})
		.done( function() {
			
			// Hide error message
			if ($.sc_chat.data.error == true)
				$.sc_chat.hide_error();
				
			// Error displayed
			$.sc_chat.data.error = false;
		});

	}

	$.sc_GET = function (mode, data, callback) {

		$.get(sc_chat.ajaxurl + '?mode=' + mode, data, callback, 'json')
		.fail(function (jqXHR) {
		
			// Error occured
			$.sc_chat.data.error = true;
			
			// Log error
			console.log(jqXHR);
			
			// Show error message
			$.sc_chat.display_error( sc_chat.tr_connecting + '...' );
			
			
			return false;
		})
		.done( function() {
			
			// Hide error message
			if ($.sc_chat.data.error == true)
				$.sc_chat.hide_error();
			
			// Error displayed
			$.sc_chat.data.error = false;
		});

	}
	
	
	

} (window.jQuery || window.Zepto));
