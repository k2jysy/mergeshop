<?php
/* $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Brainweb  Team < support@brainweb.vn>
 * @copyright  Copyright (C) 2014 brainweb.vn. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.brainweb.vn
 * @support  http://www.brainweb.vn/support/forum.html
 */

if(!class_exists('Wpo_Megamenu_Sub')){
class Wpo_Megamenu_Sub extends Walker_Nav_Menu {

	protected $megaConfig = array();

	protected $isLiveEdit = false;

	protected $widgets = array();

	protected $currentMegaconfig = null;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */

	public function __construct() {
		add_filter('nav_menu_css_class' , array($this, 'special_nav_class'), 10 , 2);
	}

	/**
	 * special_nav_class function.
	 *
	 * @access public
	 * @param mixed $classes
	 * @param mixed $item
	 * @return void
	 */
	public function special_nav_class($classes, $item){
		if(in_array('current-menu-item', $classes)){
			$classes[] = 'active ';
		}
		return $classes;
	}

    /**
     * start_lvl function.
     *
     * @access public
     * @param mixed &$output
     * @param mixed $depth
     * @return void
     */
   public  function start_lvl(  &$output, $depth = 0, $args = array(), $megaconfig=null ) {
		$indent = str_repeat( "\t", $depth );
		$class = 'dropdown-menu';
		$fcolswidth = 12;
		$cattrs = '';
		$attrw = '';
		$colclass='';
		if( isset($megaconfig->group) && $megaconfig->group ){
			$class = 'dropdown-mega';
		}
		if( isset($megaconfig->subwidth) &&  $megaconfig->subwidth ){
			$attrw .= ' style="width:'.$megaconfig->subwidth.'px"' ;
		}

		if( isset($megaconfig) && isset($megaconfig->rows) ){
			if( isset($megaconfig->rows[0]->cols) && $megaconfig->rows[0]->cols ){
				$cattrs = $this->getColumnDataConfig($megaconfig->rows[0]->cols[0]);
 				$fcolswidth = isset($megaconfig->rows[0]->cols[0]->colwidth)?$megaconfig->rows[0]->cols[0]->colwidth:12;
 				$col = $megaconfig->rows[0]->cols[0];
 				$colclass = isset($col->colclass)?$col->colclass:'';
			}
		}
		$output	   .= "\n$indent<div class=\"{$class}\" $attrw ><div class=\"dropdown-menu-inner\"><div class=\"row\"><div class=\"mega-col col-md-{$fcolswidth} {$colclass}\" {$cattrs} data-type=\"menu\"><div class=\"mega-col-inner\"><ul class=\"megamenu-items\">\n";
	}


	/**
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array(),$megaConfig=null ) {

		$megaCols ='';
		$megaRows = '';
		if( isset($megaConfig) && isset($megaConfig->rows) ){
			if( isset($megaConfig->rows[0]->cols) ){
				unset($megaConfig->rows[0]->cols[0]);
				foreach( $megaConfig->rows[0]->cols as $col ){

					$colclass = isset($col->colclass)?$col->colclass:'';
			 		$megaCols .= '<div class="mega-col col-md-'.$col->colwidth.' '.$colclass.'" '.$this->getColumnDataConfig( $col ).'> <div class="mega-col-inner">';
			 			$megaCols .= $this->renderWidgetsInCol( $col );
				 	$megaCols .= '</div></div>';
				}
				unset( $megaConfig->rows[0] );
			}

			if( $megaConfig->rows ){
				foreach( $megaConfig->rows as $row ){
					$megaRows .= '<div class="row">';
						foreach( $row->cols as $col ){
							$colclass = isset($col->colclass)?$col->colclass:'';
							 $megaRows .= '<div class="mega-col col-md-'.$col->colwidth.' '.$colclass.'" '.$this->getColumnDataConfig( $col ).'> <div class="mega-col-inner">';
							 	$megaRows .= $this->renderWidgetsInCol( $col );
							 $megaRows .= '</div></div>';
						}
					$megaRows .= '</div>';
				}
			}
		}
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul></div></div>{$megaCols}</div>{$megaRows}</div></div>\n";
		$this->currentMegaconfig = null;
	}

	/**
	 * start_el function.
	 *
	 * @access public
	 * @param mixed &$output
	 * @param mixed $item
	 * @param int $depth (default: 0)
	 * @param array $args (default: array())
	 * @param int $id (default: 0)
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item->megaconfig = new stdClass();
		$hasChilds = $args->has_children;
		$depth = $depth+1;
		$custom_configs = apply_filters( 'wpo_menu_custom_configs' ,array() );

		if( isset($this->megaConfig[$item->ID]) ){
			$item->megaconfig = $this->megaConfig[$item->ID];
			$args->has_children = true;
		}

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		//echo $depth;
		$li_attributes = '';
		$class_names = $value = '';

		$classes = array();


		$classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';
		$classes[] = 'menu-item-' . $item->ID;
		foreach ($custom_configs as  $custom_config) {
			if($custom_config['type']=='class'){
				$classes[] = $item->$custom_config['value'];
			}
		}
		//$classes[] = $item->additionclass;
		if(  $args->has_children){
			$classes[] = $depth>0 ?'dropdown-submenu parent mega':'dropdown parent mega';
			$classes[] ='depth-'.$depth;
		}

		if( isset($item->megaconfig->group) && $item->megaconfig->group ){
			$classes[] = 'mega-group';
		}
		if( isset($item->megaconfig->align) ){
			$classes[] = $item->megaconfig->align;
		}
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$li_attributes = $this->renderAttrs( $item, $args ,$depth );
		$output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$attributes .= ($args->has_children) 	    ? ' class="dropdown-toggle" ': '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= ($args->has_children) ? ' <b class="caret"></b></a>' : '</a>';
		$item_output .= $args->after;


		if( isset($item->megaconfig->rows) && $item->megaconfig->rows && !$hasChilds ){
		 	$item_output .= $this->genMegaMenuByConfig( $item , $depth );
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * display_element function.
	 *
	 * @access public
	 * @param mixed $element
	 * @param mixed &$children_elements
	 * @param mixed $max_depth
	 * @param int $depth (default: 0)
	 * @param mixed $args
	 * @param mixed &$output
	 * @return void
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
		else if ( is_object( $args[0] ) )
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );

		$cb_args = array_merge( array(&$output, $element, $depth), $args);

		call_user_func_array(array(&$this, 'start_el'), $cb_args);


		$id = $element->$id_field;

		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {


			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					$cb_args = array_merge( array(&$output, $depth), $args);
					if( isset($this->megaConfig[$element->ID]) ){
						array_push( $cb_args, $this->megaConfig[$element->ID]);

					}


					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}

				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
				unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			$cb_args = array_merge( array(&$output, $depth), $args);

			if( isset($this->megaConfig[$element->ID]) ){
				array_push( $cb_args, $this->megaConfig[$element->ID]);
			}

			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}

	/**
	 * display_element function.
	 *
	 * @access public
	 * @param stdClass $col
	 * @return String $output
	 */
	protected function getColumnDataConfig( $col ){
		$output = '';
		if( is_object($col) && $this->isLiveEdit ){
			$vars = get_object_vars($col);
			foreach( $vars as $key => $var ){
				$output .= ' data-'.$key.'="'.$var . '" ' ;
			}
		}
		return $output;
	}

	/**
	 * render menu attributes with data html format.
	 *
	 * @access public
	 * @param instance of this $menu
	 * @param mixed $args
	 * @return String $output
	 */
	protected function renderAttrs( $menu, $args ,$level){
		if( $this->isLiveEdit ) {
			$t = sprintf( ' data-id="%s"   ', $menu->ID );

			if( isset($menu->megaconfig->subwidth) &&  $menu->megaconfig->subwidth ){
				$t .= ' data-subwidth="'.$menu->megaconfig->subwidth.'" ';
			}

			$t .= ' data-submenu="'.(isset($menu->megaconfig->submenu)?$menu->megaconfig->submenu:$args->has_children).'"';
			$t .= ' data-align="'.(isset($menu->megaconfig->align)?$menu->megaconfig->align:"aligned-left").'"';

			return $t;
		}else{
			$t = sprintf( ' data-id="%s"   ', $menu->ID );

			//$t .= ' data-submenu="'.(isset($menu->megaconfig->submenu)?$menu->megaconfig->submenu:$args->has_children).'"';
			$align = explode('-', (isset($menu->megaconfig->align)?$menu->megaconfig->align:"aligned-left"));
			$t .= ' data-alignsub="'.$align[1].'"';
			$t .= ' data-level="'.($level+1).'"';
			return $t;
		}
	}

	/**
	 * render megamenu contentn by configation.
	 *
	 * @access protected
	 * @param stdClass $col
	 * @return String $output
	 */
	protected function genMegaMenuByConfig( $menu  ){

		$attrw = '';
		$class = $level > 1 ? "dropdown-submenu":"dropdown";
		$output = '';
			if( isset($menu->megaconfig->subwidth) &&  $menu->megaconfig->subwidth ){
				$attrw .= ' style="width:'.$menu->megaconfig->subwidth.'px"' ;
			}
			$class  = 'dropdown-menu mega-dropdown-menu';
			$output .= '<div class="'.$class.'" '.$attrw.' ><div class="dropdown-menu-inner">';

			foreach( $menu->megaconfig->rows  as $row ){

				$output .= '<div class="row">';
					foreach( $row->cols as $col ){
						$colclass = isset($col->colclass)?$col->colclass:'';
						 $output .= '<div class="mega-col col-md-'.$col->colwidth.' '.$colclass.'" '.$this->getColumnDataConfig( $col ).'> <div class="mega-col-inner">';
						 	$output .= $this->renderWidgetsInCol( $col );
						 $output .= '</div></div>';
					}
				$output .= '</div>';
			}
			unset($colclass);

			$output .= '</div></div>';
		return $output;
	}

	/**
	 * render list of  widgets for column.
	 *
	 * @access protected
	 * @param stdClass $col
	 * @return String $output
	 */
	protected function renderWidgetsInCol( $col ){
		 if( is_object($col) && isset($col->widgets)  ){
		 	$widgets = $col->widgets;
		 	$widgets = explode( '|wid-', '|'.$widgets );
			if( !empty($widgets) ){
				$dwidgets = WPO_Megamenu_Widget::getInstance()->loadWidgets();
				$shortcode =   WPO_Shortcodes::getInstance();
				unset( $widgets[0] );
				$output = '';
				foreach( $widgets as $wid ){
					$o = $dwidgets->getWidgetById( $wid );

					if( $o ){
						$content = !is_admin()?$shortcode->renderContent( $o->type, $o->params ):"";
						$output .= '<div id="wid-'.$wid.'" class="wpo-widget">'.$content.'</div>';
					}
				}
				return $output;
			}
		 }
	}
}
}