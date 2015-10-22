<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     WPOpal  Team <wpopal@gmail.com, support@wpopal.com>
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<!-- OFF-CANVAS MENU SIDEBAR -->
    <div id="wpo-off-canvas" class="wpo-off-canvas">
        <div class="wpo-off-canvas-body">
            <div class="wpo-off-canvas-header">
                <button type="button" class="close btn btn-close" data-dismiss="modal" aria-hidden="true">
                	<i class="fa fa-times"></i>
                </button>
            </div>
            <nav  class="navbar navbar-offcanvas navbar-static" role="navigation">
                <?php
                $args = array(  'theme_location' => 'mainmenu',
                                'container_class' => 'navbar-collapse',
                                'menu_class' => 'wpo-menu-top nav navbar-nav',
                                'fallback_cb' => '',
                                'menu_id' => 'main-menu-offcanvas',
                                'walker' => new Wpo_Megamenu_Offcanvas()
                            );
                wp_nav_menu($args);
                ?>
            </nav>
        </div>
    </div>
    <!-- //OFF-CANVAS MENU SIDEBAR -->
	<?php global $woocommerce; ?>
    <!-- START Wrapper -->
	<div class="wpo-wrapper">
		<!-- Top bar -->
		<div class="topbar">
			<div class="container">
				<div class="pull-left user-login hidden-xs">
                    <?php if( !is_user_logged_in() ){ ?>
                        <span><?php echo __('Welcome visitor you can',TEXTDOMAIN); ?></span>
                        <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('login or register','woothemes'); ?>"><?php _e(' login or register ','woothemes'); ?></a>
                    <?php }else{ ?>
                        <?php $current_user = wp_get_current_user(); ?>
                        <span><?php echo __('Welcome ',TEXTDOMAIN).$current_user->display_name; ?> !</span>
                    <?php } ?>
				</div>
            
                <?php if(has_nav_menu( 'topmenu' )){ ?>
                    <div class="topbar-menu pull-right">
                        <?php 
                            $args = array(  'theme_location' => 'topmenu',
                                            'container_class' => '',
                                            'menu_class' => 'menu-topbar'
                                        );
                            wp_nav_menu($args);
                        ?>
                    </div>
                <?php } ?>
			</div>
		</div>
		<!-- // Topbar -->
		<!-- HEADER -->
		<header id="wpo-header" class="wpo-header">
			<div class="container">
                
				<div class="header-wrap clearfix row">
					<!-- LOGO -->
					<div class="logo-in-theme text-center col-lg-4 col-md-3 col-sm-4 col-xs-12">
						<div class="logo">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php echo of_get_option('logo'); ?>" alt="<?php bloginfo( 'name' ); ?>">
							</a>
						</div>
					</div>
					<!-- //LOGO -->
                    <div class="active-mobile col-lg-6 col-md-6 col-sm-8 col-xs-12">
                        <div class="active-content">
                            <?php superstore_searchform(); ?>
                        </div>
                    </div>

                    <div class="active-mobile col-lg-2 col-md-3 hidden-xs hidden-sm">
                        <?php wpo_cartdropdown(); ?>
                    </div>
					<!-- // Setting -->
				</div>

                <div class="megamenu">
                    <!-- MENU -->
                    <nav id="wpo-mainnav" data-duration="<?php echo of_get_option('megamenu-duration',400); ?>" class="wpo-megamenu <?php echo of_get_option('magemenu-animation','slide'); ?> animate navbar navbar-default" role="navigation">
                        <div class="navbar-header">
                            <?php wpo_renderButtonToggle(); ?>
                        </div><!-- //END #navbar-header -->
                        <?php 
                            $args = array(  'theme_location' => 'mainmenu',
                                            'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
                                            'menu_class' => 'nav navbar-nav megamenu',
                                            'fallback_cb' => '',
                                            'menu_id' => 'main-menu',
                                            'walker' => new Wpo_Megamenu());
                            wp_nav_menu($args);
                        ?>
                    </nav>
                    <!-- //MENU -->
                </div>

			</div>
		</header>
		<!-- //HEADER -->