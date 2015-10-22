!function ($) {
	"use strict";
	$(document).ready(function() {
		// Ajax Swich Layout
		$('#wpo-filter .display a').click(function(){
            var query = $(this).data('query');
            var type = $(this).data('type');
            var $this = $(this);

            if(!$(this).hasClass('active')){
	            $.ajax({
	                url: ajaxurl,
	                data:{action:'wpo_display_layout',query:query,type:type},
	                type: 'POST',
	                beforeSend:function(){
	                	$this.addClass('waiting').append('<span class="loading" style="background:url('+woocommerce_params.ajax_loader_url+') no-repeat center center;display:block;background-size:16px 16px;width:100%;height:100%;position:absolute;top:0;left:0"></span>');
	                },
	                success: function(response){
	                	$this.removeClass('waiting');
	                    $('.products>.row').html(response);
	                    $('#wpo-filter .display a .loading').remove();
	                }
	            });
	        }
	        $('#wpo-filter .display a').removeClass('active');
            $(this).addClass('active');
            return false;
        });

        // Ajax QuickView
		$(document).on("click", ".quickview", function () {
		    var productslug = $(this).data('productslug');
		    $.post(ajaxurl, {action:'wpo_quickview',productslug:productslug}, function(response) {
		    	$('#wpo_modal_quickview .modal-body').html(response);
		    });
		});

		$('#wpo_modal_quickview').on('hidden.bs.modal',function(){
			$(this).find('.modal-body').empty().append('<span class="spinner"></span>');
		});
	});
}(jQuery);