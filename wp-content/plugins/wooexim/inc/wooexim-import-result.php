<?php
$post_data = array(
	'uploaded_file_path' => $_POST['uploaded_file_path'],
	'header_row' => $_POST['header_row'],
	'limit' => intval($_POST['limit']),
	'map_to' => $_POST['map_to'],
	'custom_field_name' => $_POST['custom_field_name'],
	'custom_field_visible' => $_POST['custom_field_visible'],
	'product_image_set_featured' => $_POST['product_image_set_featured'],
	'product_image_skip_duplicates' => $_POST['product_image_skip_duplicates'],
	'post_meta_key' => $_POST['post_meta_key'],
	'user_locale' => $_POST['user_locale'],
	'import_csv_separator' => $_POST['import_csv_separator'],
	'import_csv_hierarchy_separator' => $_POST['import_csv_hierarchy_separator']
);
?>
<script type="text/javascript">
    jQuery(document).ready(function($){

        $("#show_debug").click(function(){
            $("#debug").show();
            $(this).hide();
        });

        doAjaxImport(<?php echo $post_data['limit']; ?>, 0);

        function doAjaxImport(limit, offset) {
            var data = {
                "action": "wooexim-import-ajax",
                "uploaded_file_path": <?php echo json_encode($post_data['uploaded_file_path']); ?>,
                "header_row": <?php echo json_encode($post_data['header_row']); ?>,
                "limit": limit,
                "offset": offset,
                "map_to": '<?php echo (serialize($post_data['map_to'])); ?>',
                "custom_field_name": '<?php echo (serialize($post_data['custom_field_name'])); ?>',
                "custom_field_visible": '<?php echo (serialize($post_data['custom_field_visible'])); ?>',
                "product_image_set_featured": '<?php echo (serialize($post_data['product_image_set_featured'])); ?>',
                "product_image_skip_duplicates": '<?php echo (serialize($post_data['product_image_skip_duplicates'])); ?>',
                "post_meta_key": '<?php echo (serialize($post_data['post_meta_key'])); ?>',
                "user_locale": '<?php echo (serialize($post_data['user_locale'])); ?>',
                "import_csv_separator": '<?php echo (serialize($post_data['import_csv_separator'])); ?>',
                "import_csv_hierarchy_separator": '<?php echo (serialize($post_data['import_csv_hierarchy_separator'])); ?>'
            };

            //ajaxurl is defined by WordPress
            $.post(ajaxurl, data, ajaxImportCallback);
        }

        function ajaxImportCallback(response_text) {

            $("#debug").append($(document.createElement("p")).text(response_text));

            var response = jQuery.parseJSON(response_text);

            $("#insert_count").text(response.insert_count + " (" + response.insert_percent +"%)");
            $("#remaining_count").text(response.remaining_count);
            $("#row_count").text(response.row_count);

            //show inserted rows
            for(var row_num in response.inserted_rows) {
                var tr = $(document.createElement("tr"));

                if(response.inserted_rows[row_num]['success'] == true) {
                    if(response.inserted_rows[row_num]['has_errors'] == true) {
                        tr.addClass("error");
                    } else {
                        tr.addClass("success");
                    }
                } else {
                    tr.addClass("fail");
                }

                var post_link = $(document.createElement("a"));
                post_link.attr("target", "_blank");
                post_link.attr("href", "<?php echo get_admin_url(); ?>post.php?post=" + response.inserted_rows[row_num]['post_id'] + "&action=edit");
                post_link.text(response.inserted_rows[row_num]['post_id']);

                tr.append($(document.createElement("td")).append($(document.createElement("span")).addClass("icon")));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['row_id']));
                tr.append($(document.createElement("td")).append(post_link));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['name']));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['sku']));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['price']));

                var result_messages = "";
                if(response.inserted_rows[row_num]['has_messages'] == true) {
                    result_messages += response.inserted_rows[row_num]['messages'].join("\n") + "\n";
                }
                if(response.inserted_rows[row_num]['has_errors'] == true) {
                    result_messages += response.inserted_rows[row_num]['errors'].join("\n") + "\n";
                } else {
                    result_messages += "No errors.";
                }
                tr.append($(document.createElement("td")).text(result_messages));

                tr.appendTo("#inserted_rows tbody");
            }

            //show error messages
            for(var message in response.error_messages) {
                $(document.createElement("li")).text(response.error_messages[message]).appendTo(".import_error_messages");
            }

            //move on to the next set!
            if(parseInt(response.remaining_count) > 0) {
                doAjaxImport(response.limit, response.new_offset);
            } else {
                $("#import_status").addClass("complete");
            }
        }
    });
