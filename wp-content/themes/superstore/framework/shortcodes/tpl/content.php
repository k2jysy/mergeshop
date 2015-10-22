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

$add_class = (esc_attr($atts['class'])=='')?'':' '.esc_attr( $atts['class'] );

?>

<div class="content <?php echo $add_class; ?>">
    <?php echo stripslashes($atts['content']); ?>
</div>