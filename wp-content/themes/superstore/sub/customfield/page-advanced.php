<?php
/* $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     WPOpal  Team <wpopal@gmail.com, support@wpopal.com>
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
</div>