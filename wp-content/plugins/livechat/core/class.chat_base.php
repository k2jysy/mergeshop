<?php
/**
 * SCREETS-SESSION © 2013
 * Chat Base Class ( used by Chat Log and Chat User classes )
 *
 */

 
class Chat_base {
	
	/**
	 * Constructor
	 * @since 1.0
	 */
	public function __construct( array $opts ) {
		
		// Declare options as parameter
		foreach( $opts as $k => $v ){
			
			if( isset( $this->$k ) )
				$this->$k = $v;
				
		}
	}
	
}
?>