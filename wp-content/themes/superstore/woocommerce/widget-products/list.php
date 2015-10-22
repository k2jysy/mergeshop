<ul class="product_list_widget">
	<?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
		<?php wc_get_template( 'content-widget-product.php', array( 'show_rating' => true ) ); ?>
	<?php endwhile; ?>
</ul>
<?php wp_reset_postdata(); ?>