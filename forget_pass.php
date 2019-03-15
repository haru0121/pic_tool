<?php
require('function.php');

debugLogStart();

if(!empty($_POST['email'])){
    $email=$_POST['email'];
    validEmail($email,'email');

    if(empty($err_msg)){
    try{
        $dbh=dbConnect();
        $sql='SELECT count(*) FROM users WHERE email=:email AND delete_flg=0';
        $exec=array('email'=>$email);
        $stmt=querySet($dbh,$sql,$exec);
        $rst=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($rst))){
            debug('登録メールアドレス確認ok');

            $reissue_pass=random();

            $to=$email;
            $subject='パスワード再発行手続き';
            $comment=<<<EOT
パスワード再発行手続きを行います。
下記URLにて、メール記載の認証キーをご入力いただきますと
パスワードが再発行されます。

認証キー：{$reissue_pass}
キー入力URL：http://localhost:8888/freemarket/make_pass.php

なお認証キーの有効期限は30分となります。
有効期限が切れた際にはお手数ですが再度手続きを行ってください。
EOT;

            
            $mail_result=sendMail($to,$subject,$comment);

            if($mail_result){
            $_SESSION['reissue_pass']=$reissue_pass;
            $_SESSION['reissue_limit']=time()+(60*30);
            $_SESSION['email']=$email;
            $_SESSION['alart']='認証キー記載のメールを送信しました';
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            header('Location:make_pass.php');
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
$siteTitle='パスワード再発行'; 
require('head.php');
?>
<?php
require('header.php');
?>
<h2>パスワード再発行</h2>
<p>ご登録のメールアドレス宛に認証キーが送信されます。<br>
30分以内にメール内のURLから入力を行ってください</p>
<span><?php echo(get_err_msg('common'));?></span>
<form action="" method="post">
ご登録のメールアドレス：<span><?php echo(get_err_msg('email'));?></span>
<input type="text" name="email" value="<?php echo(err_value('email')); ?>">

<input type="submit" value="認証キー送信">

</form>
<a href="login.php">戻る</a>
<?php 
require('footer.php');
?>