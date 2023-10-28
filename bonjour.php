<?php
date_default_timezone_set('Europe/Paris');
$date = date('Y-m-d H:i:s', time());
$msg = "Bonjour de PHP � ".$date;
echo $msg;
phpinfo();
error_log($msg);
?>