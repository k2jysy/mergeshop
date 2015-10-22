!function($) {
	if($('#geocomplete').length>0){
		var _lat_lng = $('input[name=link]').val();
		var loca = _lat_lng;
		_lat_lng = _lat_lng.split(',');
		var center = new google.maps.LatLng(_lat_lng[0],_lat_lng[1]);
	    $("#geocomplete").geocomplete({
			map: ".map_canvas",
			types: ["establishment"],
			country: "de",
			details: ".mapdetail",
			markerOptions: {
				draggable: true
			},
			location:loca,
			mapOptions: {
				scrollwheel :true,
				zoom:15,
				center:center
			}
	    });
	    $(".googlefind button.find").click(function(){
			$("#geocomplete").trigger("geocode");
		});
	    $("#geocomplete").bind("geocode:dragged", function(event, latLng){
			$("input[name=lat]").val(latLng.lat());
			$("input[name=lng]").val(latLng.lng());
			$(".wpb_el_type_hidden input[name=link]").val(latLng.lat()+','+latLng.lng());
	    }).bind("geocode:result",function(event, latLng){
	    	$('.wpo-latgmap,.wpo-lnggmap').trigger('change');
	    });

	    $('.wpo-latgmap,.wpo-lnggmap').keyup(function(event) {
	    	var value = $('.wpo-latgmap').val()+','+$('.wpo-lnggmap').val();
	    	$(".wpb_el_type_hidden input[name=link]").val(value);
	    }).change(function(){
	    	var value = $('.wpo-latgmap').val()+','+$('.wpo-lnggmap').val();
	    	$(".wpb_el_type_hidden input[name=link]").val(value);
	    });
	}
}(window.jQuery);
