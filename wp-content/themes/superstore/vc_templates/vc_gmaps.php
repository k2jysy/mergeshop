<?php
$output = $title = $link = $size = $zoom = $type = $bubble = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'link' => '21.0173222,105.78405279999993',
    'size' => 300,
    'zoom' => 14,
    'type' => 'ROADMAP',
    'bubble' => '',
    'pancontrol'=>'',
    'zoomcontrol'=>'',
    'maptypecontrol'=>'',
    'streetscontrol'=>'',
    'el_class' => ''
), $atts));

wp_enqueue_script('base_gmap_api_js');
wp_enqueue_script('base_gmap_function_js');
$bubble = ($bubble!='' && $bubble!='0') ? 'false' : 'true';
$pancontrol = ($pancontrol!='' && $pancontrol!='0') ? 'false' : 'true';
$zoomcontrol = ($zoomcontrol!='' && $zoomcontrol!='0') ? 'false' : 'true';
$maptypecontrol = ($maptypecontrol!='' && $maptypecontrol!='0') ? 'false' : 'true';
$streetscontrol = ($streetscontrol!='' && $streetscontrol!='0') ? 'false' : 'true';
$_id = wpo_makeid();
?>

<div id="map_canvas_<?php echo $_id; ?>" class="map_canvas" style="width:100%;height:<?php echo $size; ?>px;"></div>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		var stmapdefault = '<?php echo $link; ?>';
		var marker = {position:stmapdefault}
		jQuery('#map_canvas_<?php echo $_id; ?>').gmap({
			'zoom': <?php echo $zoom;  ?>  ,
			'center': stmapdefault,
			'mapTypeId':google.maps.MapTypeId.<?php echo $type; ?>,
			<?php if($bubble=='true'){ ?>
			'callback': function() {
				var self = this;
				self.addMarker(marker).click(function(){
					//self.openInfoWindow({'content': '$location'}, self.instance.markers[0]);
				});
			},
			<?php } ?>
			panControl: <?php echo $pancontrol ?>
		});
	});
	// jQuery(window).resize(function(){
	// 	var stct = new google.maps.LatLng('{$latitude}','{$longitude}');
	// 	jQuery('#map_canvas').gmap('option', 'center', stct);
	// });
</script>