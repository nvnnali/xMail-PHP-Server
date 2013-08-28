<?php
require_once("extensions/protocol_2A/default/auth.php");
require_once("extensions/protocol_2A/default/users.php");
//require_once("extensions/protocol_2A/default/mc-util/minecraft.php");
require_once("extensions/protocol_2A/default/onlineMode.php");

// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS
// TODO: LOGOUT/SETTINGS/LETTERS

// TODO: fix online mode

// A function to handle the mode
function handleMode($mode, $authPort, $authVersion){
    $ip = $_SERVER['REMOTE_ADDR'];
    $onlineMode = periodicOnlineMode($ip, $authPort, getProtocol($authVersion));
    // TODO
    // Modes that do not need a validated connection can be placed in an if-elseif-else here
    if(strcmp($mode, "INFORMATION")==0){

        $req = array();
        $req['password-policy'] = unserialize(constant("PASSWORD_POLICY"));
        $req['time'] = time();
        $req['supported-protocols'] = unserialize(constant("SUPPORTED_VERSIONS"));
        $req['supported-minecraft-versions'] = array_keys(unserialize(constant("SUPPORTED_MC")));
        $req['this-protocol'] = "2A";
        $req['public-ip'] = $ip;
        $req['public-online-mode'] = $onlineMode;
        $req['supplied-minecraft-version'] = $authVersion;
        $req['supplied-auth-port'] = $authPort;

        $ret = portReturnRequest($req);
        echo $ret;
        recordMessage($ret);
        die();
    }else{
        validateServerConnection(); // Will die on fail
        if(strcmp($mode, "LOGIN")==0){
            attemptLogin($onlineMode);
        }else if(strcmp($mode, "REGISTER")==0){
            attemptRegister($onlineMode);
        }else if(strcmp($mode, "CHECK_LOGIN")==0){
            attemptCheckLogin($onlineMode);
        }else if(strcmp($mode, "LOGOUT")==0){
            attemptLogout($onlineMode);
        }else if(strcmp($mode, "MAIL")==0){
            attemptMail();
        }else if(strcmp($mode, "SEND")==0){
            attemptSendMail();
        }else if(strcmp($mode, "MARK_MAIL")==0){
            attemptMarkMail();
        }else{
            header("HTTP/1.0 403 Forbidden");
            echo json_encode(array("message"=>"Unknown mode. Is the protocol version correct?"));
            recordMessage("Unknown mode");
            die();
        }
    }
}

// Called if XMAIL-REQUEST-AUTH header exists
function attemptAuthRequest(){
    $auth = generateNewAuth();
    if($auth == null){
        header("HTTP/1.0 403 Forbidden");
        echo json_encode(array("message" => "Access Denied"));
            recordMessage("Access denied");
        die();
    }
    echo json_encode(array("message" => "Access Granted", "SERVER_ACCESS_UPDATE" => $auth));
    recordMessage("Access granted - ".json_encode($auth));
    die();
}

// "attempt" functions are called from handleMode either directly or indirectly
function attemptMarkMail(){
    global $sqlConn;
    if(!array_key_exists("uuid", $_GET) || !array_key_exists("isread", $_GET)){
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments"));
        recordMessage("bad args");
        die();
    }
    $uuid = clean($_GET['uuid']);
    $isRead = clean($_GET['isread']);
    if(valid($uuid) && valid($isRead)){
        if($isRead == 0 || $isRead == 1){
            $unread = $isRead==1?0:1;
            mysqli_query($sqlConn, "UPDATE `xmail_mail` SET `unread`='{$unread}' WHERE `id`='{$uuid}' LIMIT 1") or die(mysql_error());
            echo portReturnRequest(array("message"=>"Updated"));
            recordMessage("updated");
            die();
        }else{
            header("HTTP/1.0 406 Not Acceptable");
            echo json_encode(array("message"=>"Unknown read state"));
            recordMessage("unknown read state");
            die();
        }
    }else{
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments [2]"));
            recordMessage("bad args [2]");
        die();
    }
}

