<?php
function isSpam($to, $from, $message, $test=false){
	if($test){ echo "called<br>";}

	$now = time();
	//echo $now."<br>";
	// 3 tests
	$prob1 = 0;
	$prob2 = 0;
	$prob3 = 0;
	
	$prob1 = keywordSpam($message);
	if($test){ echo "p1: $prob1<br>"; }
	
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
	
	$similarUsers = mysql_query("SELECT * FROM `mail` WHERE `from`='$from' ORDER BY `id` DESC LIMIT 50");
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
		$max = max($prob1, $prob2, $prob3);
		$min = min($prob1, $prob2, $prob3);
		$a1 = 0;
		$a2 = 0;
		if($max != 0){
			$a1 = $max;
			if($min==0){
				if($prob1 < $max && $prob1 > $min){
					$a2 = $prob1;
				}else if($prob2 < $max && $prob2 > $min){
					$a2 = $prob2;
				}else if($prob3 < $max && $prob3 > $min){
					$a2 = $prob3;
				}else{
					if($prob1 < $max){
						$a2 = $prob1;
					}else if($prob2 < $max){
						$a2 = $prob2;
					}else if($prob3 < $max){
						$a2 = $prob3;
					}else{
						// All three are the same
						$a2 = $a1;
					}
				}
				$dual = false;
				if($prob1 == 0 && ($prob1 == $prob2 || $prob1 == $prob3)){
					$dual = true;
				}else if($prob2 == 0 && ($prob1 == $prob2 || $prob2 == $prob3)){
					$dual = true;
				}else if($prob3 == 0 && ($prob3 == $prob2 || $prob1 == $prob3)){
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

function keywordSpam($text){
	$probability = 0;  
    $text = strtolower($text); 
	$dict = array("http", "penis", "pills", "sale", "cheapest", "4u", "mlm", "xxx", "! and $", "! and free", "$$", ",000 and !! and $", "///////////////", "@mlm", "@public", "@savvy", "100% satisfied", "18+", "absolute", "accept credit cards", "act now! don’t hesitate!", "additional income", "addresses on cd", "adult s", "adult web", "adults only", "advertisement", "all natural", "amazing", "apply online", "as seen on", "auto email removal", "avoid bankruptcy", "be 18", "be amazed", "be your own boss", "being a member", "big bucks", "bill 1618", "billing address", "billion dollars", "brand new pager", "bulk email", "buy direct", "buying judgments", "cable converter", "call free", "call now", "calling creditors", "cancel at any time", "cannot be combined with any other offer", "can’t live without", "cards accepted", "cash bonus", "cashcashcash", "casino", "cell phone cancer scam", "cents on the dollar", "check or money order", "claims not to be selling anything", "claims to be in accordance with some spam law", "claims to be legal", "claims you are a winner", "claims you registered with some kind of partner", "click below", "click here link", "click to remove", "click to remove mailto", "compare rates", "compete for your business", "confidentially on all orders", "congratulations", "consolidate debt and credit", "copy accurately", "copy dvds", "credit bureaus", "credit card offers", "cures baldness", "dear email", "dear friend", "dear somebody", "different reply to", "dig up dirt on friends", "direct email", "direct marketing", "discusses search engine listings", "do it today", "don’t delete", "drastically reduced", "earn per week", "easy terms", "eliminate bad credit", "email harvest", "email marketing", "expect to earn", "extra income", "fantastic deal", "fast viagra delivery", "financial freedom", "find out anything", "for free", "for free!", "for free?", "for instant access", "for just $ (some amt)	 free access", "free cell phone", "free consultation", "free dvd", "free grant money", "free hosting", "free installation", "free investment", "free leads", "free membership", "free money", "free offer", "free preview", "free priority mail", "free quote", "free sample", "free trial", "free website", "friend@", "full refund", "get it now", "get paid", "get started now", "gift certificate", "great offer", "guarantee", "guarantee and", "have you been turned down?", "hello@", "hidden assets", "home employment", "human growth hormone", "if only it were that easy", "in accordance with laws", "increase sales", "increase traffic", "insurance", "investment decision", "it's effective", "join millions of americans", "laser printer", "limited time only", "long distance phone offer", "lose weight spam", "lower interest rates", "lower monthly payment", "lowest price", "luxury car", "mail in order form", "mail@", "marketing solutions", "mass email", "meet singles", "member stuff", "message contains disclaimer", "mlm", "money back", "money back", "money making", "money-back guarantee", "month trial offer", "more info and visit and $", "more internet traffic", "mortgage rates", "multi level marketing", "must be 18", "must be 21", "name brand", "new customers only", "new domain extensions", "nigerian", "no age restrictions", "no catch", "no claim forms", "no cost", "no credit check", "no disappointment", "no experience", "no fees", "no gimmick", "no inventory", "no investment", "no medical exams", "no middleman", "no obligation", "no purchase necessary", "no questions asked", "no selling", "no strings attached", "not intended", "off shore", "offer expires", "offers coupon", "offers extra cash", "offers free (often stolen) passwords", "once in lifetime", "one hundred percent free", "one hundred percent guaranteed", "one time mailing	 one-time mail", "online biz opportunity", "online pharmacy", "only $", "opportunity", "opt in", "order now", "order now!", "order status", "order today", "orders shipped by priority mail", "outstanding values", "over 18", "over 21", "pennies a day", "people just leave money laying around", "please read", "potential earnings", "print form signature", "print out and fax", "produced and sent out", "profits", "profits@", "promise you …!", "public@", "pure profit", "real thing", "refinance home", "removal instructions", "remove in quotes", "remove subject", "removes wrinkles", "reply remove subject", "requires initial investment", "reserves the right", "reverses aging", "risk free", "round the world", "s 1618", "safeguard notice", "sales@", "satisfaction", "satisfaction guaranteed", "save $", "save big money", "save up to", "score with babes", "section 301", "see for yourself", "sent in compliance", "serious cash", "serious only", "shopping spree", "sign up free today", "social security number", "special promotion", "stainless steel", "stock alert", "stock disclaimer statement", "stock pick", "stop snoring", "strong buy", "stuff on sale", "subject to credit", "success.", "success@", "supplies are limited", "take action now", "talks about hidden charges", "talks about prizes", "tells you it’s an ad", "terms and conditions", "the best rates", "the following form", "they keep your money — no refund!", "they’re just giving it away", "this isn’t junk", "this isn’t spam", "university diplomas", "unlimited", "unsecured credit/debt", "urgent", "us dollars", "vacation offers", "viagra and other drugs", "wants credit card", "we hate spam", "we honor all", "weekend getaway", "what are you waiting for?", "while supplies last", "while you sleep", "who really wins?", "why pay more?", "will not believe your eyes", "winner", "winning", "work at home", "you have been selected", "your income");
    foreach($dict as $word){
        $count = substr_count($text, $word);
        $probability += .2 * $count;
    }
    return $probability;
}
?>