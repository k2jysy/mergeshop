/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Opal  Team <opalwordpressl@gmail.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

function init_textarea_html($element) {
    /*
     Simple version without all this buttons from Wordpress
     tinyMCE.init({
     mode : "textareas",
     theme: 'advanced',
     editor_selector: $element.attr('name') + '_tinymce'
     });
     */
    // if($('#wp-link').parent().hasClass('wp-dialog')) $('#wp-link').wpdialog('destroy');
    var qt, textfield_id = $element.attr("id"),
        $form_line = $element.closest('.edit_form_line'),
        $content_holder = $form_line.find('.vc_textarea_html_content'),
        content = $content_holder.val();
    window.tinyMCEPreInit.mceInit[textfield_id] = _.extend({}, tinyMCEPreInit.mceInit['content']);

    if(_.isUndefined(tinyMCEPreInit.qtInit[textfield_id])) {
        window.tinyMCEPreInit.qtInit[textfield_id] = _.extend({}, tinyMCEPreInit.qtInit['replycontent'], {id: textfield_id})
    }
    $element.val($content_holder.val());
    qt = quicktags( window.tinyMCEPreInit.qtInit[textfield_id] );
    QTags._buttonsInit();
    window.switchEditors.go(textfield_id, 'tmce');
    /// window.tinyMCE.get(textfield_id).focus();
}

