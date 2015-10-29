<?php
/**
 * WPBakery Visual Composer Plugin
 *
 * @package VPBakeryVisualComposer
 *
 */

/**
 * Settings page for VC. list of tabs for function composer
 *
 * Settings page for VC creates menu item in admin menu as subpage of Settings section.
 * Settings are build with WP settings API and organized as tabs.
 *
 * List of tabs
 * 1. General Settings - set access rules and allowed content types for editors.
 * 2. Design Options - custom color and spacing editor for VC shortcodes elements.
 * 3. Custom CSS - add custom css to your WP pages.
 * 4. Product License - license key activation for automatic VC updates.
 * 5. My Shortcodes - automated mapping tool for shortcodes.
 *
 * @link http://codex.wordpress.org/Settings_API Wordpress settings API
 * @since 3.4
 */
class Vc_Settings {
	public $tabs;
	public $deactivate;
	public $locale;
	/**
	 * @var string
	 */
	protected $option_group = 'wpb_js_composer_settings';
	/**
	 * @var string
	 */
	protected $page = 'vc_settings';
	/**
	 * @var string
	 */
	protected static $field_prefix = 'wpb_js_';
	/**
	 * @var string
	 */
	protected static $notification_name = 'wpb_js_notify_user_about_element_class_names';
	/**
	 * @var
	 */
	protected static $color_settings;
	/**
	 * @var
	 */
	protected static $defaults;
	/**
	 * @var
	 */
	protected $composer;

	/**
	 * @var array
	 */
	protected $google_fonts_subsets_default = array( 'latin' );
	/**
	 * @var array
	 */
	protected $google_fonts_subsets = array(
		'latin',
		'vietnamese',
		'cyrillic',
		'latin-ext',
		'greek',
		'cyrillic-ext',
		'greek-ext',
	);

	/**
	 * @var array
	 */
	public $google_fonts_subsets_excluded = array();

	/**
	 * @param string $field_prefix
	 */
	public static function setFieldPrefix( $field_prefix ) {
		self::$field_prefix = $field_prefix;
	}

	/**
	 * @return string
	 */
	public function page() {
		return $this->page;
	}

	/**
	 * @return bool
	 */
	public function isEditorEnabled() {
		global $current_user;
		get_currentuserinfo();

		/** @var $settings - get use group access rules */
		$settings = $this->get( 'groups_access_rules' );

		$show = true;
		foreach ( $current_user->roles as $role ) {
			if ( isset( $settings[ $role ]['show'] ) && 'no' === $settings[ $role ]['show'] ) {
				$show = false;
				break;
			}
		}

		return $show;
	}

	/**
	 *
	 */
	public function setTabs() {
		$this->tabs = array();

		if ( $this->showConfigurationTabs() ) {
			$this->tabs['vc-general'] = __( 'General Settings', 'js_composer' );
			if ( ! vc_is_as_theme() || apply_filters( 'vc_settings_page_show_design_tabs', false ) ) {
				$this->tabs['vc-color'] = __( 'Design Options', 'js_composer' );
				$this->tabs['vc-custom_css'] = __( 'Custom CSS', 'js_composer' );
			}
		}

		if ( ! vc_is_network_plugin() || ( vc_is_network_plugin() && is_network_admin() ) ) {
			if ( ! vc_is_updater_disabled() ) {
				$this->tabs['vc-updater'] = __( 'Product License', 'js_composer' );
			}
		}
		// TODO: may allow to disable automapper
		if ( ! is_network_admin() && ! vc_automapper_is_disabled() ) {
			$this->tabs['vc-automapper'] = vc_automapper()->title();
		}
	}

	public function getTabs() {
		if ( ! isset( $this->tabs ) ) {
			$this->setTabs();
		}

		return apply_filters( 'vc_settings_tabs', $this->tabs );
	}

	/**
	 * @return bool
	 */
	public function showConfigurationTabs() {
		return ! vc_is_network_plugin() || ! is_network_admin();
	}

