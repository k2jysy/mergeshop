<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */

if ( ! defined( 'YITH_WCMG' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMG_Admin' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCMG_Admin {
        /**
         * Plugin options
         *
         * @var array
         * @access public
         * @since 1.0.0
         */
        public $options = array();

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /**
         * Various links
         *
         * @var string
         * @access public
         * @since 1.0.0
         */
        public $banner_url = 'http://cdn.yithemes.com/plugins/yith_magnifier.php?url';
        public $banner_img = 'http://cdn.yithemes.com/plugins/yith_magnifier.php';
        public $doc_url = 'http://yithemes.com/docs-plugins/yith_magnifier/';

        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        public function __construct( $version ) {
            $this->options = $this->_initOptions();
            $this->version = $version;

            //Actions
            add_action( 'init', array( $this, 'init' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
            add_filter( 'plugin_action_links_' . plugin_basename( dirname( __FILE__ ) . '/init.php' ), array( $this, 'action_links' ) );

            add_action( 'woocommerce_settings_tabs_yith_wcmg', array( $this, 'print_plugin_options' ) );
            add_action( 'woocommerce_update_options_yith_wcmg', array( $this, 'update_options' ) );
            if ( ! has_action( 'woocommerce_admin_field_slider' ) ) {
                add_action( 'woocommerce_admin_field_slider', array( $this, 'admin_fields_slider' ) );
            }
            if ( ! has_action( 'woocommerce_admin_field_picker' ) ) {
                add_action( 'woocommerce_admin_field_picker', array( $this, 'admin_fields_picker' ) );
            }
            if ( !has_action('woocommerce_admin_field_yit_wc_image_width')) {
                add_action( 'woocommerce_admin_field_yit_wc_image_width', array( $this, 'admin_fields_yit_wc_image_width' ) );
            }
            add_action( 'woocommerce_admin_field_banner', array( $this, 'admin_fields_banner' ) );
            add_action( 'admin_print_footer_scripts', array( $this, 'admin_fields_image_deps' ) );

            add_action( 'woocommerce_update_option_slider', array( $this, 'admin_update_option' ) );
            add_action( 'woocommerce_update_option_picker', array( $this, 'admin_update_option' ) );

            //Filters
            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_tab_woocommerce' ), 30 );
            if ( ! function_exists( 'WC' ) ) {
                add_filter( 'woocommerce_catalog_settings', array( $this, 'add_catalog_image_size' ) );
            }

            //Apply filters
            $this->banner_url = apply_filters( 'yith_wcmg_banner_url', $this->banner_url );

            // YITH WCMG Loaded
            do_action( 'yith_wcmg_loaded' );
        }


        /**
         * Init method:
         *  - default options
         *
         * @access public
         * @since 1.0.0
         */
        public function init() {
            $this->_default_options();
        }


        /**
         * Update plugin options.
         *
         * @return void
         * @since 1.0.0
         */
        public function update_options() {
            foreach ( $this->options as $option ) {
                woocommerce_update_options( $option );
            }
        }


        /**
         * Add Magnifier's tab to Woocommerce -> Settings page
         *
         * @access public
         *
         * @param array $tabs
         *
         * @return array
         */
        public function add_tab_woocommerce( $tabs ) {
            $tabs['yith_wcmg'] = __( 'Magnifier', 'yit' );

            return $tabs;
        }


        /**
         * Add Zoom Image size to Woocommerce -> Catalog
         *
         * @access public
         *
         * @param array $settings
         *
         * @return array
         */
        public function add_catalog_image_size( $settings ) {
            $tmp = $settings[count( $settings ) - 1];
            unset( $settings[count( $settings ) - 1] );

            $settings[] = array(
                'name'     => __( 'Catalog Zoom Images', 'yit' ),
                'desc'     => __( 'The size of images used within the magnifier box', 'yit' ),
                'id'       => 'woocommerce_magnifier_image',
                'css'      => '',
                'type'     => 'image_width',
                'default'  => array(
                    'width'  => 600,
                    'height' => 600,
                    'crop'   => true
                ),
                'std'      => array(
                    'width'  => 600,
                    'height' => 600,
                    'crop'   => true
                ),
                'desc_tip' => true
            );
            $settings[] = $tmp;
            return $settings;
        }


        /**
         * Print all plugin options.
         *
         * @return void
         * @since 1.0.0
         */
        public function print_plugin_options() {
            $links = apply_filters( 'yith_wcmg_tab_links', array(
                '<a href="#yith_wcmg_general">' . __( 'General Settings', 'yit' ) . '</a>',
                '<a href="#yith_wcmg_magnifier">' . __( 'Magnifier', 'yit' ) . '</a>',
                '<a href="#yith_wcmg_slider">' . __( 'Slider', 'yit' ) . '</a>'
            ) );

            $this->_printBanner();
            ?>

            <div class="subsubsub_section">
                <ul class="subsubsub">
                    <li>
                        <?php echo implode( ' | </li><li>', $links ) ?>
                    </li>
                </ul>
                <br class="clear" />

                <?php
                $option_theme = apply_filters( 'yith_wcmg_options_theme_plugin', $this->options );
                foreach ( $option_theme as $id => $tab ) : ?>
                    <!-- tab #<?php echo $id ?> -->
                    <div class="section" id="yith_wcmg_<?php echo $id ?>">
                        <?php woocommerce_admin_fields( $option_theme[$id] ) ?>
                    </div>
                <?php endforeach ?>
            </div>
        <?php
        }


        /**
         * Initialize the options
         *
         * @access protected
         * @return array
         * @since 1.0.0
         */
        protected function _initOptions() {
            global $woocommerce;
            $image_size = array();

            if ( version_compare( preg_replace( '/-beta-([0-9]+)/', '', $woocommerce->version ), '2.2', '<' ) ) {
                $image_width_type = 'image_width';
            }
            else {
                $image_width_type = 'yit_wc_image_width';
            }


            if ( function_exists( 'WC' ) ) {
                $image_size = array(
                    'name'     => __( 'Catalog Zoom Images', 'yit' ),
                    'desc'     => __( 'The size of images used within the magnifier box', 'yit' ),
                    'id'       => 'woocommerce_magnifier_image',
                    'css'      => '',
                    'type'     => $image_width_type,
                    'default'  => array(
                        'width'  => 600,
                        'height' => 600,
                        'crop'   => true
                    ),
                    'std'      => array(
                        'width'  => 600,
                        'height' => 600,
                        'crop'   => true
                    ),
                    'desc_tip' => true
                );
            }

            $options = array(
                'general'   => array(
                    array(
                        'name' => __( 'General Settings', 'yit' ),
                        'type' => 'title',
                        'desc' => '',
                        'id'   => 'yith_wcmg_general'
                    ),

                    array(
                        'name'    => __( 'Enable YITH Magnifier', 'yit' ),
                        'desc'    => __( 'Enable the plugin or use the Woocommerce default product image.', 'yit' ),
                        'id'      => 'yith_wcmg_enable_plugin',
                        'std'     => 'yes',
                        'default' => 'yes',
                        'type'    => 'checkbox'
                    ),

                    array(
                        'name'    => __( 'Forcing Zoom Image sizes', 'yit' ),
                        'desc'    => __( 'If disabled, you will able to customize the sizes of Zoom Images. Please disable at your own risk; the magnifier should not properly work with unproportioned image sizes.', 'yit' ),
                        'id'      => 'yith_wcmg_force_sizes',
                        'std'     => 'yes',
                        'default' => 'yes',
                        'type'    => 'checkbox'
                    ),

                    array( 'type' => 'sectionend', 'id' => 'yith_wcmg_general_end' )
                ),
                'magnifier' => array(
                    array(
                        'name' => __( 'Magnifier Settings', 'yit' ),
                        'type' => 'title',
                        'desc' => '',
                        'id'   => 'yith_wcmg_magnifier'
                    ),

                    array(
                        'name'    => __( 'Zoom Area Width', 'yit' ),
                        'desc'    => __( 'The width of magnifier box (default: auto)', 'yit' ),
                        'id'      => 'yith_wcmg_zoom_width',
                        'std'     => 'auto',
                        'default' => 'auto',
                        'type'    => 'text',
                    ),

                    array(
                        'name'    => __( 'Zoom Area Height', 'yit' ),
                        'desc'    => __( 'The height of magnifier box (default: auto)', 'yit' ),
                        'id'      => 'yith_wcmg_zoom_height',
                        'std'     => 'auto',
                        'default' => 'auto',
                        'type'    => 'text',
                    ),

                    $image_size,

                    array(
                        'name'    => __( 'Zoom Area Position', 'yit' ),
                        'desc'    => __( 'The magnifier position', 'yit' ),
                        'id'      => 'yith_wcmg_zoom_position',
                        'std'     => 'right',
                        'default' => 'right',
                        'type'    => 'select',
                        'options' => array(
                            'right'  => __( 'Right', 'yit' ),
                            'inside' => __( 'Inside', 'yit' )
                        )
                    ),

                    array(
                        'name'    => __( 'Zoom Area Mobile Position', 'yit' ),
                        'desc'    => __( 'The magnifier position with mobile devices (iPhone, Android, etc.)', 'yit' ),
                        'id'      => 'yith_wcmg_zoom_mobile_position',
                        'std'     => 'default',
                        'default' => 'inside',
                        'type'    => 'select',
                        'options' => array(
                            'default' => __( 'Default', 'yit' ),
                            'inside'  => __( 'Inside', 'yit' ),
                            'disable' => __( 'Disable', 'yit' )
                        )
                    ),

                    array(
                        'name'    => __( 'Loading label', 'yit' ),
                        'desc'    => '',
                        'id'      => 'yith_wcmg_loading_label',
                        'std'     => __( 'Loading...', 'yit' ),
                        'default' => __( 'Loading...', 'yit' ),
                        'type'    => 'text',
                    ),

                    array(
                        'name'    => __( 'Lens Opacity', 'yit' ),
                        'desc'    => '',
                        'id'      => 'yith_wcmg_lens_opacity',
                        'std'     => 0.5,
                        'default' => 0.5,
                        'type'    => 'slider',
                        'min'     => 0,
                        'max'     => 1,
                        'step'    => .1
                    ),

                    array(
                        'name'    => __( 'Blur', 'yit' ),
                        'desc'    => __( 'Add a blur effect to the small image on mouse hover.', 'yit' ),
                        'id'      => 'yith_wcmg_softfocus',
                        'std'     => 'no',
                        'default' => 'no',
                        'type'    => 'checkbox'
                    ),

                    array( 'type' => 'sectionend', 'id' => 'yith_wcmg_magnifier_end' )
                ),
                'slider'    => array(
                    array(
                        'name' => __( 'Slider Settings', 'yit' ),
                        'type' => 'title',
                        'desc' => '',
                        'id'   => 'yith_wcmg_slider'
                    ),

                    array(
                        'name'    => __( 'Enable Slider', 'yit' ),
                        'desc'    => __( 'Enable Thumbnail slider.', 'yit' ),
                        'id'      => 'yith_wcmg_enableslider',
                        'std'     => 'yes',
                        'default' => 'yes',
                        'type'    => 'checkbox'
                    ),

                    array(
                        'name'    => __( 'Enable Slider Responsive', 'yit' ),
                        'desc'    => __( 'The option fits the thumbnails within the available space. Disable it if you want to manage by yourself the thumbnails (eg. add margins, paddings, etc.)', 'yit' ),
                        'id'      => 'yith_wcmg_slider_responsive',
                        'std'     => 'yes',
                        'default' => 'yes',
                        'type'    => 'checkbox'
                    ),

                    array(
                        'name'    => __( 'Items', 'yit' ),
                        'desc'    => __( 'Number of items to show', 'yit' ),
                        'id'      => 'yith_wcmg_slider_items',
                        'std'     => 3,
                        'default' => 3,
                        'type'    => 'slider',
                        'min'     => 1,
                        'max'     => 10,
                        'step'    => 1
                    ),

                    array(
                        'name'    => __( 'Circular carousel', 'yit' ),
                        'desc'    => __( 'Determines whether the carousel should be circular.', 'yit' ),
                        'id'      => 'yith_wcmg_slider_circular',
                        'std'     => 'yes',
                        'default' => 'yes',
                        'type'    => 'checkbox'
                    ),

                    array(
                        'name'    => __( 'Infinite carousel', 'yit' ),
                        'desc'    => __( 'Determines whether the carousel should be infinite. Note: It is possible to create a non-circular, infinite carousel, but it is not possible to create a circular, non-infinite carousel.', 'yit' ),
                        'id'      => 'yith_wcmg_slider_infinite',
                        'std'     => 'yes',
                        'default' => 'yes',
                        'type'    => 'checkbox'
                    ),

                    array( 'type' => 'sectionend', 'id' => 'yith_wcmg_slider_end' )
                )
            );

            return apply_filters( 'yith_wcmg_tab_options', $options );
        }


        /**
         * Default options
         *
         * Sets up the default options used on the settings page
         *
         * @access protected
         * @return void
         * @since 1.0.0
         */
        protected function _default_options() {
            foreach ( $this->options as $section ) {
                foreach ( $section as $value ) {
                    if ( isset( $value['std'] ) && isset( $value['id'] ) ) {
                        if ( $value['type'] == 'image_width' ) {
                            add_option( $value['id'] . '_width', $value['std'] );
                            add_option( $value['id'] . '_height', $value['std'] );
                        }
                        else {
                            add_option( $value['id'], $value['std'] );
                        }
                    }
                }
            }
        }


        /**
         * Create new Woocommerce admin field: slider
         *
         * @access public
         *
         * @param array $value
         *
         * @return void
         * @since 1.0.0
         */
        public function admin_fields_slider( $value ) {
            $slider_value = ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) ?
                esc_attr( stripslashes( get_option( $value['id'] ) ) ) :
                esc_attr( $value['std'] );

            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo $value['name']; ?></label>
                </th>
                <td class="forminp">
                    <div id="<?php echo esc_attr( $value['id'] ); ?>_slider" class="yith_woocommerce_slider" style="width: 300px; float: left;"></div>
                    <div id="<?php echo esc_attr( $value['id'] ); ?>_value" class="yith_woocommerce_slider_value ui-state-default ui-corner-all"><?php echo $slider_value ?></div>
                    <input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="hidden" value="<?php echo $slider_value ?>" /> <?php echo $value['desc']; ?>
                </td>
            </tr>



            <script>
                jQuery(document).ready(function ($) {
                    $('#<?php echo esc_attr( $value['id'] ); ?>_slider').slider({
                        min  : <?php echo $value['min'] ?>,
                        max  : <?php echo $value['max'] ?>,
                        step : <?php echo $value['step'] ?>,
                        value: <?php echo $slider_value ?>,
                        slide: function (event, ui) {
                            $("#<?php echo esc_attr( $value['id'] ); ?>").val(ui.value);
                            $("#<?php echo esc_attr( $value['id'] ); ?>_value").text(ui.value);
                        }
                    });
                });
            </script>

        <?php
        }


        /**
         * Create new Woocommerce admin field: picker
         *
         * @access public
         *
         * @param array $value
         *
         * @return void
         * @since 1.0.0
         */
        public function admin_fields_picker( $value ) {
            $picker_value = ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) ?
                esc_attr( stripslashes( get_option( $value['id'] ) ) ) :
                esc_attr( $value['std'] );

            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo $value['name']; ?></label>
                </th>
                <td class="forminp">
                    <div class="color_box"><strong><?php echo $value['name']; ?></strong>
                        <input name="<?php echo esc_attr( $value['id'] ) ?>" id="<?php echo esc_attr( $value['id'] ) ?>" type="text" value="<?php echo $picker_value ?>" class="colorpick" />

                        <div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ) ?>" class="colorpickdiv"></div>
                    </div> <?php echo $value['desc']; ?></td>
            </tr>
        <?php
        }

        /**
         * Save the admin field: slider
         *
         * @access public
         *
         * @param mixed $value
         *
         * @return void
         * @since 1.0.0
         */
        public function admin_update_option( $value ) {
            global $woocommerce;

            if ( version_compare( preg_replace( '/-beta-([0-9]+)/', '', $woocommerce->version ), '2.1', '<' ) ) {
                $wc_clean = 'woocommerce_clean';
            }
            else {
                $wc_clean = 'wc_clean';
            }

            update_option( $value['id'], woocommerce_clean( $_POST[$value['id']] ) );
        }

        /**
         * Create new Woocommerce admin field: image deps
         *
         * @access public
         *
         * @param array $value
         *
         * @return void
         * @since 1.0.0
         */
        public function admin_fields_image_deps( $value ) {
            global $woocommerce;

            $force = get_option( 'yith_wcmg_force_sizes' ) == 'yes';

            if ( $force ) {
                $value['desc'] = 'These values ??are automatically calculated based on the values ??of the Single product. If you\'d like to customize yourself the values, please disable the "Forcing Zoom Image sizes" in "Magnifier" tab.';
            }

            if ( $force && isset( $_GET['page'] ) && isset( $_GET['tab'] ) && ( $_GET['page'] == 'woocommerce_settings' || $_GET['page'] == 'wc-settings' ) && $_GET['tab'] == 'catalog' ): ?>
                <script>
                    jQuery(document).ready(function ($) {
                        $('#woocommerce_magnifier_image-width, #woocommerce_magnifier_image-height, #woocommerce_magnifier_image-crop').attr('disabled', 'disabled');

                        $('#shop_single_image_size-width, #shop_single_image_size-height').on('keyup', function () {
                            var value = parseInt($(this).val());
                            var input = (this.id).indexOf('width') >= 0 ? 'width' : 'height';

                            if (!isNaN(value)) {
                                $('#woocommerce_magnifier_image-' + input).val(value * 2);
                            }
                        });

                        $('#shop_single_image_size-crop').on('change', function () {
                            if ($(this).is(':checked')) {
                                $('#woocommerce_magnifier_image-crop').attr('checked', 'checked');
                            } else {
                                $('#woocommerce_magnifier_image-crop').removeAttr('checked');
                            }
                        });

                        $('#mainform').on('submit', function () {
                            $(':disabled').removeAttr('disabled');
                        });
                    });
                </script>
            <?php endif;
        }


        /**
         * Enqueue admin styles and scripts
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function enqueue_styles_scripts() {
            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-mouse' );
            wp_enqueue_script( 'jquery-ui-slider' );

            wp_enqueue_style( 'yith_wcmg_admin', YITH_WCMG_URL . 'assets/css/admin.css' );
        }


        /**
         * Print the banner
         *
         * @access protected
         * @return void
         * @since 1.0.0
         */
        protected function _printBanner() {
            ?>
            <div class="yith_banner">
                <a href="<?php echo $this->banner_url ?>" target="_blank">
                    <img src="<?php echo $this->banner_img ?>" alt="" />
                </a>
            </div>
        <?php
        }


        /**
         * action_links function.
         *
         * @access public
         *
         * @param mixed $links
         *
         * @return void
         */
        public function action_links( $links ) {

            global $woocommerce;

            if ( version_compare( preg_replace( '/-beta-([0-9]+)/', '', $woocommerce->version ), '2.1', '<' ) ) {
                $wc_settings = 'woocommerce_settings';
            }
            else {
                $wc_settings = 'wc-settings';
            }

            $plugin_links = array(
                '<a href="' . admin_url( 'admin.php?page=' . $wc_settings . '&tab=yith_wcmg' ) . '">' . __( 'Settings', 'yit' ) . '</a>',
                '<a href="' . $this->doc_url . '">' . __( 'Docs', 'yit' ) . '</a>',
            );

            return array_merge( $plugin_links, $links );
        }

        /**
         * Create new Woocommerce admin field: yit_wc_image_width
         *
         * @access public
         * @param array $value
         * @return void
         * @since 1.1.3
         */
        public function admin_fields_yit_wc_image_width( $value ){

            $width 	= WC_Admin_Settings::get_option( $value['id'] . '[width]', $value['default']['width'] );
            $height = WC_Admin_Settings::get_option( $value['id'] . '[height]', $value['default']['height'] );
            $crop   = WC_Admin_Settings::get_option( $value['id'] . '[crop]' );
            $crop   = ( $crop == 'on' || $crop == '1' ) ? 1 : 0;
            $crop 	= checked( 1, $crop, false );

            ?><tr valign="top">
                <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?> <?php echo $value['desc'] ?></th>
                <td class="forminp image_width_settings">

                    <input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo $width; ?>" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo $height; ?>" />px

                    <label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" <?php echo $crop; ?> /> <?php _e( 'Hard Crop?', 'woocommerce' ); ?></label>

                    </td>
            </tr><?php

        }
    }
}
