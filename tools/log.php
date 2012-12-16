<?php
// includes
require_once "includes/constants.inc.php";
require_once XMAIL_CONF_PATH;
require_once "includes/misc.inc.php";
require_once "includes/keys.inc.php";

// You will need to change these values, most likely
mysql_connect($config["mysql.server"], $config["mysql.username"], $config["mysql.password"]) or die(mysql_error());
mysql_select_db($config["mysql.database"]) or die(mysql_error());

$now = time();
$validation = 15*60*1000;
$die = ($now-$validation)+5000;
echo "$now<br>$validation<br>$die<br>";
$query = mysql_query("SELECT * FROM `keys` WHERE `expire`<='$die'");
echo mysql_num_rows($query)."<br><hr>";
while($a = mysql_fetch_array($query)){
	echo $a['id']."<br>";
}
$query = mysql_query("SELECT * FROM `keys` WHERE `expire`>='$die'");
echo mysql_num_rows($query)."<br><hr>";
while($a = mysql_fetch_array($query)){
	echo $a['id']."<br>";
}
die("<hr>NOPE");

$ip = $_SERVER['REMOTE_ADDR'];

if(valid($_REQUEST['ip'])){
	$ip = clean($_REQUEST['ip']);
}

echo "<h1>Log for $ip</h1>";
echo "<i>Use <u>?ip=</u> and the IP to see log traffic for that IP. Example: www.example.com/xmail/tools/log.php?ip=127.0.0.1</i><br>";

$query = mysql_query("SELECT *  FROM `log` WHERE `ip`='$ip' ORDER BY  `log`.`time` DESC ");
while($a = mysql_fetch_array($query)){
	$id = $a['id'];
	$ip = $a['ip'];
	$page = $a['page'];
	$req = $a['variables'];
	$time = $a['time'];
	echo "ID: $id<br>IP: $ip<br>Page: $page<br>Time: $time<br>REQUEST: <br><pre>$req</pre><hr>";
}
?>