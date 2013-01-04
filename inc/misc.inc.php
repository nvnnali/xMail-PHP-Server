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
	$input = mysql_real_escape_string($input);
	$input = htmlentities($input, ENT_COMPAT, "UTF-8");
	return $input;
}

function dirty($input){
	//if(get_magic_quotes_gpc()){
	//	$input = addslashes($input);
	//}
	//$input = mysql_real_escape_string($input);
	$input = html_entity_decode($input, ENT_COMPAT, "UTF-8");
	return $input;
}

function getPageNavigation($maxPages, $targetpage, $perPage, $adjacents = 3, $extraArgs = ""){
	$adjacents = 3;
	$total_pages = $maxPages;
	$targetpage = $targetpage; 
	$limit = $perPage;
	$page = $_GET['page'];
	if($page) 
		$start = ($page - 1) * $limit;
	else
		$start = 0;
	if ($page == 0) $page = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($total_pages/$limit);
	$lpm1 = $lastpage - 1;
	$pagination = "";
	if($lastpage > 1){	
		$pagination .= "<div class=\"pagination\">";
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage?page=$prev&$extraArgs\">&laquo; previous</a> | ";
		else
			$pagination.= "<span class=\"disabled\">&laquo; previous</span> | ";
		if ($lastpage < 7 + ($adjacents * 2)){	
			for ($counter = 1; $counter <= $lastpage; $counter++){
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span> | ";
				else
					$pagination.= "<a href=\"$targetpage?page=$counter&$extraArgs\">$counter</a> | ";					
			}
		}elseif($lastpage > 5 + ($adjacents * 2)){
			if($page < 1 + ($adjacents * 2)){
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span> | ";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter&$extraArgs\">$counter</a> | ";					
				}
				$pagination.= "... | ";
				$pagination.= "<a href=\"$targetpage?page=$lpm1&$extraArgs\">$lpm1</a> | ";
				$pagination.= "<a href=\"$targetpage?page=$lastpage&$extraArgs\">$lastpage</a> | ";		
			}elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
				$pagination.= "<a href=\"$targetpage?page=1&$extraArgs\">1</a> | ";
				$pagination.= "<a href=\"$targetpage?page=2&$extraArgs\">2</a> | ";
				$pagination.= "... | ";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++){
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span> | ";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter&$extraArgs\">$counter</a> | ";					
				}
				$pagination.= "... | ";
				$pagination.= "<a href=\"$targetpage?page=$lpm1&$extraArgs\">$lpm1</a> | ";
				$pagination.= "<a href=\"$targetpage?page=$lastpage&$extraArgs\">$lastpage</a> | ";		
			}else{
				$pagination.= "<a href=\"$targetpage?page=1&$extraArgs\">1</a> | ";
				$pagination.= "<a href=\"$targetpage?page=2&$extraArgs\">2</a> | ";
				$pagination.= "... | ";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++){
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span> | ";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter&$extraArgs\">$counter</a> | ";
				}
			}
		}
		if ($page < $counter - 1) 
			$pagination.= "<a href=\"$targetpage?page=$next&$extraArgs\">next &raquo;</a>";
		else
			$pagination.= "<span class=\"disabled\">next &raquo;</span>";
		$pagination.= "</div>\n";		
	}else{
		$pagination = "<span class=\"disabled\">&laquo; previous</span> | 1 | <span class=\"disabled\">next &raquo;</span>";
	}
	return $pagination;
}
?>