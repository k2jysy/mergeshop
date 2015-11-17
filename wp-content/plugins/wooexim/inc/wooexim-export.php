<?php 
class Woo_wooexim_export {

	public $enable_export;
	
	public function __construct(){	
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );
		add_action( 'wp_ajax_save_product', 'wooexim_save_woo_products' );
		add_action( 'wp_ajax_save_category', 'wooexim_save_woo_category' );
		add_action( 'wp_ajax_save_tags', 'wooexim_save_woo_tags' );
		add_action( 'wp_ajax_save_orders', 'wooexim_save_woo_orders' );
		add_action( 'wp_ajax_save_customers', 'wooexim_save_woo_customers' );
	}
	
	public function add_menu_link() {
		add_submenu_page('wooexim-import','WOOEXIM Export Product', 'Export','manage_options','wooexim-export',	array( $this, 'settings_page' ));
		add_submenu_page('wooexim-import','WOOEXIM Product Archive', 'Archive','manage_woocommerce','wooexim-archive',	array( $this, 'settings_page1' ));
		add_submenu_page('wooexim-import','WOOEXIM Settings', 'Settings','manage_woocommerce','wooexim-settings',	array( $this, 'settings_page3' ));
	}
	
	public function settings_page( $tab = false ){
		ini_set('max_execution_time', 3000);
		$this->wxe_set_archive_table();
		$show_settings = ''; $show_archive='';$show_export='';
		if( $this->wxe_generate_data_for_export())
			$tab = 'archive';
			
		if( ! empty( $tab ) )
		{
			if( $tab == 'export')
				$show_export = 'nav-tab-active';
			if( $tab == 'archive')
				$show_archive = 'nav-tab-active';
			if( $tab == 'settings')
				$show_settings = 'nav-tab-active';			
		}
		else
		{
			if( isset( $_REQUEST['tab'] ) )
			{
				$tab = $_REQUEST['tab'];
				if( $tab == 'export')
					$show_export = 'nav-tab-active';
				if( $tab == 'archive')
					$show_archive = 'nav-tab-active';
				if( $tab == 'settings')
					$show_settings = 'nav-tab-active';
			}
			else
			{
				$tab = 'export';
				$show_export = 'nav-tab-active';
			}
		}
		$html ='';
		$html .=   '<link href="'.WOOEXIM_PATH.'css/style.css" rel="stylesheet" />';
		$html .=   '<link href="'.WOOEXIM_PATH.'css/colorbox.css" rel="stylesheet" />';
		$html .=   '<script type="text/javascript">var admin_url = "'.admin_url('admin-ajax.php').'";var pluginpath = "'.WOOEXIM_PATH.'";</script>';
		
		if( $this->is_woocommerce_activated() ) {
		$html .=  '<div class="wrap" style="border: 1px solid #e3e3e3;padding: 10px;background: #f6f6f6;">
				<div id="icon-woocommerce" class="icon32 icon32-woocommerce-importer"><br></div>
					
				<div style="background: #9b5c8f;min-height: 92px;padding: 10px;color: #fff;">
					<img style="border: 1px solid #e3e3e3;padding: 5px;float: left;margin-right: 10px; "src="' . plugin_dir_url(__FILE__) . 'thumb.jpg">			
					<h2 style="color: #fff;">WOOEXIM &raquo; Export Product</h2>
					<p style="color: #fff;line-height: 0.5;">Developed by <a style="color: #fff;" href="http://aladinsoft.com" target="_blank">AladinSoft.com</a> Version: 1.0.0</p>
					<p style="color: #fff;line-height: 0.5;">Quick and easy plugin for WooCommerce product export-import.</p>
				</div>
					
					
				<div id="content">
		<div class="overview-left">';
		$tab_html = $this->get_export_tab_data();
		$html .= $tab_html;
		$html .= '</div><!-- .overview-left -->
				</div><!-- #content --></div>';
		}
		else
			$html = '<span>Please activate woocommerce plugin to use this feature</span>';
			
		echo $html;
		echo '<script src="'.WOOEXIM_PATH.'js/custom.js"></script>';
		echo '<script src="'.WOOEXIM_PATH.'js/jquery.bpopup.min.js"></script>';
		echo '<script src="'.WOOEXIM_PATH.'js/jquery.colorbox.js"></script>';
	}
	
	
	public function settings_page1( $tab = false ){
		ini_set('max_execution_time', 3000);
		$this->wxe_set_archive_table();
		$show_settings = ''; $show_archive='';$show_export='';
		if( $this->wxe_generate_data_for_export())
			$tab = 'archive';
			
		if( $this->is_woocommerce_activated() ) {
		$html .=  '<div class="wrap" style="border: 1px solid #e3e3e3;padding: 10px;background: #f6f6f6;">
				<div id="icon-woocommerce" class="icon32 icon32-woocommerce-importer"><br></div>
					
				<div style="background: #9b5c8f;min-height: 92px;padding: 10px;color: #fff;">
					<img style="border: 1px solid #e3e3e3;padding: 5px;float: left;margin-right: 10px; "src="' . plugin_dir_url(__FILE__) . 'thumb.jpg">			
					<h2 style="color: #fff;">WOOEXIM &raquo; Export Archive</h2>
					<p style="color: #fff;line-height: 0.5;">Developed by <a style="color: #fff;" href="http://aladinsoft.com" target="_blank">AladinSoft.com</a> Version: 1.0.0</p>
					<p style="color: #fff;line-height: 0.5;">Quick and easy plugin for WooCommerce product export-import.</p>
				</div>
					
					
				<div id="content">
					
		<div class="overview-left">';
		$tab_html = $this->get_archive_tab_data();
		$html .= $tab_html;
		$html .= '</div><!-- .overview-left -->
				</div><!-- #content --></div>';
		}
		else
			$html = '<span>Please activate woocommerce plugin to use this feature</span>';
			
		echo $html;
		echo '<script src="'.WOOEXIM_PATH.'js/custom.js"></script>';
		echo '<script src="'.WOOEXIM_PATH.'js/jquery.bpopup.min.js"></script>';
		echo '<script src="'.WOOEXIM_PATH.'js/jquery.colorbox.js"></script>';
	}
	
	public function settings_page3( $tab = false ){
		ini_set('max_execution_time', 3000);

		$html ='';
		$html .=   '<link href="'.WOOEXIM_PATH.'css/style.css" rel="stylesheet" />';
		$html .=   '<link href="'.WOOEXIM_PATH.'css/colorbox.css" rel="stylesheet" />';
		$html .=   '<script type="text/javascript">var admin_url = "'.admin_url('admin-ajax.php').'";var pluginpath = "'.WOOEXIM_PATH.'";</script>';
		
		if( $this->is_woocommerce_activated() ) {
		$html .=  '<div class="wrap" style="border: 1px solid #e3e3e3;padding: 10px;background: #f6f6f6;">
				<div id="icon-woocommerce" class="icon32 icon32-woocommerce-importer"><br></div>
					
				<div style="background: #9b5c8f;min-height: 92px;padding: 10px;color: #fff;">
					<img style="border: 1px solid #e3e3e3;padding: 5px;float: left;margin-right: 10px; "src="' . plugin_dir_url(__FILE__) . 'thumb.jpg">			
					<h2 style="color: #fff;">WOOEXIM &raquo; Settings</h2>
					<p style="color: #fff;line-height: 0.5;">Developed by <a style="color: #fff;" href="http://aladinsoft.com" target="_blank">AladinSoft.com</a> Version: 1.0.0</p>
					<p style="color: #fff;line-height: 0.5;">Quick and easy plugin for WooCommerce product export-import.</p>
				</div>
					
					
				<div id="content">
					
		<div class="overview-left">';
		$tab_html = $this->wxe_get_setting_tab_data();
		$html .= $tab_html;
		$html .= '</div><!-- .overview-left -->
				</div><!-- #content --></div>
				';
		}
		else
			$html = '<span>Please activate woocommerce plugin to use this feature</span>';
			
		echo $html;
		echo '<script src="'.WOOEXIM_PATH.'js/custom.js"></script>';
		echo '<script src="'.WOOEXIM_PATH.'js/jquery.bpopup.min.js"></script>';
		echo '<script src="'.WOOEXIM_PATH.'js/jquery.colorbox.js"></script>';
	}
	
	
	
	
	function is_woocommerce_activated(){
		if ( is_plugin_active( "woocommerce/woocommerce.php" ) )
			return 1;
		else
			return 0;
	}	
	
	function wxe_set_archive_table(){
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'wooexim_export_archive 
					( 
					 	archive_id INT PRIMARY KEY AUTO_INCREMENT,
						archive_name VARCHAR(50),
						archive_type VARCHAR(30),
						author VARCHAR(30),
						category VARCHAR(30),
						date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
					)';
		$wpdb->query( $sql ); 
	}
	function get_tab_data( $tab )
	{
		$output_data = '';
		
		if( ! empty( $tab ) )
		{
			if(  $tab == 'export' )
			{
				$output_data = $this->get_export_tab_data();
			}
			else if(  $tab == 'archive' )
			{
				$output_data = $this->get_archive_tab_data();
			}	
			else if(  $tab == 'settings' )
			{
				$output_data = $this->wxe_get_setting_tab_data();
			}	
		}
		
		return $output_data;
	}
	
	function get_export_tab_data(){
		$data = '
			<div class="overview-left">
			
	<div style="width: 47.5%; margin-right: 0.5%; display: block;float: left;background: #9b5c8f;margin: 15px 0px;padding: 1%;color: #fff;">
		<h2 style="color: #FFF;">Export All Products</h2>
		<p>From here you can export all the products with all the meta informations of your store out of WooCommerce into a CSV Spreadsheet file. And you can use that file for import purpose in future.</p>
		<a class="button-primary all_export_btn export_btn" href="'.WOOEXIM_EXPORT_ADMIN_URL.'&amp;tab=export&export=all_products">Export All Products</a>
			<div class="all_product_exporting_loader">
				<img src="'.WOOEXIM_PATH.'img/animated_loading.gif" /><span>Exporting All Product Data Please Wait ... </span>
			</div>
	</div>
	
	<div style="width: 47.5%; margin-left: 0.5%; display: block;float: right;background: #9b5c8f;margin: 15px 0px;padding: 1%;color: #fff;">
		<h2 style="color: #FFF;">Export Products by Category</h2>
		<p>From here you can export products of your store from desired category into a CSV Spreadsheet file. And you can use it for import purpose in future. For this first you need to <b>Choose Categories</b>.</p>
		<a class="button-primary  cat_export_btn export_btn" href="'.WOOEXIM_EXPORT_ADMIN_URL.'&amp;tab=export&export=selected_category">Export Category Products</a>
			
			<a href="#export-products" class="button export_category_settings">Choose Categories</a>
			<div class="cat_product_exporting_loader">
				<img src="'.WOOEXIM_PATH.'img/animated_loading.gif" /><span>Exporting Please Wait ... </span>
			</div>
			<div class="category_settings">
				'.$this->wxe_get_all_woo_categories().'
			<div>
		
	</div>
</div>
		';
		return $data;
	}
	
	function get_archive_tab_data(){
		global $wpdb;
		if( isset( $_REQUEST['action'] ) )
		{
			if( $_REQUEST['action'] == 'delete' )
			{
				$id = $_REQUEST['archid'];
				if( ! empty( $id ) )
				{
					$sql = 'delete from '.$wpdb->prefix.'wooexim_export_archive where archive_id = '.$id;
					$wpdb->query( $sql );
					$name = $_REQUEST['arch_name'];
					unlink ( WOOEXIM_EXPORT_PATH.'/'. $name . ".csv");
					echo '<div class="success_mgs"><span>Archive Deleted</span></div>';
				}
			}
		}
		
		
		$sql = 'select * from '.$wpdb->prefix.'wooexim_export_archive order by date_created desc';
		$result = $wpdb->get_results( $sql );
		//echo "<pre>"; print_r($result); echo "</pre>";
		$data = '
		<table class="widefat fixed media archive" cellspacing="0">
		<thead>

			<tr>
				<th scope="col" id="icon" class="manage-column column-icon"></th>
				<th scope="col" id="title" class="manage-column column-title">Filename</th>
				<th scope="col" class="manage-column column-type">Type</th>
				<th scope="col" class="manage-column column-catgegory">Category</th>
				<th scope="col" class="manage-column column-size">Size</th>
				<th scope="col" class="manage-column column-author">Author</th>
				<th scope="col" id="title" class="manage-column column-title">Date</th>
			</tr>

		</thead>
		<tfoot>

			<tr>
				<th scope="col" class="manage-column column-icon"></th>
				<th scope="col" class="manage-column column-title">Filename</th>
				<th scope="col" class="manage-column column-type">Type</th>
				<th scope="col" class="manage-column column-catgegory">Category</th>
				<th scope="col" class="manage-column column-size">Size</th>
				<th scope="col" class="manage-column column-author">Author</th>
				<th scope="col" class="manage-column column-title">Date</th>
			</tr>

		</tfoot><tbody id="the-list">';
		
		foreach ($result as $arch )
		{
			$file_url =   WOOEXIM_EXPORT_PATH.'/'.$arch->archive_name.'.csv' ;
			$size = $this->wxe_formatSizeUnits( filesize ( $file_url ) );
			
			$data .= '
				<tr class="ddd" id="post-404" class="author-self status-inherit" valign="top">
				<td class="column-icon media-icon">
					<img width="48" height="64" src="'.WOOEXIM_PATH.'img/excelimg.png" class="attachment-80x60" alt="woo-export_products-2014_05_07.csv">				</td>
				<td class="post-title page-title column-title">
					<strong>'.$arch->archive_name.'.csv</strong>
					<div class="row-actions">
						<span class="view"><a href="'.WOOEXIM_DOWNLOAD_PATH.$arch->archive_name.'.csv" title="download">Download</a></span> | 
						<span class="trash"><a href="'.WOOEXIM_EXPORT_ADMIN_URL.'&action=delete&tab=archive&archid='.$arch->archive_id.'&arch_name='.$arch->archive_name.'" title="Delete Permanently">Delete</a></span>
					</div>
				</td>
				<td class="title">Products</td>
				<td class="title">'.$arch->category.'</td>
				<td class="title">'.$size.'</td>
				<td class="author column-author">'.$arch->author.'</td>
				<td class="date column-date">'.$arch->date_created.'</td>
			</tr>
	
		';
		}
		if( empty ( $result ) )
		{
			$data .= '<tr>
				<td colspan= "6" scope="col" class="manage-column column-icon"> 
				<span class="no_rec">	No Archive Found </span>
				</th>
			</tr>';
		}
	$data .= '</tbody></table>';
		return $data;
	}
	
	function wxe_get_all_woo_products(){
		$args = array( 
				'post_type' => 'product', 
				'posts_per_page' => 1000, 
		);
		$db_checked_products = array();
		$db_checked_products = get_option( 'wooexim_selected_products' );
		$query = new WP_Query( $args );
		$data = '';
		if( $query->have_posts() ){
			$data .= '
				<span class="setting_header">Select the product you want to export</span>
				<form action=" " method="post" >
				<ul class="table_setting">';
			while ( $query->have_posts() ) {
				$query->the_post();
				$data .= '<li>';
				$data .= '<div class="checkbx" onclick="changechk(this)">';
				
				if( !empty( $db_checked_products ) )
				{
					if( in_array( $query->post->ID, $db_checked_products ) )
						$data .= '<img class="checkimg" src="'.WOOEXIM_PATH.'img/checked.png" />
							<input class="checkbox checkeddone"  type="checkbox" name="checked_product[]" value = "'.$query->post->ID.'" checked="checked" /></div>';
					else
						$data .= '<img class="checkimg" src="'.WOOEXIM_PATH.'img/unchecked.png" />
								<input class="checkbox"  type="checkbox" name="checked_product[]" value = "'.$query->post->ID.'" /></div>';
				}
				else
					$data .= '<img class="checkimg" src="'.WOOEXIM_PATH.'img/unchecked.png" />
								<input class="checkbox"  type="checkbox" name="checked_product[]" value = "'.$query->post->ID.'" /></div>';
				
				//the_post_thumbnail( array('class'	=> "image"));
				$data .= get_the_post_thumbnail($query->post->ID, 'thumbnail', array('class'	=> "image"));
				$data .= '<span>'.get_the_title().'</span>';
				$data .= '</li>';
			}
			$data .= '<div class="clr"></div></ul>
			<input style="margin-left: 5px;width:50px;" type="submit" name="submit" value="Save" class="button button-primary save_button" onclick="return save_product()">
			</form><div class="save_loader"><img src="'.WOOEXIM_PATH.'/img/loading.gif" alt="" /><span>Saving...</span></div>';
		}
		else
		{
			$data .= '<span class="setting_header">No product has beed added yet.</span>';
		}
			return $data;
	}
	
	function wxe_get_all_woo_categories(){
		$taxonomies = array( 'product_cat' );
		$args = array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false );  
		$list =  get_terms( $taxonomies, $args ) ;
		
		$db_checked_categories = array();
		$db_checked_categories = get_option( 'wooexim_selected_categories' );
		$data = '';
		if( ! empty( $list ) )
		{	
			$data .= '<span class="setting_header">Export the product on the basis of their category</span>
			<ul class="table_setting">';
			foreach ( $list as $cat )
			{
				$data .= '<li>';
				$data .= '<div class="checkbx" onclick="changechk(this)">';
				if( !empty( $db_checked_categories ) )
				{
					if( in_array( $cat->term_id, $db_checked_categories ) )
						$data .= '<img class="checkimg" src="'.WOOEXIM_PATH.'img/checked.png" />
							<input class="checkbox checkeddone"  type="checkbox" name="checked_category[]" value = "'.$cat->term_id.'" checked="checked" />';
					else
						$data .= '<img class="checkimg" src="'.WOOEXIM_PATH.'img/unchecked.png" />
								<input class="checkbox"  type="checkbox" name="checked_category[]" value = "'.$cat->term_id.'" />';
				}
				else
					$data .= '<img class="checkimg" src="'.WOOEXIM_PATH.'img/unchecked.png" />
						<input class="checkbox"  type="checkbox" name="checked_category[]" value = "'.$cat->term_id.'" />';
				
				$data .= '<div><div>'.$cat->name.'</div></li>';
			}
			$data .=  '<div class="clr"></div></ul>
			<input style="margin-left: 5px;width:50px;" type="submit" name="submit" value="Save" class="button button-primary save_button" onclick="return save_category()">
			<div class="save_loader"><img src="'.WOOEXIM_PATH.'/img/loading.gif" alt="" /><span>Saving...</span></div>';
		}
		else
			$data .= '<span class="setting_header">No category has beed added yet.</span>';
			
		return $data;
	}
	function wxe_get_setting_tab_data(){
		if( isset( $_REQUEST['save_settings'] ) )
		{
			$prefix_name = $_REQUEST['prefix_name'];
			$author_name = $_REQUEST['author_name'];
			$subject_name = $_REQUEST['subject_name'];
			$description_archive = $_REQUEST['description_archive'];
			$field_separator = $_REQUEST['field_separator'];
			$hierarchy_separator = $_REQUEST['hierarchy_separator'];
			
			if($field_separator == ''){ $field_separator = ',';}
			if($hierarchy_separator == ''){ $hierarchy_separator = '/';}
			if($prefix_name == ''){ $prefix_name = 'wooexim_export_';}
			if($author_name == ''){ $author_name = 'by Woo EXIM';}
			
			
			update_option( 'wxe_archive_prefix', $prefix_name  );
			update_option( 'wooexim_author_name', $author_name );
			update_option( 'wooexim_subject_name', $subject_name );
			update_option( 'wooexim_description_archive', $description_archive );
			update_option( 'wooexim_field_separator', $field_separator );
			update_option( 'wooexim_hierarchy_separator', $hierarchy_separator );
			echo '<div class="success_mgs"><span>Settings Updated</span></div>';
		}
			$prefix_name = get_option( 'wxe_archive_prefix' );
			$author_name = get_option( 'wooexim_author_name' );
			$subject_name = get_option( 'wooexim_subject_name' );
			$description_archive = get_option( 'wooexim_description_archive' );
			$field_separator = get_option( 'wooexim_field_separator' );
			$hierarchy_separator = get_option( 'wooexim_hierarchy_separator' );
		
		$upload_dir = wp_upload_dir();
		$html = '
			<div class="settings_tab">
				<form action="" method="post" >
				<table>
					<tr>
						<td>Path to Your <strong>uploads</strong> Folder</td>
						<td><input type="text" name="upload_folder" value="'.$upload_dir['basedir'].'" />
							<span>Directory of your WordPress upload folder.</span>
						</td>
					</tr>
					<tr>
						<td>CSV field separator</td>
						<td><input type="text" name="field_separator" value="'.$field_separator.'" />
							<span>Enter the character used to separate each field in your CSV. The default is the comma (,) character. Some formats use a semicolon (;) instead.</span>
						</td>
					</tr>
					<tr>
						<td>Category hierarchy separator</td>
						<td><input type="text" name="hierarchy_separator" value="'.$hierarchy_separator.'" />
							<span>Enter the character used to separate categories in a hierarchical structure. The default is the forward-slash (/) character.</span>
						</td>
					</tr>
					<tr>
						<td>Prefix for archive</td>
						<td><input type="text" name="prefix_name" value="'.$prefix_name.'" />
							<span>Tags can be used: %YEAR%, %MONTH%, %DATE%, %HOUR%, %MINUTE%, %SEC%.<br/> Please use \'_\' or space to separate the letters.</span>
						</td>
					</tr>
					<tr>
						<td>Archive\'s Author Name</td>
						<td><input type="text" name="author_name" value="'.$author_name.'" />
							<span>Author name to be added with your archive.</span>
						</td>
						
					</tr>
					
					<tr>
						<td>Archive Subject</td>
						<td><input type="text" name="subject_name" value="'.$subject_name.'" />
							<span>Subject to be added with your archive.</span>
						</td>
					</tr>
					
					<tr>
						<td>Description for your Archive</td>
						<td><textarea name="description_archive">'.$description_archive.'</textarea>
							<span>Description to be added with your archive.</span>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" name="save_settings" value="Save" class="button-primary save_settings"></td>
					</tr>
				</table>
				</form>
			</div>
		';
		return $html;
	}
	function wxe_generate_data_for_export(){
		if( isset( $_REQUEST['export']) )
		{
			if( $_REQUEST['export'] == 'all_products' )
			{
				
				$args = array(
					'posts_per_page' => 10000,
					//'product_cat' => 'category-slug-here',
					'post_type' => 'product',
					'orderby' => 'title',
				);
				$the_query = new WP_Query( $args );
				// The Loop
				$full_data = array();
				global $wpdb;
				if( $the_query->have_posts() )
				{
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						
						$single_product = array();
						$product = get_product($the_query->post->ID);
						$single_product['name'] = $product->post->post_title;
						$single_product['post_content'] = $product->post->post_content;
						$single_product['post_excerpt'] = $product->post->post_excerpt;
						$single_product['post_status'] = $product->post->post_status;
						$single_product['permalink'] = get_permalink();
						
						$post_categories = wp_get_post_terms($the_query->post->ID, $taxonomy = 'product_cat');			
						$cat = ''; $ii = 0;
						foreach((array)$post_categories as $post_category):
							if($ii > 0){$cat .= '|';}
							$cat .= $post_category->name;
							$ii++;
						endforeach;
						$single_product['product_categories'] = $cat;
						
						$post_tags = wp_get_post_terms($the_query->post->ID, $taxonomy = 'product_tag');
						$tag = ''; $ii = 0;
						foreach((array)$post_tags as $post_tag):
							if($ii > 0){$tag .= '|';}
							$tag .= $post_tag->name;
							$ii++;
						endforeach;
						$single_product['product_tags'] = $tag;
						
						$single_product['sku'] = get_post_meta($the_query->post->ID, '_sku', true);
						$single_product['sale_price'] = get_post_meta($the_query->post->ID, '_sale_price', true);
						$single_product['visibility'] = get_post_meta($the_query->post->ID, '_visibility', true);
						$single_product['stock_status'] = get_post_meta($the_query->post->ID, '_stock_status', true);
						$single_product['price'] = get_post_meta($the_query->post->ID, '_price', true);
						$single_product['regular_price'] = get_post_meta($the_query->post->ID, '_regular_price', true);
						$single_product['total_sales'] = get_post_meta($the_query->post->ID, 'total_sales', true);
						$single_product['downloadable'] = get_post_meta($the_query->post->ID, '_downloadable', true);
						$single_product['virtual'] = get_post_meta($the_query->post->ID, '_virtual', true);
						$single_product['purchase_note'] = get_post_meta($the_query->post->ID, '_purchase_note', true);
						$single_product['weight'] = get_post_meta($the_query->post->ID, '_weight', true);
						$single_product['length'] = get_post_meta($the_query->post->ID, '_length', true);
						$single_product['width'] = get_post_meta($the_query->post->ID, '_width', true);
						$single_product['height'] = get_post_meta($the_query->post->ID, '_height', true);
						$single_product['sold_individually'] = get_post_meta($the_query->post->ID, '_sold_individually', true);
						$single_product['_manage_stock'] = get_post_meta($the_query->post->ID, '_manage_stock', true);
						$single_product['stock'] = get_post_meta($the_query->post->ID, '_stock', true);
						$single_product['backorders'] = get_post_meta($the_query->post->ID, '_backorders', true);
						$single_product['featured'] = get_post_meta($the_query->post->ID, '_featured', true);
						$single_product['tax_status'] = get_post_meta($the_query->post->ID, '_tax_status', true);
						$single_product['tax_class'] = get_post_meta($the_query->post->ID, '_tax_class', true);
						
						$thumbnailid = get_post_meta($the_query->post->ID, '_thumbnail_id', true);
						$upload_dir = wp_upload_dir();
						$timg = $upload_dir['baseurl'] . '/' . get_post_meta($thumbnailid, '_wp_attached_file', true);
						$imggalls = get_post_meta($the_query->post->ID, '_product_image_gallery', true);
						$imggall = explode(',', $imggalls); $kk = 0;
						foreach((array)$imggall as $nimg ){
							if( $timg != '' ){ $timg .= '|';}
							$timg .= $upload_dir['baseurl'] . '/' . get_post_meta($imggall[$kk], '_wp_attached_file', true);
							$kk++;
						}
						$single_product['images'] = $timg;
						
						$single_product['download_limit'] = get_post_meta($the_query->post->ID, '_download_limit', true);
						$single_product['download_expiry'] = get_post_meta($the_query->post->ID, '_download_expiry', true);
						$single_product['file_path'] = get_post_meta($the_query->post->ID, '_file_path', true);
						$single_product['product_url'] = get_post_meta($the_query->post->ID, '_product_url', true);
						$single_product['product_type'] = get_post_meta($the_query->post->ID, '_product_type', true);
						
						$post_sclass = wp_get_post_terms($the_query->post->ID, $taxonomy = 'product_shipping_class');
						$sclass = ''; $ii = 0;
						foreach((array)$post_sclass as $post_class):
							if($ii > 0){$sclass .= '|';}
							$sclass .= $post_class->name;
							$ii++;
						endforeach;
						$single_product['shipping_class'] = $sclass;
						
						$mydata = $wpdb->get_row("SELECT * FROM " . $wpdb->posts . " WHERE id = " . $the_query->post->ID . "");
						$single_product['comment_status'] = $mydata->comment_status;
						$single_product['ping_status'] = $mydata->ping_status;

						$full_data[] = $single_product;
					}
					//echo "<pre>"; print_r($full_data); echo "</pre>";
					$headers = array(
								'Name', 'Description', 'Short Description',  'Product Status', 'Permalink', 'Categories', 'Tags', 'SKU', 'Sale Price',
								'Visibility', 'Stock Status', 'Price', 'Regular Price', 'Total Sales',	'Downloadable',	'Virtual', 'Purchase Note', 'Weight', 'Length', 'Width', 
								'Height', 'Sold Individually', 'Manage Stock', 'Stock', 'Backorders', 'Featured', 'Tax Status', 'Tax Class', 'Images', 'Download Limit', 'Download Expiry', 'File Path', 'Product URL','Product Type',
								'Shipping Class', 'Comment Status', 'Ping Status'
							);
					
					$export_xls_name = $this->wxe_get_archive_name();
					$records = $full_data;
					$sheet_data = new Woo_ExIm_spreadsheet();
					$sheet_data->set_filename( $export_xls_name );
					$sheet_data->set_header( $headers );
					$sheet_data->set_records( $records );
					$sheet_data->do_export();
					
					$current_user = wp_get_current_user();
					$author = $current_user->user_login;
					$category = 'All';
					
					$sql = 'insert into '.$wpdb->prefix.'wooexim_export_archive 
						(archive_name, archive_type, author, category) values 
						( "'.$export_xls_name.'", "product", "'.$author.'", "'.$category.'" )';
					$wpdb->query( $sql );
					unset( $_REQUEST );
					return true;
				}
				else
				{
					echo '<span class="error_mgs">No product to export.</span>';
					unset( $_REQUEST );
					return false;
				}
				
				
			}
			else if ( $_REQUEST['export'] == 'selected_category' )
			{
				$all_cat_names= '';
				$ids = get_option( 'wooexim_selected_categories' );
				//echo "<pre>"; print_r( $ids ); echo "</pre>";
				$args = array(
					'posts_per_page' => 10000,
					'tax_query' => array(
					'relation' => 'AND',
						array(
							'taxonomy' => 'product_cat',
							'field' => 'id',
							'terms' => $ids,
							'operator' => 'IN'
						)
					),
					'post_type' => 'product',
					'orderby' => 'title'
				);
				$the_query = new WP_Query( $args );
				// The Loop
				$full_data = array();
				global $wpdb;
				if( $the_query->have_posts() )
				{
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$single_product = array();
						$product = get_product($the_query->post->ID);
						$single_product['name'] = $product->post->post_title;
						$single_product['post_content'] = $product->post->post_content;
						$single_product['post_excerpt'] = $product->post->post_excerpt;
						$single_product['post_status'] = $product->post->post_status;
						$single_product['permalink'] = get_permalink();
						
						$post_categories = wp_get_post_terms($the_query->post->ID, $taxonomy = 'product_cat');			
						$cat = ''; $ii = 0;
						foreach((array)$post_categories as $post_category):
							if($ii > 0){$cat .= '|';}
							$cat .= $post_category->name;
							$ii++;
						endforeach;
						$single_product['product_categories'] = $cat;
						
						$post_tags = wp_get_post_terms($the_query->post->ID, $taxonomy = 'product_tag');
						$tag = ''; $ii = 0;
						foreach((array)$post_tags as $post_tag):
							if($ii > 0){$tag .= '|';}
							$tag .= $post_tag->name;
							$ii++;
						endforeach;
						$single_product['product_tags'] = $tag;
						
						$single_product['sku'] = get_post_meta($the_query->post->ID, '_sku', true);
						$single_product['sale_price'] = get_post_meta($the_query->post->ID, '_sale_price', true);
						$single_product['visibility'] = get_post_meta($the_query->post->ID, '_visibility', true);
						$single_product['stock_status'] = get_post_meta($the_query->post->ID, '_stock_status', true);
						$single_product['price'] = get_post_meta($the_query->post->ID, '_price', true);
						$single_product['regular_price'] = get_post_meta($the_query->post->ID, '_regular_price', true);
						$single_product['total_sales'] = get_post_meta($the_query->post->ID, 'total_sales', true);
						$single_product['downloadable'] = get_post_meta($the_query->post->ID, '_downloadable', true);
						$single_product['virtual'] = get_post_meta($the_query->post->ID, '_virtual', true);
						$single_product['purchase_note'] = get_post_meta($the_query->post->ID, '_purchase_note', true);
						$single_product['weight'] = get_post_meta($the_query->post->ID, '_weight', true);
						$single_product['length'] = get_post_meta($the_query->post->ID, '_length', true);
						$single_product['width'] = get_post_meta($the_query->post->ID, '_width', true);
						$single_product['height'] = get_post_meta($the_query->post->ID, '_height', true);
						$single_product['sold_individually'] = get_post_meta($the_query->post->ID, '_sold_individually', true);
						$single_product['_manage_stock'] = get_post_meta($the_query->post->ID, '_manage_stock', true);
						$single_product['stock'] = get_post_meta($the_query->post->ID, '_stock', true);
						$single_product['backorders'] = get_post_meta($the_query->post->ID, '_backorders', true);
						$single_product['featured'] = get_post_meta($the_query->post->ID, '_featured', true);
						$single_product['tax_status'] = get_post_meta($the_query->post->ID, '_tax_status', true);
						$single_product['tax_class'] = get_post_meta($the_query->post->ID, '_tax_class', true);
						
						$thumbnailid = get_post_meta($the_query->post->ID, '_thumbnail_id', true);
						$upload_dir = wp_upload_dir();
						$timg = $upload_dir['baseurl'] . '/' . get_post_meta($thumbnailid, '_wp_attached_file', true);
						$imggalls = get_post_meta($the_query->post->ID, '_product_image_gallery', true);
						$imggall = explode(',', $imggalls); $kk = 0;
						foreach((array)$imggall as $nimg ){
							if( $timg != '' ){ $timg .= '|';}
							$timg .= $upload_dir['baseurl'] . '/' . get_post_meta($imggall[$kk], '_wp_attached_file', true);
							$kk++;
						}
						$single_product['images'] = $timg;
						
						$single_product['download_limit'] = get_post_meta($the_query->post->ID, '_download_limit', true);
						$single_product['download_expiry'] = get_post_meta($the_query->post->ID, '_download_expiry', true);
						$single_product['file_path'] = get_post_meta($the_query->post->ID, '_file_path', true);
						$single_product['product_url'] = get_post_meta($the_query->post->ID, '_product_url', true);
						$single_product['product_type'] = get_post_meta($the_query->post->ID, '_product_type', true);
						
						$post_sclass = wp_get_post_terms($the_query->post->ID, $taxonomy = 'product_shipping_class');
						$sclass = ''; $ii = 0;
						foreach((array)$post_sclass as $post_class):
							if($ii > 0){$sclass .= '|';}
							$sclass .= $post_class->name;
							$ii++;
						endforeach;
						$single_product['shipping_class'] = $sclass;
						
						$mydata = $wpdb->get_row("SELECT * FROM " . $wpdb->posts . " WHERE id = " . $the_query->post->ID . "");
						$single_product['comment_status'] = $mydata->comment_status;
						$single_product['ping_status'] = $mydata->ping_status;
						
						$full_data[] = $single_product;
					}
					$headers = array(
								'Name', 'Description', 'Short Description',  'Product Status', 'Permalink', 'Categories', 'Tags', 'SKU', 'Sale Price',
								'Visibility', 'Stock Status', 'Price', 'Regular Price', 'Total Sales',	'Downloadable',	'Virtual', 'Purchase Note', 'Weight', 'Length', 'Width', 
								'Height', 'Sold Individually', 'Manage Stock', 'Stock', 'Backorders', 'Featured', 'Tax Status', 'Tax Class', 'Images', 'Download Limit', 'Download Expiry', 'File Path', 'Product URL','Product Type',
								'Shipping Class', 'Comment Status', 'Ping Status'
							);
					$export_xls_name = $this->wxe_get_archive_name();
					$records = $full_data;
					$sheet_data = new Woo_ExIm_spreadsheet();
					$sheet_data->set_filename( $export_xls_name );
					$sheet_data->set_header( $headers );
					$sheet_data->set_records( $records );
					$sheet_data->do_export();
					
					
					$all_cat_names = str_replace(" ","",$all_cat_names ); 
					$all_cat_names = substr($all_cat_names, 0, -1);
					$current_user = wp_get_current_user();
					$author = $current_user->user_login;
					$category = implode(", ", array_unique( explode(",", $all_cat_names) ) );
					
					$sql = 'insert into '.$wpdb->prefix.'wooexim_export_archive 
						(archive_name, archive_type, author, category) values 
						( "'.$export_xls_name.'", "product", "'.$author.'", "'.$category.'" )';
					$wpdb->query( $sql );
					unset( $_REQUEST );
					return true;
				}
				else
				{
					echo '<span class="error_mgs">No products in the category to export.</span>';
					unset( $_REQUEST );
					return false;
				}
			}
		}
	}
	
	function wxe_get_archive_name(){
		$prefix_name = get_option( 'wxe_archive_prefix' );
		if( empty( $prefix_name ) )
		{
			$today = date("Y_m_d H_i_s");
			$prefix_name = 'wooexim_export_'.$today;
		}
		else
		{
			$real_name = '';
			if (strpos($prefix_name,'%') === false) {
				$prefix_name .= "".date("Y_m_d H_i_s");
			}
			else
			{
				$temp = explode( '%', $prefix_name );
				//echo "<pre>"; print_r($temp); echo "</pre>";
				foreach ( $temp as $val )
				{
					if(strcasecmp ( $val , 'YEAR') == 0)
						$real_name .= date("Y"); 
					else if(strcasecmp ( $val , 'MONTH') == 0)
						$real_name .= date("m"); 
					else if(strcasecmp ( $val , 'DATE') == 0)
						$real_name .= date("d"); 
					else if(strcasecmp ( $val , 'HOUR') == 0)
						$real_name .= date("H"); 
					else if(strcasecmp ( $val , 'MINUTE') == 0)
						$real_name .= date("i"); 
					else if(strcasecmp ( $val , 'SEC') == 0)
						$real_name .= date("s"); 
					else 
						$real_name .= $val;
				}
				$prefix_name = $real_name;
			}
		}
		return $prefix_name;
	}
	
	function wxe_formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}
}
?>