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

class WPO_Flickr_Widget extends WPO_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'wpo_flickr_widget',
            // Widget name will appear in UI
            __('WPO Flickr', TEXTDOMAIN),
            // Widget description
            array( 'description' => __( 'The most recent photos from flickr.', TEXTDOMAIN ), )
        );
        $this->widgetName = 'flickr';
    }

    public function widget( $args, $instance ) {
        extract( $args );
        extract( $instance );

        $title = apply_filters('widget_title', $instance['title']);
        $screen_name = $instance['screen_name'];
        $number = $instance['number'];
        $api = $instance['api'];
        if(empty($api)) {
            $api = 'c9d2c2fda03a2ff487cb4769dc0781ea';
        }

        echo $before_widget;
            if($screen_name && $number && $api) {
                require($this->renderLayout($layout));
            }

        echo $after_widget;
    }
// Widget Backend
    public function form( $instance ) {
        $defaults = array('title' => 'Photos from Flickr', 'layout' => 'default' , 'screen_name' => '', 'number' => 6, 'api' => 'c9d2c2fda03a2ff487cb4769dc0781ea');
        $instance = wp_parse_args((array) $instance, $defaults); ?>

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
            <label for="<?php echo $this->get_field_id('screen_name'); ?>">Flickr ID(<a onclick="window.open('http://idgettr.com/');return false;" href="#">Get your flickr ID</a>):</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('screen_name'); ?>" name="<?php echo $this->get_field_name('screen_name'); ?>" value="<?php echo $instance['screen_name']; ?>" />
        </p>


        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">Number of photos to show:</label>
            <input class="widefat" type="text" style="width: 30px;" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $instance['number']; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('api'); ?>">API key (Use default or get your own from <a onclick="window.open('http://idgettr.com/');return false;" href="#">Flickr APP Garden</a>):</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('api'); ?>" name="<?php echo $this->get_field_name('api'); ?>" value="<?php echo $instance['api']; ?>" />
            <small>Default key is: c9d2c2fda03a2ff487cb4769dc0781ea</small>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['screen_name'] = $new_instance['screen_name'];
        $instance['number'] = $new_instance['number'];
        $instance['api'] = $new_instance['api'];
        $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? $new_instance['layout'] : 'default';
        return $instance;

    }
}

register_widget( 'WPO_Flickr_Widget' );