<?php
include "public_html/xmail/inc/connection.php"; // Path to connection file
$now = time();
$die = $now;+(15*60);
mysql_query("DELETE FROM `keys` WHERE `expire`<='$die'");
mysql_query("DELETE FROM `session` WHERE `expires`<='$die'");
?>