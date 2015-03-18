<?php
session_start();

require('../include/functions.php');
require('../include/cookie_bus.php');

if(isset($_SESSION['valid_user'])){
    require('../include/mybook.class.php');
    require('../include/mybook.share.php');
    $publicBook=new myShare();
    $db=$publicBook->my_connect();
    /*books shared by a user*/
    $publicBook->fetch_share($db);
    $my_shared=$publicBook->book;
    $db->close();
    $drawer_num=count($my_shared); 
    /*********End fetch book***********/
?>
<!DOCTYPE html>
<head>
<title>Shared Books</title>
<meta charset='utf-8'>
<link href='../include/css/mybook_index.css' rel='stylesheet' type='text/css' />
<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
</head>
<article>
<nav>
<ul>
  <li class="light"><a href="index.php" id='current'>My Galory</a></li>
  <li class="light"><a href="append.php">New book</a></li>
  <li class="light"><a href="publicshare.php">Public</a></li>
  <li class="light"><a href="#">Search</a></li>
  <li class="light"><a href="../details.php" target='_blank'>Home</a></li>
</ul>
</nav>

<div id='content'>
<?php
/*********display bookshell*************/
if($drawer_num==0){
  echo 'No one has shared a thing!';
}
else{
  echo '<h2>Books have been shared.</h2>';
}

//display shared book
for($i=0;$i<$drawer_num;$i++){
  echo '<h2>'.$my_shared[$i][0]['category'].'</h2>';
  echo '<ul class="tabs">';
  foreach($my_shared[$i] as $onebook){
    $publicBookname=explode('@',$onebook['book']);
    echo '<li><a href="chapter.php?onebook='.$onebook['book'].'" target="_blank">'
      .$publicBookname[1].',By '.$publicBookname[0].'</a></li>';
  }
  echo '</ul><br/><br/>';
}
echo '<br/><p align="right"><a href="myshare.php" target="_blank">Share Yours?</p>';
?>
</div>
</article>
<?php require('../include/lib/footer.inc'); ?>
</html>
<?php
}
else{
  session_destroy();
  require_once('../include/lib/domain.inc');
  header('location:http://'.$cuki_dm);
}
?>