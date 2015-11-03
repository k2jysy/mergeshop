<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !function_exists( 'yith_wcpv_get_template' ) ) {
    /**
     * Get Plugin Template
     *
     * It's possible to overwrite the template from theme.
     * Put your custom template in woocommerce/product-vendors folder
     *
     * @param        $filename
     * @param array  $args
     * @param string $section
     *
     * @use   wc_get_template()
     * @since 1.0
     * @return void
     */
    function yith_wcpv_get_template( $filename, $args = array(), $section = '' ) {

        $ext           = strpos( $filename, '.php' ) === false ? '.php' : '';
        $template_name = $section . '/' . $filename . $ext;
        $template_path = WC()->template_path();
        $default_path  = YITH_WPV_TEMPLATE_PATH;

        if ( defined( 'YITH_WPV_PREMIUM' ) ) {
            $premium_template = str_replace( '.php', '-premium.php', $template_name );
            $located_premium  = wc_locate_template( $premium_template, $template_path, $default_path );
            $template_name    = file_exists( $located_premium ) ? $premium_template : $template_name;
        }

        wc_get_template( $template_name, $args, $template_path, $default_path );
    }
}

if ( !function_exists( 'yith_wcpv_check_duplicate_term_name' ) ) {
    /**
     * Check for duplicate vendor name
     *
     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
     *
     * @param $term     string The term name
     * @param $taxonomy string The taxonomy name
     *
     * @return mixed term object | WP_Error
     * @since    1.0
     */
    function yith_wcpv_check_duplicate_term_name( $term, $taxonomy ) {
        $duplicate = get_term_by( 'name', $term, $taxonomy );

        return $duplicate ? true : false;
    }
}

if ( !function_exists( 'yith_has_live_chat_plugin' ) ) {
    /**
     * Check if user has YITH Live Chat Premium plugin
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0
     * @return bool
     */
    function yith_has_live_chat_plugin() {
        return defined( 'YLC_PREMIUM' ) && YLC_PREMIUM && defined( 'YLC_VERSION' ) && version_compare( YLC_VERSION, apply_filters( 'yith_wcmv_live_chat_min_version', '1.0.5' ), '>' );
    }
}

if ( !function_exists( 'yith_has_membership_plugin' ) ) {
    /**
     * Check if user has YITH WooCommerce Membership Premium plugin
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0
     * @return bool
     */
    function yith_has_membership_plugin() {
        return defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM;
    }
}