<div id="wpo-seo">
    <p class="wpo_section ">
        <?php $mb->the_field('title'); ?>
        <label for="seo_title">SEO Title:</label>
        <input type="text" name="<?php $mb->the_name(); ?>" id="seo_title" value="<?php $mb->the_value(); ?>" />
    </p>
    <p class="wpo_section ">
        <?php $mb->the_field('keywords'); ?>
        <label for="seo_keywords">SEO Keywords:</label>
        <input type="text" name="<?php $mb->the_name(); ?>" id="seo_keywords" value="<?php $mb->the_value(); ?>" />
    </p>
    <p class="wpo_section ">
        <?php $mb->the_field('description'); ?>
        <label for="seo_description">SEO Description:</label>
        <textarea name="<?php $mb->the_name(); ?>" id="seo_description" cols="30" rows="5"><?php $mb->the_value(); ?></textarea>
    </p>
</div>

