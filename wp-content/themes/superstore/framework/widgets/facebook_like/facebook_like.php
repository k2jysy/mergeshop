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

class WPO_Facebook_Like_Box extends WPO_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'wpo_facebook_like',
            // Widget name will appear in UI
            __('WPO Facebook Like Box', TEXTDOMAIN),
            // Widget description
            array( 'description' => __( 'Adds support for Facebook Like Box. ', TEXTDOMAIN ), )
        );
        $this->widgetName = 'facebook_like';
    }

    public function widget( $args, $instance ) {
        extract( $args );
        extract( $instance );

        $title = apply_filters('widget_title', $instance['title']);
        $page_url = $instance['page_url'];
        $width = $instance['width'];
        $color_scheme = $instance['color_scheme'];
        $show_faces = isset($instance['show_faces']) ? 'true' : 'false';
        $show_stream = isset($instance['show_stream']) ? 'true' : 'false';
        $show_header = isset($instance['show_header']) ? 'true' : 'false';
        $height = '65';

        if($show_faces == 'true') {
            $height = '240';
        }

        if($show_stream == 'true') {
            $height = '515';
        }

        if($show_stream == 'true' && $show_faces == 'true' && $show_header == 'true') {
            $height = '540';
        }

        if($show_stream == 'true' && $show_faces == 'true' && $show_header == 'false') {
            $height = '540';
        }

        if($show_header == 'true') {
            $height = $height + 30;
        }

        echo $before_widget;
            require($this->renderLayout($layout));
        echo $after_widget;
    }
// Widget Backend
    public function form( $instance ) {
        $defaults = array('title' => 'Find us on Facebook', 'layout' => 'default' , 'page_url' => '', 'width' => '268', 'color_scheme' => 'light', 'show_faces' => 'on', 'show_stream' => false, 'show_header' => false);
        $instance = wp_parse_args((array) $instance, $defaults); 

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'layout' ); ?>">Template Style:</label>
            <br>
            <select name="<?php echo $this->get_field_name( 'layout' ); ?>" id="<?php echo $this->get_field_id( 'layout' ); ?>">
                <?php foreach ($this->selectLayout() as $key => $value): ?>
                    <option value="<?php echo $value; ?>" <?php selected( $instance['layout'], $value ); ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('page_url'); ?>">Facebook Page URL:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('page_url'); ?>" name="<?php echo $this->get_field_name('page_url'); ?>" value="<?php echo $instance['page_url']; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('width'); ?>">Width:</label>
            <input class="widefat" type="text" style="width: 50px;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('color_scheme'); ?>">Color Scheme:</label>
            <select id="<?php echo $this->get_field_id('color_scheme'); ?>" name="<?php echo $this->get_field_name('color_scheme'); ?>" class="widefat" style="width:100%;">
                <option <?php if ('light' == $instance['color_scheme']) echo 'selected="selected"'; ?>>light</option>
                <option <?php if ('dark' == $instance['color_scheme']) echo 'selected="selected"'; ?>>dark</option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_faces'], 'on'); ?> id="<?php echo $this->get_field_id('show_faces'); ?>" name="<?php echo $this->get_field_name('show_faces'); ?>" />
            <label for="<?php echo $this->get_field_id('show_faces'); ?>">Show faces</label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_stream'], 'on'); ?> id="<?php echo $this->get_field_id('show_stream'); ?>" name="<?php echo $this->get_field_name('show_stream'); ?>" />
            <label for="<?php echo $this->get_field_id('show_stream'); ?>">Show stream</label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($instance['show_header'], 'on'); ?> id="<?php echo $this->get_field_id('show_header'); ?>" name="<?php echo $this->get_field_name('show_header'); ?>" />
            <label for="<?php echo $this->get_field_id('show_header'); ?>">Show facebook header</label>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['page_url'] = $new_instance['page_url'];
        $instance['width'] = $new_instance['width'];
        $instance['color_scheme'] = $new_instance['color_scheme'];
        $instance['show_faces'] = $new_instance['show_faces'];
        $instance['show_stream'] = $new_instance['show_stream'];
        $instance['show_header'] = $new_instance['show_header'];
        $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? $new_instance['layout'] : 'default';
        return $instance;

    }
}

register_widget( 'WPO_Facebook_Like_Box' );