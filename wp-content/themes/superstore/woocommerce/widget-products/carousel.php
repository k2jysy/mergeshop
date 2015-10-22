<?php 
    $_id = wpo_makeid();
    $_count = 1;
    $_total = $loop->found_posts;
    $_row = 1;
    if(isset($two_rows) && $two_rows) $_row = 2;

    $colspan = floor( 12 / $columns_count );
?>
<div class="box-content">
    <div class="box-products slide" id="productcarouse-<?php echo $_id; ?>">
        <?php if($posts_per_page>$columns_count && $_total>$columns_count){ ?>
        <div class="carousel-controls">
            <a href="#productcarouse-<?php echo $_id; ?>" data-slide="prev">
                <span class="conner"><i class="fa fa-angle-left"></i></span>
            </a>
            <a href="#productcarouse-<?php echo $_id; ?>" data-slide="next">
                <span class="conner"><i class="fa fa-angle-right"></i></span>
            </a>
        </div>
        <?php } ?>
        <div class="carousel-inner">
        <?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>

            <?php if( $_count%($columns_count*$_row) == 1 ) echo '<div class="item'.(($_count==1)?" active":"").'"><div class="row">'; ?>
                <!-- Product Item -->
                <div class="<?php echo $class_column ?> col-xs-6 product-cols">
                    <?php wc_get_template_part( 'content', 'product-inner' ); ?>
                </div>
                <!-- End Product Item -->
            <?php if( ($_count%($columns_count*$_row)==0 && $_count!=1) || $_count== $posts_per_page || $_count==$_total ) echo '</div></div>'; ?>
            <?php $_count++; ?>

        <?php endwhile; ?>
        </div>
    </div>
</div>

<?php wp_reset_postdata(); ?>