	/**
	 * Render
	 *
	 * @param $tab
	 */
	public function renderTab( $tab ) {
		require_once vc_path_dir( 'CORE_DIR', 'class-vc-page.php' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		if (
			( isset( $_GET['build_css'] ) && ( '1' === $_GET['build_css'] || 'true' === $_GET['build_css'] ) )
			||
			( isset( $_GET['settings-updated'] ) && ( '1' === $_GET['settings-updated'] || 'true' === $_GET['settings-updated'] ) )
		) {
			$this->buildCustomCss(); // TODO: remove this - no needs to re-save always
		}
		$tabs = $this->getTabs();
		foreach ( $tabs as $key => $value ) {
			if ( ! vc_user_access()->part( 'settings' )->can( $key . '-tab' )->get() ) {
				unset( $tabs[ $key ] );
			}
		}
		$page = new Vc_Page();
		$page
			->setSlug( $tab )
			->setTitle( isset( $tabs[ $tab ] ) ? $tabs[ $tab ] : '' )
			->setTemplatePath( apply_filters( 'vc_settings-render-tab-' . $tab, 'pages/vc-settings/tab.php' ) );
		vc_include_template( 'pages/vc-settings/index.php',
			array(
				'pages' => $tabs,
				'active_page' => $page,
				'vc_settings' => $this,
			) );
	}

	/**
	 * Init settings page && menu item
	 * vc_filter: vc_settings_tabs - hook to override settings tabs
	 */
	public function initAdmin() {
		$this->setTabs();

		self::$color_settings = array(
			array( 'vc_color' => array( 'title' => __( 'Main accent color', 'js_composer' ) ) ),
			array( 'vc_color_hover' => array( 'title' => __( 'Hover color', 'js_composer' ) ) ),
			array( 'vc_color_call_to_action_bg' => array( 'title' => __( 'Call to action background color', 'js_composer' ) ) ),
			array( 'vc_color_google_maps_bg' => array( 'title' => __( 'Google maps background color', 'js_composer' ) ) ),
			array( 'vc_color_post_slider_caption_bg' => array( 'title' => __( 'Post slider caption background color', 'js_composer' ) ) ),
			array( 'vc_color_progress_bar_bg' => array( 'title' => __( 'Progress bar background color', 'js_composer' ) ) ),
			array( 'vc_color_separator_border' => array( 'title' => __( 'Separator border color', 'js_composer' ) ) ),
			array( 'vc_color_tab_bg' => array( 'title' => __( 'Tabs navigation background color', 'js_composer' ) ) ),
			array( 'vc_color_tab_bg_active' => array( 'title' => __( 'Active tab background color', 'js_composer' ) ) ),
		);
		self::$defaults = array(
			'vc_color' => '#f7f7f7',
			'vc_color_hover' => '#F0F0F0',
			'margin' => '35px',
			'gutter' => '15',
			'responsive_max' => '768',
			'compiled_js_composer_less' => '',
		);
		$vc_action = vc_action();
		if ( 'restore_color' === $vc_action ) {
			$this->restoreColor();
		} elseif ( 'remove_all_css_classes' === $vc_action ) {
			$this->removeAllCssClasses();
		}

		/**
		 * @since 4.5 used to call update file once option is changed
		 */
		add_action( 'update_option_wpb_js_compiled_js_composer_less', array(
			&$this,
			'buildCustomColorCss',
		) );

		/**
		 * @since 4.5 used to call update file once option is changed
		 */
		add_action( 'update_option_wpb_js_custom_css', array(
			&$this,
			'buildCustomCss',
		) );

		/**
		 * @since 4.5 used to call update file once option is changed
		 */
		add_action( 'add_option_wpb_js_compiled_js_composer_less', array(
			&$this,
			'buildCustomColorCss',
		) );

		/**
		 * @since 4.5 used to call update file once option is changed
		 */
		add_action( 'add_option_wpb_js_custom_css', array(
			&$this,
			'buildCustomCss',
		) );

		$this->deactivate = vc_license()->deactivation(); // TODO: Refactor with separate class.

		/**
		 * Tab: General Settings
		 */
		$tab = 'general';
		$this->addSection( $tab );

		$this->addField( $tab, __( 'Disable responsive content elements', 'js_composer' ), 'not_responsive_css', array(
			&$this,
			'sanitize_not_responsive_css_callback',
		), array( &$this, 'not_responsive_css_field_callback' ) );

		$this->addField( $tab, __( 'Google fonts subsets', 'js_composer' ), 'google_fonts_subsets', array(
			&$this,
			'sanitize_google_fonts_subsets_callback',
		), array( &$this, 'google_fonts_subsets_callback' ) );

		/**
		 * Tab: Design Options
		 */
		$tab = 'color';
		$this->addSection( $tab );

		// Use custom checkbox
		$this->addField( $tab, __( 'Use custom design options', 'js_composer' ), 'use_custom', array(
			&$this,
			'sanitize_use_custom_callback',
		), array( &$this, 'use_custom_callback' ) );

		foreach ( self::$color_settings as $color_set ) {
			foreach ( $color_set as $key => $data ) {
				$this->addField( $tab, $data['title'], $key, array(
					&$this,
					'sanitize_color_callback',
					), array( &$this, 'color_callback' ), array(
					'id' => $key,
					) );
			}
		}

		// Margin
		$this->addField( $tab, __( 'Elements bottom margin', 'js_composer' ), 'margin', array(
			&$this,
			'sanitize_margin_callback',
		), array( &$this, 'margin_callback' ) );

		// Gutter
		$this->addField( $tab, __( 'Grid gutter width', 'js_composer' ), 'gutter', array(
			&$this,
			'sanitize_gutter_callback',
		), array( &$this, 'gutter_callback' ) );

		// Responsive max width
		$this->addField( $tab, __( 'Mobile screen width', 'js_composer' ), 'responsive_max', array(
			&$this,
			'sanitize_responsive_max_callback',
		), array( &$this, 'responsive_max_callback' ) );
		$this->addField( $tab, false, 'compiled_js_composer_less', array(
			&$this,
			'sanitize_compiled_js_composer_less_callback',
		), array( &$this, 'compiled_js_composer_less_callback' ) );

		/**
		 * Tab: Element Class names
		 */
		$tab = 'element_css';
		$this->addSection( $tab );
		$this->addField( $tab, __( 'Row CSS class name', 'js_composer' ), 'row_css_class', array(
			&$this,
			'sanitize_row_css_class_callback',
		), array( &$this, 'row_css_class_callback' ) );
		$this->addField( $tab, __( 'Columns CSS class names', 'js_composer' ), 'column_css_classes', array(
			&$this,
			'sanitize_column_css_classes_callback',
		), array( &$this, 'column_css_classes_callback' ) );

		/**
		 * Tab: Custom CSS
		 */
		$tab = 'custom_css';
		$this->addSection( $tab );
		$this->addField( $tab, __( 'Paste your CSS code', 'js_composer' ), 'custom_css', array(
			&$this,
			'sanitize_custom_css_callback',
		), array( &$this, 'custom_css_field_callback' ) );

		/**
		 * Custom Tabs
		 */
		foreach ( $this->getTabs() as $tab => $title ) {
			do_action( 'vc_settings_tab-' . preg_replace( '/^vc\-/', '', $tab ), $this );
		}

		/**
		 * Tab: Updater
		 */
		$tab = 'updater';
		$this->addSection( $tab );
		$this->addField( $tab, __( 'Envato Username', 'js_composer' ), 'envato_username', array(
			&$this,
			'sanitize_envato_username',
		), array( &$this, 'envato_username_callback' ) );
		$this->addField( $tab, __( 'Secret API Key', 'js_composer' ), 'envato_api_key', array(
			&$this,
			'sanitize_envato_api_key',
		), array( &$this, 'envato_api_key_callback' ) );
		$this->addField( $tab, __( 'Visual Composer License Key', 'js_composer' ), 'js_composer_purchase_code', array(
			&$this,
			'sanitize_js_composer_purchase_code',
		), array( &$this, 'js_composer_purchase_code_callback' ) );
	}

	/**
	 * Creates new section.
	 *
	 * @param $tab - tab key name as tab section
	 * @param $title - Human title
	 * @param $callback - function to build section header.
	 */
	public function addSection( $tab, $title = null, $callback = null ) {
		add_settings_section( $this->option_group . '_' . $tab, $title, ( null !== $callback ? $callback : array(
			&$this,
			'setting_section_callback_function',
		) ), $this->page . '_' . $tab );
	}

	/**
	 * Create field in section.
	 *
	 * @param $tab
	 * @param $title
	 * @param $field_name
	 * @param $sanitize_callback
	 * @param $field_callback
	 * @param array $args
	 *
	 * @return $this
	 */
	public function addField( $tab, $title, $field_name, $sanitize_callback, $field_callback, $args = array() ) {
		register_setting( $this->option_group . '_' . $tab, self::$field_prefix . $field_name, $sanitize_callback );
		add_settings_field( self::$field_prefix . $field_name, $title, $field_callback, $this->page . '_' . $tab, $this->option_group . '_' . $tab, $args );

		return $this; // chaining
	}

	/**
	 *
	 */
	public function restoreColor() {
		foreach ( self::$color_settings as $color_sett ) {
			foreach ( $color_sett as $key => $value ) {
				delete_option( self::$field_prefix . $key );
			}
		}
		delete_option( self::$field_prefix . 'margin' );
		delete_option( self::$field_prefix . 'gutter' );
		delete_option( self::$field_prefix . 'responsive_max' );
		delete_option( self::$field_prefix . 'use_custom' );
	}

	/**
	 *
	 */
	public function removeAllCssClasses() {
		delete_option( self::$field_prefix . 'row_css_class' );
		delete_option( self::$field_prefix . 'column_css_classes' );
	}

	/**
	 * @param $option_name
	 *
	 * @return mixed|void
	 */
	public static function get( $option_name ) {
		return get_option( self::$field_prefix . $option_name );
	}

	/**
	 * @param $option_name
	 * @param $value
	 *
	 * @return bool
	 */
	public static function set( $option_name, $value ) {
		return update_option( self::$field_prefix . $option_name, $value );
	}

	/**
	 * Set up the enqueue for the CSS & JavaScript files.
	 *
	 */

	function adminLoad() {
		wp_enqueue_style( 'js_composer_settings', vc_asset_url( 'css/js_composer_settings.min.css' ), false, WPB_VC_VERSION, false );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'wpb_js_composer_settings' );
		wp_enqueue_script( 'ace-editor' );
		$this->locale = array(
			'are_you_sure_reset_css_classes' => __( 'Are you sure you want to reset to defaults?', 'js_composer' ),
			'are_you_sure_reset_color' => __( 'Are you sure you want to reset to defaults?', 'js_composer' ),
			'vc_updater_error' => sprintf( __( 'Envato API error. Try again later  or open support ticket at <a href="%s" target="_blank">%s</a>.', 'js_composer' ), 'http://support.wpbakery.com', 'support.wpbakery.com' ),
			'vc_updater_license_activation_success' => __( 'License successfully activated.', 'js_composer' ),
			'vc_updater_license_deactivation_success' => __( 'License Key is deactivated.', 'js_composer' ),
			'vc_updater_empty_data' => __( 'Envato Username and/or License Key are required.', 'js_composer' ),
			'vc_updater_wrong_license_key' => sprintf( __( 'Invalid License Key. Visit your profile to retrieve valid License Key or read <a href="%s" target="_blank">tutorial</a>.', 'js_composer' ), 'http://go.wpbakery.com/purchase-code' ),
			'vc_updater_wrong_data' => sprintf( __( 'Invalid data. Check your information or open support ticket at <a href="%s" target="_blank">%s</a>.', 'js_composer' ), 'http://support.wpbakery.com', 'support.wpbakery.com' ),
			'vc_updater_already_activated' => __( 'License successfully activated.', 'js_composer' ),
			'vc_updater_already_activated_another_url' => sprintf( __( 'Your License Key is already activated on another site ({site}), you should deactivate it first or <a href="%s" target="_blank">obtain new License Key</a>.', 'js_composer' ), esc_url( 'http://bit.ly/vcomposer' ) ),
			'vc_updater_activate_license' => __( 'Activate License', 'js_composer' ),
			'vc_updater_deactivate_license' => __( 'Deactivate License', 'js_composer' ),
			'wrong_username_api_key' => sprintf( __( 'Invalid Username and/or API Key. Check your data or read <a href="%s" target="_blank">tutorial</a>.', 'js_composer' ), 'http://go.wpbakery.com/activation' ),
			'saving' => __( 'Saving...', 'js_composer' ),
			'save' => __( 'Save Changes', 'js_composer' ),
			'saved' => __( 'Design Options successfully saved.', 'js_composer' ),
			'save_error' => __( 'Design Options could not be saved', 'js_composer' ),
			'form_save_error' => __( 'Problem with AJAX request execution, check internet connection and try again.', 'js_composer' ),
		);
		wp_localize_script( 'wpb_js_composer_settings', 'i18nLocaleSettings', $this->locale );
	}

