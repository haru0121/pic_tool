<?php
require('function.php');

debugLogStart();

require('login_check.php');

if(!empty($_POST)){
    debug('退会する');

    try{
        $dbh=dbConnect();

        $sql='UPDATE users SET delete_flg=1 WHERE id=:u_id';
        $exec=array(':u_id'=>$_SESSION['u_id']);
        $stmt=querySet($dbh,$sql,$exec);

        if($stmt){
            $_SESSION=array();
      if(ini_get("session.use_cookies")){
        $params=session_get_cookie_params();
        setcookie(session_name(),'',time()-42000,
        $params['path'],$params['domain'],
        $params['secure'],$params['httponly']);
      }
            session_destroy();
            debug('退会完了セッション:'.print_r($_SESSION,true));
            header("Location:login.php");
        }else{
            debug('クエリ失敗');
            $err_msg['common']=MSG7;
        }
    }catch(RuntimeException $e){
        error_log('エラー発生'.$e->getMessage());
        $err_msg['common']=MSG7;
    }
}
?>

<?php
$siteTitle='退会'; 
require('head.php');
?>
<?php
require('header.php');
?>
<h2>退会</h2>
<span><?php echo(get_err_msg('common'));?></span>
<form action="" method="post">

<input type="submit" name="delete" value="退会する">

</form>
<a href="mypage.php">戻る</a>
<?php 
require('footer.php');
?>