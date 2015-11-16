=== Pricing Deals Pro for WooCommerce ===
Contributors: varktech
Donate link: http://www.varktech.com/woocommerce/pricing-deals-pro-for-woocommerce/
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 1.1.1.2
*
Copyright 2014 AardvarkPC Services NZ, all rights reserved.  See license.txt for more details.
*

**Set up and manage incredibly flexible Pricing Deals and Marketing Promotions for your store - BOGO (Buy One Get One) Deals, Quantity bulk discounts, General Sales and Discounting, Group Pricing and More!**  

Please go to the following URL for all tutorials, documentation, etc.
http://www.varktech.com/woocommerce/pricing-deals-pro-for-woocommerce/

== Installation ==

First Download and install the Free Version from WordPress.Org

    - Use the built-in WordPress plugin installer to download, install and activate the Free version of the plugin hosted at wordpress.org/extend/plugins/maximum-purchase-for-woocommerce

Then Download and install the Pro Version from www.varktech.com

    - Download the zipped Pro Version of the plugin from Downloads ("http://www.varktech.com/products-page/download-manager/") using your Session Id or Name and Email from Purchase email issued at Checkout.
    - Upload and activate the zipped Pro Version of the plugin file to your site through the 'Plugins' menu in WordPress. 
    - Please Note: **Both the Free and Pro versions must be installed and active**
    
**WooCommerce 2.0 or above is needed to run this plugin successfully.**



= Plugin Requirements =

*   WooCommerce 2.0+
*   WordPress 3.3+
*   PHP 5+
*   Purchasing Deals for WooCommerce (free) - must be installed and active!


== Changelog ==

= 1.1.1.2 - 2015-11-07 =
* Fix - Coupon discount mini-cart intermittent display issue on 1st time 
* Enhancement - Formerly, only a single "auto add for free" rule was allowed.
		Now multiple "auto add for free" rules is fully supported. 

= 1.1.1.1 - 2015-09-28 =
* Fix - Autoadd Free item for product not yet in cart. 

= 1.1.1 - 2015-09-26 =
* Enhancement - Compatibility with woocommerce-measurement-price-calculator now available. 

= 1.1.0.9 - 2015-07-31 =
* Fix - Other rule discounts = no
* Fix - improve efficiency for Rule Discounts activated by Coupon

= 1.1.0.8 - 2015-07-25 =
* Fix - Wp-admin Rule editing - if advanced field in error and basic rule showing, 
	switch to advanced rule in update process to expose errored field. 
* Fix - fix to user tax exempt status - saved to user updated, not user making the update!
* Enhancement - New Advanced Rule Option - Rule Discount applies only 
			when a specific Coupon Code is redeemed for the cart:
		- Coupon code is entered in the Pricing Deals Rule in the Discount box area (opotional!)
		- The rule discount will not activate in the Cart for a client purchase, 
			until the correct coupon code is presented.
		- Best to use a coupon set to 'Cart Discount' and 'coupon amount' = 0.

