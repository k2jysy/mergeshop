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
if(!class_exists('WPO_Template')){
	class WPO_Template{

		public static function getImageLayout(){
			return array(
				'0-1-0'    	=> FRAMEWORK_ADMIN_IMAGE_URI . '1col.png',
	            '1-1-0'  	=> FRAMEWORK_ADMIN_IMAGE_URI . '2cl.png',
	            '0-1-1'  	=> FRAMEWORK_ADMIN_IMAGE_URI . '2cr.png',
	            '1-1-1'    	=> FRAMEWORK_ADMIN_IMAGE_URI . '3c.png',
	            '1-1-m'     => FRAMEWORK_ADMIN_IMAGE_URI . '3c-l-l.png',
            	'm-1-1'     => FRAMEWORK_ADMIN_IMAGE_URI . '3c-r-r.png'
	        );
		}

		public function configLayout($layout,$config=array()){
			switch ($layout) {
				// Two Sidebar
				case '1-1-1':
					$config['container'] 				='two-sidebar';
					$config['left-sidebar']['show'] 	= true;
					$config['left-sidebar']['class'] 	='col-sm-6 col-md-3 col-md-pull-6';
					$config['right-sidebar']['class']	='col-sm-6  col-md-3';
					$config['right-sidebar']['show'] 	= true;
					$config['main']['class'] 			='col-xs-12 col-md-6 col-md-push-3';
					break;
				//One Sidebar Right
				case '0-1-1':
					$config['container'] 				='one-sidebar-right';
					$config['left-sidebar']['show'] 	= false;
					$config['right-sidebar']['show'] 	= true;
					$config['main']['class']  			='col-xs-12 col-sm-8 col-md-9 no-sidebar-left';
					$config['right-sidebar']['class'] 	='col-xs-12 col-sm-4  col-md-3';
					break;
				// One Sidebar Left
				case '1-1-0':
					$config['container'] 				='one-sidebar-left';
					$config['left-sidebar']['show'] 	= true;
					$config['right-sidebar']['show'] 	= false;
					$config['left-sidebar']['class'] 	='col-xs-12 col-sm-4 col-sm-pull-8 col-md-3 col-md-pull-9';
					$config['main']['class'] 			='col-xs-12 col-sm-8 col-sm-push-4 col-md-9 col-md-push-3 no-sidebar-right';
					break;

				case 'm-1-1':
					$config['container'] 				='two-sidebar-right';
					$config['left-sidebar']['show'] 	= true;
					$config['left-sidebar']['class'] 	='col-sm-6 col-md-3';
					$config['right-sidebar']['class']	='col-sm-6  col-md-3';
					$config['right-sidebar']['show'] 	= true;
					$config['main']['class'] 			='col-xs-12 col-md-6';
					break;

				case '1-1-m':
					$config['container'] 				='two-sidebar-left';
					$config['left-sidebar']['show'] 	= true;
					$config['right-sidebar']['show'] 	= true;
					$config['left-sidebar']['class'] 	='col-sm-6 col-md-3 col-md-pull-6';
					$config['right-sidebar']['class']	='col-sm-6  col-md-3 col-md-pull-6';
					$config['main']['class'] 			='col-xs-12 col-md-6 col-md-push-6';
					break;
				// Fullwidth
				default:
					$config['left-sidebar']['show'] 	= false;
					$config['right-sidebar']['show'] 	= false;
					$config['main']['class'] 			='col-xs-12 no-sidebar';
					break;
			}
			return $config;
		}

		/**
		 * share box
		 */
		public function getShareBox($args,$layout=''){
			$path = FRAMEWORK_TEMPLATES.'sharebox';
			if( $layout!='' ){
				$path = $path.'-'.$layout;
			}
			$path .= '.php';

			if( is_file($path) ){
				require($path);
			}
		}

		/**
		 *
		 */
		public function getBlog(){
			$blog = array( '' => 'default' );
			$path = FRAMEWORK_TEMPLATES.'blog/blog-*';

			$files = $this->getFile($path);
			if( count($files)>0 ){
				foreach( $files as $dir ){
			        $file = str_replace('blog-','',str_replace( ".php","", basename( $dir )) );
			        $blog[$file] = $file;
			    }
			}
			return $blog;
		}

		/**
		 *
		 */
		public function getPortfolio(){
			$portfolio = array( '' => 'default' );
			$path = FRAMEWORK_TEMPLATES.'portfolio/portfolio-*';

			$files = $this->getFile($path);
			if( count($files)>0 ){
				foreach( $files as $dir ){
			        $file = str_replace('portfolio-','',str_replace( ".php","", basename( $dir )) );
			        $portfolio[$file] = $file;
			    }
			}
			return $portfolio;
		}

		/**
		 *
		 */
		public function getMasonry(){
			$masonry = array( '' => 'default' );
			$path = FRAMEWORK_TEMPLATES.'masonry/masonry-*';

			$files = $this->getFile($path);
			if( count($files)>0 ){
				foreach( $files as $dir ){
			        $file = str_replace('masonry-','',str_replace( ".php","", basename( $dir )) );
			        $masonry[$file] = $file;
			    }
			}
			return $masonry;
		}

		/**
		 *
		 */
		private function getFile( $path, $extensions='php' ){
			$file = glob( $path.$extensions );
			return $file;
		}

	}
}