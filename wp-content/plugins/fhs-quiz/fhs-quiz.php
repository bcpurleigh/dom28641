<?php
/*
Plugin Name: FHS Quiz
Description: All Quiz Functionality under this plugin.
Version: 1.0
Author: BuddyBoss
Author URI: http://www.buddyboss.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class FHS_Quiz{
	var $plugin_url;
	var $plugin_dir;
	var $plugin_prefix;
	var $plugin_version;
	var $domain;
	var $c; //contain the classes

	function __construct() {
		$this->plugin_version = '1.0';
		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_url     = plugin_dir_url( __FILE__ );
		$this->plugin_prefix  = 'fhs_quiz';
		$this->domain         = 'fhs_quiz';
		$this->db_version     = '0.1';
		$this->c              = new stdClass();
		//register all hooks.
		$this->hooks();
		$this->load_classes();
		register_activation_hook( __FILE__, array( $this, 'on_plugin_activate' ) );
	}

	function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'on_plugin_activate' ) );
	}

	/*
	* load all core style and js files.
	*/
	function enqueue_assets() {}

	/*
	* load all core style and js files for backend.
	*/
	function admin_enqueue_assets() {}

	/*
	* Load the plugin language
	*/
	function load_textdomain() {
		load_plugin_textdomain( $this->domain, false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	private function get_class_file_name( $class_name ) {
		return 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
	}

	function load_classes() {

		// include all metaboxes
		$include_files = array(
			'include/functions.php'
		);

		foreach( $include_files as $include_file ){
			$path =  $this->plugin_dir . $include_file;
			if ( file_exists( $path ) ) {
				include $path;
			}
		}

		//inclue all classes
		spl_autoload_register( function ( $class_name ) {
			$bbclasspath = array(
				'include/' . $this->get_class_file_name( $class_name ),
			);

			foreach ( $bbclasspath as $path ) {
				$path =  $this->plugin_dir . $path;
				if ( file_exists( $path ) ) {
					include $path;
					break;
				}
			}
		} );

		$this->c->fhs_quiz_public = new FHS_Quiz_Public();

	}

	function on_plugin_activate() {}

}

/*
 * Easy to call function.
 **/
function fhs_quiz() {
	global $fhs_quiz;
	return $fhs_quiz;
}

function fhs_quiz_init(){
	global $fhs_quiz;
	//load the main class
	$fhs_quiz = new FHS_Quiz();
}
add_action( 'plugins_loaded', 'fhs_quiz_init', 10 );
