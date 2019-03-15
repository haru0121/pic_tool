<?php
ini_set('log_errors','on');
ini_set('error_log','php.log');
// //////////////////////////////
// デバッグ
$debug_flg=true;
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}
function debugLogStart(){
    debug('画面表示処理スタート');
    debug('現在日時：'.time());

}

// セッション////////////////////////////////
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_lifetime',60*60*24*30);

session_start();
session_regenerate_id();

// バリデーション/////////////////////////////
$err_msg=array();
define('MSG1','入力必須です');
define('MSG2','Emailの形式で入力してください');
define('MSG3','入力内容が一致しませんでした');
define('MSG4','半角英数字で入力して下さい');
define('MSG5','6文字以上で入力してください');
define('MSG6','255文字以内で入力してください');
define('MSG7','エラーが発生しました。申し訳ございませんがしばらく経ってからやり直して下さい');
define('MSG8','そのEmailアドレスは既に登録されています');
define('MSG9','パスワードの認証に失敗しました');
// 未入力チェック

function validRequired($val,$key){
    if(empty($val)){
        global $err_msg;
        $err_msg[$key]=MSG1;
    }
    }

// Email形式チェック

function validEmail($val,$key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$val)){
        global $err_msg;
        $err_msg[$key]=MSG2;
    }
}

//Email重複チェック

function sameEmail($val,$key){
    global $err_msg;
    try{
        $dbh=dbConnect();
        $sql='SELECT count(*) FROM users WHERE email=:email AND delete_flg=0';
        $exec=array('email'=>$val);
        $stmt=querySet($dbh,$sql,$exec);
        $rst=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($rst))){
            $err_msg[$key]=MSG8;
        }
    }catch(RuntimeException $e){
        error_log('エラー'.$e->getMessage());
        $err_msg['common']=MSG7;
    }
}
// 日付チェック/////////////
function valid_date($date,$key){
    debug('日付チェック');
    validRequired($date,$key);
    if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$date)){
        global $err_msg;
        debug('日付チェックばつ');
        $err_msg[$key]='日付の形式が正しくありません';
    }
}
// エリアチェック////////////////
function valid_area($area,$key){
    debug('えりあチェック');
    validRequired($area,$key);
    if($area===0){
        global $err_msg;
        $err_msg[$key]='エリアを選択してください';
    }
    if(!preg_match("/^[1-9]+$/", $area)){
        global $err_msg;
        $err_msg[$key]='エリア選択の形式が正しくありません';
    }
}

// パスワード照合

function validPassRe($val,$val2,$key){
    if($val !== $val2){
        global $err_msg;
        $err_msg[$key]=MSG3;
    }
}

// 半角英数字チェック

function validHalf($val,$key){
    if(!preg_match("/^[a-zA-Z0-9]+$/",$val)){
        global $err_msg;
        $err_msg[$key]=MSG4;
    }
}

// 最小文字数チェック

function minStrLen($val,$key,$min=6){
    if(mb_strlen($val) < $min){
        global $err_msg;
        $err_msg[$key]=MSG5;

    }
}
// 最大文字数チェック
function maxStrLen($val,$key,$max=255){
    if(mb_strlen($val) > $max){
        global $err_msg;
        $err_msg[$key]=MSG6;

    }
}

//エラーメッセージ出力
function get_err_msg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}

// エラー時のフォームバリュー表示
function err_value($key){
    global $result;
    if(isset($_POST[$key])){
        return sani($_POST[$key]);
    }else if(isset($result[$key])){
        return sani($result[$key]);
    }
}
// 情報表示用
function getDBvalue($val){
    if(!empty($val)){
        return sani($val);
    }else{
        return '';
    }
}

// セッション使用メッセージ表示///////////////////////


// ユーザー情報取得//////////////////////////////////
function get_user($u_id){
    debug('ユーザー情報取得します');
    try{
    $dbh=dbConnect();
    $sql='SELECT * FROM users WHERE id=:u_id';
    $exec=array(':u_id'=>$u_id);
    $rst=querySet($dbh,$sql,$exec);
    if($rst){
        return $result=$rst->fetch(PDO::FETCH_ASSOC);
    }else{
        return $result='';
    }

    }catch(RuntimeException $e){
        error_log('データベース接続エラー'.$e->getMessage());
        $err_msg['common']=MSG7;
        return $result='';
    }
    }
