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

require_once(dirname(__FILE__) . '/lib/twitteroauth.php');
class WPO_Tweets_Widget extends WPO_Widget{

 	public function __construct() {
        $this->widgetName = 'twitter';
        $widget_ops = array( 'classname' => 'latest-twitter', 'description' => __('Latest Twitter widget ', TEXTDOMAIN) );

        $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'twitter-widget' );

        $this->WP_Widget( 'twitter-widget', __('WPO Twitter Widget', TEXTDOMAIN), $widget_ops, $control_ops );
    }

    public function getAgo($timestamp) {
        $difference = time() - $timestamp;

        if ($difference < 60) {
            return $difference." seconds ago";
        } else {
            $difference = round($difference / 60);
        }

        if ($difference < 60) {
            return $difference." minutes ago";
        } else {
            $difference = round($difference / 60);
        }

        if ($difference < 24) {
            return $difference." hours ago";
        }
        else {
            $difference = round($difference / 24);
        }

        if ($difference < 7) {
            return $difference." days ago";
        } else {
            $difference = round($difference / 7);
            return $difference." weeks ago";
        }
    }

    private function processString($s) {
        return preg_replace('/https?:\/\/[\w\-\.!~?&+\*\'"(),\/]+/','<a href="$0">$0</a>',$s);
    }

   public function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
      $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
      return $connection;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        //Our variables from the widget settings.
        $title = apply_filters('widget_title', $instance['title'] );
        $twitteruser = $instance['name'];
        $notweets = $instance['num'];
        $consumerkey = $instance['custommerkey'];
        $consumersecret = $instance['custommersecret'];
        $accesstoken = $instance['accesstoken'];
        $accesstokensecret = $instance['accesstokensecret'];
        $show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;
        $tpl = $instance['layout'];
        //Test


        echo $before_widget;

        
        //Display the name

        $connection = $this->getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
        $tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
        $status = json_encode($tweets);
        $fp = fopen('results.json', 'w');
        fwrite($fp, json_encode($tweets));
        fclose($fp);
        $responseJson = file_get_contents(get_home_url().'/results.json');
        if ($responseJson) {
		    $response = json_decode($responseJson);
		}
       	require($this->renderLayout($tpl));
		echo $after_widget;
    }

    //Update the widget

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        //Strip tags from title and name to remove HTML
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['name'] = strip_tags( $new_instance['name'] );
        $instance['num'] = strip_tags( $new_instance['num'] );
        $instance['custommerkey'] = strip_tags( $new_instance['custommerkey'] );
        $instance['custommersecret'] = strip_tags( $new_instance['custommersecret'] );
        $instance['accesstoken'] = strip_tags( $new_instance['accesstoken'] );
        $instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );
        $instance['show_info'] = $new_instance['show_info'];
        $instance['layout'] = $new_instance['layout'];
        return $instance;
    }


    public function form( $instance ) {
        //Set up some default widget settings.
        $defaults = array( 'title' => __('Latest tweets.', TEXTDOMAIN), 'name' => __('leotheme', TEXTDOMAIN),'custommerkey' => __('Sv4hY9ewxyyt6ffBfMiMTg', TEXTDOMAIN),'custommersecret' => __('w2AULbFWTu2kG3DnHRszG7HO4adpGCicE022MhuLYU', TEXTDOMAIN),'accesstoken' => __('1361529829-lItJW8tBYhuy1zRRYpco97TdcChNcAh6wRTZxf4', TEXTDOMAIN),'accesstokensecret' => __('2MRkUJLBAAp3g3PYRTcjDB4N4QaGCVf5aEuSyFaO9I', TEXTDOMAIN),'num' => __(1, TEXTDOMAIN));
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', TEXTDOMAIN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
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
            <label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('Tiwtter Username:', TEXTDOMAIN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'custommerkey' ); ?>"><?php _e('Custommer key:', TEXTDOMAIN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'custommerkey' ); ?>" name="<?php echo $this->get_field_name( 'custommerkey' ); ?>" value="<?php echo $instance['custommerkey']; ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'custommersecret' ); ?>"><?php _e('Custommer Secret:', TEXTDOMAIN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'custommersecret' ); ?>" name="<?php echo $this->get_field_name( 'custommersecret' ); ?>" value="<?php echo $instance['custommersecret']; ?>" style="width:100%;" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'accesstoken' ); ?>"><?php _e('Accesstoken:', TEXTDOMAIN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'accesstoken' ); ?>" name="<?php echo $this->get_field_name( 'accesstoken' ); ?>" value="<?php echo $instance['accesstoken']; ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'accesstokensecret' ); ?>"><?php _e('Accesstoken Secret:', TEXTDOMAIN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'accesstokensecret' ); ?>" name="<?php echo $this->get_field_name( 'accesstokensecret' ); ?>" value="<?php echo $instance['accesstokensecret']; ?>" style="width:100%;" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'num' ); ?>"><?php _e('Number Status:', TEXTDOMAIN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'num' ); ?>" name="<?php echo $this->get_field_name( 'num' ); ?>" value="<?php echo $instance['num']; ?>" style="width:100%;" />
        </p>
        <!--<p>
            <input class="checkbox" type="checkbox" <?php checked( $instance['show_info'], true ); ?> id="<?php echo $this->get_field_id( 'show_info' ); ?>" name="<?php echo $this->get_field_name( 'show_info' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_info' ); ?>"><?php _e('Display info publicly?', TEXTDOMAIN); ?></label>
        </p>-->

    <?php
    }
}

register_widget( 'WPO_Tweets_Widget' );

?>