</script>

<div class="wooexim_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <div style="background: #9b5c8f;min-height: 92px;padding: 10px;color: #fff;">
		<img style="border: 1px solid #e3e3e3;padding: 5px;float: left;margin-right: 10px; "src="<?php echo WOOEXIM_PATH . 'img/thumb.jpg'; ?>">
		
		<h2 style="color: #fff;"><?php _e( 'WOOEXIM &raquo; Import Product &raquo; Results', 'wooexim-import' ); ?></h2>
		<p style="color: #fff;line-height: 0.5;"><?php _e( 'Developed by <a style="color: #fff;" href="http://aladinsoft.com" target="_blank">AladinSoft.com</a> Version: 1.0.0', 'wooexim-import' ); ?></p>
		<p style="color: #fff;line-height: 0.5;"><?php _e( 'Quick and easy plugin for WooCommerce product export-import.', 'wooexim-import' ); ?></p>
	</div>

    <ul class="import_error_messages">
    </ul>

    <div id="import_status">
        <div id="import_in_progress">
            <img src="<?php echo WOOEXIM_PATH; ?>img/ajax-loader.gif"
                alt="<?php _e( 'Importing. Please do not close this window or click your browser\'s stop button.', 'wooexim-import' ); ?>"
                title="<?php _e( 'Importing. Please do not close this window or click your browser\'s stop button.', 'wooexim-import' ); ?>">

            <strong><?php _e( 'Importing. Please do not close this window or click your browser\'s stop button.', 'wooexim-import' ); ?></strong>
        </div>
        <div id="import_complete">
            <img src="<?php echo WOOEXIM_PATH; ?>img/complete.png"
                alt="<?php _e( 'Import complete!', 'wooexim-import' ); ?>"
                title="<?php _e( 'Import complete!', 'wooexim-import' ); ?>">
            <strong><?php _e( 'Import Complete! Results below.', 'wooexim-import' ); ?></strong>
        </div>

        <table>
            <tbody>
                <tr>
                    <th><?php _e( 'Processed', 'wooexim-import' ); ?></th>
                    <td id="insert_count">0</td>
                </tr>
                <tr>
                    <th><?php _e( 'Remainin', 'wooexim-import' ); ?>g</th>
                    <td id="remaining_count"><?php echo $post_data['row_count']; ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Total', 'wooexim-import' ); ?></th>
                    <td id="row_count"><?php echo $post_data['row_count']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <table id="inserted_rows" class="wp-list-table widefat fixed pages" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 30px;"></th>
                <th style="width: 80px;"><?php _e( 'CSV Row', 'wooexim-import' ); ?></th>
                <th style="width: 80px;"><?php _e( 'New Post ID', 'wooexim-import' ); ?></th>
                <th><?php _e( 'Name', 'wooexim-import' ); ?></th>
                <th><?php _e( 'SKU', 'wooexim-import' ); ?></th>
                <th style="width: 120px;"><?php _e( 'Price', 'wooexim-import' ); ?></th>
                <th><?php _e( 'Result', 'wooexim-import' ); ?></th>
            </tr>
        </thead>
        <tbody><!-- rows inserted via AJAX --></tbody>
    </table>

    <p><a id="show_debug" href="#" class="button"><?php _e( 'Show Raw AJAX Responses', 'wooexim-import' ); ?></a></p>
    <div id="debug"><!-- server responses get logged here --></div>
</div>