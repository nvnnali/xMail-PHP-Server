<?php
define("USERS_TABLE_NAME", "xmail_users");
define("USERS_USERNAME_COL", "username");
define("USERS_PASSWORD_COL", "password"); // Note: be sure to change the password policy if needed in the config.php

define("USERS_LOGIN_TABLE_NAME", "xmail_users_login");
define("USERS_IP_COL", "ip");
define("USERS_LOGIN_USERNAME_COL", "username");
define("USERS_LOGGEDIN_COL", "loggedIn");

define("USERS_MYSQL_HOST", "localhost");
define("USERS_MYSQL_USER", "xmail");
define("USERS_MYSQL_PASS", "xmail");
define("USERS_MYSQL_DATABASE", "xmail");

// From here on in there should be no need for editing

// Load sql
$sqlCon = mysqli_connect(constant("USERS_MYSQL_HOST"), constant("USERS_MYSQL_USER"), constant("USERS_MYSQL_PASS"));
$sqlDb = mysqli_select_db($sqlCon, constant("USERS_MYSQL_DATABASE"));

function isUser($name){
    global $sqlCon;
    $query = mysqli_query($sqlCon, "SELECT `".constant("USERS_USERNAME_COL")."` FROM `".constant("USERS_TABLE_NAME")."` WHERE `".constant("USERS_USERNAME_COL")."`='".$name."' LIMIT 1");
    return mysqli_num_rows($query)>0;
}

function loginUser($name, $hashedPassword){
    global $sqlCon;
    $query = mysqli_query($sqlCon, "SELECT * FROM `".constant("USERS_TABLE_NAME")."` WHERE `".constant("USERS_USERNAME_COL")."`='".$name."' AND `".constant("USERS_PASSWORD_COL")."`='".$hashedPassword."' LIMIT 1");
    if(mysqli_num_rows($query)>0){
        $ip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($sqlCon, "UPDATE `".constant("USERS_LOGIN_TABLE_NAME")."` SET `".constant("USERS_LOGGEDIN_COL")."`='1' WHERE `".constant("USERS_LOGIN_USERNAME_COL")."`='".$name."' AND `".constant("USERS_IP_COL")."`='".$ip."' LIMIT 1") or die(mysqli_error($sqlCon));
        return true;
    }
    return false;
}

function registerUser($name, $hashedPassword){
    global $sqlCon;
    $ip = $_SERVER['REMOTE_ADDR'];
	$query = mysqli_query($sqlCon, "SELECT * FROM `".constant("USERS_TABLE_NAME")."` WHERE `".constant("USERS_USERNAME_COL")."`='".$name."'") or die(mysqli_error($sqlCon));
	if(mysqli_num_rows($query)>0){
		return false;
	}
	mysqli_query($sqlCon, "INSERT INTO `".constant("USERS_LOGIN_TABLE_NAME")."` (`".constant("USERS_LOGIN_USERNAME_COL")."`, `".constant("USERS_LOGGEDIN_COL")."`, `".constant("USERS_IP_COL")."`) VALUES ('".$name."', '1', '".$ip."')") or die(mysqli_error($sqlCon));
	mysqli_query($sqlCon, "INSERT INTO `".constant("USERS_TABLE_NAME")."` (`".constant("USERS_USERNAME_COL")."`, `".constant("USERS_PASSWORD_COL")."`) VALUES ('".$name."', '".$hashedPassword."')");
    return true;
}

function logoutUser($name){
    global $sqlCon;
    mysqli_query($sqlCon, "UPDATE `".constant("USERS_LOGIN_TABLE_NAME")."` SET `".constant("USERS_LOGGEDIN_COL")."`='0' WHERE `".constant("USERS_LOGIN_USERNAME_COL")."`='".$name."' LIMIT 1") or die(mysqli_error($sqlCon));
}

function isUserLoggedIn($name){
    global $sqlCon;
    $query = mysqli_query($sqlCon, "SELECT `".constant("USERS_LOGGEDIN_COL")."` FROM `".constant("USERS_LOGIN_TABLE_NAME")."` WHERE `".constant("USERS_LOGIN_USERNAME_COL")."`='".$name."' AND `".constant("USERS_LOGGEDIN_COL")."`='1' LIMIT 1");
    return mysqli_num_rows($query)>0;
}
?>