// ピクチャ情報取得/////////////////////////////
function get_picture($p_id,$u_id){
    debug($u_id.$p_id.'写真情報取得');

    try{
        $dbh=dbConnect();
        $sql='SELECT * FROM picture WHERE id =:p_id AND user_id =:u_id AND delete_flg=0';
        $exec=array(':p_id'=>$p_id,':u_id'=>$u_id);
        $rst=querySet($dbh,$sql,$exec);
        if($rst){
        return $rst->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    
    }catch(RuntimeException $e){
        error_log('エラー発生'.$e->getMessage());
        global $err_msg;
        $err_msg['common']=MSG7;
    }
}
// 写真ユーザー情報取得///////////////////////
function get_pic_user($p_id){
    try{
        $dbh=dbConnect();
        $sql='SELECT u.u_name,u.icon FROM users AS u LEFT JOIN picture AS p ON u.id=p.user_id WHERE p.id=:p_id';
        $exec=array(':p_id'=>$p_id);
        $stmt=querySet($dbh,$sql,$exec);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(RuntimeException $e){
        error_log('エラー発生'.$e->getMessage());
    
    }
    
}
// 写真エリア取得////////////////////////////
function get_pic_area($p_id){
    try{
        $dbh=dbConnect();
        $sql='SELECT *,a.area_name AS area FROM picture AS p LEFT JOIN area AS a ON p.area_id=a.id WHERE p.id=:p_id';
        $exec=array(':p_id'=>$p_id);
        $stmt=querySet($dbh,$sql,$exec);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(RuntimeException $e){
        error_log('エラー発生'.$e->getMessage());
    
    }
    }
// いいね取得////////////////////////////
function isGood($p_id,$u_id){
    debug('いいね確認');
    try{
        $dbh=dbConnect();
        $sql='SELECT * FROM favo WHERE pic_id=:p_id AND user_id=:u_id AND delete_flg=0';
        $exec=array(':p_id'=>$p_id,'u_id'=>$u_id);
        $stmt=querySet($dbh,$sql,$exec);
        if($stmt->rowCount()){
            debug('いいね済み');
            return true;
        }else{
            debug('いいねじゃなかった');
            return false;
        }
    }catch(RuntimeException $e){
        error_log('エラー発生'.$e->getMessage());
    
    }

}
// カテゴリ取得////////////////////////////////
function get_area(){
    debug('エリアカテゴリ取得');
    try{
        $dbh=dbConnect();
        $sql='SELECT * FROM area';
        $exec=array();
        $rst=querySet($dbh,$sql,$exec);

        if($rst){
            return $rst->fetchAll();
        }else{
            return false;
        }
    }catch(RuntimeException $e){
        error_log('エラー発生'.$e->getMessage());
        global $err_msg;
        $err_msg['common']=MSG7;
    }
}
// 画面アップ/////////////////////////////////
    function up_img($val,$key){
        $path='';
        if(isset($val['error'])&&is_int($val['error'])){
            try{
            switch($val['error']){
                case 0:
                break;
                case 1:
                case 2:
                throw new RuntimeException('ファイルサイズが大きすぎます');
                break;
                case 4:
                throw new OutOfBoundsException('ファイルが未選択です');
                break;
                default:
                throw new RuntimeException('ファイルアップロードエラーが発生しました');
            }

            $type=@exif_imagetype($val['tmp_name']);
            if(!in_array($type,[IMAGETYPE_JPEG,IMAGETYPE_GIF,IMAGETYPE_PNG],true)){
                throw new RuntimeException('ファイル形式が対応外です');
            }

            $path='upload/'.sha1_file($val['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($val['tmp_name'],$path)){
                throw new RuntimeException('ファイル保存時エラー');

            }
            chmod($path,644);
            debug('ファイルアップok'.$path);
            return $path;
            }catch(OutOfBoundsException $e){
            return $path;
            }catch(RuntimeException $e){
            debug('ファイルアップの例外発生');
            error_log('ファイルアップエラー'.$e->getMessage());
            global $err_msg;
            $err_msg['$key']=$e->getMessage();
            return $path;
        }
        }else{
            return $path;
        }
    }
// サニタイズ//////////////////////////////////////
function sani($str){
   return htmlspecialchars($str,ENT_QUOTES);
}

//////////////////////////////////////////////
// DB   接続
function dbConnect(){
    debug('データベース接続');
    $dsn = "mysql:dbname=market;host=127.0.0.1;charset=utf8";
    $db_user='root';
    $db_pass='';
    $db_options=array(
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true
    );
    return new PDO($dsn,$db_user,$db_pass,$db_options);
}

function querySet($dbh,$sql,$exec){
    $stmt=$dbh->prepare($sql);
    if(!$stmt->execute($exec)){
        debug('クエリ失敗');
        debug('SQL:'.print_r($stmt,true));
        global $err_msg;
        $err_msg['common']=MSG7;
        return 0;
    }
    debug('クエリ成功');
    return $stmt;

}
// ページングsql///////////////
function page_sql($page,$limit,$dbname){
    return 'SELECT * FROM $dbname LIMIT $limit OFFSET($page-1)*10';
}


// 写真一覧取得//////////////////
function get_pictures($cur_min_num=1,$check_area,$span=10){
    debug('写真一覧取得');
    try{
        $dbh=dbConnect();
        $sql='SELECT id FROM picture';
        if(!empty($check_area)){
          
            $sql.=' WHERE area_id='.$check_area;
           
        }
            $exec=array();
        
        
        $stmt=querySet($dbh,$sql,$exec);
        $rst['total'] =$stmt->rowCount();
        $rst['total_page']=ceil($rst['total']/$span);
        if(!$rst){
            return false;
        }

        $sql='SELECT * FROM picture';
        if(!empty($check_area)){
          
            $sql.=' WHERE area_id='.$check_area;
           
        }
        $sql.=' LIMIT '.$span.' OFFSET '.$cur_min_num;
        $exec=array();
        debug($sql);
        $stmt=querySet($dbh,$sql,$exec);
        if($stmt){
            $rst['data']=$stmt->fetchAll();
            return $rst;
        }else{
            return false;
        }
    }catch(RuntimeException $e){
        error_log('エラー発生'.$e->getMessage());
        global $err_msg;
        $err_msg['common']=MSG7;
    }
}

// メールアドレス送信///////////////////////
function sendMail($to,$subject,$comment){
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    $from='undermary@gmail.com';
    $rst=mb_send_mail($to,$subject,$comment,"From:".$from);
    if($rst){
        debug('メール送信');
        
    }else{
        debug('メール送信失敗');
    }
    return $rst;
}

// ランダム生成///////////////////////////////
function random($length=8){
    return substr(bin2hex(random_bytes($length)),0,$length);
}
?>