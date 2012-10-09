<?php
include "public_html/xmail/inc/connection.php"; // Path to connection file
$query = mysql_query("SELECT * FROM `snmail` WHERE `send`<='".time()."'") or die(mysql_error());
if(mysql_num_rows($query)>0){
	while($a = mysql_fetch_array($query)){
		$to = $a['to'];
		$from = $a['from'];
		$lines = $a['lines'];
		$id = $a['id'];
		echo "INSERT $id<br>\n";
		mysql_query("INSERT INTO `snmail2` (`to`, `from`, `lines`, `id`) VALUES ('$to', '$from', '$lines', '$id')") or die(mysql_error());
	}
}
mysql_query("DELETE FROM `snmail` WHERE 1");
echo "Done";
?>