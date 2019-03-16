<?php
require('function.php');

debugLogStart();

require('login_check.php');
$_SESSION['login_time']=time();
debug('SESSION:'.print_r($_SESSION,true));

$user=get_user($_SESSION['u_id']);
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
<?php echo getDBvalue($user['u_name']); ?>


<a href="index.php">もっと見る</a>

<?php

require('menu.php');
?>
<?php 
require('footer.php');
?>