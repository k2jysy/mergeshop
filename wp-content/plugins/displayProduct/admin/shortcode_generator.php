<?php 
if (!defined('ABSPATH'))
    die("Can't load this file directly");
function dp_shortcode_generator_template(){ 
?>
<div id="wpwrap">

<div id="wpbody-content" aria-label="Main content" tabindex="0">
	<form id="displayProduct-form" action="" method="post">
            <div class="dp-container">
                <div class="wrap">
                <div class="dp-title-block">
                    <div class="wrap">
                        <div id="icon-tools"><img src="<?php echo DP_DIR;?>/assets/js/display-icon.png"></div><h2>Display product Options</h2>
                        <h5>
                            <span> &nbsp;| <a href="http://sureshopress.com/display-product-for-woocommerce/document" target="_blank">View Plugin Documentation</a></span>
                            <span>Display Product Version <?php echo DP_VER;?></span>
                        </h5>
                    </div>
                </div>
                <div id="dp-header-block" class="dp-header-block clearfix">
                        <h3 id="displayProduct-step-1" class="displayProduct-headline">Select DP Template</h3>
                </div>
                <div id="dp-shortcode-block" class="displayProduct-step-2 dp-shortcode-block clearfix">
                    <table id="displayProduct-table" class="form-table">
                                <tr>
                                        <th><label for="displayProduct-template">Select Template</label></th>
                                        <td>
                                            <?php
                                            global $wpdb;
                                            $allPageTemplate = array('');
                                            $rs = $wpdb->get_results( "
                                                SELECT ID, post_title
                                                FROM $wpdb->posts
                                                WHERE post_type = 'dp_template'	AND post_status = 'publish'
                                                ORDER BY ID DESC"
                                            );
                                            if($rs){
                                                ?><select id="displayProduct-template" name="displayProduct-template" >
                                                    <option value="">Select DP Template</option>
                                                        <?php
                                                foreach ( $rs as $r )
                                                {
                                                    ?><option value="<?php echo $r->ID ?>"><?php echo $r->post_title;?></option><?php
                                                }
                                                ?></select><?php
                                            }else{
                                                echo 'Not found DP template. Create <a href="'.admin_url( 'edit.php?post_type=dp_template' ).'">here</a>.';
                                            }
                                            ?></td>
                                </tr>
                    </table>
                </div>
            <p class="submit">
                <input type="button" id="displayProduct-submit" class="button-primary" value="<?php echo displayproduct_textdomain('Insert_Product_Shortcode');?>" name="submit" />
            </p>
             </div> </div>
            <input type="hidden" name="update_settings" value="Y">
            
        </form>
</div>
    <div class="clear"></div>
    
</div><!-- wpcontent -->


<script type="text/javascript" src="<?php echo admin_url('load-scripts.php');?>?c=1&amp;load%5B%5D=hoverIntent,common,admin-bar,jquery-ui-core,jquery-ui-widget,jquery-ui-mouse,jquery-ui-draggable,jquery-ui-slider,jquery-touch-p&amp;load%5B%5D=unch,iris,wp-color-picker,svg-painter,heartbeat,thickbox"></script>

<script type="text/javascript">
    jQuery(document).ready(function() { 
        var form = jQuery('#displayProduct-form');
        // handles the click event of the submit button
        form.find('#displayProduct-submit').click(function() {
            var options = {
                'template': ''
            };            
            var shortcode = '[displayProduct';
            for (var index in options) { 
                var value = jQuery('#displayProduct-' + index).val();
                // Type
                if (value !== options[index]) {
                    shortcode += ' id="' + value + '"';
                }
            }
            shortcode += ']';
            // inserts the shortcode into the active editor
            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

            // closes Thickbox
            tb_remove();
        });
        
    });
    </script>
<div class="clear"></div></div>
<?php } ?>