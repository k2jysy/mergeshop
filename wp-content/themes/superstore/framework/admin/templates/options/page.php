<?php
 /**
  * $Desc
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
global $wp_registered_sidebars;

?>
<div id="wpo-config">
	<p class="wpo_section page-layout">
	    <?php $mb->the_field('page_layout'); ?>
	    <label>Page Layout:</label><br>
	    <br>
		<img  src="<?php echo FRAMEWORK_ADMIN_IMAGE_URI.'1col.png'; ?>" class="layout" data-value="0-1-0" alt="">
		<img src="<?php echo FRAMEWORK_ADMIN_IMAGE_URI.'2cl.png'; ?>" class="layout" data-value="1-1-0" alt="">
		<img src="<?php echo FRAMEWORK_ADMIN_IMAGE_URI.'2cr.png'; ?>" class="layout" data-value="0-1-1" alt="">
		<img src="<?php echo FRAMEWORK_ADMIN_IMAGE_URI.'3c.png'; ?>" class="layout" data-value="1-1-1" alt="">
		<img src="<?php echo FRAMEWORK_ADMIN_IMAGE_URI.'3c-l-l.png'; ?>" class="layout" data-value="1-1-m" alt="">
		<img src="<?php echo FRAMEWORK_ADMIN_IMAGE_URI.'3c-r-r.png'; ?>" class="layout" data-value="m-1-1" alt="">
	    <select  name="<?php $mb->the_name(); ?>" style="display:none;">
	    	<option value="0-1-0" <?php $mb->the_select_state('0-1-0'); ?>><?php echo __('Fullwidth',TEXTDOMAIN); ?></option>
	    	<option value="1-1-0" <?php $mb->the_select_state('1-1-0'); ?>><?php echo __('Left Sidebar',TEXTDOMAIN); ?></option>
	    	<option value="0-1-1" <?php $mb->the_select_state('0-1-1'); ?>><?php echo __('Right Sidebar',TEXTDOMAIN); ?></option>
	    	<option value="1-1-1" <?php $mb->the_select_state('1-1-1'); ?>><?php echo __('Left & Right Sidebar',TEXTDOMAIN); ?></option>
	    	<option value="1-1-m" <?php $mb->the_select_state('1-1-m'); ?>><?php echo __('Left & Left Sidebar',TEXTDOMAIN); ?></option>
	    	<option value="m-1-1" <?php $mb->the_select_state('m-1-1'); ?>><?php echo __('Right & Right Sidebar',TEXTDOMAIN); ?></option>
	    </select>
	</p>
	<div style="clear:both;"></div>
	<p class="wpo_section left-sidebar">
	    <?php $mb->the_field('left_sidebar'); ?>
	    <label>Left Sidebar:</label>
	    <select name="<?php $mb->the_name(); ?>">
	    	<option value=""><?php echo __('--Please Select Sidebar--',TEXTDOMAIN); ?></option>
	        <?php foreach ($wp_registered_sidebars as $key => $value) { ?>
			<option value="<?php echo $value['id']; ?>" <?php $mb->the_select_state($value['id']); ?>><?php echo $value['name']; ?></option>
	        <?php } ?>
	    </select>
	</p>
	<p class="wpo_section right-sidebar">
	    <?php $mb->the_field('right_sidebar'); ?>
	    <label>Right Sidebar:</label>
	    <select name="<?php $mb->the_name(); ?>">
	    	<option value=""><?php echo __('--Please Select Sidebar--',TEXTDOMAIN); ?></option>
	        <?php foreach ($wp_registered_sidebars as $key => $value) { ?>
			<option value="<?php echo $value['id']; ?>" <?php $mb->the_select_state($value['id']); ?>><?php echo $value['name']; ?></option>
	        <?php } ?>
	    </select>
	</p>
	<p class="wpo_section breadcrumb">
	    <?php $mb->the_field('breadcrumb'); ?>
	    <label>Show Breadcrumb:</label>
	    <select name="<?php $mb->the_name(); ?>">
	    	<option value="1" <?php $mb->the_select_state('1'); ?>>Yes</option>
	    	<option value="0" <?php $mb->the_select_state('0'); ?>>No</option>
	    </select>
	</p>
	<p class="wpo_section">
	    <?php $mb->the_field('showtitle'); ?>
	    <label>Show Title:</label>
	    <select name="<?php $mb->the_name(); ?>">
	    	<option value="1" <?php $mb->the_select_state('1'); ?>>Yes</option>
	    	<option value="0" <?php $mb->the_select_state('0'); ?>>No</option>
	    </select>
	</p>
</div>