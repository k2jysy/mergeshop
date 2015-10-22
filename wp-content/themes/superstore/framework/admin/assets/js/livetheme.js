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

(function($){
	"use strict";

	// Before Load Form
	var livethemeLoad = function(){
		jQuery('body').html('<div class="spinner" style="display:block;position:relative;margin: 20% auto;float:none;z-index:99999"></div><div class="over" style="display:none;background:#fff;z-index:99998;position:absolute;top:0;left:0;width:100%;height:100%;opacity:0.65;"></div>');
		jQuery('body').append('<div id="wrapper"></div>').find('#wrapper').load(ajaxurl+'?action=livetheme_render',function(){
			jQuery('#main-preview iframe').load(function(){
				jQuery('body>.spinner').hide();
				jQuery(this).contents().find('html').css('cssText','margin-top:0px !important').find('#wpadminbar').remove();
			});
		});
	}

	jQuery(document).ready(function() {
		livethemeLoad();
	});

	// init Plugin
	$.fn.WPO_livetheme = function(opts) {
	 	/**
	 	 * initialize every element
	 	 */
	 	var config = $.extend({}, {
	 		customizeURL : ''
		}, opts);

	 	var output ='';

	 	function btn_Show_click(){
	 	 	$("#wpo-customize .btn-show").click( function(){
				$("body").toggleClass("off-customize");
			});
	 	}

	 	function select_save_file(){
	 		$(".show-for-existed").hide();

	 		$("#saved-files").change( function() {
		 		if( $(this).val() ){
					$(".show-for-notexisted").hide();
					$(".show-for-existed").show();
				}else {
					$(".show-for-notexisted").show();
					$(".show-for-existed").hide();
				}

				var url  = config.customizeURL+$(this).val()+".json?rand"+Math.random();

				$.getJSON( url, function(data) {
					var items = data;
					if( items ){
						$('#customize-body .accordion-group').each( function(){
							var i = 0;
							$("input, select", this).each( function(){
								if( $(this).data('match') ){
									if( items[$(this).data('match')] && items[$(this).data('match')][i] ){
										var el = items[$(this).data('match')][i];
									 	$(this).val( el.val );
									 	if( el.val== '') {
									 		$(this).css('background',"inherit");
									 	}
									 	else {
									 		$(this).css('background',"#"+el.val);
									 	}
									 	$(this).ColorPickerSetColor(el.val );
									}
									i++;
								}
							} );

						});
						$('#customize-body .accordion-group .input-setting[type="hidden"]').each(function(){
							var val = $(this).val();
							if(val!=""){
								$(this).parent().find('.bi-wrapper div').removeClass('active');
								$(this).parent().find('.bi-wrapper div[data-val="'+val+'"]').addClass('active');
							}
						});
					}
				});

				$("#main-preview iframe").contents().find("#customize-style-css").remove();
				if( $(this).val() ){
					var _link = $('<link rel="stylesheet" href="" id="customize-style-css">');
					_link.attr('href', config.customizeURL+$(this).val()+".css?rand="+Math.random() );
					$("#main-preview iframe").contents().find("head").append( _link );
				}

			});
	 	}

	 	function iframe_ready(){

	 		/**
			 * BACKGROUND-IMAGE SELECTION
			 */
			

			$('body').delegate('.bi-wrapper > div', 'click', function(event) {
				var $input = $(this).parent().parent().find('.input-setting');
				$input.val( $(this).data('val') );
				 $(this).parent().find('>div').removeClass('active');
				 $(this).addClass('active');

				 if( $input.data('selector') ){
					//$("#main-preview iframe").contents().find($input.data('selector')).css( $input.data('attrs'),'url('+ $(this).data('image') +')' );
					setStyle();
					// console.log($input.data('selector'));
				 }
			});

			$(".clear-bg").click( function(){

				var $parent = $(this).parent();
				var $input  = $(".input-setting", $parent );
				if( $input.val('') ) {
					if( $parent.hasClass("background-images") ) {
						$('.bi-wrapper > div',$parent).removeClass('active');
						$($input.data('selector'),$("#main-preview iframe").contents()).css( $input.data('attrs'),'none' );
					}else {
						$input.attr( 'style','' )
					}
					setStyle();
				}
				$input.val('');

			} );
			$("#main-preview iframe").load(function(){
				$("#main-preview iframe").contents().find('#wpo-customize').remove();
			});

	 		$("#main-preview iframe").ready(function(){
				$('.accordion-group input.input-setting').each( function(){
				 	 var input = this;
				 	 $(input).attr('readonly','readonly');
				 	 $(input).ColorPicker({
				 	 	onChange:function (hsb, hex, rgb) {
				 	 		$(input).css('backgroundColor', '#' + hex);
				 	 		$(input).val( hex );
				 	 		if( $(input).data('selector') ){
								//$("#main-preview iframe").contents().find($(input).data('selector')).css( $(input).data('attrs'),"#"+$(input).val() )
								setStyle();
							}
				 	 	}
				 	 });
			 	} );
				$('.accordion-group select.input-setting').change( function(){
					var input = this;
						if( $(input).data('selector') ){
						var ex = $(input).data('attrs')=='font-size'?'px':"";
						$("#main-preview iframe").contents().find($(input).data('selector')).css( $(input).data('attrs'), $(input).val() + ex);
					}
				});

			});
			//setStyle();
	 	}

	 	function setStyle(){
	 		var _iframe = $("#main-preview iframe").contents();
	 		if( _iframe.find('style#custom-style').length>0){
	 			_iframe.find('style#custom-style').remove();
	 		}
	 		output = '<style id="custom-style">';
 			$('.accordion-group input.input-setting').each(function() {
 				if($(this).val()!='' && $(this).hasClass('enable')){
 					output+=$(this).data('selector')+"{\n"+$(this).data('attrs')+":#"+$(this).val()+";}";
 				}
 			});

 			$('.bi-wrapper').each(function(){
 				var bg = $(this).find('>div.active');
 				if(bg.length>0){
 					var parent = bg.parent().parent().find('.input-setting');
 					output+=parent.data('selector')+"{\n"+parent.data('attrs')+":url('"+bg.data('image')+"');}\n";
 				}
 			});
	 		output+='</style>';
	 		_iframe.find('head').append(output);
	 	}

	 	// Ajax Action
	 	function ajax_action(){
			$('.livetheme-action .livetheme-delete').click(function(){
				var value = $('#saved-files').val();
				if(value==''){
					alert('loi');
				}else{
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {action: 'livetheme_delete',file:value},
						beforeSend: function(){
							loading(true);
						},
						success: function(response){
							location.reload();
						}
					});
				}
				return false;
			});

			$('.livetheme-action .livetheme-save').click(function(){
				$('.input-setting').each( function(){
					if( $(this).data("match") ) {
						var val = $(this).data('selector')+"|"+$(this).data('attrs');
						$(this).parent().append('<input type="hidden" name="customize_match['+$(this).data("match")+'][]" value="'+val+'"/>');
					}
				} );

				var data = $('#form').serialize();

				$.ajax({
					url: ajaxurl+'?action=livetheme_submit',
					type: 'POST',
					data: data,
					dataType:'JSON',
					beforeSend: function(){
						loading(true);
					},
					success: function(response){
						location.reload();
					}
				});

				return false;
			});

	 	}
	 	// End Ajax Action

	 	function resetInput(){
	 		$('.input-setting').val('').attr('style','');
	 	}

	 	function loading(loop){
	 		if(loop){
	 			jQuery('body>.spinner').show();
				jQuery('body>.over').show();
	 		}else{
	 			jQuery('body>.spinner').hide();
				jQuery('body>.over').hide();
	 		}
	 	}

	 	function livetheme_ajax_Load_pattern(modal){
			modal.find('.modal-body').load(ajaxurl+'?action=livetheme_load_pattern');
		}

		function livetheme_modal_manager(){
			jQuery('#myPattern').on('hidden.bs.modal',function(){
				jQuery(this).find('.modal-body').empty().append('<span class="spinner"></span>');
			});
			jQuery('#myPattern').on('shown.bs.modal',function(){
				livetheme_ajax_Load_pattern(jQuery(this));
			});
		}

		function button_action_pattern(){
			$('body').delegate('.remove-pattern', 'click', function() {
				if( confirm('Are You Sure?') ){
					var pattern = $(this).data('name');
					var $this = $(this);
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {action: 'livetheme_delete_pattern',pattern:pattern},
						success: function(response){
							if(response){
								$this.parent().remove();
								$('.bi-wrapper div[data-name="'+pattern+'"]').remove();
							}else{
								alert('ERROR!');
							}
						}
					});
				}
				return false;
			});

			$('body').delegate('.addnew-pattern', 'click', function() {
				$('#filename').click();
			});

			$('body').delegate('#filename', 'change', function() {
				var file = $(this).val();
				if(isImage(file)){
					$.ajaxFileUpload({
						url:ajaxurl+'?action=livetheme_upload_pattern',
						secureuri:false,
						fileElementId:'filename',
						dataType: 'json',
						data:{name:'logan', id:'id'},
						success: function (data, status)
						{
							if(typeof(data.error) != 'undefined')
							{
								if(data.error != '')
								{
									alert(data.error);
								}else
								{
									alert(data.msg);
									$('.list-patterns .addnew').after('<div class="col-sm-2 pattern"><div class="bg" style="background:url('+data.linkimage+');"></div><a href="#" data-name="'+data.image+'" class="remove-pattern"></a></div>');
									$('.bi-wrapper').prepend('<div style="background:url('+data.linkimage+') no-repeat center center;" data-name="'+data.image+'" class="pull-left" data-image="'+data.linkimage+'" data-val="../../../images/bg/'+data.image+'"></div>');
								}
							}
						},
						error: function (data, status, e)
						{
							alert(e);
						}
					});
				}else{
					$(this).val('');
					alert('File is not support');
				}
			});
		}

		function getExtension(filename) {
		    var parts = filename.split('.');
		    return parts[parts.length - 1];
		}

		function isImage(filename) {
		    var ext = getExtension(filename);
		    switch (ext.toLowerCase()) {
		    case 'jpg':
		    case 'gif':
		    case 'bmp':
		    case 'png':
		        return true;
		    }
		    return false;
		}


		this.each(function() {
			btn_Show_click();
			select_save_file();
			iframe_ready();
			ajax_action();
			livetheme_modal_manager();
			button_action_pattern();
		});
		return this;
	};

})(jQuery);


