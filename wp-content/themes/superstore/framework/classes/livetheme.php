<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Opal  Team <opalwordpressl@gmail.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
if(!class_exists('WPO_LiveTheme')){
class WPO_LiveTheme {

	public static function getInstance(){
		static $_instance;
		if( !$_instance ){
			$_instance = new WPO_LiveTheme();
		}
		return $_instance;
	}

	/**
	 *
	 */
	public function __construct( ){
		if(is_admin()){
			add_action('wp_ajax_livetheme_render',array($this,'ajax_renderLayout'));
			add_action('wp_ajax_livetheme_load_pattern',array($this,'ajax_load_pattern'));
			add_action('wp_ajax_livetheme_upload_pattern',array($this,'ajax_upload_pattern'));
			add_action('wp_ajax_livetheme_delete_pattern',array($this,'ajax_delete_pattern'));
			add_action('wp_ajax_livetheme_submit',array($this,'ajax_Submit'));
			add_action('wp_ajax_livetheme_delete',array($this,'ajax_Delete'));
			if(isset($_GET['page']) && $_GET['page']=='wpo_livethemeedit'){
				add_action( 'admin_enqueue_scripts', array( $this, 'initScripts' ) );
			}
		}
	}

	/**
	 *
	 */
	public function ajax_upload_pattern(){
		$error = "";
		$msg = "";
		$url = get_template_directory(). '/images/bg/';
		$fileElementName = 'filename';
		if(!empty($_FILES[$fileElementName]['error']))
		{
			switch($_FILES[$fileElementName]['error'])
			{

				case '1':
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2':
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3':
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4':
					$error = 'No file was uploaded.';
					break;
				case '6':
					$error = 'Missing a temporary folder';
					break;
				case '7':
					$error = 'Failed to write file to disk';
					break;
				case '8':
					$error = 'File upload stopped by extension';
					break;
				case '999':
				default:
					$error = 'No error code avaiable';
			}
		}elseif(empty($_FILES['filename']['tmp_name']) || $_FILES['filename']['tmp_name'] == 'none')
		{
			$error = 'No file was uploaded..';
		}else
		{
				// $msg .= " File Name: " . $_FILES['filename']['name'] . ", ";
				// $msg .= " File Size: " . @filesize($_FILES['filename']['tmp_name']);
				if(file_exists($url.$_FILES['filename']['name'])){
					$error .= '"'.$_FILES['filename']['name'].'" already exists.';
				}else{
					copy($_FILES["filename"]["tmp_name"],$url.$_FILES['filename']['name']);
					//move_uploaded_file($_FILES["filename"]["tmp_name"],$url.$_FILES['filename']['name']);
					$msg .= 'Success !';
				}
				//for security reason, we force to remove all uploaded file
				@unlink($_FILES['filename']);
		}
		echo "{";
		echo 				"linkimage:'" .get_template_directory_uri()."/images/bg/" .$_FILES['filename']['name'] ."',\n";
		echo 				"image:'" .$_FILES['filename']['name'] ."',\n";
		echo				"error: '" . $error . "',\n";
		echo				"msg: '" . $msg . "'\n";
		echo "}";
		die();
	}

	/**
	 *
	 */
	public function ajax_load_pattern(){
		$patterns = $this->getPattern();
		echo '<div class="row list-patterns">';
		?>
			<form name="form" action="" method="POST" enctype="multipart/form-data" style="display:none;">
				<input type="file" id="filename" name="filename">
			</form>
			<div class="col-sm-2 addnew">
				<a href="javascript:;" class="addnew-pattern">
					<i class="fa fa-arrow-circle-up"></i>
				</a>
			</div>
		<?php
		foreach ($patterns as $key => $pattern) {
		?>
			<div class="col-sm-2 pattern">
				<div class="bg" style="background:url(<?php echo get_template_directory_uri(); ?>/images/bg/<?php echo $pattern; ?>);"></div>
				<a href="#" data-name="<?php echo $pattern; ?>" class="remove-pattern"></a>
			</div>
		<?php
		}
		echo '</div>';
		die();
	}

	/**
	 *
	 */
	public function ajax_delete_pattern(){
		$file = get_template_directory() . '/images/bg/' . $_POST['pattern'];
		if(is_file($file)){
			echo unlink($file);
		}else{
			echo false;
		}
		die();
	}

	/**
	 *
	 */
	public function initScripts(){
		wp_enqueue_script('WPO_bootstrap_js',FRAMEWORK_STYLE_URI.'js/bootstrap.min.js');
		wp_enqueue_script('WPO_colorpicker_js' , get_template_directory_uri().'/framework/admin/assets/js/colorpicker.js' );
		wp_enqueue_style('WPO_colorpicker_css', get_template_directory_uri().'/framework/admin/assets/css/colorpicker.css');
		wp_enqueue_script('WPO_livetheme_js', get_template_directory_uri().'/framework/admin/assets/js/livetheme.js');
		wp_enqueue_style('WPO_livetheme_css', get_template_directory_uri().'/framework/admin/assets/css/livetheme.css');
		wp_enqueue_style('WPO_bootstrap_css', get_template_directory_uri().'/css/bootstrap.css');
		wp_enqueue_style('WPO_awesome_css', get_template_directory_uri().'/css/font-awesome.min.css');

	}

	/**
	 *
	 */
	public function getXmlSelectors(){
		$customizeXML = FRAMEWORK_XMLPATH.'themeeditor.xml';

 		$output = array( 'selectors' => array(), 'elements' => array() );

 		if( file_exists($customizeXML) ){
			$info = simplexml_load_string( file_get_contents($customizeXML) );

			if( isset($info->selectors->items) ){
				$label = $info->selectors->attributes();
				if(isset($label['label'])){
					$output['selectors']['label'] = (string) $label['label'];
				}else{
					$output['selectors']['label'] = 'Selectors';
				}
				foreach( $info->selectors->items as $item ){
					$vars = get_object_vars($item);
					if( is_object($vars['item']) ){
						$tmp = get_object_vars( $vars['item'] );
						$vars['selector'][] = $tmp;
					}else {
						foreach( $vars['item'] as $selector ){
							$tmp = get_object_vars( $selector );
							if( is_array($tmp) && !empty($tmp) ){
								$vars['selector'][] = $tmp;
							}
						}
					}
					unset( $vars['item'] );
					$output['selectors']['groups'][$vars['match']] = $vars;
				}
			}

			if( isset($info->elements->items) ){
				$label = $info->elements->attributes();
				if(isset($label['label'])){
					$output['elements']['label'] = (string) $label['label'];
				}else{
					$output['elements']['label'] = 'Elements';
				}
				foreach( $info->elements->items as $item ){
					$vars = get_object_vars($item);
					if( is_object($vars['item']) ){
						$tmp = get_object_vars( $vars['item'] );
						$vars['selector'][] = $tmp;
					}else {
						foreach( $vars['item'] as $selector ){
							$tmp = get_object_vars( $selector );
							if( is_array($tmp) && !empty($tmp) ){
								$vars['selector'][] = $tmp;
							}
						}
					}
					unset( $vars['item'] );
					$output['elements']['groups'][$vars['match']] = $vars;
				}
			}
		}
		return $output;
	}

	/**
	 *
	 */
	public function getFileCss( ) {
		$output = array();
		$directories = glob( FRAMEWORK_CUSTOMZIME_STYLE.'*.css');
		foreach( $directories as $dir ){
			$output[] = basename( $dir );
		}
		return $output;
	}

	/**
	 *
	 */
	public function getPattern(){
		$output = array();
		$files = glob( get_template_directory() . '/images/bg/*');
		if(isset($files) && !empty($files)){
			foreach( $files as $dir ){
				if( preg_match("#.png|.jpg|.gif#", $dir)){
					$output[] = str_replace("","",basename( $dir ) );
				}
			}
		}
		return $output;
	}

	/**
	 *
	 */
	public function ajax_renderLayout(){
		$xmlselectors = $this->getXmlSelectors();
		$patterns = $this->getPattern();
		$backgroundImageURL = get_template_directory_uri().'/images/bg/';
		$files = $this->getFileCss();
		include_once(FRAMEWORK_ADMIN_TEMPLATE_PATH.'livetheme/editor.php');
		exit();
	}

	/**
	 *
	 */
	public function ajax_Submit(){

		$data = $_POST;
		$selectors = $_POST['customize'];
		$matches = $_POST["customize_match"];
		$output = '';

		$cache = array();
		foreach( $selectors as $match => $customizes  ){
			$output .= "\r\n/* customize for $match */ \r\n";
			foreach( $customizes as $key => $customize ){
				if( isset($matches[$match]) && isset($matches[$match][$key]) ){
					$tmp = explode("|", $matches[$match][$key]);

					if( trim($customize) ) {
						$output .= $tmp[0]." { ";
						if( strtolower(trim($tmp[1])) == 'background-image'){
							$output .= $tmp[1] . ':url(../../../images/bg/'.$customize .')';
						}elseif( strtolower(trim($tmp[1])) == 'font-size' ){
							$output .= $tmp[1] . ':'.$customize.'px';
						}else {
							$output .= $tmp[1] . ':#'.$customize;
						}

						$output .= "} \r\n";
					}
					$cache[$match][] =  array('val'=>$customize,'selector'=>$tmp[0] );
				}
			}

		}
		$_type = 'new';
		if(  !empty($data['saved_file'])  ){
			if( $data['saved_file'] && file_exists(FRAMEWORK_CUSTOMZIME_STYLE.$data['saved_file'].'.css') ){
				unlink( FRAMEWORK_CUSTOMZIME_STYLE.$data['saved_file'].'.css' );
			}
			if( $data['saved_file'] && file_exists(FRAMEWORK_CUSTOMZIME_STYLE.$data['saved_file'].'.json') ){
				unlink( FRAMEWORK_CUSTOMZIME_STYLE.$data['saved_file'].'.json' );
			}
			if( empty($_POST['newfile']) ){
				$nameFile = $data['saved_file'];
			}else {
				$nameFile = preg_replace("#\s+#", "-", trim($_POST['newfile']));
				$_type='edit';
			}
		}else {
			if( empty($_POST['newfile']) ){
				$nameFile = time();
			}else {
				$nameFile = preg_replace("#\s+#", "-", trim($_POST['newfile']));
			}
		}

		if( !empty($output) ){
	 		$this->writeToCache( FRAMEWORK_CUSTOMZIME_STYLE, $nameFile, $output );
	 	}
	 	if( !empty($cache) ){
	 		$this->writeToCache( FRAMEWORK_CUSTOMZIME_STYLE, $nameFile, json_encode($cache),"json" );
	 	}
	 	echo json_encode(array('type'=>$_type,'newname'=>$nameFile,'oldname'=>$data['saved_file']));
		exit();
	}

	public function writeToCache( $folder, $file, $value, $e='css' ){
		$file = $folder  . preg_replace('/[^A-Z0-9\._-]/i', '', $file).'.'.$e ;
		$handle = fopen($file, 'w');
    	fwrite($handle, ($value));
    	fclose($handle);
	}

	/**
	 *
	 */
	public function ajax_Delete(){
		$file = $_POST['file'];
		if( !empty( $file ) ) {
			if( file_exists( FRAMEWORK_CUSTOMZIME_STYLE.$file.'.css' ) ){
				unlink( FRAMEWORK_CUSTOMZIME_STYLE.$file.'.css' );
			}
			if( FRAMEWORK_CUSTOMZIME_STYLE.$file.'.json' ){
				unlink( FRAMEWORK_CUSTOMZIME_STYLE.$file.'.json' );
			}
		}
		exit();
	}


	/**
	 *
	 */
	public function l($text){
		return __($text,TEXTDOMAIN);
	}

}
}