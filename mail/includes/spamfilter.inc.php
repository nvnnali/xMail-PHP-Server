<?php
function isSpam($to, $from, $message, $test=false){
	if($test){ echo "called<br>";}

	$now = time();
	//echo $now."<br>";
	// 2 tests
	$prob2 = 0;
	$prob3 = 0;
	
	$parts = explode(" ", $message);
	$expectedParts = strlen($message)/15; // Average word length
	$percentOff = count($parts)/$expectedParts;
	if($percentOff > 1.2){
		$percentOff = 0;
	}else if($percentOff < 0.3){
		$percentOff = 1;
	}else if($percentOff < 0.5){
		$percentOff = 0.8;
	}
	$prob2 = $percentOff;
	if($test){ echo "p2: $prob2<br>";}
	
	$time = strtotime("1 minute ago");
	$similarUsers = mysql_query("SELECT `message` FROM `mail` WHERE `from`='$from' AND `sent`<'{$time}' ORDER BY `id` DESC LIMIT 50") or die(mysql_error());
	if(mysql_num_rows($similarUsers)>0){
		$all = 0;
		while($a = mysql_fetch_array($similarUsers)){
			similar_text($message, $a['message'], $p);
			$all = $all+$p;
		}
		$all = $all/mysql_num_rows($similarUsers);
		$prob3 = $all/100;
	}
	if($test){ echo "p3: $prob3<br>";}
	
	$avg = ($prob1 + $prob2 + $prob3)/3;
	if(max($prob1, $prob2, $prob3)-min($prob1, $prob2, $prob3) >= 0.5){
		// Only average the two highest
		$max = max($prob2, $prob3);
		$min = min($prob2, $prob3);
		$a1 = 0;
		$a2 = 0;
		if($max != 0){
			$a1 = $max;
			if($min==0){
				if($prob2 < $max && $prob2 > $min){
					$a2 = $prob2;
				}else if($prob3 < $max && $prob3 > $min){
					$a2 = $prob3;
				}else{
					if($prob2 < $max){
						$a2 = $prob2;
					}else if($prob3 < $max){
						$a2 = $prob3;
					}else{
						// All three are the same
						$a2 = $a1;
					}
				}
				$dual = false;
				if($prob2 == 0 && $prob2 == $prob3){
					$dual = true;
				}else if($prob3 == 0 && $prob3 == $prob2){
					$dual = true;
				}
				if($dual){
					$a2 = $a1;
				}
			}
			$avg = ($a1+$a2)/2;
		}
	}
	$spam = FALSE;
	if($avg >= 0.7){
		$spam = TRUE;
	}else{
		$spam = FALSE;
	}
	if($test){ echo "a: $avg<br>";}
	if($test){ echo "i: $spam<hr>";}
	return $spam;
}
?>