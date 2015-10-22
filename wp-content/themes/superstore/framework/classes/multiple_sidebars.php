<?php
/*
Plugin Name: Sidebar Generator
Plugin URI: http://www.getson.info
Description: This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish. Version 1.1 now supports themes with multiple sidebars.
Version: 1.1.0
Author: Kyle Getson
Author URI: http://www.kylegetson.com
Copyright (C) 2009 Kyle Robert Getson
*/

/*
Copyright (C) 2009 Kyle Robert Getson, kylegetson.com and getson.info

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class sidebar_generator {

	public function __construct(){
		add_action('init',array($this,'init'));
		add_action('admin_menu',array($this,'admin_menu'));
		add_action('admin_enqueue_scripts', array($this,'admin_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array($this,'admin_print_scripts'));
		add_action('wp_ajax_add_sidebar', array($this,'add_sidebar') );
		add_action('wp_ajax_remove_sidebar', array($this,'remove_sidebar') );
	}

	public function init(){
		//go through each sidebar and register it
	    $sidebars = sidebar_generator::get_sidebars();
	    if(is_array($sidebars)){
			foreach($sidebars as $sidebar){
				$sidebar_class = sidebar_generator::name_to_class($sidebar);
				register_sidebar(array(
					'name'=>$sidebar,
					'id' => 'wpo-custom-sidebar-'.strtolower($sidebar_class),
					'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
					'after_widget' => '</aside>',
					'before_title' => '<h3 class="widget-title"><span>',
					'after_title' => '</span></h3>',
		    	));
			}
		}
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script( array( 'sack' ));
	}

	public function admin_print_scripts(){
		?>
			<script>
				function add_sidebar( sidebar_name )
				{

					var mysack = new sack("<?php echo site_url(); ?>/wp-admin/admin-ajax.php" );

				  	mysack.execute = 1;
				  	mysack.method = 'POST';
				  	mysack.setVar( "action", "add_sidebar" );
				  	mysack.setVar( "sidebar_name", sidebar_name );
				  	mysack.encVar( "cookie", document.cookie, false );
				  	mysack.onError = function() { alert('Ajax error. Cannot add sidebar' )};
				  	mysack.runAJAX();
					return true;
				}

				function remove_sidebar( sidebar_name,num )
				{

					var mysack = new sack("<?php echo site_url(); ?>/wp-admin/admin-ajax.php" );

				  	mysack.execute = 1;
				  	mysack.method = 'POST';
				  	mysack.setVar( "action", "remove_sidebar" );
				  	mysack.setVar( "sidebar_name", sidebar_name );
				  	mysack.setVar( "row_number", num );
				  	mysack.encVar( "cookie", document.cookie, false );
				  	mysack.onError = function() { alert('Ajax error. Cannot add sidebar' )};
				  	mysack.runAJAX();
					//alert('hi!:::'+sidebar_name);
					return true;
				}
			</script>
		<?php
	}

	public function add_sidebar(){
		$sidebars = sidebar_generator::get_sidebars();
		$name = str_replace(array("\n","\r","\t"),'',$_POST['sidebar_name']);
		$id = sidebar_generator::name_to_class($name);
		if(isset($sidebars[$id])){
			die("alert('Sidebar already exists, please use a different name.')");
		}

		$sidebars[$id] = $name;
		sidebar_generator::update_sidebars($sidebars);

		$js = "
			var tbl = document.getElementById('sbg_table');
			var lastRow = tbl.rows.length;
			// if there's no header row in the table, then iteration = lastRow + 1
			var iteration = lastRow;
			var row = tbl.insertRow(lastRow);

			// left cell
			var cellLeft = row.insertCell(0);
			var textNode = document.createTextNode('$name');
			cellLeft.appendChild(textNode);

			//middle cell
			var cellLeft = row.insertCell(1);
			var textNode = document.createTextNode('$id');
			cellLeft.appendChild(textNode);

			//var cellLeft = row.insertCell(2);
			//var textNode = document.createTextNode('[<a href=\'javascript:void(0);\' onclick=\'return remove_sidebar_link($name);\'>Remove</a>]');
			//cellLeft.appendChild(textNode)

			var cellLeft = row.insertCell(2);
			removeLink = document.createElement('a');
      		linkText = document.createTextNode('remove');
			removeLink.setAttribute('onclick', 'remove_sidebar_link(\'$name\')');
			removeLink.setAttribute('href', 'javascript:void(0)');

      		removeLink.appendChild(linkText);
      		cellLeft.appendChild(removeLink);


		";


		die( "$js");
	}

	public function remove_sidebar(){
		$sidebars = sidebar_generator::get_sidebars();
		$name = str_replace(array("\n","\r","\t"),'',$_POST['sidebar_name']);
		$id = sidebar_generator::name_to_class($name);
		if(!isset($sidebars[$id])){
			die("alert('Sidebar does not exist.')");
		}
		$row_number = $_POST['row_number'];
		unset($sidebars[$id]);
		sidebar_generator::update_sidebars($sidebars);
		$js = "
			var tbl = document.getElementById('sbg_table');
			tbl.deleteRow($row_number)

		";
		die($js);
	}

	public function admin_menu(){
		add_theme_page('Sidebars', 'Sidebars', 'manage_options', 'multiple_sidebars', array($this,'admin_page'));
	}

	public function admin_page(){
		?>
		<script>
			function remove_sidebar_link(name,num){
				answer = confirm("Are you sure you want to remove " + name + "?\nThis will remove any widgets you have assigned to this sidebar.");
				if(answer){
					//alert('AJAX REMOVE');
					remove_sidebar(name,num);
				}else{
					return false;
				}
			}
			function add_sidebar_link(){
				var sidebar_name = prompt("Sidebar Name:","");
				//alert(sidebar_name);
				add_sidebar(sidebar_name);
			}
		</script>
		<div class="wrap">
			<h2>Sidebars</h2>
			<br />
			<table class="widefat page" id="sbg_table" style="width:600px;">
				<tr>
					<th>Sidebar Name</th>
					<th>CSS class</th>
					<th>Remove</th>
				</tr>
				<?php
				$sidebars = sidebar_generator::get_sidebars();
				//$sidebars = array('bob','john','mike','asdf');
				if(is_array($sidebars) && !empty($sidebars)){
					$cnt=0;
					foreach($sidebars as $sidebar){
						$alt = ($cnt%2 == 0 ? 'alternate' : '');
				?>
				<tr class="<?php echo $alt?>">
					<td><?php echo $sidebar; ?></td>
					<td><?php echo sidebar_generator::name_to_class($sidebar); ?></td>
					<td><a href="javascript:void(0);" onclick="return remove_sidebar_link('<?php echo $sidebar; ?>',<?php echo $cnt+1; ?>);" title="Remove this sidebar">remove</a></td>
				</tr>
				<?php
						$cnt++;
					}
				}else{
					?>
					<tr>
						<td colspan="3">No Sidebars defined</td>
					</tr>
					<?php
				}
				?>
			</table><br /><br />
            <div class="add_sidebar">
				<a href="javascript:void(0);" onclick="return add_sidebar_link()" title="Add a sidebar" class="button-primary">+ Add New Sidebar</a>

			</div>

		</div>
		<?php
	}

	/**
	 * called by the action get_sidebar. this is what places this into the theme
	*/
	public function get_sidebar($name="0"){
		if(!is_singular()){
			if($name != "0"){
				dynamic_sidebar($name);
			}else{
				dynamic_sidebar('avada-blog-sidebar');
			}
			return;//dont do anything
		}
		wp_reset_query();
		global $wp_query;
		$post = $wp_query->get_queried_object();
		$selected_sidebar = get_post_meta($post->ID, 'sbg_selected_sidebar', true);
		$selected_sidebar_replacement = get_post_meta($post->ID, 'sbg_selected_sidebar_replacement', true);
		$did_sidebar = false;
		//this page uses a generated sidebar
		if($selected_sidebar != '' && $selected_sidebar != "0"){
			if(is_array($selected_sidebar) && !empty($selected_sidebar)){
				for($i=0;$i<sizeof($selected_sidebar);$i++){

					if($name == "0" && $selected_sidebar[$i] == "0" &&  $selected_sidebar_replacement[$i] == "0"){
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						dynamic_sidebar('avada-blog-sidebar');//default behavior
						$did_sidebar = true;
						break;
					}elseif($name == "0" && $selected_sidebar[$i] == "0"){
						//we are replacing the default sidebar with something
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						dynamic_sidebar($selected_sidebar_replacement[$i]);//default behavior
						$did_sidebar = true;
						break;
					}elseif($selected_sidebar[$i] == $name){
						//we are replacing this $name
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						$did_sidebar = true;
						dynamic_sidebar($selected_sidebar_replacement[$i]);//default behavior
						break;
					}
					//echo "<!-- called=$name selected={$selected_sidebar[$i]} replacement={$selected_sidebar_replacement[$i]} -->\n";
				}
			}
			if($did_sidebar == true){
				echo "";
				return;
			}
			//go through without finding any replacements, lets just send them what they asked for
			if($name != "0"){
				dynamic_sidebar($name);
			}else{
				dynamic_sidebar('avada-blog-sidebar');
			}
			echo "";
			return;
		}else{
			if($name != "0"){
				dynamic_sidebar($name);
			}else{
				dynamic_sidebar('avada-blog-sidebar');
			}
		}
	}

	/**
	 * replaces array of sidebar names
	*/
	public function update_sidebars($sidebar_array){
		$sidebars = update_option('sbg_sidebars',$sidebar_array);
	}

	/**
	 * gets the generated sidebars
	*/
	public function get_sidebars(){
		$sidebars = get_option('sbg_sidebars');
		return $sidebars;
	}
	public function name_to_class($name){
		$class = str_replace(array(' ',',','.','"',"'",'/',"\\",'+','=',')','(','*','&','^','%','$','#','@','!','~','`','<','>','?','[',']','{','}','|',':',),'',$name);
		return $class;
	}

}
new sidebar_generator();

?>