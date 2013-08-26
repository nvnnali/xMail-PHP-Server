<?php
define("USERS_TABLE_NAME", "xmail_users");
define("USERS_USERNAME_COL", "username");
define("USERS_PASSWORD_COL", "password"); // Note: be sure to change the password policy if needed in the config.php
define("USERS_LOGGEDIN_COL", "loggedIn");

// Only set the following if USERS_USE_MAIN_SQL is false
define("USERS_MYSQL_HOST", "localhost");
define("USERS_MYSQL_USER", "xmail");
define("USERS_MYSQL_PASS", "xmail");
define("USERS_MYSQL_DATABASE", "xmail");

// From here on in there should be no need for editing

// Load sql
$sqlCon = mysqli_connect(constant("USERS_MYSQL_HOST"), constant("USERS_MYSQL_USER"), constant("USERS_MYSQL_PASS")); // need true for "new connection"
$sqlDb = mysqli_select_db($sqlCon, constant("USERS_MYSQL_DATABASE"));

function isUser($name){
    global $sqlCon;
    $query = mysqli_query($sqlCon, "SELECT `".constant("USERS_USERNAME_COL")."` FROM `".constant("USERS_TABLE_NAME")."` WHERE `".constant("USERS_USERNAME_COL")."`='".$name."' LIMIT 1");
    return mysql_num_rows($query)>0;
}

function loginUser($name, $hashedPassword){
    global $sqlCon;
    $query = mysqli_query($sqlCon, "SELECT * FROM `".constant("USERS_TABLE_NAME")."` WHERE `".constant("USERS_USERNAME_COL")."`='".$name."' AND `".constant("USERS_PASSWORD_COL")."`='".$hashedPassword."' LIMIT 1");
    if(mysqli_num_rows($query)>0){
        mysqli_query($sqlCon, "UPDATE `".constant("USERS_TABLE_NAME")."` SET `".constant("USERS_LOGGEDIN_COL")."`='1' WHERE `".constant("USERS_USERNAME_COL")."`='".$name."' AND `".constant("USERS_PASSWORD_COL")."`='".$hashedPassword."' LIMIT 1") or die(mysqli_error($sqlCon));
        return true;
    }
    return false;
}

function registerUser($name, $hashedPassword){
    global $sqlCon;
    mysqli_query($sqlCon, "INSERT INTO `".constant("USERS_TABLE_NAME")."` (`".constant("USERS_USERNAME_COL")."`, `".constant("USERS_PASSWORD_COL")."`, `".constant("USERS_LOGGEDIN_COL")."`) VALUES ('".$name."', '".$hashedPassword."', '1')") or die(mysqli_error($sqlCon));
    return true;
}

function logoutUser($name){
    global $sqlCon;
    mysqli_query($sqlCon, "UPDATE `".constant("USERS_TABLE_NAME")."` SET `".constant("USERS_LOGGEDIN_COL")."`='0' WHERE `".constant("USERS_USERNAME_COL")."`='".$name."' LIMIT 1") or die(mysqli_error($sqlCon));
}

function isUserLoggedIn($name){
    global $sqlCon;
    $query = mysqli_query($sqlCon, "SELECT `".constant("USERS_LOGGEDIN_COL")."` FROM `".constant("USERS_TABLE_NAME")."` WHERE `".constant("USERS_USERNAME_COL")."`='".$name."' AND `".constant("USERS_LOGGEDIN_COL")."`='1' LIMIT 1");
    return mysqli_num_rows($query)>0;
}
?>