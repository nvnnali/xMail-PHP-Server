<?php
/*
MISC FUNCTIONS USED BY SERVER
*/
function valid($input){
	$input = trim($input);
	return isset($input) && $input!=null && $input!="";
}

function clean($input){
	if(get_magic_quotes_gpc()){
		$input = stripslashes($input);
	}
	//$input = mysql_real_escape_string($input);
	$input = htmlentities($input, ENT_COMPAT, "UTF-8");
	return $input;
}
?>