= 1.1.0.6 - 2015-07-07 =
* Fix - Auto add free item function. 
* Enhancement - Auto add free item function:
		- Can now add multiple free items using the Get Group Amount count.
		- New Filter ==> $0 Price shown as 'Free' unless overridden by filter:
			add_filter('vtprd_show_zero_price_as_free',FALSE); 
			(in your theme's functions.php file)

= 1.1 - 2015-04-19 =
* Enhancement - In the Buy Group Filter, added Logged-in Role to Single product and single product with variations:
	By Single Product with Variations   (+ Logged-in Role) 
	By Single Product    (+ Logged-in Role)          

= 1.0.6.2 - 2015-04-10 =
* Fix - Cart issue if only Catalog discount used, now fixed.

= 1.0.6.1 - 2015-04-09 =
* Fix - Balance out discount if $$ discount greater than item value

= 1.0.6.0 - 2014-12-11 =
* Fix - Short msg stripslashes fix

= 1.0.5.9 - 2014-09-04 =
* Fix - Rare Discount by each counting issue  - matches Free v1.0.8.7

= 1.0.5.8 - 2014-08-16 =
* Fix - Rare variation categories list issue  - matches Free v1.0.8.6
* Enhancement - Variation Attributes

= 1.0.5.7 - 2014-08-6 =
* Enhancement - Pick up User Login and apply to Cart realtime  - matches Free v1.0.8.4
* Enhancement - Upgraded discount exclusion for pricing tiers, when "Discount Applies to ALL" 
* Enhancement - Pick up admin changes to Catalog rules realtime

= 1.0.5.6 - 2014-07-30 =
* Fix - Auto Insert free product name in discount reporting - matches Free v1.0.8.2 

= 1.0.5.5 - 2014-07-27 =
* Fix - Refactored "Discount This" limits
	If 'Buy Something, Discount This Item' is selected,
	Get Group Amount is now *an absolute amount* of units/$$ applied to
	working with the Get Group Repeat amount 

= 1.0.5.4 - 2014-06-30 =
* Fix - Inclusion List
* Enhancement - math improvement for group pricing

= 1.0.5.3 - 2014-06-19 =
* Enhancement - VAT pricing - include Woo wildcard in suffix text
* Enhancement - Taxation messaging as needed in checkout
* Fix - PHP floating point rounding

= 1.0.5.2 - 2014-06-05 =
* Fix - post-purchase processing

= 1.0.5.1 - 2014-05-29 =
* Fix - Package Pricing
* Fix - group pricing rounding issue

= 1.0.5 - 2014-05-07 =
* Enhancement - New apply_filters for additional population criteria in core/vtprd-apply-rules.php => usage example at bottom of file
* Fix -VAT inclusive for Cart totals
* Fix -Warnings fix
* Fix -$product_variations_list

= 1.0.5 - 2014-5-08=
* Fix -VAT inclusive for Cart totals
* Fix -Warnings fix
* Enhancement - hook added for additional population logic - filter "vtprd_additional_inpop_include_criteria"
* Fix -$product_variations_list fix


= 1.0.4 - 2014-05-01 =
* Fix - lifetime counter educated to count all products as a single iteration, if discount is applied to the group.
* Fix - apply skip sale logic fix

= 1.0.3 - 2014-04-26 =
* Fix - warnings for lifetime address info
* Fix - Get group repeat logic
* Enhancement - e_notices made switchable, based on 'Test Debugging Mode Turned On' settings switch


= 1.0.2 - 2014-04-14 =
* Fix - warnings on UI update error

= 1.0.1 - 2014-03-31 =
* Fix - warning on install in front end if no rule
* Fix - removed red notices to change host timezone on install
* Fix - removed deprecated WOO hook
* Fix - BOGO discount this fix
* Enhancement - reformatted the rule screen, hover help now applies to Label, rather than data field 

= 1.0 - 2014-03-15 =
* Initial Public Release

=====================
== Terms and Conditions ==
=====================

Order Acceptance
----------------

Order acceptance will take place on the dispatch of the products ordered.
Non-acceptance of an order by VarkTech.com may be a result of one of the following:

    *Our inability to obtain authorization for your payment.
    *The identification of a pricing or product description error.

 

Product Delivery
----------------

All purchases on this website regard software and support, and there is no physical product shipped.   All software is downloadable, and is can be downloaded following receipt of the order confirmation email.  Once you have made a purchase, you will be able to download your plugins from http://www.varktech.com/download-pro-plugins/, using your Session Id or Name and Email from Checkout.


Licensing
---------

Purchase of a single software plugin grants the purchaser a single website license without time limit, for both a production website and a development version of the same website only.  This license also grants the purchaser access to new versions of the software, as they become available.


Support and Refund Policy
-------------------------

We do not issue refunds once the order is accomplished.  Varktech will work with the customer to try to solve any compatibility issues with other major WordPress plugins, but VarkTech is not responsible for compatibility issues with 3rd party plugins.  Any issues of theme compatibility fall outside the Support scope