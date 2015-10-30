<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that its ready for translation.
 *
 * @class     WC_POS_i18n
 * @package   WooCommerce POS
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_i18n {

  private $github_url;

  /**
   * Constructor
   */
  public function __construct() {

    // raw github url for language packs
    // todo: use last commit info and switch to cdn
    //    $this->github_url = 'https://cdn.rawgit.com/kilbot/WooCommerce-POS-Language-Packs/master/';
    $this->github_url = 'https://raw.githubusercontent.com/kilbot/WooCommerce-POS-Language-Packs/master/';

    //    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
    $this->load_plugin_textdomain();
    add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
    add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );
    add_filter( 'woocommerce_pos_enqueue_scripts', array( $this, 'js_locale' ) );
    add_filter( 'woocommerce_pos_admin_enqueue_scripts', array( $this, 'js_locale' ) );

    // ajax
    add_action( 'wp_ajax_wc_pos_update_translations', array( $this, 'update_translations' ) );
  }

  /**
   * Load the plugin text domain for translation.
   */
  public function load_plugin_textdomain() {

    $locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-pos' );
    $dir = trailingslashit( WP_LANG_DIR );

    load_textdomain( 'woocommerce-pos', $dir . 'woocommerce-pos/woocommerce-pos-' . $locale . '.mo' );
    load_textdomain( 'woocommerce-pos', $dir . 'plugins/woocommerce-pos-' . $locale . '.mo' );

    // admin translations
    if ( is_admin() ) {
      load_textdomain( 'woocommerce-pos', $dir . 'woocommerce-pos/woocommerce-pos-admin-' . $locale . '.mo' );
      load_textdomain( 'woocommerce-pos', $dir . 'plugins/woocommerce-pos-admin-' . $locale . '.mo' );
    } else {
      load_textdomain( 'woocommerce', $dir . 'woocommerce/woocommerce-admin-' . $locale . '.mo' );
      load_textdomain( 'woocommerce', $dir . 'plugins/woocommerce-admin-' . $locale . '.mo' );
    }

  }

  /**
   * Check GitHub repo for updated language packs
   *
   * @param      $transient
   * @param bool $force
   * @return mixed
   */
  public function update_check( $transient, $force = false ) {
    $locale = get_locale();

    // pre_set_site_transient_update_plugins is called twice
    // we only want to act on the second run
    // also only continue for non English locales
    if ( empty( $transient->checked ) || strpos( $locale, 'en_' ) === 0 ) {
      return $transient;
    }

    // get package.json from github
    $request = wp_remote_get(
      $this->github_url . 'package.json',
      array( 'timeout' => 45 )
    );

    if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
      return $transient;
    }

    // see if translation pack exists
    $response = json_decode( wp_remote_retrieve_body( $request ) );
    $transient = apply_filters( 'woocommerce_pos_language_packs_upgrade', $transient, $response, $this->github_url, $force );
    if ( !isset( $response->locales->$locale ) ) {
      return $transient;
    }

    // compare
    $new = strtotime( $response->locales->$locale );
    $options = get_option( 'woocommerce_pos_language_packs' );

    if ( isset( $options[ $locale ] ) && $options[ $locale ] >= $new && !$force ) {
      return $transient;
    }

    // update required
    $transient->translations[] = array(
      'type'       => 'plugin',
      'slug'       => 'woocommerce-pos',
      'language'   => $locale,
      'version'    => WC_POS_VERSION,
      'updated'    => date( 'Y-m-d H:i:s', $new ),
      'package'    => $this->github_url . 'packages/woocommerce-pos-' . $locale . '.zip',
      'autoupdate' => 1
    );

    return $transient;

  }

  /**
   * Update the database with new language pack date
   * TODO: is there no later hook for translation install?
   *
   * @param $reply
   * @param $package
   * @param $upgrader
   *
   * @return mixed
   */
  public function upgrader_pre_download( $reply, $package, $upgrader ) {

    if ( isset( $upgrader->skin->language_update )
      && $upgrader->skin->language_update->slug == 'woocommerce-pos'
    ) {

      $options = get_option( 'woocommerce_pos_language_packs', array() );
      $locale = get_locale();
      $options[ $locale ] = current_time( 'timestamp' );
      if ( !add_option( 'woocommerce_pos_language_packs', $options, '', 'no' ) ) {
        update_option( 'woocommerce_pos_language_packs', $options );
      }
    }

    return $reply;
  }

  /**
   * Force update translations from AJAX
   */
  public function update_translations() {
    // security
    WC_POS_Server::check_ajax_referer();

    header( "Content-Type: text/event-stream" );
    header( "Cache-Control: no-cache" );
    header( "Access-Control-Allow-Origin: *" );

    echo ":" . str_repeat( " ", 2048 ) . PHP_EOL; // 2 kB padding for IE

    $this->manual_update();

    die();
  }

  /**
   * Force update translations
   */
  public function manual_update() {
    ob_start();
    $locale = get_locale();
    $creds = request_filesystem_credentials( $_GET[ 'security' ], '', false, false, null );

    /* translators: wordpress */
    $this->flush( sprintf( __( 'Updating translations for %1$s (%2$s)&#8230;' ), 'WooCommerce POS', $locale ) );

    $transient = (object)array( 'checked' => true );
    $update = $this->update_check( $transient, true );

    if ( empty( $update->translations ) ) {
      /* note: no translation exists */
      $this->flush( 'No translations found for ' . $locale . '. <a href="mailto:support@woopos.com.au">Contact us</a> if you would like to help translate WooCommerce POS into your language.' );
      $this->flush( 'complete' );

      return;
    }

    if ( !$creds || !WP_Filesystem( $creds ) ) {
      /* translators: wordpress */
      $this->flush( __( 'Translation update failed.' ) );
      $this->flush( 'complete' );

      return;
    }

    foreach ( $update->translations as $translation ) {

      /* translators: wordpress */
      $this->flush( sprintf( __( 'Downloading translation from <span class="code">%s</span>&#8230;' ), $translation[ 'package' ] ) );

      $response = wp_remote_get(
        $translation[ 'package' ],
        array( 'sslverify' => false, 'timeout' => 60, 'filename' => $locale . '.zip' )
      );

      if ( is_wp_error( $response ) || ( $response[ 'response' ][ 'code' ] < 200 || $response[ 'response' ][ 'code' ] >= 300 ) ) {
        /* translators: wordpress */
        $this->flush( __( 'Translation update failed.' ) );
        continue;
      }

      global $wp_filesystem;

      $upload_dir = wp_upload_dir();
      $file = trailingslashit( $upload_dir[ 'path' ] ) . $locale . '.zip';

      // Save the zip file
      if ( !$wp_filesystem->put_contents( $file, $response[ 'body' ], FS_CHMOD_FILE ) ) {
        /* translators: wordpress */
        $this->flush( __( 'Translation update failed.' ) );
        continue;
      }

      // Unzip the file to wp-content/languages/plugins directory
      $dir = trailingslashit( WP_LANG_DIR ) . 'plugins/';
      $unzip = unzip_file( $file, $dir );
      if ( true !== $unzip ) {
        /* translators: wordpress */
        $this->flush( __( 'Translation update failed.' ) );
        continue;
      }

      // Delete the package file
      $wp_filesystem->delete( $file );

      // Update options timestamp
      $key = str_replace( '-', '_', $translation[ 'slug' ] ) . '_language_packs';
      $options = get_option( $key, array() );
      $options[ $locale ] = current_time( 'timestamp' );
      if ( !add_option( $key, $options, '', 'no' ) ) {
        update_option( $key, $options );
      }

      /* translators: wordpress */
      $this->flush( __( 'Translation updated successfully.' ) );

    }

    $this->flush( 'complete' );

    return;

  }

  /**
   * Flush output
   *
   * @param $data
   */
  private function flush( $data ) {
    echo 'data:' . $data . PHP_EOL;
    echo PHP_EOL;
    ob_flush();
    flush();
  }

  /**
   * Load translations for js plugins
   *
   * @param $scripts
   * @return string
   */
  public function js_locale( array $scripts ) {
    $locale = apply_filters( 'plugin_locale', get_locale(), WC_POS_PLUGIN_NAME );
    $dir = WC_POS_PLUGIN_PATH . 'languages/js/';
    $url = WC_POS_PLUGIN_URL . 'languages/js/';
    list( $country ) = explode( '_', $locale );

    if ( is_readable( $dir . $locale . '.js' ) ) {
      $scripts[ 'locale' ] = $url . $locale . '.js';
    } elseif ( is_readable( $dir . $country . '.js' ) ) {
      $scripts[ 'locale' ] = $url . $country . '.js';
    }

    return $scripts;
  }

  /**
   * Return currency denomination for a given country code
   *
   * @param string $code
   * @return array
   */
  static public function currency_denominations( $code = '' ) {
    if ( !$code ) {
      $code = get_woocommerce_currency();
    }
    $denominations = json_decode( file_get_contents( WC_POS_PLUGIN_PATH . 'includes/denominations.json' ) );

    return isset( $denominations->$code ) ? $denominations->$code : $denominations;
  }

  /**
   * i18n payload to init POS app
   *
   * @return mixed
   */
  static public function payload() {

    return apply_filters( 'woocommerce_pos_i18n', array(
      'titles'   => array(
        'browser'       => _x( '浏览', 'system status: browser capabilities', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'cart'          => __( '购物车', 'woocommerce' ),
        /* translators: woocommerce */
        'checkout'      => __( '结账', 'woocommerce' ),
        /* translators: woocommerce */
        'coupons'       => __( '优惠卷', 'woocommerce' ),
        /* translators: woocommerce */
        'customers'     => __( '顾客', 'woocommerce' ),
        /* translators: woocommerce */
        'fee'           => __( '其他费用', 'woocommerce' ),
        'hotkeys'       => _x( '热键', 'keyboard shortcuts', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'order'         => __( '订单', 'woocommerce' ),
        /* translators: woocommerce */
        'orders'        => __( '订单', 'woocommerce' ),
        /* translators: woocommerce */
        'products'      => __( '商品', 'woocommerce' ),
        /* translators: woocommerce */
        'receipt'       => __( '收据', 'woocommerce' ),
        /* translators: woocommerce */
        'shipping'      => __( '配送', 'woocommerce' ),
        'to-pay'        => __( '需要支付', 'woocommerce-pos' ),
        'paid'          => __( '已付款', 'woocommerce-pos' ),
        'unpaid'        => __( '未付款', 'woocommerce-pos' ),
        'email-receipt' => __( '电子邮件收据', 'woocommerce-pos' ),
        'open'          => _x( '打开', 'order status, ie: open order in cart', 'woocommerce-pos' ),
        'change'        => _x( '变化', 'Money returned from cash sale', 'woocommerce-pos' ),
        'support-form'  => __( 'Support Form', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'system-status' => __( '系统状态', 'woocommerce' ),
      ),
      'buttons'  => array(
        /* translators: woocommerce */
        'checkout'        =>  __( '结账', 'woocommerce' ),
        'clear'           => _x( '清空', 'system status: delete local records', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'close'           => __( '关闭' ),
        /* translators: woocommerce */
        'coupon'          => __( '优惠券', 'woocommerce' ),
        'discount'        => __( '折扣', 'woocommerce-pos' ),
        /* translators: wordpress */
        'email'           => __( '电子邮件' ),
        /* translators: woocommerce */
        'fee'             => __( '其他费用', 'woocommerce' ),
        /* translators: woocommerce */
        'new-order'       => __( '确认订单', 'woocommerce' ),
        /* translators: woocommerce */
        'note'            => __( '备注', 'woocommerce' ),
        /* translators: wordpress */
        'print'           => __( '打印' ),
        'process-payment' => __( '立即支付', 'woocommerce-pos' ),
        /* translators: wordpress */
        'refresh'         => __( '刷新' ),
        'restore'         => _x( '恢复默认设置', 'restore default settings', 'woocommerce-pos' ),
        'return'          => _x( '返回', 'Numpad return key', 'woocommerce-pos' ),
        'return-to-sale'  => __( '返回购买', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'save'            => __( '保存', 'woocommerce' ),
        'send'            => __( '发送', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'shipping'        => __( '配送', 'woocommerce' ),
        'void'            => __( '清空', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'expand-all'      => __( '全部展开', 'woocommerce' ),
        /* translators: woocommerce */
        'close-all'       => __( '关闭所有', 'woocommerce' ),
        'legacy'          => __( 'Enable legacy server support', 'woocommerce-pos' ),
      ),
      'messages' => array(
        /* translators: woocommerce */
        'choose'      => __( 'Choose an option', 'woocommerce' ),
        /* translators: woocommerce */
        'error'       => __( 'Sorry, there has been an error.', 'woocommerce' ),
        /* translators: woocommerce */
        'loading'     => __( '运行中 ...' ),
        /* translators: woocommerce */
        'success'     => __( 'Your changes have been saved.', 'woocommerce' ),
        'browser'     => __( 'Your browser is not supported!', 'woocommerce-pos' ),
        'legacy'      => __( 'Unable to use RESTful HTTP methods', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'no-products' => __( '没有找到商品', 'woocommerce' ),
        /* translators: woocommerce */
        'cart-empty'  => __( '你的购物车中没有商品.', 'woocommerce' ),
        'no-gateway'  => __( 'No payment gateways enabled.', 'woocommerce-pos' ),
        /* translators: woocommerce */
        'no-customer' => __( '顾客没有找到', 'woocommerce' )
      ),
      'plural'   => array(
        'records' => _x( 'record |||| records', 'eg: 23 records', 'woocommerce-pos' ),
      )
    ) );

  }

}