	/**
	 * Access groups
	 * @deprecated 4.8
	 */
	public function groups_access_rules_callback() {
		global $wp_roles;
		$groups = is_object( $wp_roles ) ? $wp_roles->roles : array();

		$settings = ( $settings = get_option( self::$field_prefix . 'groups_access_rules' ) ) ? $settings : array();
		$show_types = array(
			'all' => __( 'Show Visual Composer & default editor', 'js_composer' ),
			'only' => __( 'Show only Visual Composer', 'js_composer' ),
			'no' => __( "Don't allow to use Visual Composer", 'js_composer' ),
		);
		$shortcodes = WPBMap::getShortCodes();
		$size_line = ceil( count( array_keys( $shortcodes ) ) / 3 );
		?>
		<div class="wpb_settings_accordion" id="wpb_js_settings_access_groups" xmlns="http://www.w3.org/1999/html">
		<?php
		if ( is_array( $groups ) ) :
			foreach ( $groups as $key => $params ) :
				if ( ( isset( $params['capabilities']['edit_posts'] ) && true === $params['capabilities']['edit_posts'] )
				     || ( isset( $params['capabilities']['edit_pages'] ) && true === $params['capabilities']['edit_pages'] )
				) :
					$allowed_setting = isset( $settings[ $key ]['show'] ) ? $settings[ $key ]['show'] : 'all';
					$shortcode_settings = isset( $settings[ $key ]['shortcodes'] ) ? $settings[ $key ]['shortcodes'] : array();
					?>
					<h3 id="wpb-settings-group-<?php echo $key ?>-header">
						<a href="#wpb-settings-group-<?php echo $key ?>">
							<?php echo $params['name'] ?>
						</a>
					</h3>
					<div id="wpb-settings-group-<?php echo $key ?>" class="accordion-body">
						<div class="visibility settings-block">
							<label
								for="wpb_composer_access_<?php echo $key ?>"><b><?php _e( 'Visual Composer access', 'js_composer' ) ?></b></label>
							<select id="wpb_composer_access_<?php echo $key ?>"
							        name="<?php echo self::$field_prefix . 'groups_access_rules[' . $key . '][show]' ?>">
								<?php foreach ( $show_types as $i_key => $name ) : ?>
									<option
										value="<?php echo $i_key ?>"<?php echo $allowed_setting == $i_key ? ' selected="true"' : '' ?>><?php echo $name ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="shortcodes settings-block">
							<div class="title"><b><?php _e( 'Enabled shortcodes', 'js_composer' ); ?></b></div>
							<?php $z = 1;
							foreach ( $shortcodes as $sc_base => $el ) : ?>
								<?php if ( ! in_array( $el['base'], array(
									'vc_column',
									'vc_row',
									'vc_row_inner',
									'vc_column_inner',
								) )
								) : ?>
									<?php if ( 1 === $z ) : ?><div class="pull-left"><?php endif ?>
									<label>
										<input
											type="checkbox"
											<?php if ( isset( $shortcode_settings[ $sc_base ] ) && 1 === (int) $shortcode_settings[ $sc_base ] ) : ?>checked="true"
											<?php endif ?>name="<?php echo self::$field_prefix . 'groups_access_rules[' . $key . '][shortcodes][' . $sc_base . ']' ?>"
											value="1"/>
										<?php echo $el['name'] ?><?php if ( isset( $el['deprecated'] ) && false !== $el['deprecated'] ) {
											echo ' <i>' . sprintf( __( '(deprecated since v%s)', 'js_composer' ), $el['deprecated'] ) . '</i>';
} ?>
									</label>
									<?php if ( $z == $size_line ) : ?></div><?php $z = 0; endif;
									$z += 1; ?>
								<?php endif ?>
							<?php endforeach ?>
							<?php if ( 1 !== $z ) : ?></div><?php endif ?>
						<div class="vc_clearfix"></div>
						<div class="select-all">
							<a href="#"
							   class="wpb-settings-select-all-shortcodes"><?php echo __( 'Select All', 'js_composer' ) ?></a>
							| <a href="#"
							     class="wpb-settings-select-none-shortcodes"><?php echo __( 'Select none', 'js_composer' ) ?></a>
						</div>
					</div>
					</div>
				<?php
				endif;
			endforeach;
		endif;
		?>
		</div>
		<p class="description"><?php _e( 'Define access rules for different user groups.', 'js_composer' ); ?></p>
	<?php
	}

