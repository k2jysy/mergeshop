<?php
/**
 * $Desc
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
?>
<form role="search" method="get" class="searchform" action="<?php echo home_url( '/' ); ?>">
    <div class="wpo-search input-group">
        <input name="s" id="s" maxlength="40" class="form-control input-large input-search" type="text" size="20" placeholder="查找...">
        <span class="input-group-addon input-large btn-search">
            <input type="submit" id="searchsubmit" class="fa" value="&#xf002;" />
            <?php if(class_exists('WooCommerce')){ ?>
            <input type="hidden" name="post_type" value="product" />
            <?php } ?>
        </span>
	</div>
</form>  


