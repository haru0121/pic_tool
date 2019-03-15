<?php
require('function.php');

debugLogStart();
debug('セッション変数の中身：'.print_r($_SESSION,true));
if(empty($_SESSION['reissue_pass'])){
    debug('セッション無し、戻ります');
    header("Location:login.php");
}


// $_SESSION['reissue_pass']=$reissue_pass;
//             $_SESSION['reissue_limit']=time()+(60*30);
//             =$email;
//             $_SESSION['alart']='認証キー記載のメールを送信しました';


if(!empty($_POST['reissue_pass_re'])&&!empty($_SESSION['reissue_limit'])){
    $reissue_pass_re=$_POST['reissue_pass_re'];
    $reissue_limit=$_SESSION['reissue_limit'];
    validRequired($reissue_pass_re,'reissue_pass_re');
    validPassRe($reissue_pass_re,$_SESSION['reissue_pass'],'reissue_pass_re');

    if($reissue_limit < time()){
            $err_msg['reissue_pass_re']='認証キーの有効期限切れです';
        }
    
    if(empty($err_msg)){
        debug('バリデーションok');
        $pass=random();
        $email=$_SESSION['email'];
    try{
        $dbh=dbConnect();
        $sql='UPDATE users SET pass=:pass WHERE email=:email AND delete_flg=0';
        $exec=array('pass'=>password_hash($pass,PASSWORD_DEFAULT),'email'=>$email);
        $stmt=querySet($dbh,$sql,$exec);
        if($stmt){
            debug('passデータベース登録完了');
            $to=$email;
            $subject='パスワード再発行';
            $comment=<<<EOT
パスワードを再発行致しました。

新しいパスワード：{$pass}
ログインはこちら：http://localhost:8888/freemarket/login.php

ログイン後はマイページのパスワード編集画面から、パスワードの変更をお願い致します。
今後ともサービスをお楽しみください。

EOT;

            
            $mail_result=sendMail($to,$subject,$comment);

            if($mail_result){
            session_unset();
            $_SESSION['alart']='新しいパスワード記載のメールを送信しました';
            debug('セッション変数の中身：'.print_r($_SESSION,true));

            header('Location:login.php');
            exit();
            }else{
                debug('送信失敗');
                $err_msg['common']='メールの送信に失敗しました。お手数ですがメール受信設定の確認等をお試しください';

            }

            
        }
    }catch(RuntimeException $e){
        error_log('エラー'.$e->getMessage());
        $err_msg['common']=MSG7;
    }
}
}
?>

<?php
$siteTitle='認証キー入力'; 
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

<h2>認証キー入力</h2>
<p>メール記載の認証キーを入力するとパスワードが再発行されます<br>
<span><?php echo(get_err_msg('common'));?></span>
<form action="" method="post">
認証キー(有効期限:30分)：<span><?php echo(get_err_msg('reissue_pass_re'));?></span>
<input type="text" name="reissue_pass_re" value="<?php echo(err_value('reissue_pass_re')); ?>">

<input type="submit" value="パスワード再発行">

</form>
認証キーの有効期限切れは<a href="forget_pass.php">コチラ</a>
<a href="login.php">戻る</a>
<?php 
require('footer.php');
?>