	/**
	 * Content types checkboxes list callback function
	 * @deprecated 4.8
	 */
	public function content_types_field_callback() {
		$pt_array = ( $pt_array = get_option( 'wpb_js_content_types' ) ) ? ( $pt_array ) : vc_default_editor_post_types();
		foreach ( $this->getPostTypes() as $pt ) {
			if ( ! in_array( $pt, $this->getExcluded() ) ) {
				$checked = ( in_array( $pt, $pt_array ) ) ? ' checked' : '';
				?>
				<label>
					<input type="checkbox"<?php echo $checked; ?> value="<?php echo $pt; ?>"
					       id="wpb_js_post_types_<?php echo $pt; ?>"
					       name="<?php echo self::$field_prefix . 'content_types' ?>[]">
					<?php echo $pt; ?>
				</label><br>
			<?php
			}
		}
		?>
		<p
			class="description indicator-hint"><?php _e( 'Select content types available to Visual Composer.', 'js_composer' ); ?></p>
	<?php
	}

	/**
	 * Themes Content types checkboxes list callback function
	 * @deprecated 4.8
	 */
	public function theme_content_types_field_callback() {
		$pt_array = ( $pt_array = get_option( 'wpb_js_theme_content_types' ) ) ? $pt_array : vc_manager()->editorPostTypes();
		foreach ( $this->getPostTypes() as $pt ) {
			if ( ! in_array( $pt, $this->getExcluded() ) ) {
				$checked = ( in_array( $pt, $pt_array ) ) ? ' checked' : '';
				?>
				<label>
					<input type="checkbox"<?php echo $checked; ?> value="<?php echo $pt; ?>"
					       id="wpb_js_post_types_<?php echo $pt; ?>"
					       name="<?php echo self::$field_prefix . 'theme_content_types' ?>[]">
					<?php echo $pt; ?>
				</label><br>
			<?php
			}
		}
		?>
		<p
			class="description indicator-hint"><?php _e( 'Select content types available to Visual Composer.', 'js_composer' ); ?></p>
	<?php
	}

