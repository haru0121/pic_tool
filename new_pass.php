<?php
require('function.php');

debugLogStart();

require('login_check.php');
$_SESSION['login_time']=time();
debug('SESSION:'.print_r($_SESSION,true));

if(!empty($_POST)){
    debug('POST送信されました');
    $old_pass=$_POST['old_pass'];
    $new_pass=$_POST['new_pass'];
    $new_pass_re=$_POST['new_pass_re'];
    validRequired($old_pass,'old_pass');
    validRequired($new_pass,'new_pass');
    validRequired($new_pass_re,'new_pass_re');
    
    if(empty($err_msg)){
        debug('空送信チェック通過');
        $u_id=$_SESSION['u_id'];
        $result=get_user($u_id);
        
        if(!password_verify($old_pass,$result['pass'])){
        $err_msg['old_pass']=MSG9;
        }
        validHalf($new_pass,'new_pass');  
        minStrLen($new_pass,'new_pass');
        maxStrLen($new_pass,'new_pass');
        validPassRe($new_pass,$new_pass_re,'new_pass_re');
        
                if(empty($err_msg)){
                    debug('バリデーションok');
                    try{
                    $dbh=dbConnect();
                    $sql='UPDATE users SET pass=:pass WHERE id=:u_id';
                    $exec=array(':pass'=>password_hash($new_pass,PASSWORD_DEFAULT),':u_id'=>$_SESSION['u_id']);
                    $stmt=querySet($dbh,$sql,$exec);

                    if(!empty($stmt)){
                        debug('パスワード変更完了');
                        $to=$result['email'];
                        $subject='パスワード変更のおしらせ';
                        $comment=<<<EOT
{$result['u_name']}様
いつもご利用ありがとうございます。
貴方様のアカウントのパスワードが
変更されましたのでご連絡致します。

なお、このメールに覚えのない方は
下記アドレスにご連絡くださいませ。

------------------------------
Email undermary@gmail.com

EOT;

                        sendMail($to,$subject,$comment);
                        $_SESSION['alart']='パスワードを変更しました';
                        header('Location:mypage.php');
                        exit();
                    }
                
            }catch(RuntimeException $e){
                error_log('エラー発生'.$e->getMessage());
                $err_msg['common']=MSG7;
            }
        }
    }
        
    
}
?>


<?php
$siteTitle='パスワード変更'; 
require('head.php');
?>
<?php
require('header.php');
?>
<h2>パスワード変更</h2>
<p>ご登録のメールアドレス宛に新しいパスワードへの変更メールが送信されます。</p>
<span><?php echo(get_err_msg('common'));?></span>
<form action="" method="post">
古いパスワード：<span><?php echo(get_err_msg('old_pass'));?></span>
<input type="password" name="old_pass" value="<?php echo(err_value('old_pass')); ?>">
新しいパスワード：<span><?php echo(get_err_msg('new_pass'));?></span>
<input type="password" name="new_pass" value="<?php echo(err_value('new_pass')); ?>">
新しいパスワード(再入力)：<span><?php echo(get_err_msg('new_pass_re'));?></span>
<input type="password" name="new_pass_re" value="<?php echo(err_value('new_pass_re')); ?>">
<input type="submit" value="パスワード変更">

</form>
<a href="mypage.php">戻る</a>
<?php 
require('footer.php');
?>