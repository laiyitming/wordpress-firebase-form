<?php
/*
Plugin Name: Wordpress Forms with Firebase
Description: This plugin is used to create forms to save data on firbase server 
Version: 0.1.0
Author: Christian Lai Yit Ming
*/

//Store session data in MySQL Table
if (!defined('FIREBASE_FORMS_SESSION')) define( 'FIREBASE_FORMS_SESSION', 'firebaseform_session');

//Code to manage session START
global $wpdb;
include_once plugin_dir_path( __FILE__ ).'manage-session.php';
$sessionob = new manageSession($wpdb);
session_set_save_handler (array($sessionob, 'ses_open'),
	array($sessionob, 'ses_close'),
	array($sessionob, 'ses_read'),
	array($sessionob, 'ses_write'),
	array($sessionob, 'ses_destroy'),
	array($sessionob, 'ses_gc'));
//End

	
class firebase_forms{
	
	function __construct(){				
		if (!session_id()) {
			session_start();
		}	
				
		add_action( 'wp_enqueue_scripts', array( $this, 'addjscss') );				
		add_action( "wp_ajax_loginuser", array( $this, "loginuser"));
		add_action( "wp_ajax_nopriv_loginuser", array( $this, "loginuser") );	
		add_action( "wp_ajax_ff_notificationtoken_ajax", array( $this, "ff_notificationtoken_ajax"));
		add_action( "wp_ajax_nopriv_ff_notificationtoken_ajax", array( $this, "ff_notificationtoken_ajax") );	
		
		add_action( 'admin_enqueue_scripts', array( $this, 'addadminjscss') );
		add_action( 'admin_menu', array( $this, 'firebase_admin_settings') );	
			
		add_shortcode('firebase_form_shortcode', array( $this, 'firebase_form_shortcode' ));	
		add_shortcode('firebase_contest_shortcode', array( $this, 'firebase_contest' ));	
	}
	
	function loginuser() {
		
		$response ["success"] = 0;
		$response["msg"] = "Invalid request";
		
		if(isset($_REQUEST["logout"])){
			if(isset($_SESSION["uid"])){
				unset($_SESSION["uid"]);
				unset($_SESSION["name"]);
				unset($_SESSION["email"]);
				unset($_SESSION["photo"]);
				unset($_SESSION["anonymous"]);
				$response["success"] = 1;
			}else{
				$response["success"] = 0;
			}
			$response["msg"] = "";
		}else{
			if(isset($_REQUEST["uid"])){
				if(!isset($_SESSION["uid"])){					
					$_SESSION["uid"] = sanitize_text_field($_REQUEST["uid"]);
					$_SESSION["name"] = sanitize_text_field($_REQUEST["name"]);
					$_SESSION["email"]= sanitize_text_field($_REQUEST["email"]);
					$_SESSION["photo"]= sanitize_text_field($_REQUEST["photo"]);
					$_SESSION["anonymous"]= sanitize_text_field($_REQUEST["anonymous"]);
					
					$response["success"] = 1;
					$response["msg"] = "User login";
				}else{
					$response["success"] = 0;
				}
			}
		}
		header('Content-Type: application/json');
		print json_encode($response);
		die;
	}
	
	function firebase_form_shortcode($attributes) {
		ob_start();				
		global $wpdb;
	            
		//Init firebase app
		include_once 'lib/init_firebase.php';	

		if(isset($_SESSION["uid"])){		
				
			include_once("view/header.php");
			print '<div class="row" id="fbformcontainer">';
            include_once("view/form-1.php");	
            print '</div>';	
			
		} else {					
			include_once("view/login.php");
		}		
		
		return ob_get_clean();	
	}
	
