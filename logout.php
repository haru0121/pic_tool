<?php 
require('function.php');
debug('ログアウト');
session_destroy();
debug('SESSION'.print_r($_SESSION,true));
header('Location:login.php');
?>