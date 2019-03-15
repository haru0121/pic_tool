<?php
require('function.php');

debugLogStart();

require('login_check.php');
$_SESSION['login_time']=time();
debug('SESSION:'.print_r($_SESSION,true));


// if(!empty($_SESSION['login_time'])&& !empty($_SESSION['u_id'])){
//     debug('sessionアリ');
//     debug('SESSION:'.print_r($_SESSION,true));

//     if($_SESSION['login_time'] + $_SESSION['login_limit']> time() ){
//         $_SESSION['login_time']=time();

//     }else{
//         debug('セッションタイムアウト');
//         session_destroy();
//         header('Location:login.php');
//     }
// }else{
//     debug('session無し');
//     header('Location:login.php');
// }
?>
<?php 
$siteTitle='マイページ'; 
require('head.php');
?>
<?php
require('header.php');
?>
<?php
if(!empty($_SESSION['alart'])){ ?>
<div class='alart'>
<?php echo($_SESSION['alart']); ?>
</div>
<?php $_SESSION['alart']=''; ?>
<?php } ?>

<?php
require('menu.php');
?>
<?php 
require('footer.php');
?>