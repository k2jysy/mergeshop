
<div class="wpo-shortcodes">
	<ul class="wrapper clearfix">
		<?php foreach( $shortcodes as $key => $shortcode ){ ?>
		<li class="shortcode-col">
			<div class="wpo-shorcode-button btn btn-default" data-name="<?php echo $shortcode['name'];?>">
				<?php if( isset($shortcode['icon']) &&  $shortcode['icon'] ){ ?>
					<div class="wpo-icon wpo-icon-<?php echo $shortcode['icon'];?>"></div>
				<?php } ?>
				<div class="content">
					<div class="title"><?php echo $shortcode['title'] ?></div>
					<em><?php echo $shortcode['desc'] ?></em>
				</div>
			</div>
		</li>
		<?php  } ?>
	</ul>
</div>
<script>
jQuery(".wpo-shortcodes").WPO_Shortcode();
</script>