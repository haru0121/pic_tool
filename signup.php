<?php
require('function.php');

debugLogStart();

if(!empty($_POST)){
    debug('POST送信されました');
    $email=$_POST['email'];
    $pass=$_POST['pass'];
    $pass_re=$_POST['pass_re'];
    validRequired($email,'email');
    validRequired($pass,'pass');
    validRequired($pass_re,'pass_re');

    if(empty($err_msg)){
    debug('空送信チェック通過');
    validEmail($email,'email');
    validPassRe($pass,$pass_re,'pass');

        if(empty($err_msg)){
            debug('パスワード照合通過');
            validHalf($pass,'pass');
            minStrLen($pass,'pass');
            maxStrLen($pass,'pass');
            if(empty($err_msg)){
                sameEmail($email,'email');
            if(empty($err_msg)){
                try{
                    debug('バリデーションok');
                    $dbh=dbConnect();
                    $sql='INSERT INTO users(email,pass,login_date,create_date) VALUES(:email,:pass,:login_date,:create_date)';
                    $exec=array(':email'=>$email,':pass'=>password_hash($pass,PASSWORD_DEFAULT),':login_date'=>date('Y-m-d H:i:s'),':create_date'=>date('Y-m-d H:i:s'));
                    $rst=querySet($dbh,$sql,$exec);
                    if($rst){
                        debug('データ登録完了');
                        
                        $_SESSION['u_id']=$dbh->lastInsertId();
                        $_SESSION['login_time']=time();
                        $_SESSION['login_limit']=3600;
                        debug('マイページへ遷移');
                        header('Location:mypage.php');
                        exit();
                    }
                }catch(RuntimeException $e){
                    error_log('エラー'.$e->getMessage());
                    $err_msg['common']=MSG7;
                }
            }
            }
        }
    }
}
?>

<?php 
$siteTitle='新規登録'; 
require('head.php');
?>
<header>
<nav>
<ul>
<li><a href="signup.php">新規登録</a></li>
<li><a href="login.php">ログイン</a></li>
</ul>

</nav>
</header>
<h2>新規登録</h2>
<?php echo(get_err_msg('common'));?>
<form method="post">
メールアドレス：<span><?php echo(get_err_msg('email'));?></span>
<input type="text" name="email" value="<?php echo(err_value('email')); ?>">
パスワード：<span><?php echo(get_err_msg('pass'));?></span>
<input type="password" name="pass" value="<?php echo(err_value('pass')); ?>">
パスワード(再入力):<span><?php echo(get_err_msg('pass_re'));?></span>
<input type="password" name="pass_re" value="<?php echo(err_value('pass_re')); ?>">
<input type="submit" value="登録する">

</form>
<?php 
require('footer.php');
?>