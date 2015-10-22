<?php
$output = $title =  $onclick = $custom_links = $img_size = $custom_links_target = $images = $el_class = $partial_view = '';
$mode = $slides_per_view = $wrap = $autoplay = $hide_pagination_control = $hide_prev_next_buttons = $speed ='';
extract(shortcode_atts(array(
    'title' => '',
    'onclick' => 'link_image',
    'custom_links' => '',
    'custom_links_target' => '',
    'img_size' => 'thumbnail',
    'images' => '',
    'el_class' => '',
    'mode' => 'horizontal',
    'slides_per_view' => '1',
    'wrap' => '',
    'autoplay' => '',
    'hide_pagination_control' => false,
    'hide_prev_next_buttons' => false,
    'speed' => '5000',
    'partial_view' => ''
), $atts));
$el_class = $this->getExtraClass($el_class);
$_id = wpo_makeid();
$images = explode(",", $images);
?>
<div id="wpo-carousel-<?php echo $_id; ?>" class="carousel slide <?php echo $el_class; ?>" data-ride="carousel">

    <div class="carousel-inner">
        <?php foreach ($images as $key => $value): ?>
            <?php $img = wp_get_attachment_image_src($value,'full'); ?>
        <div class="item <?php echo ($key==0)?"active":""; ?>">
            <img src="<?php echo $img[0]; ?>">
        </div>
        <?php endforeach ?>
    </div>
    <?php if(!$hide_pagination_control){ ?>
    <ol class="carousel-indicators">
        <?php for($i=0;$i<count($images);$i++){ ?>
        <li data-target="#wpo-carousel-<?php echo $_id; ?>" data-slide-to="<?php echo $i; ?>" class="<?php echo ($i==0)?"active":""; ?>"></li>
        <?php } ?>
    </ol>
    <?php } ?>
    
    <?php if(!$hide_prev_next_buttons){ ?>
    <a class="left carousel-control" href="#wpo-carousel-<?php echo $_id; ?>" data-slide="prev">
        <span class="fa fa-angle-left"></span>
    </a>
    <a class="right carousel-control" href="#wpo-carousel-<?php echo $_id; ?>" data-slide="next">
        <span class="fa fa-angle-right"></span>
    </a>
    <?php } ?>
</div>
