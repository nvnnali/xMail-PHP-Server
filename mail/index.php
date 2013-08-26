<?php
require_once("config.php");
require_once("util/sanitizer.php");
require_once("util/misc.php");

error_reporting(E_ALL ^ E_NOTICE);

// Connect to the DB
$sqlConn = mysqli_connect(constant("MYSQL_HOST"), constant("MYSQL_USERNAME"), constant("MYSQL_PASSWORD")) or die("Con Error: ".mysql_error());
mysqli_select_db($sqlConn, constant("MYSQL_DATABASE")) or die("DB Error: ".mysql_error());

// Records messages to a file
function recordMessage($data){
    if(false){ // Log flag
        file_put_contents("log.txt", time()." - ".$_SERVER['REMOTE_ADDR'].":: ".$data."\n", FILE_APPEND);
    }
}

// Used for header stuff
if(!function_exists('getallheaders')){
    function getallheaders(){
        $headers = '';
        foreach($_SERVER as $name => $value){
            if(substr($name, 0, 5) == 'HTTP_'){
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

// More headers
function getRealHeaders(){
    $headers = getallheaders();
    $newHeaders = array();
    foreach ($headers as $name => $value) {
        $up = strtoupper($name);
        if(startsWith($up, "XMAIL-")){
            $newHeaders[$up] = $value;
        }else{
            $newHeaders[$name] = $value;
        }
    }
    return $newHeaders;
}

$headers = getRealHeaders();

// Logging
{
    foreach ($headers as $name => $value) {
        recordMessage("HEADER $name: $value");
    }
    foreach ($_GET as $name => $value){
        recordMessage("_GET $name: $value");
    }
    foreach ($_POST as $name => $value){
        recordMessage("_POST $name: $value");
    }
    foreach ($_REQUEST as $name => $value){
        recordMessage("_REQUEST $name: $value");
    }
    foreach ($_SERVER as $name => $value){
        recordMessage("_SERVER $name: $value");
    }
}

// Debug mode check, default to off whenever possible
if(array_key_exists("XMAIL-DEBUG", $headers)){
    $debug = clean($headers['XMAIL-DEBUG']);
    if(valid($debug) && $debug=="true"){
        echo "<pre><b>HEADERS</b>\n";
        foreach ($headers as $name => $value) {
            echo "$name: $value\n";
        }
        echo "</pre><hr>\n";
    }
}

// Check for auth port
$authPort = -1;
$authVersion = "NO VERSION";
if(array_key_exists("XMAIL-AUTH-PORT", $headers)){
    $authPort = clean($headers["XMAIL-AUTH-PORT"]);
    if(array_key_exists("XMAIL-AUTH-VERSION", $headers)){
        $authVersion = clean($headers["XMAIL-AUTH-VERSION"]);
    }else{
        header("XMAIL-AUTH-VERSION: Auth check failed, no version supplied");
        recordMessage("XMAIL-AUTH-VERSION: Auth check failed, no version supplied");
    }
}else{
    header("XMAIL-AUTH-PORT: Auth check failed, no port supplied");
    recordMessage("XMAIL-AUTH-PORT: Auth check failed, no port supplied");
}

// Skip auth check
$skipAuth = false;
if(array_key_exists("XMAIL-REQUEST-AUTH", $headers)){
    $skipAuth = true;
}

// Setup for authorization
if(isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $auth_params = explode(":" , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    $_SERVER['PHP_AUTH_USER'] = $auth_params[0];
    unset($auth_params[0]);
    $_SERVER['PHP_AUTH_PW'] = implode('',$auth_params);
}

// Do authentication
if(!$skipAuth){
    if(!isset($_SERVER['PHP_AUTH_USER'])){
        header('WWW-Authenticate: Basic realm="xMail API"');
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(array("message"=>"Invalid username or password"));
        recordMessage("Invalid username or password [1]");
        recordMessage("-- END --");
        die();
    }else{
        if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
            header('WWW-Authenticate: Basic realm="xMail API"');
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(array("message"=>"Invalid username or password"));
            recordMessage("Invalid username or password [2]");
            recordMessage("-- END --");
            die();
        }
        $username = clean($_SERVER['PHP_AUTH_USER']);
        $password = clean($_SERVER['PHP_AUTH_PW']);
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = mysqli_query($sqlConn, "SELECT `id` FROM `xmail_server_connections` WHERE `ip`='{$ip}' AND `username`='{$username}' AND `password`='{$password}' LIMIT 1") or die(mysqli_error($sqlConn));
        if(mysqli_num_rows($query)<=0){
            header('WWW-Authenticate: Basic realm="xMail API"');
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(array("message"=>"Invalid username or password: Failed to verify account"));
            recordMessage("Invalid username or password: Failed to verify account");
            recordMessage("-- END --");
            die();
        }else{
            // Valid login
        }
    }
}

// Check protocol version
if(!array_key_exists("XMAIL-PROTOCOL-VERSION", $headers)){
    header("HTTP/1.0 406 Not Acceptable");
    echo json_encode(array("message"=>"Version not supplied"));
    recordMessage("Protocol version not supplied");
    recordMessage("-- END --");
    die();
}
$protocolVersion = clean($headers['XMAIL-PROTOCOL-VERSION']);
if(!valid($protocolVersion) || !in_array($protocolVersion, unserialize(constant("SUPPORTED_VERSIONS"))) || !file_exists("protocol/".$protocolVersion.".php")){
    header("HTTP/1.0 406 Not Acceptable");
    echo json_encode(array("message"=>"Version not supported"));
    recordMessage("Protocol version not supported");
    recordMessage("-- END --");
    die();
}

// Load protocol
require_once("protocol/".$protocolVersion.".php");

// Check for a ban
checkBan(); // will die if banned

if($skipAuth){
    attemptAuthRequest();
    die();
}

// Check for a MODE
if(!array_key_exists("XMAIL-MODE", $headers)){
    header("HTTP/1.0 406 Not Acceptable");
    echo json_encode(array("message"=>"Mode not supplied"));
    recordMessage("Mode not supplied");
    recordMessage("-- END --");
    die();
}

// Execute the mode
$mode = clean($headers['XMAIL-MODE']);
if(valid($mode)){
    handleMode($mode, $authPort, $authVersion);
}else{
    header("HTTP/1.0 406 Not Acceptable");
    recordMessage("Invalid mode");
    echo json_encode(array("message"=>"Invalid Mode"));
    recordMessage("-- END --");
}
?>