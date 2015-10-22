<?php
/* $Desc
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
if(!class_exists('WPO_Shortcode_Base')){
abstract class WPO_Shortcode_Base{

	protected $name;
	protected $options = array();
	protected $key;
	protected $is_content='';
	protected $excludedMegamenu = false;
	private $params;

	public function __construct( ){
		$this->setInputContent();
		$this->params =  WPO_Params::getInstance();
	}

	public function getName(){
		return $this->name;
	}

	public function getButton( $data=null ){
		return array( 'title' => '' , 'desc'=>'' );
	}

	public function getOptions(){

	}

	public function isExcludedMenu(){
		return $this->excludedMegamenu;
	}

	protected function setInputContent(){
		foreach ($this->options as $option) {
			if(isset($option['content']) && $option['content']==true){
				$this->is_content = $option['id'];
				break;
			}
		}
	}

	/**
	 *
	 */
	public function makeCode( $inputs ){
		$string = '['.$this->key;
			foreach ($inputs as $key => $input) {
				if($key!=$this->is_content){
					if($input!=''){
						$string .= ' '.$key.'=\''.$this->checkInputValue($input).'\'';
					}
				}
			}
		$string.= ']';
		if($this->is_content!=''){
			$string.=$inputs[$this->is_content];
		}
		$string.= ' [/'.$this->key.'] ';
		return $string;
	}

	private function checkInputValue( $string ){
		$array = array (
	        '\''        => '&#39;',
	        '"'        => '&#34;'
	    );
	    $string = strtr($string, $array);
	    return $string;
	}

	public function getAttrs( $atts=array() ){
		 return shortcode_atts(
            array(
            'class' => '',
        ), $atts );
	}

	public function renderForm($type='',$id=0){
		$this->getOptions();

		$name = $this->setValueInput($id);

		$default = array(
			'default' => '',
			'hint'   => ''
		);
	?>
		<form id="wpo-shortcode-form" role="form">
			<div class="form-group">
				<label for="shortcode_name">Name:</label>
				<input value="<?php echo $name; ?>" class="form-control" type="text" id="shortcode_name" name="shortcode_name">
			</div>
	<?php
		foreach($this->options as $option){

			$option = array_merge( $default, $option );
			$explain = '';

			if( $option['explain'] ){
				$explain = '<em class="explain">'.$option['explain'].'</em>';
			}
			if(is_array($option['default'])){
				//var_dump($option['default']);
			}else{
				if( !trim($option['default']) ){
			 		$option['default'] = $option['hint'];
			 	}
			}
		 	
		 	$this->params->getParam($option);
		 }
	?>
			<span class="spinner spinner-button" style="float:left;"></span>
			<button type="button" class="btn btn-primary wpo-button-save"><?php echo $this->l('Save'); ?></button>
			<button type="button" class="btn btn-default wpo-button-back"><?php echo $this->l('Back to list'); ?></button>
			<input type="hidden" name="shortcodetype" value="<?php echo $type; ?>">
			<input type="hidden" name="shortcodeid" value="<?php echo $id; ?>">
		</form>
	<?php
	}

	private function setValueInput($id=0){
		$name ='';
		if($id==0){
			for($i=0;$i<count($this->options);$i++){
				if(!isset($this->options[$i]['default'])){
					$this->options[$i]['default']='';
				}
			}
		}else{
			$obj = WPO_Megamenu_Widget::getInstance();
			$values = $obj->getWidgetById($id);
			if( is_array($values->params) ){
				foreach ($values->params as $key => $value) {
					for($i=0;$i<count($this->options);$i++){
						if($this->options[$i]['id']==$key){
							if(is_array($value)){
								$value = implode(',',$value);
							}
							$this->options[$i]['default']=$value;
							break;
						}
					}
				}
			}
			$name=$values->name;
		}
		return $name;
	}

	private function renderUploader( $_id, $_name ) {
		$output = '';
		$id = '';
		$name = '';

		$id = strip_tags( strtolower( $_id ) );

		// If a value is passed and we don't have a stored value, use the value that's passed through.
		if ( $_value != '' && $value == '' ) {
			$value = $_value;
		}

		if ($_name != '') {
			$name = $_name;
		}
		else {
			$name = $option_name.'['.$id.']';
		}

		$output .= '<input id="' . $id . '" class="upload" type="text" name="'.$name.'" placeholder="' . __('No file chosen', TEXTDOMAIN) .'" />' . "\n";
		if ( function_exists( 'wp_enqueue_media' ) ) {
			if ( ( $value == '' ) ) {
				$output .= '<input id="upload-' . $id . '" class="upload-button button" type="button" value="' . __( 'Upload', TEXTDOMAIN ) . '" />' . "\n";
			} else {
				$output .= '<input id="remove-' . $id . '" class="remove-file button" type="button" value="' . __( 'Remove', TEXTDOMAIN ) . '" />' . "\n";
			}
		} else {
			$output .= '<p><i>' . __( 'Upgrade your version of WordPress for full media support.', TEXTDOMAIN ) . '</i></p>';
		}

		if ( $_desc != '' ) {
			$output .= '<span class="of-metabox-desc">' . $_desc . '</span>' . "\n";
		}

		$output .= '<div class="screenshot" id="' . $id . '-image">' . "\n";
		$output .= '</div>' . "\n";
		return $output;
	}

	public function l( $text ){
		return __( $text, TEXTDOMAIN );
	}

	/**
	 * this method check overriding layout path in current template
	 */
	public function render( $atts ){
		$tpl = FRAMEWORK_TEMPLATES .'shortcodes/'.$this->name.'.php';
		$tpl_default = FRAMEWORK_SHORTCODE.'tpl/'.$this->name.'.php';
		ob_start();
		if(  is_file($tpl) ){
			require($tpl);
		}else if( is_file($tpl_default) ){
			require($tpl_default);
		}
		return ob_get_clean();
	}
}
}