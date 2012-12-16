<?php
echo json_encode(array("result" => sha1($_REQUEST['sha1']), "input" => $_REQUEST['sha1']));
?>