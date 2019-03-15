<?php
require('function.php');

debugLogStart();
$check_area=(!empty($_GET['area']))? intval($_GET['area']):'';
$page=(!empty($_GET['p']))? $_GET['p']:1;
if(!is_int((int)$page)){
    header('Location:index.php');
}
// 表示件数
$list_span=10;
// ページ始まりの一番最初のコンテンツの番号
$cur_min_num=(($page-1)*$list_span);

$pictures=get_pictures($cur_min_num,$check_area);
// エリアカテゴリ取得
$area_cat=get_area();


?>
<?php 
$siteTitle='写真'; 
require('head.php');
?>
<?php
require('header.php');
?>

<!-- エリアカテゴリ -->
<form method="get">
    <select name='area'>
        <option value="0">選択してください</option>
        <?php foreach ($area_cat as $key => $area) { ?>
            <option value="<?php echo $area['id']?>"><?php echo $area['area_name']?></option>
        <?php }?>
    </select>
    <input type="submit" value="検索">
</form>

<span><?php echo sani($pictures['total']);?></span>件見つかりました。
<span><?php echo $cur_min_num+1?></span>-<span><?php echo $cur_min_num+$list_span; ?></span>件/
<span><?php echo sani($pictures['total']);?></span>
<?php if(!empty($pictures)){?>
<?php foreach ($pictures['data'] as $key => $pic) { ?>
       <a href="showpic.php?p_id=<?php echo $pic['id']?>&p=<?php echo $page?><?php if(!empty($check_area)){echo '&area='.$check_area;}?>"><img src="<?php echo $pic['picture1']?>" width="200px" height="200px"></a> 
        
<?php }?>
<?php }?>
    
<!-- ページング -->
<?php 
$col_num=5;
$total_page=$pictures['total_page'];

if($page==$total_page && $total_page>=$col_num){
    $min_page=$page-4;
    $max_page=$page;
}elseif ($page==($total_page-1) && $total_page>=$col_num){
    $min_page=$page-3;
    $max_page=$page+1;
}elseif ($page==2 && $total_page>=$col_num){
    $min_page=$page-1;
    $max_page=$page+3;
}elseif ($page==1 && $total_page>=$col_num){
    $min_page=$page;
    $max_page=$page+4;
}elseif ($total_page<$col_num){
    $min_page=1;
    $max_page=$total_page;
}else{
    $min_page=$page-2;
    $max_page=$page+2;
}
?>

<ul class="page_links">
<?php if($page !=1){ ?>
<li><a href="?p=1">&lt;</a></li>
<?php } ?>

<?php 
for($i=$min_page;$i<=$max_page;$i++){
?>
<li><a href="?p=<?php echo $i; ?>"><?php echo $i; ?></a></li>
<?php 
}
?>

<?php if($page !=$max_page){ ?>
<li><a href="?p=<?php echo $max_page?>">&gt;</a></li>
<?php } ?>
</ul>
<?php 
require('footer.php');
?>