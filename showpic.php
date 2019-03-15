<?php
require('function.php');

debugLogStart();
$p_id=(!empty($_GET['p_id']))? $_GET['p_id']:'';
$u_id=(!empty($_SESSION['u_id']))? $_SESSION['u_id']:'';
$check_area=(!empty($_GET['area']))? intval($_GET['area']):'';
$page=(!empty($_GET['p']))? $_GET['p']:'1';
// 写真とエリア情報
$p_rst=get_pic_area($p_id);
// 投稿者情報
$u_rst=get_pic_user($p_id);
?>

<?php 
$siteTitle=''; 
require('head.php');
?>
<?php
require('header.php');
?>

<i class="fas fa-heart js_click_good <?php if(isGood($p_id,$u_id)){ echo 'active'; }?>" data-picid="<?php if(!empty($_SESSION['login_time'])){echo sani($p_id);}else{echo '';}?>"></i>
<img src="<?php echo getDBvalue($p_rst['picture1']);?>" alt="<?php echo getDBvalue($p_rst['title']); ?>" width="100%">
<?php echo getDBvalue($p_rst['title']);?>
<?php echo getDBvalue($p_rst['cutline']);?>
<?php echo getDBvalue($p_rst['sh_date']);?>
<?php echo getDBvalue($p_rst['area']);?>
<?php echo getDBvalue($u_rst['u_name']);?>
<img src="<?php echo getDBvalue($u_rst['icon']);?>" alt="投稿者アイコン" width="100px">

<a href="index.php?p=<?php echo getDBvalue($page)?><?php if(!empty($check_area)){echo '&area='.$check_area;}?>">&lt;一覧へ戻る</a>
<?php 
require('footer.php');
?>