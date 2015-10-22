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

class WPO_Posts extends WPO_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'wpo_posts',
            // Widget name will appear in UI
            __('WPO Posts Widget', TEXTDOMAIN),
            // Widget description
            array( 'description' => '' )
        );
        $this->widgetName = 'posts';
    }

    public function widget( $args, $instance ) {
        extract( $args );
        extract( $instance );
        $title = apply_filters( 'widget_title', $title );
        echo $before_widget;
            wp_reset_query();
            require($this->renderLayout($layout));
            wp_reset_postdata();
        echo $after_widget;
    }
    // Widget Backend
    public function form( $instance ) {
        $defaults = array(  'title' => 'List Posts',
                            'layout' => 'default' ,
                            'ids' => array(),
                            'class'=>''
                            );
        $instance = wp_parse_args((array) $instance, $defaults);
        $posts = get_posts( array('orderby'=>'title','posts_per_page'=>-1) );
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:', TEXTDOMAIN ); ?></label>
            <br>
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
            <label for="<?php echo $this->get_field_id( 'ids' ); ?>"><?php echo __( 'Posts:', TEXTDOMAIN ); ?></label>
            <br>
            <select multiple name="<?php echo $this->get_field_name( 'ids' ); ?>[]" id="<?php echo $this->get_field_id( 'ids' ); ?>" style="width:100%;height:250px;">
               <?php foreach( $posts as $value ){ ?>
                <?php
                    $selected = ( in_array($value->ID, $instance['ids'] ) )?' selected="selected"':'';
                ?>

                <option value="<?php echo $value->ID; ?>" <?php echo $selected; ?>>
                    <?php echo $value->post_title; ?>
                </option>
               <?php } ?>
            </select>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? $new_instance['layout'] : 'default';
        $instance['ids'] = $new_instance['ids'];
        return $instance;

    }
}
register_widget( 'WPO_Posts' );