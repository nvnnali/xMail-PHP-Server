<?php
function valid($input){
    $input = trim($input);
    return isset($input) && $input!=null && $input!="";
}

function clean($input){
    if(get_magic_quotes_gpc()){
        $input = stripslashes($input);
    }
    global $sqlConn;
    $input = mysqli_real_escape_string($sqlConn, $input);
    $input = htmlentities($input, ENT_COMPAT, "UTF-8");
    return $input;
}

function dirty($input){
    //if(get_magic_quotes_gpc()){
    //    $input = addslashes($input);
    //}
    //$input = mysql_real_escape_string($input);
    $input = stripslashes($input);
    $input = html_entity_decode($input, ENT_COMPAT, "UTF-8");
    return $input;
}
?>