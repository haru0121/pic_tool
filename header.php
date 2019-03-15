<header>
<nav>
<ul>

<?php if(empty($_SESSION['u_id'])){ ?>
<li><a href="signup.php">新規登録</a></li>
<li><a href="login.php">ログイン</a></li>

<?php }else{    ?>
<li><a href="mypage.php">マイページ</a></li>
<li><a href="logout.php">ログアウト</a></li>
<?php } ?>
</ul>

</nav>
</header>