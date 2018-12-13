<?php
/**
 * @name manageSession class
* @desc This class is used to insert data and fetch data from tables
* @author Christian Lai Yit Ming
*/
global $wpdb;
if (!defined('SESSION_TABLE')) define("SESSION_TABLE", $wpdb->prefix.FIREBASE_FORMS_SESSION);

class manageSession {
	public $wpdbobj;
	function __construct($wpdb){		
		$this->wpdbobj = $wpdb;
	}
	
	function ses_open($path, $name) {
		return TRUE;
	}
	
	function ses_close() {
		return TRUE;
	}
	
	function ses_read($ses_id) {		
		$result = array();
		$sql = "SELECT `data` FROM ".SESSION_TABLE." WHERE `id` = '".$ses_id."'";
		$result = $this->wpdbobj->get_results($sql);
		// Session nicht gefunden, return leeren String
		if (isset($result) && count($result) == 0){
			return '';
		}
		return $result[0]->data;
	}
	
	//------------------------------------
	
	function ses_write($ses_id, $data) {		
		$result = array();			
		// is a session with this id in the database?
		$qu = "SELECT * FROM ".SESSION_TABLE." WHERE id = '".$ses_id."'";
		$result = $this->wpdbobj->get_results($qu);
		//error_log("SESSION DATA :".$data);
		if(isset($result) && count($result) > 0) {
			$session_update = "UPDATE
			".SESSION_TABLE."
			SET
			expire = '".time()."',
			data = '".$data."'
			WHERE
			id = '".$ses_id."'";
			$this->wpdbobj->query($session_update);
			return true;
		}else {
			$session_insert = "INSERT INTO ".SESSION_TABLE."(`id`, `expire`, `data`) VALUES ('".$ses_id."', '".time()."','".$data."')";
			$this->wpdbobj->query($session_insert);
			return true;
		}
		// an unknown error occured
		return false;
	}
	
	//------------------------------------
	
	function ses_destroy($ses_id) {
		$result = array();
		$sql = "DELETE FROM ".SESSION_TABLE." WHERE `id` = '".$ses_id."'";
		return (bool)$this->wpdbobj->query($sql);
	}
	
	//------------------------------------
	
	function ses_gc($life) {
		$result = array();
		$ses_life = time()-$life;
		$sql = "DELETE FROM ".SESSION_TABLE." WHERE `expire` < ".$ses_life."";
		return (bool)$this->wpdbobj->query($sql);
	}
	
}