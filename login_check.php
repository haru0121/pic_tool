<?php
if(empty($_SESSION['login_time']) || empty($_SESSION['u_id'])){
    debug('session無し');
    header('Location:login.php');
    exit();
}else{
if($_SESSION['login_time'] + $_SESSION['login_limit'] < time()){
    debug('セッションタイムアウト');
    session_destroy();
    header('Location:login.php');
    exit();
}
}
?>