function attemptMail(){
    global $sqlConn;
    if(!array_key_exists("username", $_GET) || !array_key_exists("folder", $_GET)){
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments"));
        recordMessage("bad args");
        die();
    }
    $username = clean($_GET['username']);
    $folder = clean($_GET['folder']);
    if(valid($username) && valid($folder)){
        $f = "to";
        $condition = "AND `tags` LIKE '%\&quot;{$folder}\&quot;%'";
        if(strtoupper($folder)=="ALL"){
            $condition = "";
        }else if(strtoupper($folder)=="READ"){
            $condition = "AND `unread`='0'";
        }else if(strtoupper($folder)=="UNREAD"){
            $condition = "AND `unread`='1'";
        }else if(strtoupper($folder)=="SENT"){
            $f = "from";
        }
        $query = mysqli_query($sqlConn, "SELECT * FROM `xmail_mail` WHERE `{$f}`='{$username}' {$condition}") or die(mysql_error());
        if(mysqli_num_rows($query)>0){
            $mail = array();
            $i = 0;
            while($a = mysqli_fetch_array($query)){
                $unread = $a['unread']==1;
                $msg = array(
                    "to"=>$a['to'],
                    "from"=>$a['from'],
                    "date"=>$a['date'],
                    "sent-from"=>$a['senderIP'],
                    "unread"=>$unread,
                    "tags"=>json_decode(dirty($a['tags']), true),
                    "attachments"=>json_decode(dirty($a['attachments']), true),
                    "uuid"=>$a['id']
                );
                $mail[$i]=$msg;
                $i++;
            }
            echo portReturnRequest(array("number"=>$i, "mail"=>$mail));
            recordMessage("mail sent");
            die();
        }else{
            echo portReturnRequest(array("number"=>0));
            recordMessage("no mail");
            die();
        }
    }else{
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments [2]"));
        recordMessage("bad args [2]");
        die();
    }
}

function attemptSendMail(){
    global $sqlConn;
    $mail = dirty($_GET['mail']);
    if(valid($mail)){
        $arr = json_decode($mail, true);
        if($arr != null){
            $to = $arr['to'];
            $from = $arr['from'];
            $date = $arr['date'];
            $ip = $arr['sent-from'];
            $unread = $arr['unread'];
            $tags = $arr['tags'];
            $attachments = $arr['attachments'];
            if(valid($to) && valid($from) && valid($date) && valid($ip) && valid($unread)){
                $tags = json_encode($tags);
                $tags = clean($tags);

                $attachments = json_encode($attachments);
                $attachments = clean($attachments);
                if(valid($tags) && valid($attachments)){
                    mysqli_query($sqlConn, "INSERT INTO `xmail_mail` (`to`,`from`,`date`,`senderIP`,`unread`,`tags`,`attachments`) VALUES ('{$to}','{$from}','{$date}','{$ip}','{$unread}','{$tags}','{$attachments}')") or die(mysql_error());
                    echo portReturnRequest(array("sent"=>true));
                    die();
                }else{
                    header("HTTP/1.0 406 Not Acceptable");
                    echo json_encode(array("message"=>"Bad arguments [1]"));
                    recordMessage("bad args [1]");
                    die();
                }
            }else{
                header("HTTP/1.0 406 Not Acceptable");
                echo json_encode(array("message"=>"Bad arguments [2]"));
                recordMessage("bad args [2]");
                die();
            }
        }else{
            header("HTTP/1.0 406 Not Acceptable");
            echo json_encode(array("message"=>"Bad arguments [3]"));
            recordMessage("bad args [3]");
            die();
        }
    }else{
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments [4]"));
        recordMessage("bad args [4]");
        die();
    }
}

function attemptLogin($onlineMode){
    global $sqlConn;
    if($onlineMode){
        header('HTTP/1.0 418 I\'m a teapot');
        echo portReturnRequest(array("message"=>"User logged in"));
        recordMessage("logged in - online mode");
        die();
    }
    if(!array_key_exists("username", $_GET) || !array_key_exists("password", $_GET)){
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments"));
        recordMessage("bad args");
        die();
    }
    $username = clean($_GET['username']);
    $password = clean($_GET['password']);
    $loggedIn = loginUser($username, $password);
    if($loggedIn){
        header('HTTP/1.0 418 I\'m a teapot');
        echo portReturnRequest(array("message"=>"User logged in"));
        recordMessage("logged in");
        die();
    }else{
        echo portReturnRequest(array("message"=>"User not logged in"));
        recordMessage("not logged in - bad un/pw");
        die();
    }
}

