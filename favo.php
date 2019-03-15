<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
$login_flg=true;
debug('favo.phpだ');
if(empty($_SESSION['login_time']) || empty($_SESSION['u_id'])){
    debug('session無し');
    $login_flg=false;
}else{
if($_SESSION['login_time'] + $_SESSION['login_limit'] < time()){
    debug('セッションタイムアウト');
    session_destroy();
    $login_flg=false;
}
}
if($login_flg){
    if(isset($_POST['p_id'])&&isset($_SESSION['u_id'])){
        debug('ポストアリ');
        $p_id=$_POST['p_id'];
        try{
            $dbh=dbConnect();
            $sql='SELECT * FROM favo WHERE pic_id=:p_id AND user_id=:u_id AND delete_flg=0';
            $exec=array(':p_id'=>$p_id,'u_id'=>$_SESSION['u_id']);
            $stmt=querySet($dbh,$sql,$exec);
            $resultCount=$stmt->rowCount();
            if(!empty($resultCount)){
                debug('すでにお気に入り済み、消します');
            $sql='DELETE FROM favo WHERE pic_id=:p_id AND user_id=:u_id AND delete_flg=0';
            $exec=array(':p_id'=>$p_id,'u_id'=>$_SESSION['u_id']);
            $stmt=querySet($dbh,$sql,$exec);
            }else{
                debug('未お気に入り、追加します');
                $sql='INSERT INTO favo(pic_id,user_id) VALUES(:p_id,:u_id)';
                $exec=array(':p_id'=>$p_id,'u_id'=>$_SESSION['u_id']);
                $stmt=querySet($dbh,$sql,$exec);
            }
        }catch(Exception $e){
            error_log('エラー発生'.$e->getMessage());
        }
    }
}else{
    debug('ログインしてください');

}

?>