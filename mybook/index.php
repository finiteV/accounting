<?php

session_start();

require('../include/functions.php');
require('../include/cookie_bus.php');

if(isset($_SESSION['valid_user'])){
    require('../include/mybook.class.php');
    $book=new mybook($_SESSION['valid_user']);
    $db=$book->my_connect();
    $book->fetch_books($db);
    /*******debuge***********/
    //print_r($book->category);
    //echo count($book->book).'<br/>';
    //print_r($book->book);
    /*********display bookshell*************/
    $drawer_num=count($book->book);
    $mybooks=$book->book;
    $db->close();
?>
<!DOCTYPE html>
<head>
<title>Books</title>
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
if($drawer_num==0){
  echo 'Your Bookshell is Empty!';
}
for($i=0;$i<$drawer_num;$i++){
  /*******debuge*********/
  //print_r($mybooks[$i]);
  /********************/
  echo '<h2>'.$mybooks[$i][0]['category'].'</h2>';
  echo '<ul class="tabs">';
  foreach($mybooks[$i] as $onebook){
    $bookname=explode('@',$onebook['book']);
    echo '<li><a href="chapter.php?onebook='.$onebook['book'].'" target="_blank">'.$bookname[1].'</a></li>';
  }
  echo '</ul><br/><br/>';
}
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
