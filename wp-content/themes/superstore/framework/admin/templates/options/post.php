<div id="wpo-post">
    <p class="wpo_section ">
        <?php $mb->the_field('embed'); ?>
        <label for="embed_post">Embed Post format:</label>
        <input type="text" name="<?php $mb->the_name(); ?>" id="embed_post" value="<?php $mb->the_value(); ?>" />
    </p>
    <div class="wpo_embed_view">
        <span class="spinner" style="float:none;"></span>
        <div class="result"></div>
    </div>
</div>

<script>
	WPO_Admin.params_Embed('#embed_post','#wpo-post');
</script>