<?php

/**
 * Database Version Update
 */

//Add support to YITH Product Vendors db version 1.0.1
function yith_vendors_update_db_1_0_1() {
    $vendors_db_option = get_option( 'yith_product_vendors_db_version' );
    if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.1', '<' ) ) {
        global $wpdb;

        $sql = "SELECT woocommerce_term_id as vendor_id, meta_value as user_id
                    FROM {$wpdb->woocommerce_termmeta} as wtm
                    WHERE wtm.meta_key = %s
                    AND woocommerce_term_id IN (
                        SELECT DISTINCT term_id as vendor_id
                        FROM {$wpdb->term_taxonomy} as tt
                        WHERE tt.taxonomy = %s
                    )";

        $results = $wpdb->get_results( $wpdb->prepare( $sql, 'owner', YITH_Vendors()->get_taxonomy_name() ) );

        foreach ( $results as $result ) {
            $user = get_user_by( 'id', $result->user_id );

            if ( $user ) {
                update_woocommerce_term_meta( $result->vendor_id, 'registration_date', get_date_from_gmt( $user->user_registered ) );
                update_woocommerce_term_meta( $result->vendor_id, 'registration_date_gmt', $user->user_registered );
                if( defined( 'YITH_WPV_PREMIUM' ) ){
                    $user->add_cap( 'view_woocommerce_reports' );
                }
            }
        }

        update_option( 'yith_product_vendors_db_version', '1.0.1' );
    }
}

//Add support to YITH Product Vendors db version 1.0.2
function yith_vendors_update_db_1_0_2() {
    $vendors_db_option = get_option( 'yith_product_vendors_db_version' );
    if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.2', '<' ) ) {
        global $wpdb;

        $sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_commissions` CHANGE `rate` `rate` DECIMAL(5,4) NOT NULL";
        $wpdb->query( $sql );

        update_option( 'yith_product_vendors_db_version', '1.0.2' );
    }
}

//Add support to YITH Product Vendors db version 1.0.6
function yith_vendors_update_db_1_0_3(){
    $vendors_db_option = get_option( 'yith_product_vendors_db_version' );
    if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.3', '<' ) ) {
        /**
         * Create "Become a Vendor" Page
         */
         if( defined( 'YITH_WPV_PREMIUM' ) ){
            $become_a_vendor_page = get_option( 'yith_wpv_become_a_vendor_page_id' );
            $become_a_vendor_page === false && YITH_Vendors_Admin_Premium::create_become_a_vendor_page();
         }

        /**
         * Show Gravatar Option
         */
        $vendors = YITH_Vendors()->get_vendors();
        foreach( $vendors as $vendor ){
            if( empty( $vendor->show_gravatar ) )  {
                $vendor->show_gravatar = 'yes';
            }
        }
        update_option( 'yith_product_vendors_db_version', '1.0.3' );
    }
}

add_action( 'admin_init', 'yith_vendors_update_db_1_0_1' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_2' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_3' );