	/**
	 *
	 */
	public function custom_css_field_callback() {
		$value = ( $value = get_option( self::$field_prefix . 'custom_css' ) ) ? $value : '';
		echo '<textarea name="' . self::$field_prefix . 'custom_css' . '" class="wpb_csseditor custom_css" style="display:none">' . $value . '</textarea>';
		echo '<pre id="wpb_csseditor" class="wpb_content_element custom_css" >' . $value . '</pre>';
		echo '<p class="description indicator-hint">' . __( 'Add custom CSS code to the plugin without modifying files.', 'js_composer' ) . '</p>';
	}

	/**
	 * Not responsive checkbox callback function
	 */
	public function not_responsive_css_field_callback() {
		$checked = ( $checked = get_option( self::$field_prefix . 'not_responsive_css' ) ) ? $checked : false;
		?>
		<label>
			<input type="checkbox"<?php echo( $checked ? ' checked' : '' ) ?> value="1"
			       id="wpb_js_not_responsive_css" name="<?php echo self::$field_prefix . 'not_responsive_css' ?>">
			<?php _e( 'Disable', 'js_composer' ) ?>
		</label><br/>
		<p
			class="description indicator-hint"><?php _e( 'Disable content elements from "stacking" one on top other on small media screens (Example: mobile devices).', 'js_composer' ); ?></p>
	<?php
	}

	/**
	 * Google fonts subsets callback
	 */
	public function google_fonts_subsets_callback() {
		$pt_array = ( $pt_array = get_option( self::$field_prefix . 'google_fonts_subsets' ) ) ? $pt_array : $this->googleFontsSubsets();
		foreach ( $this->getGoogleFontsSubsets() as $pt ) {
			if ( ! in_array( $pt, $this->getGoogleFontsSubsetsExcluded() ) ) {
				$checked = ( in_array( $pt, $pt_array ) ) ? ' checked' : '';
				?>
				<label>
					<input type="checkbox"<?php echo $checked; ?> value="<?php echo $pt; ?>"
					       id="wpb_js_gf_subsets_<?php echo $pt; ?>"
					       name="<?php echo self::$field_prefix . 'google_fonts_subsets' ?>[]">
					<?php echo $pt; ?>
				</label><br>
			<?php
			}
		}
		?>
		<p
			class="description indicator-hint"><?php _e( 'Select subsets for Google Fonts available to content elements.', 'js_composer' ); ?></p>
	<?php
	}

	/**
	 * Get subsets for google fonts.
	 *
	 * @since  4.3
	 * @access public
	 * @return array
	 */
	public function googleFontsSubsets() {
		if ( ! isset( $this->google_fonts_subsets_settings ) ) {
			$pt_array = vc_settings()->get( 'google_fonts_subsets' );
			$this->google_fonts_subsets_settings = $pt_array ? $pt_array : $this->googleFontsSubsetsDefault();
		}

		return $this->google_fonts_subsets_settings;
	}

	/**
	 * @return array
	 */
	public function googleFontsSubsetsDefault() {
		return $this->google_fonts_subsets_default;
	}

	/**
	 * @return array
	 */
	public function getGoogleFontsSubsets() {
		return $this->google_fonts_subsets;
	}

