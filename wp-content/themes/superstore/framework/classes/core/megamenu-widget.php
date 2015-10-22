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
if(!class_exists('WPO_Megamenu_Widget')){
class WPO_Megamenu_Widget  {

	private $widgets = array();

	public static function getInstance(){
		static $_instance;
		if( !$_instance ){
			$_instance = new WPO_Megamenu_Widget();
		}
		return $_instance;
	}

	public function __construct(  ){
		$this->createMegaMenuTables();
	}

	public function createMegaMenuTables(){
		global $wpdb;
		$table_name = $wpdb->prefix . "megamenu_widgets";
		if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
			$sql = "
				CREATE TABLE IF NOT EXISTS `{$table_name}` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(250) NOT NULL,
					`type` varchar(255) NOT NULL,
					`params` text NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}

	/*
	 * get list of widget rows.
	 */
	public function getWidgets(){
		global $wpdb;
		$sql = ' SELECT * FROM '.DB_PREFIX.'megamenu_widgets ';
	 	$rows = $wpdb->get_results( $sql );
	 	$output = array();
	 	if( $rows ){
	 		foreach( $rows as $i => $row ){
	 			if( $row->params ){
	 				$row->params = unserialize(  $row->params );
	 			}
	 			$output[$row->id] = $row;
	 		}
	 	}
		return $output;
	}

	/**
	 *
	 */
	public function loadWidgets(){
		if( empty($this->widgets) ){
			$this->widgets = $this->getWidgets();
		}
		return $this;
	}

	public function delete( $id ){
		global $wpdb;
		return $wpdb->delete( DB_PREFIX.'megamenu_widgets', array( 'id' => $id ), array( '%d' ) );
	}

	/**
	 * get widget data row by id
	 */
	public function getWidgetById( $id ){
		if( isset($this->widgets[$id]) ){
			return $this->widgets[$id];
		}else {
			global $wpdb;
			$sql = ' SELECT * FROM '.DB_PREFIX.'megamenu_widgets WHERE id='.(int)$id;
		 	$rows = $wpdb->get_results( $sql );
	 	 	if( isset($rows[0]) ){
				$rows[0]->params = unserialize(  $rows[0]->params );
			}
			return $rows[0];
		}
		return $output;
	}

	/**
	 * Save Data Post in database
	 */
	public function saveData( $post ){
		$data = array(
			'id'	 => '',
			'params' => '',
			'type'	 => ''
		);

		$data = array_merge( $data, $post );

		if( $data['params'] ){
			$data['params'] = serialize( $data['params'] );
		}
		$id = $data['id'];

		unset( $data['id'] );

		if( $id ){
			$sql = ' UPDATE  '.DB_PREFIX.'megamenu_widgets SET ';
			foreach( $data as $key => $value ){
				$tmp[] = "`".$key."`='".$this->db->escape( $value )."'";
			}
			$sql .= implode( ',',$tmp ) . ' WHERE id='.(int)$id;

			$this->db->query( $sql );

		}else {
			$sql = ' INSERT INTO '.DB_PREFIX.'megamenu_widgets('.implode(',', array_flip($data) ).')';
			$tmp = array();
			foreach( $data as $value ){
				$tmp[] = "'".$this->db->escape( $value )."'";
			}
			$sql .= " VALUES(".implode(',',$tmp).") ";

			$this->db->query( $sql );
			$id = $this->db->getLastId();
		}

	 	$data['id'] = $id;

		return $data;
	}
}
}