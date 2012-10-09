<?php
include "mobile/MobileDetect.php";
$mobile = new Mobile_Detect();
// Tablet detection detects mobile then tablet, the code below causes a "too many redirects". 
/*if($mobile->isTablet() && !$_SESSION['desktoplock'] && !startsWith(curPageURL(), TABLET_URL)){
	header("Location: ".str_replace(MOBILE_URL, TABLET_URL, curPageURL()));
}else */if($mobile->isMobile() && !$_SESSION['desktoplock'] && !startsWith(curPageURL(), MOBILE_URL)){
	header("Location: ".str_replace(URL, MOBILE_URL, curPageURL()));
}else{
	// Do nothing
}

// TODO: Reformat

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
?>