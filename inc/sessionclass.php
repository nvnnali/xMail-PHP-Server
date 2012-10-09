<?php
class Session{
	
	public $name = "Unknown";
	public $ip = "127.0.0.1";
	public $key = "nokey";
	
	function __construct($name, $ip, $key){
		$this->name = $name;
		$this->ip = $ip;
		$this->key = $key;
	}
	
}
?>