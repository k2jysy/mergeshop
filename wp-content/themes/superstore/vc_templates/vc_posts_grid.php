<?php
$grid_link = $grid_layout_mode = $title = $filter= '';
$posts = array();
extract(shortcode_atts(array(
    'title' => '',
    'grid_columns_count' => 1,
    'grid_teasers_count' => 1,
    'grid_layout' => 'title,thumbnail,text', // title_thumbnail_text, thumbnail_title_text, thumbnail_text, thumbnail_title, thumbnail, title_text
    'grid_link_target' => '_self',
    'filter' => '', //grid,
    'grid_thumb_size' => 'thumbnail',
    'grid_layout_mode' => 'fitRows',
    'el_class' => '',
    'teaser_width' => '12',
    'orderby' => NULL,
    'order' => 'DESC',
    'loop' => '',
), $atts));
if(empty($loop)) return;
$this->getLoop($loop);
$my_query = $this->query;
$args = $this->loop_args;
$teaser_blocks = vc_sorted_list_parse_value($grid_layout);

$columgrid = 12/$grid_columns_count;

?>

<section class="box recent-blog<?php echo (($el_class!='')?' '.$el_class:''); ?>">
    <div class="box-heading">
        <?php echo $title; ?>
    </div>
    <div class="box-content row">
            <?php while ( $my_query->have_posts() ): $my_query->the_post(); ?>
            <div class="col-sm-<?php echo $columgrid; ?> col-md-<?php echo $columgrid; ?>">

                        <div>
                            <a href="<?php the_permalink(); ?>" title="">
                                <?php the_post_thumbnail('blog-thumbnails');?>
                            </a>
                        </div>
                        <div class="description">
                            <div class="blog-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </div>
                            <div class="blog-description"><?php echo wpo_excerpt(15,'...');; ?></div>


                        </div>

            </div>
            <?php endwhile; ?>
    </div>
</section>
<?php
wp_reset_query();