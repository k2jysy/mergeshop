<?php
/**
 * class-woorolepricinglight.php
 *
 * Copyright (c) Antonio Blanco http://www.blancoleon.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Antonio Blanco
 * @package woorolepricinglight
 * @since woorolepricing 1.0.0
 */

/**
 * WooRolePricingLight class
 */
class WooRolePricingLight {

	public static function init() {
	
		add_filter('woocommerce_get_price', array( __CLASS__, 'woocommerce_get_price' ), 10, 2);
		
		// filter <del> tags for variable products
		add_filter('woocommerce_variable_sale_price_html', array ( __CLASS__, 'woocommerce_variable_sale_price_html' ), 10, 2 );
		
	}
	
	public static function woocommerce_get_price ( $price, $product ) {
		global $post, $woocommerce;

		$baseprice = $price;
		$result = $baseprice;
		
		if ( ($post == null) || !is_admin() ) {
		
			$commission = self::get_commission( $product );
			
			if ( $commission ) {
				
				$baseprice = $product->get_regular_price();
				
				if ( $product->get_sale_price() != $product->get_regular_price() && $product->get_sale_price() == $product->price ) {
					if ( get_option( "wrp-baseprice", "regular" )=="sale" ) {
						$baseprice = $product->get_sale_price();
					}
				}
				$product_price = $baseprice;
				
				$type = get_option( "wrp-method", "rate" );
				$result = 0;
				if ($type == "rate") {
					// if rate and price includes taxes
					if ( $product->is_taxable() && get_option('woocommerce_prices_include_tax') == 'yes' ) {
						$_tax       = new WC_Tax();
						$tax_rates  = $_tax->get_shop_base_rate( $product->tax_class );
						$taxes      = $_tax->calc_tax( $baseprice, $tax_rates, true );
						$product_price      = $_tax->round( $baseprice - array_sum( $taxes ) );
					}

					$result = self::bcmul($product_price, $commission, WOO_ROLE_PRICING_LIGHT_DECIMALS);
					
					if ( $product->is_taxable() && get_option('woocommerce_prices_include_tax') == 'yes' ) {
						$_tax       = new WC_Tax();
						$tax_rates  = $_tax->get_shop_base_rate( $product->tax_class );
						$taxes      = $_tax->calc_tax( $result, $tax_rates, false ); // important false
						$result      = $_tax->round( $result + array_sum( $taxes ) );
					}
				} else {
					$result = self::bcsub($product_price, $commission, WOO_ROLE_PRICING_LIGHT_DECIMALS);
				}
			}
		}
		return $result;
	}
	
		
	/**
	 * Filter <del> tabs for variable products
	 * @param String $pricehtml
	 * @param Object $product
	 * @return String
	 */
	public static function woocommerce_variable_sale_price_html ( $pricehtml, $product ) {
		$string = $pricehtml;
	
		global $post, $woocommerce;
	
		if ( ($post == null) || !is_admin() ) {
			$commission = self::get_commission( $product );
			if ( $commission ) {
				$string=preg_replace('/<del[^>]*>.+?<\/del>/i', '', $string);
			}
		}
		return $string;
	}
	
	// extra functions
	
	public static function get_commission ( $product ) {
		global $post, $woocommerce;
	
		
		$user = wp_get_current_user();
		$user_roles = $user->roles;
		$user_role = array_shift($user_roles);
		
		$discount = 0;
		
		if ( $user_role !== null ) {
			if ( get_option( "wrp-" . $user_role, "-1" ) !== "-1" ) {
				$discount = get_option( "wrp-" . $user_role );
			}
		}
		if ( $discount ) {
			$method = get_option( "wrp-method", "rate" );
			if ( $method == "rate" ) {
				$discount = self::bcsub ( 1, $discount, WOO_ROLE_PRICING_LIGHT_DECIMALS );
				// for security reasons, set 0
				if ( $discount < 0 ) {
					$discount = 0;
				}
			}
		}
	
		return $discount;
		
	}
	
	public static function bcmul( $data1, $data2, $prec = 0 ) {
		$result = 0;
		if ( function_exists('bcmul') ) {
			$result = bcmul( $data1, $data2, $prec );
		} else {
			$value = $data1 * $data2;
			if ($prec) {
				$result = round($value, $prec);
			}
		}
		return $result;
	}
	
	public static function bcsub( $data1, $data2, $prec = 0 ) {
		$result = 0;
		if ( function_exists('bcsub') ) {
			$result = bcsub( $data1, $data2, $prec );
		} else {
			$value = $data1 - $data2;
			if ($prec) {
				$result = round($value, $prec);
			}
		}
		return $result;
	}
	
}
WooRolePricingLight::init();
