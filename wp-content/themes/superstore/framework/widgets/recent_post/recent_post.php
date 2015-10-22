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

class WPO_Recent_Post extends WPO_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'wpo_recent_post',
            // Widget name will appear in UI
            __('WPO Recent Posts Widget', TEXTDOMAIN),
            // Widget description
            array( 'description' => __( 'Show list of recent post', TEXTDOMAIN ), )
        );
        $this->widgetName = 'recent_post';
    }

    public function widget( $args, $instance ) {
        extract( $args );
        extract( $instance );
        $title = apply_filters( 'widget_title', $title );
        echo $before_widget;
            require($this->renderLayout($layout));
        echo $after_widget;
    }
// Widget Backend
    public function form( $instance ) {
        $defaults = array(  'title' => 'Latest Post',
                            'layout' => 'default' ,
                            'number_post' => '4',
                            'post_type' =>  'post');
        $instance = wp_parse_args((array) $instance, $defaults);
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', TEXTDOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
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
            <label for="<?php echo $this->get_field_id('post_type'); ?>">
                <?php echo __('Type:', TEXTDOMAIN ); ?>
            </label>
            <br>
            <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
                <?php foreach (get_post_types(array('public' => true)) as $key => $value) { ?>
                    <?php if($key!='attachment' && $key!='product'){ ?>
                    <option value="<?php echo $key; ?>" <?php selected($instance['post_type'],$key); ?> ><?php echo $value; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number_post' ); ?>"><?php _e( 'Num Posts:', TEXTDOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'number_post' ); ?>" name="<?php echo $this->get_field_name( 'number_post' ); ?>" type="text" value="<?php echo  $instance['number_post']; ?>" />
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['post_type'] = $new_instance['post_type'];
        $instance['number_post'] = ( ! empty( $new_instance['number_post'] ) ) ? strip_tags( $new_instance['number_post'] ) : '';
        $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? $new_instance['layout'] : 'default';
        return $instance;

    }
}

register_widget( 'WPO_Recent_Post' );