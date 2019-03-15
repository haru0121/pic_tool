<?php
require('function.php');

debugLogStart();

require('login_check.php');
$_SESSION['login_time']=time();
debug('SESSION:'.print_r($_SESSION,true));

$result=get_user($_SESSION['u_id']);

if(!empty($_POST)){
    debug('POST送信アリ');
    $email=$_POST['email'];
    
    if(!empty($_FILES['icon'])){
        $file=$_FILES['icon'];
        $path=up_img($file,'common');
        
    }else{
        $path='';
    }
    if(empty($path)){
        $path=$result['icon'];
    }
    if($email !== $result['email']){
        validRequired($email,'email');
        validEmail($email,'email');
        maxStrLen($email,'email');
        if(empty($err_msg)){
            sameEmail($email,'email');
        }
    }
    
    if($_POST['u_name'] !== $result['u_name']){
        maxStrLen($_POST['u_name'],'u_name');
        if($_POST['u_name']===''){
            $_POST['u_name']=$result['u_name'];
        }
    }

    if($_POST['intro'] !== $result['intro']){
        maxStrLen($_POST['intro'],'intro');
        if($_POST['intro']===''){
            $_POST['intro']=$result['intro'];
        }
    }
    
    

    if(empty($err_msg)){
        debug('バリデーションok');
        $u_name=$_POST['u_name'];
        $intro=$_POST['intro'];

        try{

            $dbh=dbConnect();
            $sql='UPDATE users SET email=:email,u_name=:u_name,icon=:icon,intro=:intro WHERE id=:u_id';
            $exec=array(':email'=>$email,':u_name'=>$u_name,':icon'=>$path,':intro'=>$intro,':u_id'=>$_SESSION['u_id']);
            $stmt=querySet($dbh,$sql,$exec);
            
            if($stmt){
                debug('更新成功');
                debug('マイページ遷移');
                $_SESSION['alart']='ユーザー情報を編集しました';
                header('Location:mypage.php');
                exit();
            }else{
                $err_msg['common']=MSG7;
            }
        }catch(RuntimeException $e){
            error_log('エラー発生'.$e->getMessage());
            $err_msg['common']=MSG7;
        }

    }
}
?>
<?php 
$siteTitle='プロフィール編集'; 
require('head.php');
?>
<?php
require('header.php');
?>

<?php
require('menu.php');

?>
<span><?php echo(get_err_msg('common'));?></span>
<form action="" method="post" enctype="multipart/form-data">
メールアドレス：<span><?php echo(get_err_msg('email'));?></span>
<input type="text" name="email" value="<?php echo err_value('email');?>">
ニックネーム：<span><?php echo(get_err_msg('u_name'));?></span>
<input type="text" name="u_name" value="<?php echo err_value('u_name');?>">
自己紹介：<span><?php echo(get_err_msg('intro'));?></span>
<input type="text" name="intro" value="<?php echo err_value('intro');?>">
ユーザーアイコン：
<div class="icon_form"><label>
<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
<input type="file" name="icon" class="input_img" >
<img src="<?php if(!empty($result['icon'])){ echo($result['icon']);}else{ echo 'upload/sample-img.png';} ?>" alt="ユーザーアイコン" style="<?php if(empty($result['icon'])){?>display:none<?php }else{?>display:block<?php }?>">
ここに画像をドロップ</label>
</div>
<input type="submit" value="変更する">
</form>
<?php 
require('footer.php');
?>