(function($) {
	$.fn.PavMegamenuEditor = function(opts) {
		// default configuration
		var config = $.extend({}, {
			lang:null,
			opt1: null,
			action:null,
			action_menu:null,
			text_warning_select:'Please select One to remove?',
			text_confirm_remove:'Are you sure to remove footer row?',
			JSON:null
		}, opts);

		/**
		 * active menu
		 */
		var activeMenu = null;

		/**
	 	 * fill data values for  top level menu when clicked menu.
	 	 */

		function processMenu( item , _parent, _megamenu ){

			$(".form-setting").hide();
		    $("#menu-form").show();
			$.each( $("#menu-form form").serializeArray(), function(i, input ){
				var val = '';
				if( $(_parent).data( input.name.replace("menu_","")) ){
					val = $(_parent).data( input.name.replace("menu_",""));
				}
				 $('[name='+input.name+']',"#menu-form").val(  val );
			});

			if( activeMenu.data("align") ){
				$(".button-alignments button").removeClass("active");
				$( '[data-option="'+activeMenu.data("align") +'"]').addClass("active");

			}
		}

		/**
	 	 * fill data values for  top level menu when clicked Sub menu.
	 	 */
		function processSubMenu( item , _parent, _megamenu ){

			var pos =  $(item).offset();
		    $('#submenu-form').css('left',pos.left  - 30 );
			$('#submenu-form').css('top',pos.top - $('#submenu-form').height() );
	 		$("#submenu-form").show();

			$.each( $("#submenu-form form").serializeArray(), function(i, input ){
				 $('[name='+input.name+']',"#submenu-form").val( $(_parent).data( input.name.replace("submenu_",""))  );
			} ) ;

		}

		/**
	 	 * menu form handler
	 	 */
		function menuForm(){
			$("input, select","#menu-form").change( function (){

			 	if( activeMenu ){
			 		if( $(this).hasClass('menu_submenu')   ) {
					 	var item = $("a",activeMenu);

				 		if( $(this).val()  && !$(item).hasClass( 'dropdown-toggle' ) ) {
				 			$(item).addClass( 'dropdown-toggle' );
				 			$(item).attr( 'data-toggle', 'wpo-dropdown' );

				 		 	var div = '<div class="dropdown-menu"><div class="dropdown-menu-inner"><div class="row active"></div></div></div>';
				 		 	$(activeMenu).addClass('parent').addClass('dropdown');
				 		 	$(activeMenu).append( div );
				 		 	$(activeMenu).removeClass('disable-menu');
				 		} else if(  $(this).val() == 1 ) {
			 				$(activeMenu).removeClass('disable-menu');
				 		}else {
			 				//$(activeMenu).addClass('disable-menu');
			 				$(activeMenu).removeClass('parent').find('.dropdown-menu').remove();
				 		}
				 		$(activeMenu).data('submenu', $(this).val() );
				 	}else if( $(this).hasClass('menu_subwidth') ){
				 		var width = parseInt( $(this).val() );
				 		if( width > 200 ){
				 			$(".dropdown-menu", activeMenu ).width( width );
 							$(activeMenu ).children('.dropdown-menu').width( width );
			 				$(activeMenu ).children('.dropdown-mega').width( width );
				 		}
				 	}
			 	}
		 	} );

			$("input.menu_subwidth","#menu-form").keypress( function( event ){

				if ( event.which == 13 ) {
				    event.preventDefault();
				}
				var $this = this;
				 setTimeout( function(){
			 		var width = parseInt( $($this).val() );
				 	if( width > 199 ){
			 			$(activeMenu ).children('.dropdown-menu').width( width );
			 			$(activeMenu ).children('.dropdown-mega').width( width );
			 			$(activeMenu).data('subwidth', width );
			 		}

				 }, 300 );
			} );
		}

		/**
	 	 * submenu handler.
	 	 */
		function submenuForm(){
			$("select, input", '#submenu-form').change( function(){
			 	 if( activeMenu ){
			 	 	if( $(this).attr('name') == 'submenu_group' ){
			 	 		if( $(this).val() == 1 ){
		 	 				$(activeMenu).addClass('mega-group');
					 		$(activeMenu).children(".dropdown-menu").addClass('dropdown-mega').removeClass('dropdown-menu');

			 	 		}else {
					 		$(activeMenu).removeClass('mega-group');
					 		$(activeMenu).children(".dropdown-mega").addClass('dropdown-menu').removeClass('dropdown-mega');
					 	}
					 	$( activeMenu ).data('group', $(this).val() );
			 	 	}
			 	 }
			} );
		}

		/**
	 	 * listen Events to operator Elements of MegaMenu such as link, colum, row and Process Events of buttons of setting forms.
	 	 */
		function listenEvents( $megamenu ){

			/**
			 *  Link Action Event Handler.
			 */
			$('.form-setting').hide();
			$( 'a', $megamenu ).click( function(event){

				var $this = this;
				var  $parent = $(this).parent();
				/* remove all current row and column are actived */
				$(".row", $megamenu).removeClass('active');
				$(".mega-col", $megamenu).removeClass('active');

			//	if( $parent.parent().hasClass('megamenu') ){
				 	var pos =  $(this).offset();
				    $('#menu-form').css('left',pos.left  - 30 );
					$('#menu-form').css('top',pos.top - $('#menu-form').height() );
			//	}




 				activeMenu = $parent;

 				if($parent.hasClass('depth-0 parent')){
 					$('select[name="menu_submenu"] option[value="1"]').prop('selected', true);
 				}else{
 					$('select[name="menu_submenu"] option[value="0"]').prop('selected', true);
 				}

			 	if( $parent.hasClass('dropdown-submenu') ){
			 		 $( ".dropdown-submenu", $parent.parent() ).removeClass( 'open' );
			 		 $parent.addClass('open');
			 		 processSubMenu( $this, $parent, $megamenu );
			 	}else {
			 		if( $parent.parent().hasClass('megamenu') ){
	                	 $("ul.navbar-nav > li" ).removeClass('open');
	             	}
	                $parent.addClass('open');

                 	processMenu ( $this, $parent, $megamenu );

	             }

		         event.stopPropagation();
		         return false;
			});


			/**
			 * Row action Events Handler
			 */
			 $("#menu-form .add-row").click( function(){
			 	var row = $( '<div class="row"></div>'  );
			 	var child = $(activeMenu).children('.dropdown-menu').children('.dropdown-menu-inner');
			 	child.append( row );
			 	child.children(".row").removeClass('active');
			 	row.addClass('active');

			 });

			  $("#menu-form .remove-row").click( function(){
			  	if( activeMenu ){
			  		 var hasMenuType = false;
			  		 $(".row.active", activeMenu).children('.mega-col').each( function(){
			  		 	if( $(this).data('type') == 'menu' ){
			  		 		hasMenuType = true;
			  		 	}
			  		 });

			  		if( hasMenuType == false ){
		  				$(".row.active", activeMenu).remove();
		  			}else {
		  				alert( 'You can remove Row having Menu Item(s) Inside Columns' );
		  				return true;
		  			}
		  			removeRowActive();
			  	}

			 });

			 $($megamenu).delegate( '.row', 'click', function(e){
		 		$(".row",$megamenu).removeClass('active');
		 		$(this).addClass('active');
		 		e.stopPropagation();
	    	 });

			 /**
			  * Column action Events Handler
			  */
			 $("#menu-form .add-col").click( function(){
		 		if ( activeMenu ){
		 			var num = 6;
		 			var col = $( '<div class="col-md-'+num+' mega-col active"><div></div></div>'  );
		 			$(".mega-col",activeMenu).removeClass('active');
					$( ".row.active", activeMenu ).append( col );
					col.data( 'colwidth', num );
					var cols = $(".dropdown-menu .mega-col", activeMenu ).length;
					$(activeMenu).data('cols', cols);
		 		}

		 		recalculateColsWidth();
			 } );

			 $(".remove-col").click( function(){
			 	if( activeMenu ){
			 		if( $(".mega-col.active", activeMenu).data('type') == 'menu' ) {
			 			alert('You could not remove this column having menu item(s)');
			 			return true;
			 		}else {
			 			$(".mega-col.active", activeMenu).remove();
			 		}
			 	}

			 	removeColumnActive();
			 	recalculateColsWidth();
			 } );


		 	$($megamenu).delegate( '.mega-col > div', 'click', function(e){

		 		$(".mega-col",$megamenu).removeClass('active');


		 		var colactive = $(this).parent();
	 		 	var pos =  $(colactive).offset();
	 		 	$(colactive).addClass('active');

		 		$("#column-form").css({'top':pos.top-$("#column-form").height(), 'left':pos.left}).show();

		 		if( $(this).data('type') != 'menu' ){
		 	 		$("#widget-form").css({'top':pos.top+$(colactive).height(), 'left':pos.left}).show();
		 		}else{
		 			$("#widget-form").hide();
		 		}

		 		$(".row",$megamenu).removeClass('active');

		 		$(colactive).parent().addClass('active');
		 		$("#submenu-form").hide();
		 		$.each( $(colactive).data(), function( i, val ){
	 				$('[name='+i+']','#column-form').val( val );
	 			} );

		 		e.stopPropagation();
		 	} );


		 	/**
		 	 * Column Form Action Event Handler
		 	 */
		 	$('input, select', '#column-form').change( function(){
		 		if( activeMenu ) {
		 			var col = $( ".mega-col.active", activeMenu );
		 			if( $(this).hasClass('colwidth') ){
		 				var cls = $(col).attr('class').replace(/col-md-\d+/,'');
		 				$(col).attr('class', cls + ' col-md-' + $(this).val() );
		 			}
		 			$(col).data( $(this).attr('name') ,$(this).val() );
		 		}
	 		} );

		 	$(".form-setting").each( function(){
		 		var $p = $(this);
		 		$(".popover-title span",this).click( function(){
		 			if( $p.attr('id') == 'menu-form' ){
		 				removeMenuActive();
		 			}else if( $p.attr('id') == 'column-form' ){
		 				removeColumnActive();
		 			}else {
		 				$('#submenu-form').hide();
		 				$('#widget-form').hide();
		 			}
		 		} );
		 	} );

	 		$( ".form-setting" ).draggable();

 			/**
 			 * inject widgets
 			 */
 			 $("#btn-inject-widget").click( function(){
 			 	var wid = $('select', $(this).parent() ).val();
 				if( wid > 0 ){
 					var col = $( ".mega-col.active", activeMenu );
 					var a =  $(col).data( 'widgets') ;

 					if( $(col).data( 'widgets') ){
 						if( $(col).data( 'widgets').indexOf("wid-"+wid ) == -1 ) {
 							$(col).data( 'widgets', a +"|wid-"+wid );
 						}
 					}else {
 						$(col).data( 'widgets', "wid-"+wid );
 				 	}
		 			$(col).children('div').html('<div class="loading">Loading....</div>');
 				 	$.ajax({
						url: config.action_widget,
						data:'widgets='+$(col).data( 'widgets'),
						type:'POST',
						}).done(function( data ) {
				 		$(col).children('div').html( data );

				 		$("#widget-form").css({'top':$( ".mega-col.active").offset().top+$(col).height()}).show();


				   });

 				}else {
 					alert( 'Please select a widget to inject' );
 				}
 			 } );


 			 /**
 			  * unset mega menu setting
 			  */
 			  $("#unset-data-menu").click( function(){
 				 if( confirm('Are you sure to reset megamenu configuration') ){
 				    $.ajax({
						url: config.action,
						data: 'reset=1',
						type:'POST',
						}).done(function( data ) {
					 		 location.reload();
				    });
				}
				return false;
 			  } );


 			$($megamenu).delegate( '.wpo-widget', 'hover', function(){
		 		//$(".row",$megamenu).removeClass('active');
		 		// $(this).addClass('active');
	    	 	 var w = $(this);
	    	 	 	var col = $(this).parent().parent();
	    	 	 if( $(this).find('.w-setting').length<= 0 ){
	    	 	 	var _s = $('<span class="w-setting"></span>');
	    	 	 	$(w).append(_s);
	    	 	 	_s.click( function(){

	    	 	 		var dws = col.data('widgets')+"|";
	    	 	 	 	var dws = dws.replace( $(w).attr('id')+"|",'' ).replace(/\|$/,'');
	    	 	 		col.data('widgets',dws);
	    	 	 		$(w).remove();
	    	 	 		$("#widget-form").css({'top':$( ".mega-col.active").offset().top+$(col).height()}).show();
	    	 	 	} );
	    	 	 }
	    	 });


 			$(".button-alignments button").click( function(){
 				if( activeMenu ){
	 				$(".button-alignments button").removeClass( "active");
	 				$(this).addClass( 'active' );

	 				$(activeMenu).data( 'align', $(this).data("option") );
	 			 	var cls = $( activeMenu ).attr("class").replace(/aligned-\w+/,"");
	 			  	$( activeMenu ).attr( 'class', cls );
	 				$( activeMenu ).addClass( $(this).data("option") );
 				}
 			} );
		}

		function recalculateColsWidth(){
			if( activeMenu ){
				var childnum = $( "#mainmenutop .row.active" ).children(".mega-col").length;

				var dcol = Math.floor( 12/childnum );
				var a = 12%childnum;
				$( "#mainmenutop .row.active" ).children(".mega-col").each( function(i, col ) {
					if( a > 0 && (i == childnum-1) ){
			 			dcol = dcol+a;
					}
		 			var cls = $(this).attr('class').replace(/col-md-\d+/,'');
		 			$(this).attr('class', cls + ' col-md-' + dcol );
					$(this).data( 'colwidth', dcol );
				});
			}
		}

	 	/**
	 	 * remove active status for current row.
	 	 */
	 	function removeRowActive(){
	 		$('#column-form').hide();
 			$( "#mainmenutop .row.active" ).removeClass('active');
	 	}

	 	/**
	 	 * remove column active and hidden column form.
	 	 */
	 	function removeColumnActive(){
	 		$('#column-form').hide();$('#widget-form').hide();
	 		$( "#mainmenutop .mega-col.active" ).removeClass('active');
	 	}

	 	/**
	 	 * remove active status for current menu, row and column and hidden all setting forms.
	 	 */
	 	function removeMenuActive(){
	 		$('.form-setting').hide();
	 		$( "#mainmenutop .open" ).removeClass('open');
	 		$( "#mainmenutop .row.active" ).removeClass('active');
 			$( "#mainmenutop .mega-col.active" ).removeClass('active');
 			if( activeMenu ) {
		 		activeMenu = null;
	 		}
	 	}

	 	/**
	 	 * process saving menu data using ajax request. Data Post is json string
	 	 */
	 	function saveMenuData(){
	 	 	var output = new Array();
	 	 	 $("#megamenu-content #mainmenutop li.parent").each( function() {
				 	var data = $(this).data();
				 	data.rows = new Array();

				 	$(this).children('.dropdown-menu').children('div').children('.row').each( function(){
				 		var row =  new Object();
				 		row.cols = new Array();
			 			$(this).children(".mega-col" ).each( function(){
			 				row.cols.push( $(this).data() );
			 			} );
			 			data.rows.push(row);
				 	} );

				 	output.push( data );
	 	 	 }  );
 	 	 	var j = JSON.stringify( output );
 	 	 	var params = 'params='+j;

 	 	 	$.ajax({
				url: config.action_menu,
				data:params,
				type:'POST',
				}).done(function( data ) {
		 		 location.reload();
		   });
	 	}

	 	/**
	 	 * Make Ajax request to fill widget content into column
	 	 */
	 	function loadWidgets(){
	 		$("#wpo-progress").hide();
	 		var ajaxCols = new Array();
	 		$("#megamenu-content #mainmenutop .mega-col").each( function() {
	 		 	var col = $(this);

	 		 	if( $(col).data( 'widgets') && $(col).data("type") != "menu" ){
	 		 		ajaxCols.push( col );
				}
	 		});

	 		var cnt = 0;
	 		if( ajaxCols.length > 0 ){
	 			$("#wpo-progress").show();
	 			$("#megamenu-content").hide();
	 		}
	 		$.each( ajaxCols, function (i, col) {
	 			$.ajax({
					url: config.action_widget,
					data:'widgets='+$(col).data( 'widgets'),
					type:'POST',
					}).done(function( data ) {
			 		col.children('div').html( data );
			 		cnt++;
			 		$("#wpo-progress .progress-bar").css("width", (cnt*100)/ajaxCols.length+"%" );
			 		if( ajaxCols.length == cnt ){
			 			$("#wpo-progress").delay(600).fadeOut();
			 			$("#megamenu-content").delay(1000).fadeIn();
			 		}
		 			$( "a", col ).attr( 'href', '#megamenu-content' );
			   });
	 		});
	 	}

	 	/**
	 	 * reload menu data using in ajax complete and add healders to process events.
	 	 */
	 	function reloadMegamenu(){
			var megamenu = $("#megamenu-content #mainmenutop");
			$( "a", megamenu ).attr( 'href', '#' );
			$( '[data-toggle="dropdown"]', megamenu ).attr('data-toggle','wpo-dropdown');
			listenEvents( megamenu );
			submenuForm();
			menuForm();
		 	loadWidgets();
	 	}

	 	/**
	 	 * initialize every element
	 	 */
		this.each(function() {
			var megamenu = this;

			$("#form-setting").hide();
			reloadMegamenu(  );

			$("#save-data-menu").click( function(){
	 			saveMenuData();
			} );

	 	// 	saveMenuData();
			/*
			$.ajax({
				url: config.action,
				}).done(function( data ) {
			 		$("#megamenu-content").html( data );
			 		reloadMegamenu(  );
			 		$("#save-data-menu").click( function(){
			 			saveMenuData();
			 		} );
		   }); */
			addModalBox();
			editWidget();
			removeWidget();
		});

		function removeWidget(){
			$('.tab-content').delegate('.wpo-delete','click',function(){
				if(confirm('Are you sure?')){
					var $tr = $(this).parent().parent();
					$.ajax({
						url: ajaxurl+'?action=wpo_shortcode_delete',
						type: 'POST',
						data: {id: $(this).attr('data-id')},
						success:function(response){
							if(response){
								$tr.fadeOut(200);
								setTimeout(function(){
									$tr.remove();
									$('.dropdown-menu-inner #wid-'+response).remove();
									$('#widget-form [name="inject_widget"] option[value="'+response+'"]').remove();
								},250);
							}
						}
					});
				}
				return false;
			});
		}

		function editWidget(){
			$('.megamenu-pages').undelegate('.wpo-edit-widget', 'click');
			$('.megamenu-pages').delegate('.wpo-edit-widget', 'click',function(){
				$('#myModal .modal-content .spinner.top').show();
				$('#myModal .modal-dialog').css('width',980);
				var $type = $(this).attr('data-type');
				var $id = $(this).attr('data-id');
				$('#myModal .modal-body .modal-body-inner').html("");
				$('#myModal .modal-body .modal-body-inner').load(ajaxurl+'?action=wpo_shortcode_button&type='+$type+'&id='+$id,function(){
					$('#myModal .modal-content .spinner.top').hide();
					$('#myModal .modal-body .modal-body-inner .wpo-button-back').remove();
					$('#myModal .modal-body .textarea_html').each(function(){
						init_textarea_html($(this));
				    });
				});
				$('#myModal').modal();
				return false;
			});
			$('#myModal').undelegate('.wpo-button-save', 'click');
			$('#myModal').delegate('.wpo-button-save', 'click', function(event) {
				var datastring = $('#wpo-shortcode-form').serialize();
				$.ajax({
					url: ajaxurl+'?action=wpo_shortcode_save',
					type: 'POST',
					dataType :'JSON',
					data: datastring,
					beforeSend:function(){
						$('#myModal .wpo-widget-message').html("");
						$('#myModal #wpo-shortcode-form').find('input,button,radio,select,textarea').prop('disabled',true);
						$('#myModal #wpo-shortcode-form .spinner-button').show();
					},
					success:function(response){
						$('#myModal #wpo-shortcode-form').find('input,button,radio,select,textarea').prop('disabled',false);
						$('#myModal .wpo-widget-message').append('<div class="alert alert-success"><strong>'+response.message+'</strong><a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a></div>');
						$('#myModal #wpo-shortcode-form .spinner-button').hide();
						$('#manage-widget table tr[data-widget-id="'+response.id+'"] .name').text(response.title);
						$('#widget-form [name="inject_widget"] option[value="'+response.id+'"]').text(response.title);
					}
				});
			});

			$('#myModal').on('hidden.bs.modal', function () {
				$('#myModal .wpo-widget-message').html("");
			});
		}

		function addModalBox(){
			$(".btn-modal").click( function(){
				$('#myModal .modal-content .spinner.top').show();
				$('#myModal .modal-dialog').css('width',980);
				$('#myModal .modal-body .modal-body-inner').html("");
				$('#myModal .modal-body .modal-body-inner').load($(this).attr('href'),function(){
					$('#myModal .modal-body .spinner.top').hide();
				});
				$('#myModal').modal();
				$('#myModal').attr('rel', $(this).attr('rel') );
				return false;
			} );
		}
		//disable submit form with Enter key
		$(".form-setting input").keypress(function(event) {
			if (event.which == 13) {
				event.preventDefault();
				return false;
			}
		});
		return this;
	};

})(jQuery);



