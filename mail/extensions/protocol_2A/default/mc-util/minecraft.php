<?php
// This is to check if a server is in online or offline mode.

include_once(dirname(__FILE__)."/config.php");
define("CLI_REQUIRED", false);
require_once(dirname(__FILE__)."/misc/dependencies.php");
require_once(dirname(__FILE__)."/classes/MinecraftClient.class.php");

function isOnlineMode($server, $port, $protocol){
    if($protocol<=0){
        return false;
    }
    $client = new MinecraftClient($server, $protocol, $port);
    $client->connect("XMAIL_AUTH", "");
    return $client->isOnlineMode;
}
?>