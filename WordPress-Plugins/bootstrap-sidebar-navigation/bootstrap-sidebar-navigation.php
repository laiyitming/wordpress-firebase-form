<?php
/*
Plugin Name: Boostrap sidebar navigation
Description: This plugin is used to add sidebar in pages, use shorcode [bootstrap_sidebar_navigation] to add sidebar. 
Version: 0.1.0
Author: Christian Lai Yit Ming
*/
global $wpdb;

class bootstrap_sidebar_navigation{
	
	function __construct(){	
		add_action( 'wp_enqueue_scripts', array($this, 'bootstrap_sidebar_jscss'));		
		add_shortcode( 'bootstrap_sidebar_navigation', array($this, 'bootstrap_sidebar_navigation_init') );		
	}	
	/**
	 * Load javascript and styles 
	 * @param string $hook
	 */
	function  bootstrap_sidebar_jscss($hook) {	
		global $post;
		if ( has_shortcode($post->post_content, 'bootstrap_sidebar_navigation') ) {
			wp_enqueue_script('jquery');
			wp_register_script('bootstrap_sidebar_js', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");
			wp_enqueue_script( 'bootstrap_sidebar_js' );

			wp_register_script( 'bootstrap_sidebar_ajaxjs', plugins_url( '/js/bootstrap-sidebar.js', __FILE__ ));
			wp_localize_script( 'bootstrap_sidebar_ajaxjs', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script( 'bootstrap_sidebar_ajaxjs' );
			
			wp_register_style( 'bootstrap_sidebar_css', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" );
	        wp_enqueue_style( 'bootstrap_sidebar_css' );
	        
			wp_register_style( 'bootstrap_sidebar_inlinecss', plugins_url( '/css/bootstrap-sidebar.css', __FILE__ ) );
	        wp_enqueue_style( 'bootstrap_sidebar_inlinecss' );
		}        	
	}	
	
	function bootstrap_sidebar_navigation_init(){		
		ob_start();
		print '<script> var pluginUrl = "'.plugins_url().'";</script>';
		//If user logged in to firebase then only show sidebar navigation
		if(isset($_SESSION["uid"]) && strlen(trim($_SESSION["uid"])) > 0){
			include_once("view/navigation.php");
		}
		
		return ob_get_clean();
	}	
}
new bootstrap_sidebar_navigation();
?>