/* =========================================================
 * media-editor.js v1.0.0
 * =========================================================
 * Copyright 2013 Wpbakery
 *
 * WP 3.5 Media manager integration into Visual Composer.
 * ========================================================= */
(function ($) {
    var media = wp.media,
        Attachment = media.model.Attachment,
        Attachments = media.model.Attachments,
        Query = media.model.Query,
        l10n = {set_image:'Set Image',add_images:'Add Image'},
        workflows = {};
    // wp.media.controller.VcSingleImage extends featuredImage controller
    // ---------------------------------
    media.controller.VcSingleImage = media.controller.FeaturedImage.extend({
        defaults:_.defaults({
            id:'vc-single-image',
            filterable:'uploaded',
            multiple:false,
            toolbar:'vc-single-image',
            title:l10n.set_image,
            priority:60,
            syncSelection:false
        }, media.controller.Library.prototype.defaults),
        updateSelection:function () {
            var selection = this.get('selection'),
                id = media.VcSingleImage.getData(),
                attachment;
            if ('' !== id && -1 !== id) {
                attachment = Attachment.get(id);
                attachment.fetch();
            }
            selection.reset(attachment ? [ attachment ] : []);
        }
    });
    media.controller.VcGallery = media.controller.VcSingleImage.extend({
        defaults:_.defaults({
            id:'vc-gallery',
            title:l10n.add_images,
            toolbar:'main-insert',
            filterable:'uploaded',
            library:media.query({type:'image'}),
            multiple:'add',
            editable:true,
            priority:60,
            syncSelection:false
        }, media.controller.Library.prototype.defaults),
        updateSelection:function () {
            var selection = this.get('selection'),
                ids = media.vc_editor.getData(),
                attachments;
            if ('' !== ids && -1 !== ids) {
                attachments = _.map(ids.split(/,/), function (id) {
                    return Attachment.get(id);
                });
            }
            selection.reset(attachments);
        }
    });

    media.VcSingleImage = {
        getData:function () {
            return this.$hidden_ids.val();
        },
        set:function (selection) {

            this.$img_ul.html(_.template($('#vc-settings-image-block').html(), selection.attributes));

            this.$clear_button.show();

            this.$hidden_ids.val(selection.get('id')).trigger('change');
            return false;
        },
        frame:function (element) {
            this.element = element;

            this.$button = $(this.element);
            this.$block = this.$button.closest('.edit_form_line');
            this.$hidden_ids = this.$block.find('.gallery_widget_attached_images_ids');
            this.$img_ul = this.$block.find('.gallery_widget_attached_images_list');
            this.$clear_button = this.$img_ul.next();

            // TODO: Refactor this all params as template

            if (this._frame)
                return this._frame;
            this._frame = wp.media({
                state:'vc-single-image',
                states:[ new wp.media.controller.VcSingleImage() ]
            });
            this._frame.on('toolbar:create:vc-single-image', function (toolbar) {
                this.createSelectToolbar(toolbar, {
                    text:l10n.set_image
                });
            }, this._frame);

            this._frame.state('vc-single-image').on('select', this.select);
            return this._frame;
        },
        select:function () {
            var selection = this.get('selection').single();
            wp.media.VcSingleImage.set(selection ? selection : -1);
        }
    };

    media.view.MediaFrame.VcGallery = media.view.MediaFrame.Post.extend({
        // Define insert-vc state.
        createStates:function () {
            var options = this.options;

            // Add the default states.
            this.states.add([
                // Main states.
                new media.controller.VcGallery()
            ]);
        },
        // Removing let menu from manager
        bindHandlers:function () {
            media.view.MediaFrame.Select.prototype.bindHandlers.apply(this, arguments);
            this.on('toolbar:create:main-insert', this.createToolbar, this);

            var handlers = {
                content:{
                    'embed':'embedContent',
                    'edit-selection':'editSelectionContent'
                },
                toolbar:{
                    'main-insert':'mainInsertToolbar'
                }
            };

            _.each(handlers, function (regionHandlers, region) {
                _.each(regionHandlers, function (callback, handler) {
                    this.on(region + ':render:' + handler, this[ callback ], this);
                }, this);
            }, this);
        },
        // Changing main button title
        mainInsertToolbar:function (view) {
            var controller = this;

            this.selectionStatusToolbar(view);

            view.set('insert', {
                style:'primary',
                priority:80,
                text:l10n.add_images,
                requires:{ selection:true },

                click:function () {
                    var state = controller.state(),
                        selection = state.get('selection');

                    controller.close();
                    state.trigger('insert', selection).reset();
                }
            });
        }
    });
    media.vc_editor = _.clone(media.editor);
    _.extend(media.vc_editor, {
        $vc_editor_element:null,
        getData:function () {
            var $button = wp.media.vc_editor.$vc_editor_element,
                $block = $button.closest('.edit_form_line'),
                $hidden_ids = $block.find('.gallery_widget_attached_images_ids');
            return $hidden_ids.val();
        },
        insert:function (images) {
            var $button = wp.media.vc_editor.$vc_editor_element,
                $block = $button.closest('.edit_form_line'),
                $hidden_ids = $block.find('.gallery_widget_attached_images_ids'),
                $img_ul = $block.find('.gallery_widget_attached_images_list'),
                $clear_button = $img_ul.next(),
                $thumbnails_string = '';

            _.each(images, function (image) {
                $thumbnails_string += _.template($('#vc-settings-image-block').html(), image);
            });
            $hidden_ids.val(_.map(images,function (image) {
                return image.id;
            }).join(',')).trigger('change');
            $img_ul.html($thumbnails_string);
        },
        open:function (id) {
            var workflow, editor;

            id = this.id(id);

            workflow = this.get(id);

            // Initialize the editor's workflow if we haven't yet.
            if (!workflow)
                workflow = this.add(id);

            return workflow.open();
        },
        add:function (id, options) {
            var workflow = this.get(id);
            if (workflow)
                return workflow;

            workflow = workflows[ id ] = new media.view.MediaFrame.VcGallery(_.defaults(options || {}, {
                state:'vc-gallery',
                title:l10n.add_images,
                library:{ type:'image' },
                multiple:true
            }));
            workflow.on('insert', function (selection) {
                var state = workflow.state(),
                    data = [];

                selection = selection || state.get('selection');
                if (!selection)
                    return;

                this.insert(_.map(selection.models, function (model) {
                    return model.attributes;
                }));
            }, this);
            return workflow;
        },
        init:function () {
            $('body').unbind('click.vcGalleryWidget').on('click.vcGalleryWidget', '.gallery_widget_add_images', function (event) {
                var $this = $(this),
                    editor = 'visual-composer';
                wp.media.vc_editor.$vc_editor_element = $(this);
                if ($this.attr('use-single') === 'true') {
                    wp.media.VcSingleImage.frame(this).open('vc-editor');
                    return;
                }
                event.preventDefault();
                $this.blur();
                wp.media.vc_editor.open(editor);
            });
        }
    });
    _.bindAll(media.vc_editor, 'open');
    $(document).ready(function () {
        media.vc_editor.init();
    });
}(jQuery));