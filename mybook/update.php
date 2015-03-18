<?php

session_start();

if(isset($_SESSION['valid_user']) && !empty($_POST['chapterid'])
  && !empty($_POST['upcontent'])){
    //echo nl2br($_POST['content']);
    /*********variable***************/
    $chapterid=$_POST['chapterid'];
    $upcontent=$_POST['upcontent'];
    //$content=str_replace("\n",'<br/>&ensp;&ensp;',$content)
    if (!get_magic_quotes_gpc()){
      $chapterid=addslashes($chapterid);     
      $upcontent=addslashes($upcontent);
    }
    require('../include/mybook.class.php');
    $book=new mybook($_SESSION['valid_user']);
    /*********Interact with database*****************/
    $db=$book->my_connect();
    /*******debuge**********/
    //echo $chapterid.'<br/>'.$upcontent;
    /*******************/
    $sucess=$book->edit_chapter($db,$chapterid,$upcontent);
    if(!$sucess){
      echo 'Sorry,No Such Chapter. Whatever it was that you were looking for is obviously not here. Stop messing around and go back to the home page!';
    }
    else{
//       echo 'Update Sucessfully! You May want to go back or close this page.';
        //成功更新,返回更新内容,供jquery的post的data
        echo stripslashes($upcontent);        
    }
    $db->close();
}
else{
  session_destroy();
  require_once('../include/lib/domain.inc');
  header('location:http://'.$cuki_dm);
}
?>
