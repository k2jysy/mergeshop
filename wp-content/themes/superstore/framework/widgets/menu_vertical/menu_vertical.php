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

class WPO_Menu_Vertical extends WPO_Widget {
    public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'wpo_menu_vertical',
            // Widget name will appear in UI
            __('WPO Menu Vertical Widget', TEXTDOMAIN),
            // Widget description
            array( 'description' => __( 'Show Menu Vertical', TEXTDOMAIN ), )
        );
        $this->widgetName = 'menu_vertical';
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
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }else {
            $title ='';
        }
        // Widget admin form
        $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
        foreach ($menus as $menu) {
        	$option_menu[$menu->term_id]=$menu->name;
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', TEXTDOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'menu' ); ?>">Menu:</label>
            <br>
            <select name="<?php echo $this->get_field_name( 'menu' ); ?>" id="<?php echo $this->get_field_id( 'menu' ); ?>">
            	<option value="" <?php selected( $instance['menu'], $menu->term_id ); ?>>---Select Menu---</option>
                <?php foreach ($menus as $key => $menu): ?>
                    <option value="<?php echo $menu->term_id; ?>" <?php selected( $instance['menu'], $menu->term_id ); ?>><?php echo $menu->name; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'position' ); ?>">Position:</label>
            <br>
            <select name="<?php echo $this->get_field_name( 'position' ); ?>" id="<?php echo $this->get_field_id( 'position' ); ?>">
                <option value="left" <?php selected( $instance['position'], 'left' ); ?>>Left</option>
                <option value="right" <?php selected( $instance['position'], 'right' ); ?>>Right</option>
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

    public function update( $new_instance, $old_instance ) {
    	
    	$instance = $old_instance;

        $instance['title'] = $new_instance['title'];
        $instance['menu'] = $new_instance['menu'];
        $instance['position'] = $new_instance['position'];
        $instance['layout'] = $new_instance['layout'];

        return $instance;

    }
}

register_widget( 'WPO_Menu_Vertical' );