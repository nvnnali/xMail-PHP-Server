<?php
include_once "inc/user.inc.php";
include_once "inc/misc.inc.php";
include_once "inc/alerts.inc.php";
include_once "inc/mail.inc.php";

unset($_SESSION['username']);
unset($_SESSION);

session_destroy();
header("Location: login.php?success=You+have+been+logged+out");
?>