<?php

session_start();

require('../include/functions.php');
require('../include/cookie_bus.php');

if(isset($_SESSION['valid_user'])){
  if(!empty($_POST['content']) && !empty($_POST['title'])
    && !empty($_POST['section']) && !empty($_POST['category']) 
    && !empty($_POST['chapter']) ){
    //echo nl2br($_POST['content']);
    /*********variable***************/
    $title=$_POST['title'];
    $section=$_POST['section'];
    $chapter=$_POST['chapter'];
    $category=$_POST['category'];
    $content=$_POST['content'];
    //$content=str_replace("\n",'<br/>&ensp;&ensp;',$content)
    if (!get_magic_quotes_gpc()){
      $title=addslashes($title);     
      $section=addslashes($section);
      $chapter=addslashes($chapter);
      $category=addslashes($category);
      $content=addslashes($content);
    }
    /*****debuge for datesafty********/
    //echo $content;
    /***********/
    require('../include/mybook.class.php');
    $book=new mybook($_SESSION['valid_user']);
    /**********See if category given is legal******************/
    $cat_array=$book->category;
    if(!in_array($category,$cat_array)){
      echo 'You are breaking my site. Stop messing around and go back to the home page';
    }
    else{
      /*********Interact with database*****************/
      $db=$book->my_connect();
      $title=$_SESSION['valid_user'].'@'.$title;  //specify it according to user
      $dup=$book->add_new($db,$chapter,$content,$section,$title,$category,$_SESSION['valid_user']);
      if(!$dup){
        echo 'Duplicate Information!';
      }
      else{
        echo 'Job Done!';
      }
      $db->close();
    }
  }

?>
<!DOCTYPE html>
<head>
<title>Add New</title>
<meta charset='utf-8'>
<link href='../include/css/mybook_append.css' rel='stylesheet' type='text/css' />
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

<div id='append'>
<form action='append.php' method='post' enctype='utf-8'>
<div >
<p><input type='text' name='title' placeholder='title' class='append_txt'>
<input type='text' name='section' placeholder='section' class='append_txt'>
<input type='text' name='chapter' placeholder='chapter' class='append_txt'>
<select id="Category" name='category'>
<option value="0">Category</option>
<option value="专业资料">专业资料</option>
<option value="语言">语言</option>
<option value="法律">法律</option>
<option value="文学">文学</option>
<option value="IT资料">IT资料</option>
<option value="办公文书">办公文书</option>
<option value="自然科学">自然科学</option>
<option value="综合性图书">综合性图书</option>
</select>
<input type='submit' class='button' value='add'></p>
<p><textarea type='text' name='content' placeholder='content' rows='50' cols='80' wrap='soft'></textarea></p>
</form>
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