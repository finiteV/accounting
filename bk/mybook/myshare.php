<?php
//需要修改header部分
session_start();

require('../include/functions.php');
require('../include/cookie_bus.php');

if(isset($_SESSION['valid_user'])){
    require('../include/mybook.class.php');
    require('../include/mybook.share.php');
    $mybooks=new myShare();
    $db=$mybooks->my_connect();
    /**********Dealing Post data****************/

    if(!empty($_POST['privatebook'])){
      $beprivte=$_POST['privatebook'];
      foreach($beprivte as $oneprivate){
        if (!get_magic_quotes_gpc()){
          $oneprivate=addslashes($oneprivate);
        }
        /*******/
        //echo $oneprivate;
        /******/
	$done=$mybooks->share_or_not($db,$oneprivate,true);
	if(!$done){
	  die('Bad Request! Whatever it was that you were looking for is obviously not here. Stop messing around and go back to the home page!');
	}
      }      
    }
    
    if(!empty($_POST['sharedbook'])){
      $beshared=$_POST['sharedbook'];
      foreach($beshared as $oneshare){
        if (!get_magic_quotes_gpc()){
          $oneshare=addslashes($oneshare);
        }   
	$done=$mybooks->share_or_not($db,$oneshare,false);
	if(!$done){
	  die('Bad Request! Whatever it was that you were looking for is obviously not here. Stop messing around and go back to the home page!');
	}
      }
    }    
    /*********End*****************/
    /*books shared by a user*/
    $mybooks->fetch_share($db,$_SESSION['valid_user']);
    $my_shared=$mybooks->book;
    /******books not shared of someone*******/
    $mybooks->fetch_private($db,$_SESSION['valid_user']);
    $my_private=$mybooks->book;
    $db->close();
    $drawer_num[0]=count($my_shared); 
    $drawer_num[1]=count($my_private);
    /*********End fetch book***********/
?>
<!DOCTYPE html>
<head>
<title>My shared Books</title>
<meta charset='utf-8'>
<link href='../include/css/mybook_share1.css' rel='stylesheet' type='text/css' />
<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
</head>
<article>

<div id='content'>
<?php
/*********display bookshell*************/
if($drawer_num[0]==0 && $drawer_num[1]){
  echo 'Your shelf is empty!';
}

echo '<form action="myshare.php" method="post" enctype="utf-8"><br/>'
    ,'<h2>Public Books.</h2>';
//display shared book
for($i=0;$i<$drawer_num[0];$i++){
  echo '<h3>'.$my_shared[$i][0]['category'].'</h3>';
  foreach($my_shared[$i] as $onebook){
    $mypublicname=explode('@',$onebook['book']);
    echo '<input type="checkbox" name="sharedbook[]" value='.$onebook['book'].'>'.$mypublicname[1].'&ensp;&ensp;';
  }
}

//display private book
echo '<h2>Private Books.</h2>';
for($i=0;$i<$drawer_num[1];$i++){
  echo '<h3>'.$my_private[$i][0]['category'].'</h3>';
  foreach($my_private[$i] as $onebook){
    $myprivatename=explode('@',$onebook['book']);
    echo '<label><input type="checkbox" name="privatebook[]" value='.$onebook['book'].'>'.$myprivatename[1].'</label>&ensp;&ensp;';
  }
}
echo '<br/><br/><input type="submit" class="bt" value="Ok"><label>(Note: Select the oppsite.)</label></form>';
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