jQuery.extend({
	handleError: function( s, xhr, status, e ) {
        // If a local callback was specified, fire it
        if ( s.error )
            s.error( xhr, status, e );
        // If we have some XML response text (e.g. from an AJAX call) then log it in the console
        else if(xhr.responseText)
            console.log(xhr.responseText);
    },
    createUploadIframe: function(id, uri)
	{
			//create frame
            var frameId = 'jUploadFrame' + id;
            var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';
			if(window.ActiveXObject)
			{
                if(typeof uri== 'boolean'){
					iframeHtml += ' src="' + 'javascript:false' + '"';

                }
                else if(typeof uri== 'string'){
					iframeHtml += ' src="' + uri + '"';

                }
			}
			iframeHtml += ' />';
			jQuery(iframeHtml).appendTo(document.body);

            return jQuery('#' + frameId).get(0);
    },
    createUploadForm: function(id, fileElementId, data)
	{
		//create form
		var formId = 'jUploadForm' + id;
		var fileId = 'jUploadFile' + id;
		var form = jQuery('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
		if(data)
		{
			for(var i in data)
			{
				jQuery('<input type="hidden" name="' + i + '" value="' + data[i] + '" />').appendTo(form);
			}
		}
		var oldElement = jQuery('#' + fileElementId);
		var newElement = jQuery(oldElement).clone();
		jQuery(oldElement).attr('id', fileId);
		jQuery(oldElement).before(newElement);
		jQuery(oldElement).appendTo(form);



		//set attributes
		jQuery(form).css('position', 'absolute');
		jQuery(form).css('top', '-1200px');
		jQuery(form).css('left', '-1200px');
		jQuery(form).appendTo('body');
		return form;
    },

    ajaxFileUpload: function(s) {
        // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout
        s = jQuery.extend({}, jQuery.ajaxSettings, s);
        var id = new Date().getTime()
		var form = jQuery.createUploadForm(id, s.fileElementId, (typeof(s.data)=='undefined'?false:s.data));
		var io = jQuery.createUploadIframe(id, s.secureuri);
		var frameId = 'jUploadFrame' + id;
		var formId = 'jUploadForm' + id;
        // Watch for a new set of requests
        if ( s.global && ! jQuery.active++ )
		{
			jQuery.event.trigger( "ajaxStart" );
		}
        var requestDone = false;
        // Create the request object
        var xml = {}
        if ( s.global )
            jQuery.event.trigger("ajaxSend", [xml, s]);
        // Wait for a response to come back
        var uploadCallback = function(isTimeout)
		{
			var io = document.getElementById(frameId);
            try
			{
				if(io.contentWindow)
				{
					 xml.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
                	 xml.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;

				}else if(io.contentDocument)
				{
					 xml.responseText = io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;
                	xml.responseXML = io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document;
				}
            }catch(e)
			{
				jQuery.handleError(s, xml, null, e);
			}
            if ( xml || isTimeout == "timeout")
			{
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    if ( status != "error" )
					{
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = jQuery.uploadHttpData( xml, s.dataType );
                        // If a local callback was specified, fire it and pass it the data
                        if ( s.success )
                            s.success( data, status );

                        // Fire the global callback
                        if( s.global )
                            jQuery.event.trigger( "ajaxSuccess", [xml, s] );
                    } else
                        jQuery.handleError(s, xml, status);
                } catch(e)
				{
                    status = "error";
                    jQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                if( s.global )
                    jQuery.event.trigger( "ajaxComplete", [xml, s] );

                // Handle the global AJAX counter
                if ( s.global && ! --jQuery.active )
                    jQuery.event.trigger( "ajaxStop" );

                // Process result
                if ( s.complete )
                    s.complete(xml, status);

                jQuery(io).unbind()

                setTimeout(function()
									{	try
										{
											jQuery(io).remove();
											jQuery(form).remove();

										} catch(e)
										{
											jQuery.handleError(s, xml, null, e);
										}

									}, 100)

                xml = null

            }
        }
        // Timeout checker
        if ( s.timeout > 0 )
		{
            setTimeout(function(){
                // Check to see if the request is still happening
                if( !requestDone ) uploadCallback( "timeout" );
            }, s.timeout);
        }
        try
		{

			var form = jQuery('#' + formId);
			jQuery(form).attr('action', s.url);
			jQuery(form).attr('method', 'POST');
			jQuery(form).attr('target', frameId);
            if(form.encoding)
			{
				jQuery(form).attr('encoding', 'multipart/form-data');
            }
            else
			{
				jQuery(form).attr('enctype', 'multipart/form-data');
            }
            jQuery(form).submit();

        } catch(e)
		{
            jQuery.handleError(s, xml, null, e);
        }

		jQuery('#' + frameId).load(uploadCallback	);
        return {abort: function () {}};

    },

    uploadHttpData: function( r, type ) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        // If the type is "script", eval it in global context
        if ( type == "script" )
            jQuery.globalEval( data );
        // Get the JavaScript object, if JSON is used.
        if ( type == "json" )
            eval( "data = " + data );
        // evaluate scripts within html
        if ( type == "html" )
            jQuery("<div>").html(data).evalScripts();

        return data;
    }
})

