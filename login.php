<?php
require('function.php');

debugLogStart();

if(!empty($_SESSION['login_time']) && !empty($_SESSION['u_id'])){
    debug('ログイン済');
    if($_SESSION['login_time'] + $_SESSION['login_limit'] > time()){
        debug('セッションタイムOK');
        header('Location:mypage.php');
    }
}
if(!empty($_POST)){
    debug('POST送信されました');
    $email=$_POST['email'];
    $pass=$_POST['pass'];
    validRequired($email,'email');
    validRequired($pass,'pass');

    if(empty($err_msg)){
    debug('空送信チェック通過');
    validEmail($email,'email');
    validHalf($pass,'pass');
        
            if(empty($err_msg)){
                try{
                    debug('バリデーションok');
                    $dbh=dbConnect();
                    $sql='SELECT pass,id FROM users WHERE email=:email AND delete_flg=0';
                    $exec=array(':email'=>$email);
                    $rst=querySet($dbh,$sql,$exec);
                    if($rst !==0){
                        $result=$rst->fetch(PDO::FETCH_ASSOC);
                        if(password_verify($pass,$result['pass'])){
                        debug('パスワードマッチ');
                        
                        $_SESSION['u_id']=$result['id'];
                        $_SESSION['login_time']=time();

                        if(!empty($_POST['login_save'])){
                            debug('ログイン認証チェックあり');
                            $_SESSION['login_limit']=3600*24*30;
                        }else{
                            debug('ログイン認証チェック無し');
                            $_SESSION['login_limit']=3600;
                        }
                        header('Location:mypage.php');
                    exit();
                    
                    }else{
                        debug('パスワードアンマッチ');
                        $err_msg['common']='メールアドレスまたはパスワードが違います';
                    }
                }else{
                    debug('くえりfalse');
                    $err_msg['common']='メールアドレスまたはパスワードが違います';
                }
                }catch(RuntimeException $e){
                    error_log('エラー'.$e->getMessage());
                    $err_msg['common']=MSG7;
                }
            }
        }
        
    
}
?>


<?php
$siteTitle='ログイン'; 
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

<h2>ログイン</h2>
<span><?php echo(get_err_msg('common'));?></span>
<form action="" method="post">
登録メールアドレス：<span><?php echo(get_err_msg('email'));?></span>
<input type="text" name="email" value="<?php echo(err_value('email')); ?>">
パスワード：<span><?php echo(get_err_msg('pass'));?></span>
<input type="text" name="pass" value="<?php echo(err_value('pass')); ?>">
<input type="checkbox" name="login_save" value="on">ログイン状態保持
<input type="submit" value="ログイン">

</form>
パスワードをお忘れの方は<a href="forget_pass.php">コチラ</a>
<?php 
require('footer.php');
?>