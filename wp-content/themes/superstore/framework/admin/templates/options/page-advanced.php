<?php
/* $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Opal  Team <opalwordpressl@gmail.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

    $object = new WPO_Template();

?>
<div id="wpo-portfolio">
    <p class="wpo_section ">
        <?php $mb->the_field('count'); ?>
        <label for="pf_number">Pages show at most:</label>
        <input type="text" name="<?php $mb->the_name(); ?>" id="pf_number" value="<?php $mb->the_value(); ?>" />
    </p>

    <p class="wpo_section">
        <?php $mb->the_field('column'); ?>
        <label>Columns:</label>
        <select name="<?php $mb->the_name(); ?>">
            <option value="col-xs-12">1</option>
            <option value="col-sm-6" <?php $mb->the_select_state('col-sm-6'); ?>>2</option>
            <option value="col-sm-4" <?php $mb->the_select_state('col-sm-4'); ?>>3</option>
            <option value="col-sm-3" <?php $mb->the_select_state('col-sm-3'); ?>>4</option>
        </select>
    </p>

    <p class="wpo_section wpo_portfolio">
        <?php $mb->the_field('portfolio-layout'); ?>
        <label>Portfolio Layout:</label>
        <select name="<?php $mb->the_name(); ?>">
            <?php foreach ($object->getPortfolio() as $key => $value) { ?>
                <option value="<?php echo $key; ?>" <?php $mb->the_select_state( $key ); ?>>
                    <?php echo $value; ?>
                </option>
            <?php } ?>
        </select>
    </p>

    <p class="wpo_section wpo_masonry">
        <?php $mb->the_field('masonry-layout'); ?>
        <label>Masonry Layout:</label>
        <select name="<?php $mb->the_name(); ?>">
            <?php foreach ($object->getMasonry() as $key => $value) { ?>
                <option value="<?php echo $key; ?>" <?php $mb->the_select_state( $key ); ?>>
                    <?php echo $value; ?>
                </option>
            <?php } ?>
        </select>
    </p>

    <p class="wpo_section wpo_blog">
        <?php $mb->the_field('blog-layout'); ?>
        <label>Blog Layout:</label>
        <select name="<?php $mb->the_name(); ?>">
            <?php foreach ($object->getBlog() as $key => $value) { ?>
                <option value="<?php echo $key; ?>" <?php $mb->the_select_state( $key ); ?>>
                    <?php echo $value; ?>
                </option>
            <?php } ?>
        </select>
    </p>

</div>

