<?php
function generateNewAuth($force=false){
    $ip = $_SERVER['REMOTE_ADDR'];
    global $sqlConn;
    if(!$force){
        $query = mysqli_query($sqlConn, "SELECT `lastcheck` FROM `xmail_server_connections` WHERE `ip`='{$ip}' LIMIT 1") or die(mysqli_error($sqlConn));
        if(mysqli_num_rows($query)>0){
            $lastCheck = mysqli_result($query, 0, "lastcheck");
            // TODO: Proper abuse check
            /*if(time()-$lastCheck<3600){
                header("HTTP/1.0 403 Forbidden");
                echo json_encode(array("message" => "Access Denied"));
                die();
            }else{*/
                mysqli_query($sqlConn, "DELETE FROM `xmail_server_connections` WHERE `ip`='{$ip}' LIMIT 1") or die(mysqli_error($sqlConn));
            //}
        }
    }

    $now = time();

    $un = sha1(uniqid());
    $pw = sha1(uniqid());
    $se = sha1(uniqid());

    mysqli_query($sqlConn, "INSERT INTO `xmail_server_connections` (`ip`,`username`,`password`,`secret`,`lastcheck`) VALUES ('{$ip}', '{$un}', '{$pw}', '{$se}', '{$now}')") or die(mysqli_error($sqlConn));

    return array("username"=>$un, "password"=>$pw, "secret"=>$se);
}

function portReturnRequest($array){
    $ip = $_SERVER['REMOTE_ADDR'];
    global $sqlConn;
    $query = mysqli_query($sqlConn, "SELECT `lastcheck` FROM `xmail_server_connections` WHERE `ip`='{$ip}' LIMIT 1") or die(mysqli_error($sqlConn));
    $auth = null;
    if(mysqli_num_rows($query)>0){
        $lastCheck = mysqli_result($query, 0, "lastcheck");
        if(time()-$lastCheck>=rand(300,4200)){
            $auth = generateNewAuth(true);
        }
    }
    if($auth!=null){
        $array['SERVER_ACCESS_UPDATE'] = $auth;
    }
    echo json_encode($array);
    unset($array);
}
?>