	function firebase_contest($attributes){
		ob_start();		
		$success = "";
		$error  = array();	
		
		//Init firebase app
		include_once 'lib/init_firebase.php';	
		
		if(!empty($attributes["contest"])){	
			$query["query"]["bool"]["must"] = array("match"=> array("contest_id" => $attributes["contest"])); 			
			$data= array(
					"index" => "contest",
					"type" => "users",
					"post_data" => $query);
			$data = json_encode($data);
				
			$response = $this->firebase_call_api( FIREBASE_ES_API, $data, FIREBASE_AWS_API_SECRET);
			if($users = json_decode($response)){	
				$participants = json_decode($users->message);									
				$all_imags = $participants->hits->hits;				
			}					
			include_once("view/contest.php");			
		}else{
			echo "Invalid contest";
		}	
		return ob_get_clean();	
	}
	
	function firebase_call_api( $apiurl, $data, $apisecret){	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		               array('Content-Type:application/json',
		                     'x-api-key: '.$apisecret)
		               );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$output = curl_exec($ch);
		$response = curl_getinfo($ch);
		curl_close($ch);
		return $output;		
	}
	
	/**
	 * This function is used to add option to admin nav bar
	 */
	function firebase_admin_settings() {
		add_menu_page('Firebase Contests', 'Firebase Contests', 'manage_options', 'firebase-contest-list', array($this, 'firebase_contest_list'),'',5);
		add_submenu_page('options-writing.php','Firebase Contest Users','Firebase Contest Users','manage_options','firebase-contest-users', array($this, 'firebase_contest_users'));
	}	

	function firebase_contest_users(){
		include_once 'lib/init_firebase.php';	
		$success = "";
		$error  = array();			
		if(isset($_GET["cidusers"]) && !empty($_GET["cidusers"])){
			include_once 'lib/firebaseLib.php';				
			$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
			
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				if($_POST["action"] == ""){
					$error[] = "Please select action"; 	
				}
				if(!isset($_POST["imgs"]) || (isset($_POST["imgs"]) && count($_POST["imgs"]) == 0)){
					$error[] = "Please select at least one record";
				}
				if(count($error) == 0){
					foreach ($_POST["imgs"] as $k=>$img){
						$imgdata = explode("$$$$", $img);
						$updata = array("status"=> $_POST["action"]);					
						$return = $firebase->update("/wp_fb_images/".$_GET["cidusers"]."/".$imgdata[0]."/".$imgdata[1], $updata);
					} 
					$success = "Selected records updated successfully";					
				}					
			}	
			
			$all_users = $firebase->get("/wp_fb_images/".$_GET["cidusers"]);
			$all_users = json_decode($all_users);			
			
		}else{
			$error[] = "Invalid contest"; 
		}
		
		$fname = __DIR__.'/view/admin/'.sanitize_file_name(strtolower($_GET['cname'])).'.php';
		print '<div class="row">';

		//Check if contest admin file is exist then include it if it is not then include default file
		if(file_exists($fname)){
			include_once $fname;
		}else{
			include_once 'view/admin/contest-users.php';
		}       
        print '</div>';	
	}
	
	function firebase_contest_list(){
		include_once 'lib/init_firebase.php';	
			
		if(isset($_SESSION["uid"])){
			$success = "";
			$error  = array();		
			
			include_once 'lib/firebaseLib.php';				
			$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
			
			if(isset($_GET["dcid"]) && strlen(trim($_GET["dcid"])) > 0){
				$firebase->delete("/wp_fb_contests/" . $_REQUEST ['dcid'] );
				$success = "Contest deleted successfully";
				?> 
				<script>
					window.location.href = "<?php echo remove_query_arg(array("dcid"))?>";
				</script>
				<?php 
			}
			
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				if(empty($_POST["cname"])){
					$error[] = "Please enter contest name"; 	
				}				
				$autoapprove = empty($_POST["autoapprove"])? 0:1;
				if(count($error) == 0){
					if(isset($_GET["cid"]) && strlen(trim($_GET["cid"])) > 0){
						$updata = array("name"=> $_POST["cname"], "auto_approve" => $autoapprove);
						$return = $firebase->update( "/wp_fb_contests/".$_GET["cid"], $updata);
						$success = "Contest updated successfully";
					}else{
						$updata = array("name"=> $_POST["cname"],"createdate" => date("Y-m-d H:i:s"));
						$return = $firebase->push ( "/wp_fb_contests", $updata);
						$success = "Contest added successfully";				
					}	
				}					
			}
			if(isset($_GET["cid"]) && strlen(trim($_GET["cid"])) > 0){
				$contest = $firebase->get("/wp_fb_contests/".$_GET["cid"]);
				$contest = json_decode($contest);
				$_POST["cname"] = $contest->name;
				$_POST["auto_approve"] = $contest->auto_approve;
			}
			$all_contests = $firebase->get("/wp_fb_contests");
			$all_contests = json_decode($all_contests);
			
			print '<div class="row">';			
            include_once 'view/admin/contest.php';
            print '</div>';	
            		
		} else {					
			include_once("view/login.php");
		}			
	}
	
	function addjscss(){	
		global $post;
		//If page contain shortcode "firebase_form_shortcode" Then only load firebase JS lib and css.
		if (!is_search() && (has_shortcode($post->post_content, 'firebase_form_shortcode') || has_shortcode($post->post_content, 'firebase_contest_shortcode')) ) {	
			wp_enqueue_style( 'js-firebasecss', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
			wp_enqueue_style( 'js-firebasecss-common', plugins_url( '/css/common.css', __FILE__ ));	
			wp_enqueue_script('jquery');
			if(has_shortcode($post->post_content, 'firebase_form_shortcode')){
				wp_enqueue_script( 'js-firebasejs', 'https://www.gstatic.com/firebasejs/4.8.1/firebase.js', false );
				wp_register_script( 'js-firebaselogin', plugins_url( '/js/common.js?v='.time(), __FILE__ ));
				wp_localize_script( 'js-firebaselogin', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
				wp_enqueue_script( 'js-firebaselogin' );
				wp_register_script( 'js-firebasemsgappjs', 'https://www.gstatic.com/firebasejs/4.4.0/firebase-app.js', false );
				wp_register_script( 'js-firebasemsgjs', 'https://www.gstatic.com/firebasejs/4.4.0/firebase-messaging.js', false );			
				wp_register_script( 'js-firebaseuijs', 'https://cdn.firebase.com/libs/firebaseui/2.5.1/firebaseui.js', false );
				wp_enqueue_script( 'js-firebaseuijs' );	
				wp_enqueue_script( 'js-sel2-js',plugins_url( '/js/select2.min.js', __FILE__ ), false );	
				wp_enqueue_style( 'js-sel2-uicss', plugins_url( '/css/select2.min.css', __FILE__ ) );				
				wp_enqueue_style( 'js-firebaseuicss', 'https://cdn.firebase.com/libs/firebaseui/2.5.1/firebaseui.css' );
				
			}
		}
	}
	
	function addadminjscss(){
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'js-firebasecss-admin', plugins_url( '/css/admin.css', __FILE__ ));			
		wp_enqueue_style( 'js-firebasecss', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
		wp_enqueue_script( 'js-firebasejs', 'https://www.gstatic.com/firebasejs/4.8.1/firebase.js', false );
		wp_register_script( 'js-firebasemsgappjs', 'https://www.gstatic.com/firebasejs/4.4.0/firebase-app.js', false );
		wp_register_script( 'js-firebasemsgjs', 'https://www.gstatic.com/firebasejs/4.4.0/firebase-messaging.js', false );				
		wp_register_script( 'js-firebaseuijs', 'https://cdn.firebase.com/libs/firebaseui/2.5.1/firebaseui.js', false );
		wp_enqueue_script( 'js-firebaseuijs' );
		wp_enqueue_style( 'js-firebaseuicss', 'https://cdn.firebase.com/libs/firebaseui/2.5.1/firebaseui.css' );			
		wp_register_script( 'js-firebaselogin', plugins_url( '/js/admin.js?v='.time(), __FILE__ ));
		wp_localize_script( 'js-firebaselogin', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
		wp_enqueue_script( 'js-firebaselogin' );	
		
	}
}
new firebase_forms();
?>
