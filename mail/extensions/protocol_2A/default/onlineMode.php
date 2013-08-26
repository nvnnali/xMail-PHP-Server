<?php
function periodicOnlineMode($server, $port, $protocol){
    return false;
    /*
    if($port <= 0 || $protocol=="NO VERSION"){
        return false;
    }
    $server = clean($server);
    $port = clean($port);
    global $sqlConn;
    $query = mysqli_query($sqlConn, "SELECT `id`,`onlineMode`,`lastCheck` FROM `serveronlinemode` WHERE `ip`='{$server}' AND `port`='{$port}' LIMIT 1") or die(mysqli_error($sqlConn));
    $isOnline = false;
    if(mysqli_num_rows($query)>0){
        $isOnline = mysqli_result($query, 0, "onlineMode");
        $lastCheck = mysqli_result($query, 0, "lastCheck");
        if(time()-3600 > $lastCheck){
            $isOnline = isOnlineMode($server, $port, $protocol);
            $id = mysqli_result($query, 0);
            mysqli_query($sqlConn, "UPDATE `serveronlinemode` SET `onlineMode`='{$isOnline}' WHERE `id`='{$id}'");
        }
    }else{
        $isOnline = isOnlineMode($server, $port, $protocol);
        $now = time();
        mysqli_query($sqlConn, "INSERT INTO `serveronlinemode` (`onlineMode`,`lastCheck`,`ip`,`port`) VALUES ('{$isOnline}', '{$now}', '{$server}', '{$port}')");
    }
    return $isOnline;
    */
}
?>