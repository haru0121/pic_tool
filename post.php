<?php
require('function.php');

debugLogStart();


require('login_check.php');
$_SESSION['login_time']=time();
debug('SESSION:'.print_r($_SESSION,true));
$edit_flg='';
$p_result='';
// リクエストパラメータ
$p_id=(!empty($_GET['p_id']))? $_GET['p_id']:'';
$p_result=(!empty($p_id))? get_picture($p_id,$_SESSION['u_id']):'';
$edit_flg=(!empty($p_result))? true : false;
$area_data=get_area(); 

if(!empty($p_id)&& empty($p_result)){
    debug('ユーザーＩＤ、一致しません');
    header('Location:mypage.php');
}

// 送信後//////////
if(!empty($_POST)){
    debug('POST送信アリ');
    $picture1='';
    debug(print_r($_FILES['picture1'],true));
    if(!empty($_FILES['picture1']['name'])){
        $picture1=up_img($_FILES['picture1'],'common');
    }
    if(empty($picture1) && !empty($p_result['picture1'])){
        $picture1=$p_result['picture1'];
    }
    if(empty($picture1)){
        $err_msg['common']='画像が登録されませんでした、再度お試しください';
    }
    if(empty($err_msg)){
        debug('画像ok');
        $title = $_POST['title'];
        $cutline = $_POST['cutline'];
        $sh_date = $_POST['sh_date']; 
        $area = $_POST['area'];
        debug(print_r($p_result,true));
        if(empty($p_result)){
            // 新規画面の時
            debug('新規画面バリデーション');
            if(!empty($title)){
            maxStrLen($title,'title');
            }
            if(!empty($ctline)){
            maxStrLen($cutline,'cutline');
            }
            valid_date($sh_date,'sh_date');
            valid_area($area,'area');
            debug(print_r($err_msg,true));
            
        }else{
            // 編集画面のとき
            if($p_result['title']!==$title){
                maxStrLen($title,'title');
            }
            if($p_result['cutline']!==$cutline){
                maxStrLen($cutline,'cutline');
            }
            if($p_result['sh_date']!==$sh_date){
                valid_date($sh_date,'sh_date');
            }
            if($p_result['area']!==$area){
                valid_area($area,'area');
            }

        }

        if(empty($err_msg)){
            debug('バリデーションok');
            try{
                $dbh=dbConnect();
                if($edit_flg){
                    debug('情報更新です');
                    $sql='UPDATE picture SET picture1=:picture1,title=:title,cutline=:cutline,sh_date=:sh_date,area_id=:area_id 
                    WHERE id=:p_id AND user_id=:u_id AND delete_flg=0';
                    $exec=array(':picture1'=>$picture1,':title'=>$title,':cutline'=>$cutline,':sh_date'=>$sh_date,':area_id'=>$area,'p_id'=>$p_id,':u_id'=>$_SESSION['u_id']);
                }else{
                    debug('新規登録です');
                    $sql='INSERT INTO picture(picture1,user_id,title,cutline,sh_date,area_id) VALUES(:picture1,:u_id,:title,:cutline,:sh_date,:area_id)';
                    $exec=array(':picture1'=>$picture1,':u_id'=>$_SESSION['u_id'],':title'=>$title,':cutline'=>$cutline,':sh_date'=>$sh_date,':area_id'=>$area);
                }
                $rst=querySet($dbh,$sql,$exec);
                if($rst){
                    debug('登録成功');
                    $_SESSION['alart']='登録しました';
                    header("Location:mypage.php");
                    exit();
                }else{
                    debug('登録失敗');
                    $err_msg['common']=MSG7;
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
$siteTitle;
if($edit_flg){
    $siteTitle='投稿編集'; 
} else{
    $siteTitle='新規投稿';
}
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
<div class="icon_form"><label>
<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
<input type="file" name="picture1" class="input_img" >
<img src="<?php if(!empty($p_result['picture1'])){ echo($p_result['picture1']);}?>" alt="投稿写真" style="<?php if(empty($p_result['picture1'])){?>display:none<?php }else{?>display:block<?php }?>">
ここに画像をドロップ</label>
</div>

タイトル：<span><?php echo(get_err_msg('title'));?></span>
<input type="text" name="title" value="<?php echo err_value('title');?>">
説明：<span><?php echo(get_err_msg('cutline'));?></span>
<input type="text" name="cutline" value="<?php echo err_value('cutline');?>">
撮影日：<span><?php echo(get_err_msg('sh_date'));?></span>
<input type="date" name="sh_date" value="<?php if(!empty($_POST['sh_date'])){
        echo(sani($_POST['sh_date']));
        }else{echo date('Y-m-j');}?>">
撮影エリア：<span><?php echo(get_err_msg('area'));?></span>
    <select name="area" >
    <option value="0" <?php if(empty($p_result['area_id'])) echo 'selected';?>>選択してください</option>
    <?php 
    foreach($area_data as $key=>$val){
    ?>
    <option value="<?php echo $val['id'] ?>">
    <?php echo $val['area_name']; ?>
    </option>
    <?php 
    }
    ?>
</select>


<input type="submit" value="<?php if($edit_flg){ echo '変更する';}else{ echo '投稿する';}?>">
</form>
<?php 
require('footer.php');
?>

