<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Orders
 * @package    Yithemes
 * @since      Version 1.6
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Orders' ) ) {

    class YITH_Orders {

        /**
         * Main instance
         *
         * @var string
         * @since 1.4.0
         */
        protected static $_instance = null;

        /**
         * Constructor
         */
        public function __construct() {
            add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'check_suborder' ), 10, 2 );

            /* Prevent Multiple Email Notifications for Suborders */
            add_filter( 'woocommerce_email_recipient_new_order', array( $this, 'woocommerce_email_recipient_new_order' ), 10, 2 );
            add_filter( 'woocommerce_email_recipient_cancelled_order', array( $this, 'woocommerce_email_recipient_new_order' ), 10, 2 );
            add_filter( 'woocommerce_email_enabled_customer_processing_order', array( $this, 'woocommerce_email_enabled_new_order' ), 10, 2 );
            add_filter( 'woocommerce_email_enabled_customer_completed_order', array( $this, 'woocommerce_email_enabled_new_order' ), 10, 2 );
            add_filter( 'woocommerce_email_enabled_customer_partially_refunded_order', array( $this, 'woocommerce_email_enabled_new_order' ), 10, 2 );
            add_filter( 'woocommerce_email_enabled_customer_refunded_order', array( $this, 'woocommerce_email_enabled_new_order' ), 10, 2 );

            add_action( 'woocommerce_order_refunded', array( $this, 'order_refunded' ), 10, 2 );
            add_action( 'woocommerce_refund_deleted', array( $this, 'refund_deleted' ), 10, 2 );

            add_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10, 3 );

            /* Single Order Page for Vendor */
            add_filter( 'wc_order_is_editable', array( $this, 'vendor_single_order_page' ) );

            add_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10, 3 );

            /* Single Order Page for Vendor */
            add_filter( 'wc_order_is_editable', array( $this, 'vendor_single_order_page' ) );

             /* Order Item Meta */
            add_action( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );

            /* Order Table */
            add_filter( 'manage_shop_order_posts_columns', array( $this, 'shop_order_columns' ) );
            add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_shop_order_columns' ) );

            /* Order MetaBoxes */
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

            $sync_enabled = get_option( 'yith_wpv_vendors_option_order_synchronization', 'yes' );

            if( $sync_enabled ){
                /* SubOrder Sync */
                add_action( 'woocommerce_order_status_changed', array( $this, 'suborder_status_synchronization' ), 10, 3 );
                /* Order Meta Synchronization */
                add_action( 'woocommerce_process_shop_order_meta', array( $this, 'suborder_meta_synchronization' ), 65, 2 );
                /* Commission  Synchronization */
                add_action( 'yith_wcmv_after_single_register_commission', array( $this, 'register_commission_to_parent_order' ), 10, 4 );

                /**
                 * Other Ajax Action:
                 *
                 * load_order_items
                 * woocommerce_EVENT => nopriv
                 */
                $ajax_events = array(
                    'add_order_item'                => false,
                    /*'add_order_fee'               => false,*/
                    /*'add_order_shipping'          => false,*/
                    'add_order_tax'                 => false,
                    'remove_order_item'             => false,
                    'remove_order_tax'              => false,
                    'reduce_order_item_stock'       => false,
                    'increase_order_item_stock'     => false,
                    /*'add_order_item_meta'         => false, */
                    'remove_order_item_meta'        => false,
                    'calc_line_taxes'               => false,
                    'save_order_items'              => false,
                    'add_order_note'                => false,
                    'delete_order_note'             => false,
                );

                foreach ( $ajax_events as $ajax_event => $nopriv ) {
                    add_action( "wp_ajax_woocommerce_{$ajax_event}", array( __CLASS__, $ajax_event ), 5 );
                    $nopriv && add_action( "wp_ajax_nopriv_woocommerce_{$ajax_event}", array( __CLASS__, $ajax_event, 5 ) );
                }
            }
        }

        /**
         * Check for vendor sub-order
         *
         * $parent_order_id string The parent order id
         * $posted          mixed  Array of posted form data.
         *
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @since   1.6
         * @return  void
         */
        public function check_suborder( $parent_order_id, $posted ) {
            $parent_order       = wc_get_order( $parent_order_id );
            $items              = $parent_order->get_items();
            $products_by_vendor = array();
            $suborder_ids       = array();

            //check for vendor product
            foreach ( $items as $item ) {
                $vendor = yith_get_vendor( $item['product_id'], 'product' );
                if ( $vendor->is_valid() ) {
                    $products_by_vendor[$vendor->id][] = $item;
                }
            }

            $vendor_count = count( $products_by_vendor );

            //Vendor's items ? NO
            if ( $vendor_count == 0 ) {
                return false;
            }

            //Vendor's items ? YES
            else {
                //add sub-order to parent
                update_post_meta( $parent_order_id, 'has_sub_order', true );

                foreach ( $products_by_vendor as $vendor_id => $vendor_products ) {
                    //create sub-orders
                    $suborder_ids[] = $this->create_suborder( $parent_order, $vendor_id, $vendor_products, $posted );
                }

                if( ! empty( $suborder_ids ) ){
                    foreach( $suborder_ids as $suborder_id ){
                        do_action( 'yith_wcmv_checkout_order_processed', $suborder_id );
                    }
                }
            }
        }

        /**
         * Create vendor sub-order
         *
         *
         * @param $parent_order WC_Order
         * @param $vendor_id
         * @param $vendor_products
         * @param $posted
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.6
         * @return void
         */
        public function create_suborder( $parent_order, $vendor_id, $vendor_products, $posted ) {
            /** @var $parent_order WC_Order */
            $vendor     = yith_get_vendor( $vendor_id, 'vendor' );
            $order_data = apply_filters( 'woocommerce_new_order_data', array(
                    'post_type'     => 'shop_order',
                    'post_title'    => sprintf( __( 'Order &ndash; %s', 'woocommerce' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'woocommerce' ) ) ),
                    'post_status'   => 'wc-' . apply_filters( 'woocommerce_default_order_status', 'pending' ),
                    'ping_status'   => 'closed',
                    'post_excerpt'  => isset( $posted['order_comments'] ) ? $posted['order_comments'] : '',
                    'post_author'   => $vendor->get_owner(),
                    'post_parent'   => $parent_order->id,
                    'post_password' => uniqid( 'order_' ) // Protects the post just in case
                )
            );

            $suborder_id       = wp_insert_post( $order_data );
            $suborder          = wc_get_order( $suborder_id );
            $parent_line_items = $parent_order->get_items( 'line_item' );

            if ( ! empty( $suborder_id ) && ! is_wp_error( $suborder_id ) ) {
                $order_total = $order_tax = $discount = 0;
                $product_ids = array();

                // now insert line items
                foreach ( $vendor_products as $item ) {
                    $order_total += (float) $item['line_total'];
                    $order_tax += (float) $item['line_tax'];
                    $product_ids[] = $item['product_id'];

                    $item_id = wc_add_order_item( $suborder_id, array(
                            'order_item_name' => $item['name'],
                            'order_item_type' => 'line_item'
                        )
                    );

                    if ( $item_id ) {
                        $metakeys = array(
                            'qty',
                            'tax_class',
                            'product_id',
                            'variation_id',
                            'line_subtotal',
                            'line_total',
                            'line_tax',
                            'line_subtotal_tax',
                            'line_tax_data'
                        );

                        foreach ( $metakeys as $key ) {
                            /**
                             * Use maybe_unserialize() because wc_add_order_item_meta()
                             * use maybe_serialize() that reserialize the serialized string
                             * for backward compatibility and to prevent the end of the world.
                             *
                             * @see wp-includes/functions.php:382
                             * @see woocommerce/includes/wc-order-functions.php:449
                             * @use wp-includes/meta.php:31
                             */
                            wc_add_order_item_meta( $item_id, "_{$key}", maybe_unserialize( $item[$key] ) );
                            if ( 'product_id' == $key ) {
                                foreach ( $parent_line_items as $line_item_id => $line_item_value ) {
                                    //@internal $key == 'product_id'
                                    if ( $item[$key] == $line_item_value[$key] ) {
                                        // add line item to retrieve simply the parent line_item_id
                                        wc_add_order_item_meta( $item_id, '_parent_line_item_id', $line_item_id );
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    
                    //Calculate Discount
                    $discount += ( $item['line_subtotal'] - $item['line_total'] );
                }

                //Billing order meta
                $billing_order_meta = array(
                    'billing_country',
                    'billing_first_name',
                    'billing_last_name',
                    'billing_company',
                    'billing_address_1',
                    'billing_address_2',
                    'billing_city',
                    'billing_state',
                    'billing_postcode',
                    'billing_email',
                    'billing_phone'
                );

                foreach ( $billing_order_meta as $order_meta_key ) {
                    $meta_key = '_' . $order_meta_key;
                    update_post_meta( $suborder_id, $meta_key, $parent_order->$order_meta_key );
                }

                //Shipping order meta
                $shipping_order_meta = array(
                    'shipping_country',
                    'shipping_first_name',
                    'shipping_last_name',
                    'shipping_company',
                    'shipping_address_1',
                    'shipping_address_2',
                    'shipping_city',
                    'shipping_state',
                    'shipping_postcode'
                );

                foreach ( $shipping_order_meta as $order_meta_key ) {
                    $meta_key = '_' . $order_meta_key;
                    update_post_meta( $suborder_id, $meta_key, $parent_order->$order_meta_key );
                }

                //Shipping
                //TODO: to add when vendor can manage shipping
                $shipping_cost = 0;

                //Coupons
                $order_coupons = $parent_order->get_used_coupons();
                if ( ! empty( $order_coupons ) ) {
                    foreach ( $order_coupons as $order_coupon ) {
                        $coupon = new WC_Coupon( $order_coupon );

                        if ( $coupon && in_array( $product_ids, $coupon->product_ids ) ) {
                            $order_item_id = wc_add_order_item( $suborder_id, array(
                                    'order_item_name' => $order_coupon,
                                    'order_item_type' => 'coupon'
                                )
                            );

                            // Add line item meta
                            if ( $order_item_id ) {
                                $order_item_value = isset( WC()->cart->coupon_discount_amounts[$order_coupon] ) ? WC()->cart->coupon_discount_amounts[$order_coupon] : 0;
                                $meta_key         = 'discount_amount';
                                wc_add_order_item_meta( $order_item_id, $meta_key, $order_item_value );
                            }
                        }
                    }
                }

                //Calculate Total
                $order_in_total = $order_total + $shipping_cost + $order_tax;

                $totals = array(
                    'shipping'           => wc_format_decimal( $shipping_cost ),
                    'cart_discount'      => wc_format_decimal( $discount ),
                    'cart_discount_tax'  => 0,
                    'tax'                => wc_format_decimal( $order_tax ),
                    'order_shipping_tax' => 0,
                    'total'              => wc_format_decimal( $order_in_total ),
                );

                //Set tax. N.B.: needs total to works
                if ( function_exists( 'WC' ) && is_a( WC()->cart, 'WC_Cart' ) ) {
                    /** @var WC_Cart $cart */
                    $_cart = WC()->cart;
                    foreach ( array_keys( $_cart->taxes + $_cart->shipping_taxes ) as $tax_rate_id ) {
                        if ( $_cart && $tax_rate_id && apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' ) !== $tax_rate_id ) {
                            $suborder->add_tax( $tax_rate_id, $totals['tax'], $totals['order_shipping_tax'] );
                        }
                    }
                }

                //Set totals
                foreach ( $totals as $meta_key => $meta_value ) {
                    $suborder->set_total( $meta_value, $meta_key );
                }

                //Set other order meta
                $order_meta = array(
                    '_payment_method'       => $parent_order->payment_method,
                    '_payment_method_title' => $parent_order->payment_method_title,
                    '_order_key'            => apply_filters( 'woocommerce_generate_order_key', uniqid( 'order_' ) ),
                    '_customer_user'        => $parent_order->customer_user,
                    '_prices_include_tax'   => $parent_order->prices_include_tax,
                    '_order_currency'       => get_post_meta( $parent_order->id, '_order_currency', true ),
                    '_customer_ip_address'  => get_post_meta( $parent_order->id, '_customer_ip_address', true ),
                    '_customer_user_agent'  => get_post_meta( $parent_order->id, '_customer_user_agent', true )
                );

                foreach ( $order_meta as $meta_key => $meta_value ) {
                    update_post_meta( $suborder_id, $meta_key, $meta_value );
                }
            }

            return $suborder_id;
        }

        /**
         * Parent to Child synchronization
         *
         *
         * @param $parent_order_id  The parent id order
         * @param $old_status       Old Status
         * @param $new_status       New Status
         *
         * @internal param \WC_Order $parent_order
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return void
         */
        public function suborder_status_synchronization( $parent_order_id, $old_status, $new_status ) {
            //Check if order have sub-order
            if ( wp_get_post_parent_id( $parent_order_id ) ) {
                return false;
            }

            $suborder_ids = self::get_suborder( $parent_order_id );
            if ( ! empty( $suborder_ids ) ) {
                foreach ( $suborder_ids as $suborder_id ) {
                    /** @var $suborder WC_Order */
                    $suborder = wc_get_order( $suborder_id );
                    $suborder->update_status( $new_status, _x( 'Update by admin: ', 'Order note', 'yith_wc_product_vendors' ) );
                }
            }
        }

        /**
         * Parent to Child synchronization
         *
         *
         * @param $parent_order_id  The parent id order
         * @param $parent_order     The parent order
         *
         * @internal param \WC_Order $parent_order
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return void
         */
        public function suborder_meta_synchronization( $parent_order_id, $parent_order ) {
            //Check if order have sub-order
            if ( wp_get_post_parent_id( $parent_order_id ) ) {
                return false;
            }

            /** @var $suborder WC_Order */
            /** @var $parent_order WC_Order */
            $suborder_ids = self::get_suborder( $parent_order_id );
            $parent_order = wc_get_order( $parent_order_id );
            if ( ! empty( $suborder_ids ) ) {
                foreach ( $suborder_ids as $suborder_id ) {
                    $suborder               = wc_get_order( $suborder_id );
                    $child_items            = array_keys( $suborder->get_items() );
                    $_post                  = $_POST;
                    $_post['order_item_id'] = $child_items;
                    $suborder_line_total    = 0;

                    foreach ( $child_items as $child_items_id ) {
                        $parent_item_id = $suborder->get_item_meta( $child_items_id, '_parent_line_item_id' );
                        $parent_item_id = absint( array_shift( $parent_item_id ) );
                        foreach ( $_post as $meta_key => $meta_value ) {
                            //TODO: Shipping Cost
                            switch ( $meta_key ) {
                                case 'line_total':
                                case 'line_subtotal':
                                case 'order_item_tax_class':
                                case 'order_item_qty':
                                case 'refund_line_total':
                                case 'refund_order_item_qty':
                                    if ( isset( $_post[$meta_key][$parent_item_id] ) ) {
                                        $_post[$meta_key][$child_items_id] = $_post[$meta_key][$parent_item_id];
                                        unset( $_post[$meta_key][$parent_item_id] );
                                    }
                                    break;

                                case 'shipping_cost':
                                    if ( isset( $_post[$meta_key][$parent_item_id] ) ) {
                                        $_post[$meta_key][$child_items_id] = 0;
                                        unset( $_post[$meta_key][$parent_item_id] );
                                    }
                                    break;
                                default: //nothing to do
                                    break;
                            }
                        }

                        //Calculate Order Total
                        if ( isset( $_post['line_total'][$child_items_id] ) ) {
                            $suborder_line_total += wc_format_decimal( $_post['line_total'][$child_items_id] );
                        }
                    }

                    //New Order Total
                    $_post['_order_total'] = wc_format_decimal( $suborder_line_total );

                    /**
                     * Don't use save method by WC_Meta_Box_Order_Items class because I need to filter the POST information
                     * use wc_save_order_items( $order_id, $items ) function directly.
                     * @see WC_Meta_Box_Order_Items::save( $suborder_id, $suborder ); in woocommerce\includes\admin\meta-boxes\class-wc-meta-box-order-items.php:45
                     * @see wc_save_order_items( $order_id, $items ); in woocommerce\includes\admin\wc-admin-functions.php:176
                     */
                    wc_save_order_items( $suborder_id, $_post );
                    WC_Meta_Box_Order_Downloads::save( $suborder_id, $suborder );
                    WC_Meta_Box_Order_Data::save( $suborder_id, $suborder );
                    WC_Meta_Box_Order_Actions::save( $suborder_id, $suborder );
                }
            }
        }

        /**
         * Get suborder from parent_order_id
         *
         *
         * @param bool|int $parent_order_id The parent id order
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return void
         */
        public static function get_suborder( $parent_order_id = false ) {
            $suborder_ids = array();
            if ( $parent_order_id ) {
                global $wpdb;
                $suborder_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent=%d AND post_type=%s", absint( $parent_order_id ), 'shop_order' ) );
            }

            return $suborder_ids;
        }

        /**
         * Get parent item id from child item id
         *
         *
         * @param $suborder         The suborder object
         * @param $child_item_id    The child item id
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return   int|bool The parent item id if exist, false otherwise
         */
        public static function get_parent_item_id( $suborder = false, $child_item_id ) {
            global$wpdb;
            $parent_item_id = false;

            if( ! $suborder ){
                $parent_item_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT order_item_id FROM {$wpdb->order_itemmeta} WHERE meta_id=%d", $child_item_id ) );
                $parent_item_id = ! empty( $parent_item_id ) ? $parent_item_id : false;
            }

            else {
                $parent_item_id = $suborder->get_item_meta( $child_item_id, '_parent_line_item_id' );
                $parent_item_id = ! empty( $parent_item_id ) ? absint( array_shift( $parent_item_id ) ) : false;
            }


            return $parent_item_id;
        }

        /**
         * Get parent item id from child item id
         *
         * @param $parent_item_id
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return   int|bool The parent item id if exist, false otherwise
         */
        public static function get_child_item_id( $parent_item_id ) {
            global $wpdb;
            $child_item_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_item_id FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND meta_value=%d", '_parent_line_item_id', absint( $parent_item_id ) ) );
            return $child_item_id;
        }

        /**
         * Get line item id from parent item id
         *
         * @param $order_item_id The parent order_item_id
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return   int|bool The child item id if exist, false otherwise
         */
        public static function get_line_item_id_from_parent( $order_item_id ){
            global $wpdb;
            return $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT order_item_id FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND meta_value=%d", '_parent_line_item_id', $order_item_id ) );
        }

        /**
         * Save order items ajax sync
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return void
         * @access public static
         */
        public static function save_order_items() {
            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            if ( isset( $_POST['order_id'] ) && isset( $_POST['items'] ) ) {
                $parent_order_id = absint( $_POST['order_id'] );
                //Check if order have sub-order
                if ( ! wp_get_post_parent_id( $parent_order_id ) ) {
                    global $wpdb;
                    // Parse the jQuery serialized items
                    $_post = $_POST;
                    parse_str( $_post['items'], $_post['items'] );
                    $suborder_ids = self::get_suborder( $parent_order_id );
                    foreach ( $suborder_ids as $suborder_id ) {
                        $order_total                     = 0;
                        $suborder                        = wc_get_order( $suborder_id );
                        $child_items                     = array_keys( $suborder->get_items() );
                        $_post['items']['order_item_id'] = $child_items;
                        foreach ( $child_items as $child_item_id ) {
                            $parent_item_id = self::get_parent_item_id( $suborder, $child_item_id );
                            foreach ( $_post['items'] as $meta_key => $meta_value ) {
                                if ( ! in_array( $meta_key, array( 'order_item_id', '_order_total' ) ) && isset( $_post['items'][$meta_key][$parent_item_id] ) ) {
                                    $_post['items'][$meta_key][$child_item_id] = $_post['items'][$meta_key][$parent_item_id];
                                    unset( $_post['items'][$meta_key][$parent_item_id] );
                                }
                            }

                            /* === Calc Order Totals === */
                            if ( ! empty( $_post['items']['line_total'][$child_item_id] ) ) {
                                $order_total += wc_format_decimal( $_post['items']['line_total'][$child_item_id] );
                                if( isset( $_post['items']['line_tax'][$child_item_id] ) ){
                                    $line_taxes = $_post['items']['line_tax'][$child_item_id];
                                    foreach( $line_taxes as $line_tax ){
                                        $order_total += wc_format_decimal( $line_tax );
                                    }
                                }
                            }

                            /* === Calc Refund Totals === */
                            if ( ! empty( $_post['items']['refund_line_total'][$child_item_id] ) ) {
                                $order_total += wc_format_decimal( $_post['items']['refund_line_total'][$child_item_id] );
                            }
                            /* ======================== */
                        }

                        /* === Save Parent Meta === */
                        $meta_keys   = isset( $_post['items']['meta_key'] )     ? $_post['items']['meta_key'] : array();
                        $meta_values = isset( $_post['items']['meta_value'] )   ? $_post['items']['meta_value'] : array();
                        foreach ( $meta_keys as $meta_id => $meta_key ) {
                            $meta_value           = ( empty( $meta_values[$meta_id] ) && ! is_numeric( $meta_values[$meta_id] ) ) ? '' : $meta_values[$meta_id];
                            $parent_order_item_id = self::get_parent_item_id( false, $meta_id );
                            $child_order_item_id  = self::get_child_item_id( $parent_order_item_id );
                            '_child__commission_id' != $meta_key && wc_update_order_item_meta( $child_order_item_id, '_parent_' . $meta_key, '_commission_id' != $meta_key ? $meta_id : $meta_values[ $meta_id ] );
                        }
                        /* ======================== */

                        // Add order total
                        $_post['items']['_order_total'] = $order_total;

                        // Save order items
                        wc_save_order_items( $suborder_id, $_post['items'] );
                    }
                }

                else {
                    //is suborder
                    //TODO: Suborder sub-routine
                }
            }
        }

        /**
         * Remove order items ajax sync
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.6
         * @return void
         * @access public static
         */
        public static function remove_order_item() {
            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $order_item_ids = $_POST['order_item_ids'];
            if ( ! is_array( $order_item_ids ) && is_numeric( $order_item_ids ) ) {
                $order_item_ids = array( $order_item_ids );
            }
            //TODO: add check order_id if ( ! wp_get_post_parent_id( $parent_order_id ) ) {
            if ( sizeof( $order_item_ids ) > 0 ) {
                /** @var $wpdb wpdb */
                global $wpdb;
                foreach ( $order_item_ids as $order_item_id ) {
                    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND order_item_id=%d", '_product_id', absint( $order_item_id ) ) );
                    $vendor     = yith_get_vendor( $product_id, 'product' );
                    if ( $vendor->is_valid() ) {
                        $child_order_item_id = self::get_line_item_id_from_parent( $order_item_id );
                        ! empty( $child_order_item_id ) && wc_delete_order_item( absint( $child_order_item_id ) );
                    }
                }
            }
        }

        /**
         * Add WooCommerce order notes to suborder
         *
         * @since    1.6
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  void
         */
        public static function add_order_note() {

            check_ajax_referer( 'add-order-note', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $post_id   = absint( $_POST['post_id'] );
            $note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
            $note_type = $_POST['note_type'];

            $is_customer_note = $note_type == 'customer' ? 1 : 0;

            if ( $post_id > 0 ) {
                if ( ! wp_get_post_parent_id( $post_id ) ) {
                    //Add the order note to parent order
                    $order          = wc_get_order( $post_id );
                    $parent_note_id = $order->add_order_note( $note, $is_customer_note, true );

                    echo '<li rel="' . esc_attr( $parent_note_id ) . '" class="note ';
                    if ( $is_customer_note ) {
                        echo 'customer-note';
                    }
                    echo '"><div class="note_content">';
                    echo wpautop( wptexturize( $note ) );
                    echo '</div><p class="meta"><a href="#" class="delete_note">' . __( 'Delete note', 'woocommerce' ) . '</a></p>';
                    echo '</li>';

                    $suborder_ids = self::get_suborder( $post_id );
                    if ( ! empty( $suborder_ids ) ) {
                        foreach ( $suborder_ids as $suborder_id ) {
                            $suborder = wc_get_order( $suborder_id );
                            $note_id  = $suborder->add_order_note( _x( 'Update by admin: ', 'Order note', 'yith_wc_product_vendors' ) . $note, $is_customer_note, true );
                            add_comment_meta( $note_id, 'parent_note_id', $parent_note_id );
                        }
                    }
                    /**
                     * Call die(); to prevent WooCommerce action.
                     * Updated Parent and Child orders
                     */
                    die();
                }

                 else {
                    //is suborder
                    //TODO: Suborder sub-routine
                }
            }
        }

        /**
         * Remove WooCommerce order notes to suborder
         *
         * @since    1.6
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  void
         */
        public static function delete_order_note() {
            check_ajax_referer( 'delete-order-note', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            global $wpdb;
            $parent_note_id = absint( $_POST['note_id'] );
            $note_ids       = $wpdb->get_col( $wpdb->prepare( "SELECT comment_id FROM {$wpdb->commentmeta} WHERE meta_key=%s  AND meta_value=%d", 'parent_note_id', $parent_note_id ) );

            if ( ! empty( $note_ids ) ) {
                foreach( $note_ids as $note_id ){
                    wp_delete_comment( $note_id );
                }
            }
        }

        /**
         * Reduce order item stock
         */
        public static function reduce_order_item_stock() {
            self::order_item_stock( 'reduce' );
        }

         /**
         * Increase order item stock
         */
        public static function increase_order_item_stock() {
            self::order_item_stock( 'increase' );
        }

        /**
         * Reduce order item stock
         */
        public static function order_item_stock( $ajax_call_type ) {
            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $order_id  = absint( $_POST['order_id'] );
            if ( ! wp_get_post_parent_id( $order_id ) ) {
                $order_item_ids = isset( $_POST['order_item_ids'] ) ? $_POST['order_item_ids'] : array();
                $order_item_qty = isset( $_POST['order_item_qty'] ) ? $_POST['order_item_qty'] : array();
                $order          = wc_get_order( $order_id );
                $order_items    = $order->get_items();

                if ( $order && ! empty( $order_items ) && sizeof( $order_item_ids ) > 0 ) {

                    foreach ( $order_items as $item_id => $order_item ) {
                        // Only reduce checked items
                        if ( ! in_array( $item_id, $order_item_ids ) ) {
                            continue;
                        }

                        $_product   = $order->get_product_from_item( $order_item );
                        $vendor     = yith_get_vendor( $_product, 'product' );
                        if ( $vendor->is_valid() && $_product->exists() && $_product->managing_stock() && isset( $order_item_qty[$item_id] ) && $order_item_qty[$item_id] > 0 ) {
                            global $wpdb;

                            $old_stock           = $_product->get_stock_quantity();
                            $child_order_item_id = self::get_line_item_id_from_parent( $item_id );
                            $suborder_id         = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id=%d", absint( $child_order_item_id ) ) );
                            $suborder            = wc_get_order( $suborder_id );
                            $note                = '';
                            if( 'reduce' == $ajax_call_type ){
                                $stock_change   = apply_filters( 'woocommerce_reduce_order_stock_quantity', $order_item_qty[$item_id], $item_id );
                                $new_stock      = $old_stock - $stock_change;
                                $note           = sprintf( __( 'Item #%s stock reduced from %s to %s.', 'woocommerce' ), $order_item['product_id'], $old_stock, $new_stock );
                            }

                            elseif( 'increase' == $ajax_call_type ) {
                                $stock_change   = apply_filters( 'woocommerce_restore_order_stock_quantity', $order_item_qty[$item_id], $item_id );
                                $new_stock      = $old_stock + $stock_change;
                                $note           = sprintf( __( 'Item #%s stock increased from %s to %s.', 'woocommerce' ), $order_item['product_id'], $old_stock, $new_stock );
                            }

                            ! empty( $note ) && $suborder->add_order_note( $note );
                        }
                    }
                }
            }

            else {
                    //is suborder
                    //TODO: Suborder sub-routine
                }
        }

        /**
         * Remove order item meta
         */
        public static function remove_order_item_meta(){
            global $wpdb;

            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $parent_meta_id     = absint( $_POST['meta_id'] );
            $parent_meta_key    = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT meta_key FROM {$wpdb->order_itemmeta} WHERE meta_id=%d", $parent_meta_id ) );
            $child_meta_id      = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT meta_id FROM {$wpdb->order_itemmeta} WHERE meta_value=%d AND meta_key=%s", $parent_meta_id, '_parent_' . $parent_meta_key ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND meta_id=%d", '_parent_' . $parent_meta_key, $child_meta_id ) );
        }

        /**
         * Add order item via ajax
         */
        public static function add_order_item() {
            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $item_to_add = sanitize_text_field( $_POST['item_to_add'] );
            $order_id    = absint( $_POST['order_id'] );

            if ( ! wp_get_post_parent_id( $order_id ) ) {
                // Find the item
                if ( ! is_numeric( $item_to_add ) ) {
                    die();
                }

                $post = get_post( $item_to_add );

                if ( ! $post || ( 'product' !== $post->post_type && 'product_variation' !== $post->post_type ) ) {
                    die();
                }

                $_product     = wc_get_product( $post->ID );
                $order        = wc_get_order( $order_id );
                $order_taxes  = $order->get_taxes();
                $class        = 'new_row';
                $suborders_id = 0;

                $vendor = yith_get_vendor( $item_to_add, 'product' );

                if ( $vendor->is_valid() ) {
                    $vendor_suborder_id = $vendor->get_orders( 'suborder' );
                    $suborders_ids      = self::get_suborder( $order_id );
                    $suborder_id        = array_intersect( $vendor_suborder_id, $suborders_ids );

                    if ( is_array( $suborder_id ) && count( $suborder_id ) == 1 ) {
                        $suborder_id = array_shift( $suborder_id );
                    }
                }

                // Set values
                $item     = array();
                $item_ids = array();

                $item['product_id']        = $_product->id;
                $item['variation_id']      = isset( $_product->variation_id ) ? $_product->variation_id : '';
                $item['variation_data']    = $item['variation_id'] ? $_product->get_variation_attributes() : '';
                $item['name']              = $_product->get_title();
                $item['tax_class']         = $_product->get_tax_class();
                $item['qty']               = 1;
                $item['line_subtotal']     = wc_format_decimal( $_product->get_price_excluding_tax() );
                $item['line_subtotal_tax'] = '';
                $item['line_total']        = wc_format_decimal( $_product->get_price_excluding_tax() );
                $item['line_tax']          = '';
                $item['type']              = 'line_item';

                // Add line item

                foreach ( array( 'parent_id' => $order_id, 'child_id' => $suborder_id ) as $type => $id ) {
                    $item_ids[$type] = wc_add_order_item( $id, array(
                        'order_item_name' => $item['name'],
                        'order_item_type' => 'line_item'
                    ) );
                }

                wc_add_order_item_meta( $item_ids['child_id'], '_parent_line_item_id', $item_ids['parent_id'] );

                foreach ( $item_ids as $key => $item_id ) {
                    // Add line item meta
                    if ( $item_id ) {
                        wc_add_order_item_meta( $item_id, '_qty', $item['qty'] );
                        wc_add_order_item_meta( $item_id, '_tax_class', $item['tax_class'] );
                        wc_add_order_item_meta( $item_id, '_product_id', $item['product_id'] );
                        wc_add_order_item_meta( $item_id, '_variation_id', $item['variation_id'] );
                        wc_add_order_item_meta( $item_id, '_line_subtotal', $item['line_subtotal'] );
                        wc_add_order_item_meta( $item_id, '_line_subtotal_tax', $item['line_subtotal_tax'] );
                        wc_add_order_item_meta( $item_id, '_line_total', $item['line_total'] );
                        wc_add_order_item_meta( $item_id, '_line_tax', $item['line_tax'] );

                        // Since 2.2
                        wc_add_order_item_meta( $item_id, '_line_tax_data', array( 'total' => array(), 'subtotal' => array() ) );

                        // Store variation data in meta
                        if ( $item['variation_data'] && is_array( $item['variation_data'] ) ) {
                            foreach ( $item['variation_data'] as $key => $value ) {
                                wc_add_order_item_meta( $item_id, str_replace( 'attribute_', '', $key ), $value );
                            }
                        }

                        do_action( 'woocommerce_ajax_add_order_item_meta', $item_id, $item );
                    }
                }

                $item['item_meta']       = $order->get_item_meta( $item_ids['parent_id'] );
                $item['item_meta_array'] = $order->get_item_meta_array( $item_ids['parent_id'] );
                $item                    = $order->expand_item_meta( $item );
                $item                    = apply_filters( 'woocommerce_ajax_order_item', $item, $item_ids['parent_id'] );

                /**
                 * WooCommerce Template Hack:
                 * Copy the parent item id into the variable $item_id
                 */
                $item_id = $item_ids['parent_id'];
                include( WC()->plugin_path() . '/includes/admin/meta-boxes/views/html-order-item.php' );

                /**
                 * Prevent call default WooCommerce add_order_item() method
                 */
                die();
            }

            else {
                //is suborder
                //TODO: Suborder sub-routine
            }
        }

        /**
         * Add commission id from parent to child order
         */
        public function register_commission_to_parent_order( $commission_id, $child_item_id, $key, $suborder ){
            // add line item to retrieve simply the commission associated (child order)
            $parent_item_id = self::get_parent_item_id( $suborder, $child_item_id );
            ! empty( $parent_item_id ) && wc_add_order_item_meta( $parent_item_id, '_child_' . $key, $commission_id );
        }

        /**
         * Add order tax column via ajax
         */
        public static function add_order_tax() {
            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $order_id = absint( $_POST['order_id'] );

            if ( ! wp_get_post_parent_id( $order_id ) ) {
                $rate_id = absint( $_POST['rate_id'] );
                $suborder_ids = self::get_suborder( $order_id );

                foreach( $suborder_ids as $suborder_id ){
                    $suborder = ! empty( $suborder_id ) ? wc_get_order( absint( $suborder_id ) ) : false;
                    $suborder && $suborder->add_tax( $rate_id, 0, 0 );
                }
            }

            else {
                //is suborder
                //TODO: Suborder sub-routine
            }
        }

        /**
         * Calc line tax
         */
        public static function calc_line_taxes() {
            check_ajax_referer( 'calc-totals', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $order_id     = absint( $_POST['order_id'] );

            if ( ! wp_get_post_parent_id( $order_id ) ) {
                $_post        = $_POST;
                $suborder_ids = self::get_suborder( $order_id );

                foreach ( $suborder_ids as $suborder_id ) {
                    self::add_line_taxes( $suborder_id );
                }
            }

            else {
                //is suborder
                //TODO: Suborder sub-routine
            }
        }

        public static function add_line_taxes( $order_id ) {
            global $wpdb;

            check_ajax_referer( 'calc-totals', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $tax            = new WC_Tax();
            $items          = array();
            $country        = strtoupper( esc_attr( $_POST['country'] ) );
            $state          = strtoupper( esc_attr( $_POST['state'] ) );
            $postcode       = strtoupper( esc_attr( $_POST['postcode'] ) );
            $city           = wc_clean( esc_attr( $_POST['city'] ) );
            $order          = wc_get_order( absint( $order_id ) );
            $taxes          = array();
            $shipping_taxes = array();

            // Parse the jQuery serialized items
            parse_str( $_POST['items'], $items );

            // Prevent undefined warnings
            if ( ! isset( $items['line_tax'] ) ) {
                $items['line_tax'] = array();
            }

            if ( ! isset( $items['line_subtotal_tax'] ) ) {
                $items['line_subtotal_tax'] = array();
            }

            $items['order_taxes'] = array();

            // Action
            $items = apply_filters( 'woocommerce_ajax_calc_line_taxes', $items, $order_id, $country, $_POST );

            // Get items and fees taxes
            if ( isset( $items['order_item_id'] ) ) {
                $line_total = $line_subtotal = $order_item_tax_class = array();
                foreach ( $items['order_item_id'] as $parent_item_id ) {
                    $parent_item_id = absint( $parent_item_id );
                    $item_id        = self::get_child_item_id( $parent_item_id );

                    if( empty( $item_id ) ){
                        //no current suborder items
                        continue;
                    }

                    $line_total[$item_id]           = isset( $items['line_total'][$parent_item_id] ) ? wc_format_decimal( $items['line_total'][$parent_item_id] ) : 0;
                    $line_subtotal[$item_id]        = isset( $items['line_subtotal'][$parent_item_id] ) ? wc_format_decimal( $items['line_subtotal'][$parent_item_id] ) : $line_total[$parent_item_id];
                    $order_item_tax_class[$item_id] = isset( $items['order_item_tax_class'][$parent_item_id] ) ? sanitize_text_field( $items['order_item_tax_class'][$parent_item_id] ) : '';
                    $product_id                     = $order->get_item_meta( $item_id, '_product_id', true );

                    $vendor = yith_get_vendor( $product_id, 'product' );

                    if( ! $vendor->is_valid() ){
                        // no vnedor products
                        continue;
                    }

                    $vendor_order_ids = $vendor->get_orders( 'suborder' );

                    if( ! in_array( $order_id, $vendor_order_ids ) ){
                        // the current product isn't in the current suborder
                        continue;
                    }

                    // Get product details
                    if ( get_post_type( $product_id ) == 'product' ) {
                        $_product        = wc_get_product( $product_id );
                        $item_tax_status = $_product->get_tax_status();
                    }
                    else {
                        $item_tax_status = 'taxable';
                    }

                    if ( '0' !== $order_item_tax_class[$item_id] && 'taxable' === $item_tax_status ) {
                        $tax_rates = WC_Tax::find_rates( array(
                            'country'   => $country,
                            'state'     => $state,
                            'postcode'  => $postcode,
                            'city'      => $city,
                            'tax_class' => $order_item_tax_class[$item_id]
                        ) );

                        $line_taxes          = WC_Tax::calc_tax( $line_total[$item_id], $tax_rates, false );
                        $line_subtotal_taxes = WC_Tax::calc_tax( $line_subtotal[$item_id], $tax_rates, false );

                        // Set the new line_tax
                        foreach ( $line_taxes as $_tax_id => $_tax_value ) {
                            $items['line_tax'][$item_id][$_tax_id] = $_tax_value;
                        }

                        // Set the new line_subtotal_tax
                        foreach ( $line_subtotal_taxes as $_tax_id => $_tax_value ) {
                            $items['line_subtotal_tax'][$item_id][$_tax_id] = $_tax_value;
                        }

                        // Sum the item taxes
                        foreach ( array_keys( $taxes + $line_taxes ) as $key ) {
                            $taxes[$key] = ( isset( $line_taxes[$key] ) ? $line_taxes[$key] : 0 ) + ( isset( $taxes[$key] ) ? $taxes[$key] : 0 );
                        }
                    }
                }
            }

            // Get shipping taxes
            if ( isset( $items['shipping_method_id'] ) ) {
                $matched_tax_rates = array();

                $tax_rates = WC_Tax::find_rates( array(
                    'country'   => $country,
                    'state'     => $state,
                    'postcode'  => $postcode,
                    'city'      => $city,
                    'tax_class' => ''
                ) );

                if ( $tax_rates ) {
                    foreach ( $tax_rates as $key => $rate ) {
                        if ( isset( $rate['shipping'] ) && 'yes' == $rate['shipping'] ) {
                            $matched_tax_rates[$key] = $rate;
                        }
                    }
                }

                $shipping_cost = $shipping_taxes = array();

                foreach ( $items['shipping_method_id'] as $item_id ) {
                    $item_id                 = absint( $item_id );
                    $shipping_cost[$item_id] = isset( $items['shipping_cost'][$parent_item_id] ) ? wc_format_decimal( $items['shipping_cost'][$parent_item_id] ) : 0;
                    $_shipping_taxes         = WC_Tax::calc_shipping_tax( $shipping_cost[$item_id], $matched_tax_rates );

                    // Set the new shipping_taxes
                    foreach ( $_shipping_taxes as $_tax_id => $_tax_value ) {
                        $items['shipping_taxes'][$item_id][$_tax_id] = $_tax_value;

                        $shipping_taxes[$_tax_id] = isset( $shipping_taxes[$_tax_id] ) ? $shipping_taxes[$_tax_id] + $_tax_value : $_tax_value;
                    }
                }
            }

            // Remove old tax rows
            $order->remove_order_items( 'tax' );

            // Add tax rows
            foreach ( array_keys( $taxes + $shipping_taxes ) as $tax_rate_id ) {
                $order->add_tax( $tax_rate_id, isset( $taxes[$tax_rate_id] ) ? $taxes[$tax_rate_id] : 0, isset( $shipping_taxes[$tax_rate_id] ) ? $shipping_taxes[$tax_rate_id] : 0 );
            }

            // Create the new order_taxes
            foreach ( $order->get_taxes() as $tax_id => $tax_item ) {
                $items['order_taxes'][$tax_id] = absint( $tax_item['rate_id'] );
            }

            foreach ( $items as $meta_key => $meta_values ) {
                if ( is_array( $meta_values ) ) {
                    foreach ( $meta_values as $key => $meta_value ) {
                        if ( 'order_taxes' == $meta_key ) {
                            continue;
                        }

                        else if ( 'order_item_id' == $meta_key ) {
                            $child_item_id = self::get_child_item_id( $meta_value );
                            if ( $child_item_id ) {
                                $items[$meta_key][$key] = $child_item_id;
                            }

                            else {
                                unset( $items[$meta_key][$key] );
                            }
                        }

                        else if ( 'meta_key' == $meta_key || 'meta_value' == $meta_key ) {
                            unset( $items[$meta_key][$key] );
                        }

                        else {
                            if ( 'line_tax' == $meta_key || 'line_subtotal_tax' == $meta_key || 'refund_line_tax' == $meta_key ) {
                                $line_tax_ids   = $items[$meta_key];
                                $child_item_ids = array_keys( $order->get_items() );
                                foreach ( $line_tax_ids as $line_tax_id => $line_tax_value ) {
                                    if ( ! in_array( $line_tax_id, $child_item_ids ) ) {
                                        unset( $items[$meta_key][$line_tax_id] );
                                    }
                                }
                            }

                            else {
                                $child_item_id = self::get_child_item_id( $meta_value );
                                if ( $child_item_id ) {
                                    $items[$meta_key][$child_item_id] = $items[$meta_key][$key];
                                    unset( $items[$meta_key][$key] );
                                }
                            }
                        }
                    }
                }

                else if ( '_order_total' == $meta_key ) {
                    $items['_order_total'] = $order->get_total();
                }
            }

            if( ! empty( $items['order_item_id'] ) ) {
                wc_save_order_items( $order_id, $items );
            }
        }

        /**
         * Remove an order tax
         */
        public static function remove_order_tax() {

            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die( - 1 );
            }

            $order_id = absint( $_POST['order_id'] );

            if ( ! wp_get_post_parent_id( $order_id ) ) {
                $rate_id                = absint( $_POST['rate_id'] );
                $parent_order           = wc_get_order( $order_id );
                $parent_taxes           = $parent_order->get_taxes();
                $suborder_ids           = self::get_suborder( $order_id );
                $parent_tax_to_remove   =  $parent_taxes[ $rate_id ];

                foreach( $suborder_ids as $suborder_id ){
                    $suborder       = wc_get_order( $suborder_id );
                    $suborder_taxes = $suborder->get_taxes();
                    foreach( $suborder_taxes as $suborder_tax_key => $suborder_tax_item ){
                        $suborder_tax_item['rate_id'] == $parent_tax_to_remove['rate_id']
                        &&
                        $suborder_tax_item['name'] == $parent_tax_to_remove['name']
                        &&
                        $suborder_tax_item['label'] == $parent_tax_to_remove['label']
                        &&
                        wc_delete_order_item( $suborder_tax_key );
                    }
                }
            }

            else {
                //is suborder
                //TODO: Suborder sub-routine
            }
        }

        /**
         * Check for new order email
         */
        public function woocommerce_email_enabled_new_order( $enabled, $object ){
            return $enabled && is_a( $object, 'WC_Order' ) && wp_get_post_parent_id( $object->id ) != 0 ? false: $enabled;
        }

        /**
         * Check for email recipient
         */
        public function woocommerce_email_recipient_new_order( $recipient, $object ){
            return ( $recipient == get_option('recipient') || $recipient == get_option('admin_email') ) && $object instanceof WC_Order && wp_get_post_parent_id( $object->id ) ? false: $recipient;
        }

        /**
         * Handle a refund via the edit order screen.
         * Called after wp_ajax_woocommerce_refund_line_items action
         *
         * @use woocommerce_order_refunded action
         * @see woocommerce\includes\class-wc-ajax.php:2295
         */
        public function order_refunded( $order_id, $parent_refund_id ) {

            if ( ! wp_get_post_parent_id( $order_id ) ) {
                $create_refund              = true;
                $refund                     = false;
                $parent_line_item_refund    = 0;
                $refund_amount              = wc_format_decimal( sanitize_text_field( $_POST['refund_amount'] ) );
                $refund_reason              = sanitize_text_field($_POST['refund_reason']);
                $line_item_qtys             = json_decode(sanitize_text_field(stripslashes($_POST['line_item_qtys'])), true);
                $line_item_totals           = json_decode(sanitize_text_field(stripslashes($_POST['line_item_totals'])), true);
                $line_item_tax_totals       = json_decode(sanitize_text_field(stripslashes($_POST['line_item_tax_totals'])), true);
                $api_refund                 = $_POST['api_refund'] === 'true' ? true : false;
                $restock_refunded_items     = $_POST['restock_refunded_items'] === 'true' ? true : false;
                $order                      = wc_get_order( $order_id );
                $parent_order_total         = wc_format_decimal( $order->get_total() );
                $suborder_ids               = self::get_suborder( $order_id );

                //calculate line items total from parent order
                foreach ($line_item_totals as $item_id => $total) {
                    $parent_line_item_refund += wc_format_decimal($total);
                }

                foreach( $suborder_ids as $suborder_id ){
                    $suborder               = wc_get_order( $suborder_id );
                    $suborder_items_ids     = array_keys( $suborder->get_items() );
                    $suborder_total         = wc_format_decimal( $suborder->get_total() );
                    $max_refund             = wc_format_decimal($suborder_total - $suborder->get_total_refunded());
                    $child_line_item_refund = 0;

                    // Prepare line items which we are refunding
                    $line_items = array();
                    $item_ids   = array_unique(array_merge(array_keys($line_item_qtys, $line_item_totals)));

                    foreach ($item_ids as $item_id) {
                        $child_item_id = self::get_child_item_id( $item_id );
                        if( $child_item_id && in_array( $child_item_id, $suborder_items_ids  ) ){
                            $line_items[$child_item_id] = array('qty' => 0, 'refund_total' => 0, 'refund_tax' => array());
                        }
                    }

                    foreach ($line_item_qtys as $item_id => $qty) {
                        $child_item_id = self::get_child_item_id( $item_id );
                        if( $child_item_id && in_array( $child_item_id, $suborder_items_ids  ) ){
                            $line_items[$child_item_id]['qty'] = max($qty, 0);
                        }
                    }

                    foreach ($line_item_totals as $item_id => $total) {
                        $child_item_id = self::get_child_item_id( $item_id );
                        if( $child_item_id && in_array( $child_item_id, $suborder_items_ids  ) ){
                            $total = wc_format_decimal( $total );
                            $child_line_item_refund += $total;
                            $line_items[$child_item_id]['refund_total'] = $total;
                        }
                    }

                    foreach ($line_item_tax_totals as $item_id => $tax_totals) {
                        $child_item_id = self::get_child_item_id( $item_id );
                        if( $child_item_id && in_array( $child_item_id, $suborder_items_ids  ) ){
                            $line_items[$child_item_id]['refund_tax'] = array_map('wc_format_decimal', $tax_totals);
                        }
                    }

                    //calculate refund amount percentage
                    $suborder_refund_amount = ( ( ( $refund_amount - $parent_line_item_refund ) * $suborder_total ) / $parent_order_total );
                    $suborder_total_refund = wc_format_decimal($child_line_item_refund + $suborder_refund_amount) ;

                    if ( ! $refund_amount || $max_refund < $suborder_total_refund || 0 > $suborder_total_refund) {
                        /**
                         * Invalid refund amount.
                         * Check if suborder total != 0 create a partial refund, exit otherwise
                         */
                        $surplus                = wc_format_decimal( $suborder_total_refund - $max_refund );
                        $suborder_total_refund  = $suborder_total_refund - $surplus;
                        $create_refund          = $suborder_total_refund > 0 ? true : false;
                    }

                    if( $create_refund ) {
                        // Create the refund object
                        $refund = wc_create_refund(array(
                                'amount'     => $suborder_total_refund,
                                'reason'     => $refund_reason,
                                'order_id'   => $suborder->id,
                                'line_items' => $line_items
                            )
                        );

                        add_post_meta( $refund->id, '_parent_refund_id', $parent_refund_id );
                    }
                }
            }
        }

        /**
         * Handle a refund via the edit order screen.
         * Called after wp_ajax_woocommerce_delete_refund action
         *
         * @use woocommerce_refund_deleted action
         * @see woocommerce\includes\class-wc-ajax.php:2328
         */
        public static function refund_deleted( $refund_id, $parent_order_id ) {
            check_ajax_referer( 'order-item', 'security' );

            if ( ! current_user_can( 'edit_shop_orders' ) ) {
                die(-1);
            }

            if ( ! wp_get_post_parent_id( $parent_order_id ) ) {
                global $wpdb;
                $child_refund_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key=%s AND meta_value=%s", '_parent_refund_id', $refund_id ) );

                foreach( $child_refund_ids as $child_refund_id ){
                    if ( $child_refund_id && 'shop_order_refund' === get_post_type( $child_refund_id ) ) {
                        $order_id = wp_get_post_parent_id( $child_refund_id );
                        wc_delete_shop_order_transients( $order_id );
                        wp_delete_post( $child_refund_id );
                    }
                }
            }
        }

        /**
         * Change commission label value
         *
         * @param           $attribute_label  The Label Value
         * @param           $meta_key         The Meta Key value
         * @param bool|\The $product          The Product object
         *
         * @return string           The label value
         */
        public function commissions_attribute_label( $attribute_label, $meta_key, $product = false ) {
            global $pagenow;

            if ( $product && 'post.php' == $pagenow && isset( $_GET['post'] ) && $order = wc_get_order( $_GET['post'] ) ) {
                $line_items    = $order->get_items( 'line_item' );
                $item_meta_key = wp_get_post_parent_id( $order->id ) ? '_commission_id' : '_child__commission_id';
                foreach ( $line_items as $line_item_id => $line_item ) {
                    if ( $line_item['product_id'] == $product->id ) {
                        $commission_id   = wc_get_order_item_meta( $line_item_id, $item_meta_key, true );
                        $admin_url       = YITH_Commission( $commission_id )->get_view_url( 'admin' );
                        $attribute_label = $item_meta_key == $meta_key ? sprintf( "<a href='%s' class='%s'>" . __( 'commission_id', 'yith_wc_product_vendors' ) . '</a>', $admin_url, 'commission-id-label' ) : $attribute_label;
                    }
                }
            }
            return $attribute_label;
        }

        /**
         * Filter the edit order page for vendors
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.6
         * @param $check
         * @return bool
         */
        public function vendor_single_order_page( $check ){
            global $theorder;
            $vendor             = yith_get_vendor( 'current', 'user' );
            $is_ajax            = defined( 'DOING_AJAX' ) && DOING_AJAX;
            $is_order_details   = is_admin() && ! $is_ajax && 'shop_order' == get_current_screen()->id;

            if( ( $vendor->is_valid() || ( $vendor->is_super_user() && is_object( $theorder ) && wp_get_post_parent_id( $theorder->id )  ) ) && $is_order_details && 'wc_order_is_editable' == current_filter() ){
                $check = false;
            }
            return $check;
        }
                /**
         * Filters meta to hide, to add to the list item order meta added by author class
         *
         * @param $to_hidden Array of order_item_meta meta_key to hide
         *
         * @return array New array of order item meta to hide
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function hidden_order_itemmeta( $to_hidden ) {
            if( ! defined( 'WP_DEBUG' ) || ( defined( 'WP_DEBUG' ) && ! WP_DEBUG ) ){
                $to_hidden[] = '_parent_line_item_id';
            }

            if ( ! apply_filters( 'yith_show_commissions_order_item_meta', YITH_Commissions()->show_order_item_meta ) ) {
                $to_hidden[] = '_commission_id';
                $to_hidden[] = '_parent__commission_id';
            }

            return $to_hidden;
        }

        /**
         * Add and reorder order table column
         *
         * @param $order_columns The order table column
         *
         * @return string           The label value
         */
        public function shop_order_columns( $order_columns ) {
            $vendor = yith_get_vendor( 'current', 'user' );
            if ( $vendor->is_super_user() ) {
                $suborder      = array( 'suborder' => _x( 'Suborders', 'Admin: Order table column', 'yith_wc_product_vendors' ) );
                $ref_pos       = array_search( 'order_title', array_keys( $order_columns ) );
                $order_columns = array_slice( $order_columns, 0, $ref_pos + 1, true ) + $suborder + array_slice( $order_columns, $ref_pos + 1, count( $order_columns ) - 1, true );
            }
            return $order_columns;
        }

        /**
         * Output custom columns for coupons
         *
         * @param  string $column
         */
        public function render_shop_order_columns( $column ) {
            global $post, $the_order;

            if ( empty( $the_order ) || $the_order->id != $post->ID ) {
                $_the_order = wc_get_order( $post->ID );
            }

            else {
                $_the_order = $the_order;
            }

            switch ( $column ) {
                case 'suborder' :
                    $suborder_ids = self::get_suborder( $_the_order->id );

                    if ( $suborder_ids ) {
                        foreach ( $suborder_ids as $suborder_id ) {
                            $suborder  = wc_get_order( $suborder_id );
                            $vendor    = yith_get_vendor( $suborder->post->post_author, 'user' );
                            $order_uri = esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' );

                            printf( '<mark class="%s tips" data-tip="%s">%s</mark> <strong><a href="%s">#%s</a></strong> <small class="yith-wcmv-suborder-owner">(%s %s)</small><br/>',
                                sanitize_title( $suborder->get_status() ),
                                wc_get_order_status_name( $suborder->get_status() ),
                                wc_get_order_status_name( $suborder->get_status() ),
                                $order_uri,
                                $suborder_id,
                                __( 'in', 'Order table details', 'yith_wc_product_vendors' ),
                                $vendor->name
                            );
                        }
                    }

                    else {
                        echo '<span class="na">&ndash;</span>';
                    }

                    break;
            }
        }

         /**
         * Add suborder metaboxes for Vendors order
         *
         * @return void
          * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function add_meta_boxes() {
            if( 'shop_order' != get_current_screen()->id ){
                return;
            }

            global $post;
            $vendor         = yith_get_vendor( 'current', 'user' );
            $has_suborder   = self::get_suborder( absint( $post->ID ) );
            $is_suborder    = wp_get_post_parent_id( absint( $post->ID ) );

            if( $vendor->is_super_user() ){
                if( $has_suborder ){
                    $metabox_suborder_description = _x( 'Suborders', 'Admin: Single order page. Suborder details box', 'yith_wc_product_vendors' ) . ' <span class="tips" data-tip="' . esc_attr__( 'Note: from this box you can monitor the status of suborders associated to individual vendors.', 'woocommerce' ) . '">[?]</span>';
                    add_meta_box( 'woocommerce-suborders', $metabox_suborder_description, array( $this, 'output' ), 'shop_order', 'side', 'core', array( 'metabox' => 'suborders' ) );
                }

                else if( $is_suborder ){
                    $metabox_parent_order_description = _x( 'Parent order', 'Admin: Single order page. Parent order details box', 'yith_wc_product_vendors' );
                    add_meta_box( 'woocommerce-parent-order', $metabox_parent_order_description, array( $this, 'output' ), 'shop_order', 'side', 'high', array( 'metabox' => 'parent-order' ) );
                }
            }
        }

        /**
         * Output the suborder metaboxes
         *
         * @param $post     The post object
         * @param $param    Callback args
         *
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function output( $post, $param ) {
            switch ( $param['args']['metabox'] ) {
                case 'suborders':
                    $suborder_ids = self::get_suborder( absint( $post->ID ) );
                    echo '<ul class="suborders-list single-orders">';
                    foreach ( $suborder_ids as $suborder_id ) {
                        $suborder     = wc_get_order( absint( $suborder_id ) );
                        $vendor       = yith_get_vendor( $suborder->post->post_author, 'user' );
                        $suborder_uri = esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' );
                        echo '<li class="suborder-info">';
                        printf( '<mark class="%s tips" data-tip="%s">%s</mark> <strong><a href="%s">#%s</a></strong> <small class="single-order yith-wcmv-suborder-owner">%s %s</small><br/>',
                            sanitize_title( $suborder->get_status() ),
                            wc_get_order_status_name( $suborder->get_status() ),
                            wc_get_order_status_name( $suborder->get_status() ),
                            $suborder_uri,
                            $suborder_id,
                            __( 'in', 'Order table details', 'yith_wc_product_vendors' ),
                            $vendor->name
                        );
                        echo '<li>';
                    }
                    echo '</ul>';
                    break;

                case 'parent-order':
                    $parent_order_id    = wp_get_post_parent_id( absint( $post->ID ) );
                    $parent_order_uri   = esc_url( 'post.php?post=' . absint( $parent_order_id ) . '&action=edit' );
                    printf( '<a href="%s">&#8592; %s</a>', $parent_order_uri,  _x( 'Return to main order', 'Admin: single order page. Link to parent order', 'yith_wc_product_vendors' ) );
                    break;
            }
        }
    }
}