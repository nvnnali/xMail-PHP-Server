<?php
$config 					= array();
$config["mysql.server"]   	= 'localhost';
$config["mysql.username"] 	= 'xmailun';
$config["mysql.password"] 	= 'xmailpw';
$config["mysql.database"] 	= 'xmaildb';

$config["timezone"]         = 'America/Edmonton';

$config["settings.nologin"] = false; // If true, external users do not need to login

// ##########################################################
// # END CONFIGURATION      #=====#       END CONFIGURATION #
// ##########################################################

date_default_timezone_set($config["timezone"]);
mysql_connect($config["mysql.server"], $config["mysql.username"], $config["mysql.password"]) or die(mysql_error());
mysql_select_db($config["mysql.database"]) or die(mysql_error());

session_start();
?>