<?php
include "inc/user.php";
unset($_SESSION['username']);
unset($_SESSION);
session_destroy();
header("Location: login.php?success=You+have+been+logged+out");
?>