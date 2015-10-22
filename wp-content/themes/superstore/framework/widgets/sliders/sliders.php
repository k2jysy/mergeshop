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


class WPO_Sliders_Widget extends WPO_Widget{

 	public function __construct() {
        $this->widgetName = 'sliders';
        $widget_ops = array( 'classname' => 'sliders', 'description' => __('Description ', TEXTDOMAIN) );
        $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sliders-widget' );
        $this->WP_Widget( 'sliders-widget', __('WPO Sliders Widget', TEXTDOMAIN), $widget_ops, $control_ops );
        add_action('wp_enqueue_scripts', array( $this, 'initScripts' ));
    }

    public function widget( $args, $instance ) {
        extract( $args );
        $tpl = $instance['layout'];
        echo $before_widget;
        //Display the name
       	require($this->renderLayout($tpl));
		echo $after_widget;
    }

    //Update the widget
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['layout'] = $new_instance['layout'];
        return $instance;
    }


    public function form( $instance ) {
        //Set up some default widget settings.
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'posttype' ); ?>">Type:</label>
            <br>
            <select name="<?php echo $this->get_field_name( 'posttype' ); ?>" id="<?php echo $this->get_field_id( 'posttype' ); ?>">
                <?php foreach (get_post_types(array('public'=>true)) as $key => $value): ?>
                    <?php if( $key=='portfolio' || $key=='post' || $key=='sliders' ): ?>
                        <option value="<?php echo $key; ?>" <?php selected( $instance['posttype'], $key ); ?>><?php echo $value; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
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
        <?php
    }

    public function initScripts(){
        $url = get_template_directory_uri().'/framework/widgets/sliders/assets/';
        wp_enqueue_script('camera_js',$url.'js/camera.min.js');
        wp_enqueue_script('kwicks_js',$url.'js/jquery.kwicks.min.js');

        wp_enqueue_style( 'camera_css', $url.'css/camera.css');
        wp_enqueue_style( 'kwicks_css', $url.'css/accordion.kwicks.min.css');
    }
}

register_widget( 'WPO_Sliders_Widget' );

?>