<div class="products">
	<div class="row product-items">
		<?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
			<div class="<?php echo $class_column ?> product-cols">
				<?php wc_get_template_part( 'content', 'product-inner' ); ?>
			</div>
		<?php endwhile; ?>
	</div>
</div>
<?php wp_reset_postdata(); ?>

