<?php
$cache = true;
if(file_exists("xmail-rank.txt")){
	$store = file_get_contents("xmail-rank.txt");
	$json = json_decode($store);
	$rank = $json->{'rank'};
	$active = $json->{'active'};
	$cache = time() >= $json->{'recache'};
}
if($cache){
	$in = file_get_contents("https://mcstats.org/api/1.0/xMail");
	$json = json_decode($in);
	$rank = $json->{'rank'};
	$in = file_get_contents("https://mcstats.org/api/1.0/");
	$json = json_decode($in);
	$active = $json->{'plugins'}->{'active'};
	$array = array("rank"=>$rank, "active"=>$active, "recache"=>time()+(60*60));
	file_put_contents("xmail-rank.txt", json_encode($array));
}
echo "xMail ranks as number $rank on <a href='http://mcstats.org' target='_BLANKS'>Metrics</a> (of $active plugins).";
?>