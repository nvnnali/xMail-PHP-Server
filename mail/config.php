<?php
// ===========================
// ||     Basic Settings    ||
// ===========================
define("MYSQL_HOST",     "localhost");
define("MYSQL_USERNAME", "xmail");
define("MYSQL_PASSWORD", "xmail");
define("MYSQL_DATABASE", "xmail");

// ===========================
// ||   Advanced Settings   ||
// ===========================

/*
 Password Policy Settings:

 These settings allow for you, as an owner, to set the password
 policy. If any part of this policy is invalid xMail will default 
 to "SHA1, NO SALT". Supported methods are "sha1" and "md5". To
 use a different method, please open a request. Changing this after
 some accounts are created will NOT update passwords! This is to tell
 the xMail plugin (and other clients) what to send as a "password".
 
 Again, THIS WILL NOT UPDATE THE DATABASE IF THIS IS CHANGED.
*/
define("PASSWORD_POLICY", serialize(array(
    "method"=>"sha1",
    "salt"=>array(
        "salt"=>"",
        "format"=>"P" // S = salt, P = password, anything else is ignored
                      // No "S" means "do not use salt"
    ))));

// These 2 "supported versions" constants are to be left untouched unless you know what you are doing.
define("SUPPORTED_VERSIONS", serialize(array("2A")));
define("SUPPORTED_MC", serialize(array(
    "1.5.2" => 61,
    "1.5.1" => 60,
    "1.5" => 60,
    "1.5.0" => 60,
    "1.4.7" => 51,
    "1.4.6" => 51,
    "1.4.6" => 51,
    "1.4.5" => 49,
    "1.4.4" => 49,
    "1.4.3" => 48,
    "1.4.2" => 47,
    "1.4.1" => 47,
    "1.4.0" => 47,
    "1.4" => 47,
    "1.3.2" => 39,
    "1.3.1" => 39,
    "1.3.0" => 39,
    "1.3" => 39,
    "1.2.5" => 29,
    "1.2.4" => 29,
    "1.2.3" => 28,
    "1.2.2" => 28,
    "1.2.1" => 28,
    "1.2.0" => 28,
    "1.2" => 28,
    "1.1.0" => 23,
    "1.1" => 23,
    "1.0.1" => 22,
    "1.0.0" => 22,
    "1.0" => 22,
    "NO VERSION" => -1
)));

// Gets the protocol version for a specified MC version
function getProtocol($version){
    $versions = unserialize(constant("SUPPORTED_MC"));
    if(array_key_exists($version, $versions)){
        return $versions[$version];
    }else{
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Minecraft version not supported"));
        die();
    }
}
?>