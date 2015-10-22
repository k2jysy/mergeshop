var WPO_Admin = window.WPO_Admin || {};

!function ($) {
	"use strict";
	$.extend(WPO_Admin, {
		init: function(){
			$('#section-customize-theme,#section-magemenu-menu').find('.explain a').addClass('button-primary').css('float','none');
			WPO_Admin.PageOption();
		},
		//define
		// params
	    params_Embed : function (input_id,parent_id){
	    	var input = jQuery(input_id);
	    	if(input.length>0){
		    	var oembed_url = input.val();
		    	if(oembed_url!="" && oembed_url.length>6){
		    		jQuery(parent_id+' .spinner').css('display','block');
		    		WPO_Admin.param_start_ajax_embed(oembed_url,parent_id);
		    	}
		        input.on('keyup', function (event) {
					WPO_Admin.param_ajax_Embed(jQuery(this), event,parent_id,input_id);
				});
			}
	    },

	    param_ajax_Embed : function (obj, e,parent_id,input_id) {
			// get typed value
			var oembed_url = obj.val();
			// only proceed if the field contains more than 6 characters
			if (oembed_url.length < 6)
				return;
			// only proceed if the user has pasted, pressed a number, letter, or whitelisted characters
			if (e === 'paste' || e.which <= 90 && e.which >= 48 || e.which >= 96 && e.which <= 111 || e.which == 8 || e.which == 9 || e.which == 187 || e.which == 190) {
				jQuery(parent_id+' .spinner').css('display','block');
				// clear out previous results
				jQuery(parent_id+' .result').html('');
				// and run our ajax function
				setTimeout(function () {
					// if they haven't typed in 500 ms
					if (jQuery(input_id+':focus').val() == oembed_url) {
						WPO_Admin.param_start_ajax_embed(oembed_url,parent_id);
					}
				}, 500);
			}
		},
		param_start_ajax_embed : function (oembed_url,parent_id){
		    jQuery.ajax({
				type : 'post',
				dataType : 'json',
				url : window.ajaxurl,
				data : {
					'action': 'wpo_post_embed',
					'oembed_url': oembed_url
				},
				success: function (response) {
					jQuery(parent_id+' .spinner').css('display','none');
					// if we have a response id
					if(response.check){
	                    jQuery(parent_id+' .wpo_embed_view .result').addClass('active').append(response.video);
	                    jQuery(parent_id+' .wpo_embed_view .result').append('<a class="remove-embed"></a>');
	                    jQuery(parent_id+' .result a.remove-embed').click(function(){
	                    	jQuery(parent_id).find('input').val("");
	                    	jQuery(parent_id+' .result').html("");
	                    	jQuery(parent_id+' .result').removeClass('active');
	                    });
	                }else{
	                	jQuery(parent_id+' .wpo_embed_view .result').append(response.video);
	                }
				}
			});
		},
		// Page Option Config
		PageOption : function(){
	    	var check = jQuery('#wpo-config .page-layout select').val();
			jQuery('#wpo-config .page-layout img').each(function(index,item){
				if(check == jQuery(this).attr('data-value'))
					jQuery(this).addClass('selected');
			});
			jQuery('#wpo-config .page-layout img').on("page-layout-select",function(){
				jQuery('#wpo-config .page-layout img.layout').removeClass('selected');
				jQuery(this).addClass('selected');
				var arr = jQuery(this).attr('data-value').split('-');
				if(arr[0]=='1' || arr[0] == 'm'){
					jQuery('#wpo-config .left-sidebar').slideDown(400);
				}else{
					jQuery('#wpo-config .left-sidebar').slideUp(400);
				}
				if(arr[2]=='1' || arr[2] == 'm'){
					jQuery('#wpo-config .right-sidebar').slideDown(400);
				}else{
					jQuery('#wpo-config .right-sidebar').slideUp(400);
				}
			});
			jQuery('#wpo-config .page-layout img.selected').trigger('page-layout-select');
			jQuery('#wpo-config .page-layout img.layout').click(function(event) {
				jQuery(this).addClass('selected').trigger('page-layout-select');
				jQuery('#wpo-config .page-layout select').val(jQuery(this).attr('data-value'));
			});
	    }
	});

	$(document).ready(function(){
		WPO_Admin.init();
	});

}(jQuery);


