<?php
require_once "inc/user.inc.php";
require_once "inc/misc.inc.php";
require_once "inc/alerts.inc.php";
require_once "inc/mail.inc.php";

unset($_SESSION['username']);
unset($_SESSION);

session_destroy();
header("Location: login.php?success=You+have+been+logged+out");
?>