function attemptRegister($onlineMode){
    global $sqlConn;
    if($onlineMode){
        header('HTTP/1.0 418 I\'m a teapot');
        echo portReturnRequest(array("message"=>"User logged in"));
        recordMessage("logged in - online mode");
        die();
    }
    if(!array_key_exists("username", $_GET) || !array_key_exists("password", $_GET)){
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments"));
        recordMessage("bad args");
        die();
    }
    $username = clean($_GET['username']);
    $password = clean($_GET['password']);
    if(isUser($username)){
        header('HTTP/1.0 402 Payment Required');
        echo portReturnRequest(array("message"=>"Username taken"));
        recordMessage("un taken");
        die();
    }else{
        if(registerUser($username, $password)){
            header('HTTP/1.0 418 I\'m a teapot');
            echo portReturnRequest(array("message"=>"User registered & logged in"));
            recordMessage("logged in/registered");
            die();
        }else{
            echo portReturnRequest(array("message"=>"Unexpected error"));
            recordMessage("unexpected error");
            die();
        }
    }
}

function attemptCheckLogin($onlineMode){
    global $sqlConn;
    if($onlineMode){
        header('HTTP/1.0 418 I\'m a teapot');
        echo portReturnRequest(array("message"=>"User logged in"));
        recordMessage("logged in - online mode");
        die();
    }
    if(!array_key_exists("username", $_GET)){
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments"));
        recordMessage("bad args");
        die();
    }
    $username = clean($_GET['username']);
    $loggedIn = isUserLoggedIn($username);
    if($loggedIn){
        header('HTTP/1.0 418 I\'m a teapot');
        echo portReturnRequest(array("message"=>"User logged in"));
        recordMessage("logged in");
    }else{
        echo portReturnRequest(array("message"=>"User not logged in"));
        recordMessage("not logged in");
    }
}

function attemptLogout($onlineMode){
    global $sqlConn;
    if($onlineMode){
        header('HTTP/1.0 405 Method not allowed');
        echo portReturnRequest(array("message"=>"Cannot log out of an online mode server"));
        recordMessage("cannot log out - online mode");
        die();
    }
    if(!array_key_exists("username", $_GET)){
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Bad arguments"));
        recordMessage("bad args");
        die();
    }
    $username = clean($_GET['username']);
    logoutUser($username);
    echo portReturnRequest(array("message"=>"Logged out"));
    recordMessage("not logged in (logout requested)");
}

// ban functions
function banIP($ip){
    global $sqlConn;
    // TODO
}

function checkBan(){
    global $sqlConn;
    $ip = $_SERVER['REMOTE_ADDR'];
    $banned = false;
    $isTemp = false;

    // TODO: Check ban

    if($banned){
        header("HTTP/1.0 403 Forbidden");
        echo json_encode(array("message"=>"Banned from xMail server", "temporary" => $isTemp));
        recordMessage("banned - temp? ".$isTemp);
        die();
    }
}

// We need to verify the secret for the server. This is applied in addition to the HTTP Basic auth
// as a second barrier for invalidated connections. This is considered the handshake.
//
// This is a random value that can be changed by any request (will add docs as to how a client should
// handle the update as it could be appended to something like a mail message). Failure to send this
// will fail the handshake. Multiple attempts at requesting authentication to reset this code will
// force a temporary ban from the server
function validateServerConnection(){
    global $sqlConn;
    $headers = getRealHeaders();
    $ip = $_SERVER['REMOTE_ADDR'];
    $username = clean($_SERVER['PHP_AUTH_USER']);
    $password = clean($_SERVER['PHP_AUTH_PW']);
    if(array_key_exists("XMAIL-SERVER-KEY", $headers)){
        $serverKey = clean($headers['XMAIL-SERVER-KEY']);
        if(!valid($serverKey)){
            header("HTTP/1.0 406 Not Acceptable");
            echo json_encode(array("message"=>"Connection handshake failed"));
            recordMessage("== HANDSHAKE FAILED [e-00]");
            die();
        }else{
            $query = mysqli_query($sqlConn, "SELECT `id` FROM `xmail_server_connections` WHERE `ip`='{$ip}' AND `secret`='{$serverKey}' AND `username`='{$username}' AND `password`='{$password}' LIMIT 1") or die(mysql_error());
            if(mysqli_num_rows($query)<=0){
                header("HTTP/1.0 406 Not Acceptable");
                echo json_encode(array("message"=>"Connection handshake failed [e-01]"));
                recordMessage("== HANDSHAKE FAILED [e-01]");
                die();
            }
        }
    }else{
        header("HTTP/1.0 406 Not Acceptable");
        echo json_encode(array("message"=>"Connection handshake failed [e-02]"));
        recordMessage("== HANDSHAKE FAILED [e-02]");
        die();
    }
}
?>