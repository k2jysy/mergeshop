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
		<footer id="wpo-footer" class="wpo-footer">
			<?php if(is_active_sidebar('newletter')){ ?>
            <div class="footer-top">
                <div class="container">
                    <div class="newletter">
                        <?php dynamic_sidebar('newletter'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>
			<div class="footer-center container">
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">					
						<div class="inner">
							<?php dynamic_sidebar('footer-1'); ?>
						</div>						
					</div>
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="inner">
							<?php dynamic_sidebar('footer-2'); ?>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="inner">
							<?php dynamic_sidebar('footer-3'); ?>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="inner">
							<?php dynamic_sidebar('footer-4'); ?>
						</div>
					</div>
				</div>
			</div>

			
			<div class="wpo-copyright">
				<div class="container">
					<div class="row">
						<div class="col-sm-12 copyright">
							<address class="pull-left">
								<?php echo of_get_option('copyright','Copyright 2014 Powered by <a href="http://themeforest.net/user/Opal_WP/?ref=dancof">Opal Team</a> All Rights Reserved.'); ?>
							</address>
							<?php 
								$img_footer = of_get_option('image-footer','');
								if($img_footer!=''){
							?>
							<aside class="paypal pull-right">
								<img src="<?php echo $img_footer; ?>" />
							</aside>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

		</footer>
	</div>
	<!-- END Wrapper -->
	<?php wp_footer(); ?>
</body>
</html>