	/**
	 * @param $subsets
	 *
	 * @return bool
	 */
	public function setGoogleFontsSubsets( $subsets ) {
		if ( is_array( $subsets ) ) {
			$this->google_fonts_subsets = $subsets;

			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function getGoogleFontsSubsetsExcluded() {
		return $this->google_fonts_subsets_excluded;
	}

	/**
	 * @param $excluded
	 *
	 * @return bool
	 */
	public function setGoogleFontsSubsetsExcluded( $excluded ) {
		if ( is_array( $excluded ) ) {
			$this->google_fonts_subsets_excluded = $excluded;

			return true;
		}

		return false;
	}

	/**
	 * Row css class callback
	 */
	public function row_css_class_callback() {
		$value = ( $value = get_option( self::$field_prefix . 'row_css_class' ) ) ? $value : '';
		echo ! empty( $value ) ? $value : '<i>' . __( 'Empty value', 'js_composer' ) . '</i>';
	}

	/**
	 * Not responsive checkbox callback function
	 *
	 */
	public function use_custom_callback() {
		$field = 'use_custom';
		$checked = ( $checked = get_option( self::$field_prefix . $field ) ) ? $checked : false;
		?>
		<label>
			<input type="checkbox"<?php echo( $checked ? ' checked' : '' ) ?> value="1"
			       id="wpb_js_<?php echo $field; ?>" name="<?php echo self::$field_prefix . $field ?>">
			<?php _e( 'Enable', 'js_composer' ) ?>
		</label><br/>
		<p
			class="description indicator-hint"><?php _e( 'Enable the use of custom design options (Note: when checked - custom css file will be used).', 'js_composer' ); ?></p>
	<?php
	}

	/**
	 *
	 */
	public function color_callback( $args ) {
		$field = $args['id'];
		$value = ( $value = get_option( self::$field_prefix . $field ) ) ? $value : $this->getDefault( $field );
		echo '<input type="text" name="' . self::$field_prefix . $field . '" value="' . $value . '" class="color-control css-control">';
	}

	/**
	 *
	 */
	public function margin_callback() {
		$field = 'margin';
		$value = ( $value = get_option( self::$field_prefix . $field ) ) ? $value : $this->getDefault( $field );
		echo '<input type="text" name="' . self::$field_prefix . $field . '" value="' . $value . '" class="css-control">';
		echo '<p class="description indicator-hint css-control">' . __( 'Change default vertical spacing between content elements (Example: 20px).', 'js_composer' ) . '</p>';
	}

	/**
	 *
	 */
	public function gutter_callback() {
		$field = 'gutter';
		$value = ( $value = get_option( self::$field_prefix . $field ) ) ? $value : $this->getDefault( $field );
		echo '<input type="text" name="' . self::$field_prefix . $field . '" value="' . $value . '" class="css-control"> px';
		echo '<p class="description indicator-hint css-control">' . __( 'Change default horizontal spacing between columns, enter new value in pixels.', 'js_composer' ) . '</p>';
	}

	/**
	 *
	 */
	public function responsive_max_callback() {
		$field = 'responsive_max';
		$value = ( $value = get_option( self::$field_prefix . $field ) ) ? $value : $this->getDefault( $field );
		echo '<input type="text" name="' . self::$field_prefix . $field . '" value="' . $value . '" class="css-control"> px';
		echo '<p class="description indicator-hint css-control">' . __( 'By default content elements "stack" one on top other when screen size is smaller than 768px. Change the value to change "stacking" size.', 'js_composer' ) . '</p>';
	}

	/**
	 *
	 */
	public function compiled_js_composer_less_callback() {
		$field = 'compiled_js_composer_less';
		echo '<input type="hidden" name="' . self::$field_prefix . $field . '" value="">'; // VALUE must be empty
	}

	/**
	 *
	 */
	public function envato_username_callback() {
		$field = 'envato_username';
		$value = ( $value = get_option( self::$field_prefix . $field ) ) ? $value : '';
		echo '<input type="text" name="' . self::$field_prefix . $field . '" value="' . $value . '"' . $this->disableIfActivated() . '>';
		echo '<p class="description indicator-hint">' . __( 'Enter your Envato username.', 'js_composer' ) . '</p>';
	}

	/**
	 *
	 */
	public function js_composer_purchase_code_callback() {
		$field = 'js_composer_purchase_code';
		$value = ( $value = get_option( self::$field_prefix . $field ) ) ? $value : '';
		echo '<input type="text" name="' . self::$field_prefix . $field . '" value="' . $value . '"' . $this->disableIfActivated() . '>';
		echo '<p class="description indicator-hint">' . sprintf( __( 'Please enter your CodeCanyon Visual Composer license key, you can find your key by following the instructions on <a href="%s" target="_blank">this page</a>. (Example of license key: bjg759fk-kvta-6584-94h6-75jg8vblatftq)', 'js_composer' ), esc_url( 'http://go.wpbakery.com/purchase-code' ) ) . '</p>';
	}

	/**
	 *
	 */
	public function envato_api_key_callback() {
		$field = 'envato_api_key';
		$value = ( $value = get_option( self::$field_prefix . $field ) ) ? $value : '';
		echo '<input type="password" name="' . self::$field_prefix . $field . '" value="' . $value . '"' . $this->disableIfActivated() . '>';
		echo '<p class="description indicator-hint">' . sprintf( __( "Enter your API key, you can find your API key by following the instructions on <a href='%s' target='_blank'>this page</a>.", 'js_composer' ), esc_url( 'http://go.wpbakery.com/faq-api-key' ) ) . '</p>';
	}

	/**
	 * @param $key
	 *
	 * @return string
	 */
	public function getDefault( $key ) {
		return ! empty( self::$defaults[ $key ] ) ? self::$defaults[ $key ] : '';
	}

	/**
	 * @return string
	 */
	public function disableIfActivated() {
		if ( ! isset( $this->deactivate_license ) ) {
			$this->deactivate_license = vc_license()->deactivation();
		}

		return empty( $this->deactivate_license ) ? '' : ' disabled="true" class="vc_updater-passive"';
	}

	/**
	 * Callback function for settings section
	 *
	 * @param $tab
	 */
	public function setting_section_callback_function( $tab ) {
		if ( 'wpb_js_composer_settings_color' === $tab['id'] ) : ?>
			<div class="tab_intro">
				<p>
					<?php _e( 'Here you can tweak default Visual Composer content elements visual appearance. By default Visual Composer is using neutral light-grey theme. Changing "Main accent color" will affect all content elements if no specific "content block" related color is set.', 'js_composer' ) ?>
				</p>
			</div>
		<?php elseif ( 'wpb_js_composer_settings_updater' === $tab['id'] ) : ?>
			<div class="tab_intro">
				<?php if ( vc_is_as_theme() ) : ?>
					<div class="updated inline">
						<p>
							<?php _e( 'Please activate your license in Product License tab!', 'js_composer' ) ?>
						</p>
					</div>
				<?php endif ?>
				<p>
					<?php //_e('Add your Envato credentials, to enable auto updater. With correct login credentials Visual Composer will be updated automatically (same as other plugins do).', 'js_composer') ?>
					<?php echo sprintf( __( 'A valid license key qualifies you for support and enables automatic updates. <strong>A license key may only be used for one Visual Composer installation on one WordPress site at a time.</strong> If you previosly activated your license key on another site, then you should deactivate it first or <a href="%s" target="_blank">obtain new license key</a>.', 'js_composer' ), esc_url( 'http://bit.ly/vcomposer' ) ); ?>
				</p>
			</div>
		<?php endif;
	}

	/**
	 * @return array
	 * @deprecated 4.8
	 */
	protected function getExcluded() {
		if ( ! isset( $this->vc_excluded_post_types ) ) {
			$this->vc_excluded_post_types = apply_filters( 'vc_settings_exclude_post_type',
			array( 'attachment', 'revision', 'nav_menu_item', 'mediapage' ) );
		}

		return $this->vc_excluded_post_types;
	}

	/**
	 * @return array
	 * @deprecated 4.8
	 */
	protected function getPostTypes() {
		return get_post_types( array( 'public' => true ) );
	}

	/**
	 * Sanitize functions
	 *
	 */

	// {{

	/**
	 * Access rules for user's groups
	 *
	 * @param $rules - Array of selected rules for each user's group
	 * @deprecated 4.8
	 *
	 * @return array
	 */

	public function sanitize_group_access_rules_callback( $rules ) {
		$sanitize_rules = array();
		$groups = get_editable_roles();
		foreach ( $groups as $key => $params ) {
			if ( isset( $rules[ $key ] ) ) {
				$sanitize_rules[ $key ] = $rules[ $key ];
			}
		}

		return $sanitize_rules;
	}

	/**
	 * @param $rules
	 *
	 * @return mixed
	 */
	public function sanitize_not_responsive_css_callback( $rules ) {
		return $rules;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function sanitize_row_css_class_callback( $value ) {
		return $value;
	}

	/**
	 * Post types fields sanitize
	 *
	 * @param $post_types - Post types array selected by user
	 *
	 * @deprecated 4.8
	 * @return array
	 */

	public function sanitize_post_types_callback( $post_types ) {
		$pt_array = array();
		if ( isset( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types as $pt ) {
				if ( ! in_array( $pt, $this->getExcluded() ) && in_array( $pt, $this->getPostTypes() ) ) {
					$pt_array[] = $pt;
				}
			}
		}

		return $pt_array;
	}

	/**
	 * @param $subsets
	 *
	 * @return array
	 */
	public function sanitize_google_fonts_subsets_callback( $subsets ) {
		$pt_array = array();
		if ( isset( $subsets ) && is_array( $subsets ) ) {
			foreach ( $subsets as $pt ) {
				if ( ! in_array( $pt, $this->getGoogleFontsSubsetsExcluded() ) && in_array( $pt, $this->getGoogleFontsSubsets() ) ) {
					$pt_array[] = $pt;
				}
			}
		}

		return $pt_array;
	}

	/**
	 * @param $rules
	 *
	 * @return mixed
	 */
	public function sanitize_use_custom_callback( $rules ) {
		return $rules;
	}

	/**
	 * @param $css
	 *
	 * @return mixed
	 */
	public function sanitize_custom_css_callback( $css ) {
		return $css;
	}

	/**
	 * @param $css
	 *
	 * @return mixed
	 */
	public function sanitize_compiled_js_composer_less_callback( $css ) {
		return $css;
	}

	/**
	 * @param $color
	 *
	 * @return mixed
	 */
	public function sanitize_color_callback( $color ) {
		return $color;
	}

	/**
	 * @param $margin
	 *
	 * @return mixed
	 */
	public function sanitize_margin_callback( $margin ) {
		$margin = preg_replace( '/\s/', '', $margin );
		if ( ! preg_match( '/^\d+(px|%|em|pt){0,1}$/', $margin ) ) {
			add_settings_error( self::$field_prefix . 'margin', 1, __( 'Invalid Margin value.', 'js_composer' ), 'error' );
		}

		return $margin;
	}

	/**
	 * @param $gutter
	 *
	 * @return mixed
	 */
	public function sanitize_gutter_callback( $gutter ) {
		$gutter = preg_replace( '/[^\d]/', '', $gutter );
		if ( ! $this->_isGutterValid( $gutter ) ) {
			add_settings_error( self::$field_prefix . 'gutter', 1, __( 'Invalid Gutter value.', 'js_composer' ), 'error' );
		}

		return $gutter;
	}

	/**
	 * @param $responsive_max
	 *
	 * @return mixed
	 */
	public function sanitize_responsive_max_callback( $responsive_max ) {
		if ( ! $this->_isNumberValid( $responsive_max ) ) {
			add_settings_error( self::$field_prefix . 'responsive_max', 1, __( 'Invalid "Responsive max" value.', 'js_composer' ), 'error' );
		}

		return $responsive_max;
	}

	/**
	 * @param $username
	 *
	 * @return mixed
	 */
	public function sanitize_envato_username( $username ) {
		return $username;
	}

	/**
	 * @param $api_key
	 *
	 * @return mixed
	 */
	public function sanitize_envato_api_key( $api_key ) {
		return $api_key;
	}

	/**
	 * @param $code
	 *
	 * @return mixed
	 */
	public function sanitize_js_composer_purchase_code( $code ) {
		return $code;
	}

	// }}
	/**
	 * @param $number
	 *
	 * @return int
	 */
	public static function _isNumberValid( $number ) {
		return preg_match( '/^[\d]+(\.\d+){0,1}$/', $number );

	}

	/**
	 * @param $gutter
	 *
	 * @return int
	 */
	public static function _isGutterValid( $gutter ) {
		return self::_isNumberValid( $gutter );
	}

	/**
	 * @return bool
	 */
	public static function requireNotification() {
		$row_css_class = ( $value = get_option( self::$field_prefix . 'row_css_class' ) ) ? $value : '';
		$column_css_classes = ( $value = get_option( self::$field_prefix . 'column_css_classes' ) ) ? $value : '';

		$notification = get_option( self::$notification_name );
		if ( 'false' !== $notification && ( ! empty( $row_css_class ) || strlen( implode( '', array_values( $column_css_classes ) ) ) > 0 ) ) {
			update_option( self::$notification_name, 'true' );

			return true;
		}

		return false;
	}

	public function useCustomCss() {
		$use_custom = get_option( self::$field_prefix . 'use_custom', false );

		return $use_custom;
	}

	public function getCustomCssVersion() {
		$less_version = get_option( self::$field_prefix . 'less_version', false );

		return $less_version;
	}

	/**
	 *
	 */
	public function rebuild() {
		/** WordPress Template Administration API */
		require_once( ABSPATH . 'wp-admin/includes/template.php' );
		/** WordPress Administration File API */
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$this->initAdmin();
		$this->buildCustomCss(); // TODO: remove this - no needs to re-save always
	}

	/**
	 *
	 */
	public static function buildCustomColorCss() {
		/**
		 * Filesystem API init.
		 * */
		$url = wp_nonce_url( 'admin.php?page=vc-color&build_css=1', 'wpb_js_settings_save_action' );
		self::getFileSystem( $url );
		global $wp_filesystem;
		/**
		 *
		 * Building css file.
		 *
		 */
		if ( false === ( $js_composer_upload_dir = self::checkCreateUploadDir( $wp_filesystem, 'use_custom', 'js_composer_front_custom.css' ) ) ) {
			return;
		}

		$filename = $js_composer_upload_dir . '/js_composer_front_custom.css';
		$use_custom = get_option( self::$field_prefix . 'use_custom' );
		if ( ! $use_custom ) {
			$wp_filesystem->put_contents( $filename, '', FS_CHMOD_FILE );

			return;
		}
		$css_string = get_option( self::$field_prefix . 'compiled_js_composer_less' );
		if ( strlen( trim( $css_string ) ) > 0 ) {
			update_option( self::$field_prefix . 'less_version', WPB_VC_VERSION );
			// HERE goes the magic
			if ( ! $wp_filesystem->put_contents( $filename, $css_string, FS_CHMOD_FILE ) ) {
				if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
					add_settings_error( self::$field_prefix . 'main_color', $wp_filesystem->errors->get_error_code(), __( 'Something went wrong: js_composer_front_custom.css could not be created.', 'js_composer' ) . ' ' . $wp_filesystem->errors->get_error_message(), 'error' );
				} elseif ( ! $wp_filesystem->connect() ) {
					add_settings_error( self::$field_prefix . 'main_color', $wp_filesystem->errors->get_error_code(), __( 'js_composer_front_custom.css could not be created. Connection error.', 'js_composer' ), 'error' );
				} elseif ( ! $wp_filesystem->is_writable( $filename ) ) {
					add_settings_error( self::$field_prefix . 'main_color', $wp_filesystem->errors->get_error_code(), sprintf( __( 'js_composer_front_custom.css could not be created. Cannot write custom css to "%s".', 'js_composer' ), $filename ), 'error' );
				} else {
					add_settings_error( self::$field_prefix . 'main_color', $wp_filesystem->errors->get_error_code(), __( 'js_composer_front_custom.css could not be created. Problem with access.', 'js_composer' ), 'error' );
				}
				delete_option( self::$field_prefix . 'use_custom' );
				delete_option( self::$field_prefix . 'less_version' );
			}
		}
	}

	/**
	 * Builds custom css file using css options from vc settings.
	 *
	 * @return bool
	 */
	public static function buildCustomCss() {
		/**
		 * Filesystem API init.
		 * */
		$url = wp_nonce_url( 'admin.php?page=vc-color&build_css=1', 'wpb_js_settings_save_action' );
		self::getFileSystem( $url );
		global $wp_filesystem;
		/**
		 * Building css file.
		 */
		if ( false === ( $js_composer_upload_dir = self::checkCreateUploadDir( $wp_filesystem, 'custom_css', 'custom.css' ) ) ) {
			return true;
		}

		$filename = $js_composer_upload_dir . '/custom.css';
		$css_string = '';
		$custom_css_string = get_option( self::$field_prefix . 'custom_css' );
		if ( ! empty( $custom_css_string ) ) {
			$assets_url = vc_asset_url( '' );
			$css_string .= preg_replace( '/(url\(\.\.\/(?!\.))/', 'url(' . $assets_url, $custom_css_string );
		}

		if ( ! $wp_filesystem->put_contents( $filename, $css_string, FS_CHMOD_FILE ) ) {
			if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				add_settings_error( self::$field_prefix . 'custom_css', $wp_filesystem->errors->get_error_code(), __( 'Something went wrong: custom.css could not be created.', 'js_composer' ) . $wp_filesystem->errors->get_error_message(), 'error' );
			} elseif ( ! $wp_filesystem->connect() ) {
				add_settings_error( self::$field_prefix . 'custom_css', $wp_filesystem->errors->get_error_code(), __( 'custom.css could not be created. Connection error.', 'js_composer' ), 'error' );
			} elseif ( ! $wp_filesystem->is_writable( $filename ) ) {
				add_settings_error( self::$field_prefix . 'custom_css', $wp_filesystem->errors->get_error_code(), __( 'custom.css could not be created. Cannot write custom css to "' . $filename . '".', 'js_composer' ), 'error' );
			} else {
				add_settings_error( self::$field_prefix . 'custom_css', $wp_filesystem->errors->get_error_code(), __( 'custom.css could not be created. Problem with access.', 'js_composer' ), 'error' );
			}

			return false;
		}

		return true;

	}

	/**
	 * @param $wp_filesystem
	 * @param $option
	 * @param $filename
	 *
	 * @return bool|string
	 */
	public static function checkCreateUploadDir( $wp_filesystem, $option, $filename ) {
		$js_composer_upload_dir = self::uploadDir();
		if ( ! $wp_filesystem->is_dir( $js_composer_upload_dir ) ) {
			if ( ! $wp_filesystem->mkdir( $js_composer_upload_dir, 0777 ) ) {
				add_settings_error( self::$field_prefix . $option, $wp_filesystem->errors->get_error_code(), __( sprintf( '%s could not be created. Not available to create js_composer directory in uploads directory (' . $js_composer_upload_dir . ').', $filename ), 'js_composer' ), 'error' );

				return false;
			}
		}

		return $js_composer_upload_dir;
	}

	/**
	 * @return string
	 */
	public static function uploadDir() {
		$upload_dir = wp_upload_dir();
		global $wp_filesystem;

		return $wp_filesystem->find_folder( $upload_dir['basedir'] ) . vc_upload_dir();
	}

	/**
	 * @return string
	 */
	public static function uploadURL() {
		$upload_dir = wp_upload_dir();

		return $upload_dir['baseurl'] . vc_upload_dir();
	}

	/**
	 * @return string
	 */
	public static function getFieldPrefix() {
		return self::$field_prefix;
	}

	/**
	 * @param string $url
	 */
	protected static function getFileSystem( $url = '' ) {
		if ( empty( $url ) ) {
			$url = wp_nonce_url( 'admin.php?page=vc-general', 'wpb_js_settings_save_action' );
		}
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
			_e( 'This is required to enable file writing for js_composer', 'js_composer' );
			exit(); // stop processing here
		}
		$upload_dir = wp_upload_dir();
		if ( ! WP_Filesystem( $creds, $upload_dir['basedir'] ) ) {
			request_filesystem_credentials( $url, '', true, false, null );
			_e( 'This is required to enable file writing for js_composer', 'js_composer' );
			exit();
		}
	}

	/**
	 * @return string
	 */
	public function getOptionGroup() {
		return $this->option_group;
	}
}

/**
 * Backward capability for third-party-plugins
 */
class WPBakeryVisualComposerSettings extends Vc_Settings {

}
