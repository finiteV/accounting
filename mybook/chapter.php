<?php
/*
*修改最后header的url
*/
session_start();

require('../include/functions.php');
require('../include/cookie_bus.php');

if(isset($_SESSION['valid_user']) && !empty($_GET['onebook'])){
    /********Get variable**************/
    $onebook=$_GET['onebook'];
    if (!get_magic_quotes_gpc()){
      $onebook=addslashes($onebook);     
    }
    require('../include/mybook.class.php');
    $book=new mybook($_SESSION['valid_user']);
    $db=$book->my_connect();
    //it may fail by wrong encoding,if not.
    $onebook = $book->chang_default_encode($onebook);
    
    $editable=$book->is_editable($_SESSION['valid_user'],$onebook);
    $section=$book->fetch_section($db,$onebook);
    if(!$section){
      require_once('../include/lib/domain.inc');
      header('location:http://'.$cuki_dm);
      exit;
    }
?>
<!DOCTYPE html>
<head>
<title>Reading</title>
<meta charset='utf-8'>
<link href='../include/css/mybook_chapter.css' rel='stylesheet' type='text/css' />
<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
</head>
<article>
<nav>
<ul>
<?php
/*********show nav***************/
  $nav_item=0;  //ul id number
  //preset js for display with mobile
  echo '<script language="javascript">var status=true;</script>';
  foreach($section as $sec){
    /*********for js**************/
    echo '<li><button class="btn" onclick="'."unhidden('mymenu$nav_item'".',status)'.'">'.$sec['section'];
    echo '</button><ul id="'."mymenu$nav_item".'">';
    $nav_item++;
    
    /********make for mathjax****************/
    $sec['section']=addslashes($sec['section']);
    /**************End***********************/
    $chapter=$book->fetch_chapter($db,$sec['section'],$onebook);
    if(!$chapter){
      die('Error');
    }
    $chapter_num=count($chapter);
    for($i=0;$i<$chapter_num;$i++){
      /*****debuge*************/
      //print_r($chapter[$i]);
      /******************/
      /*****for mathjax**********/
      $onebook=stripslashes($onebook);
      /***********/
      echo '<li><a href="chapter.php?chapterid='.$chapter[$i]['chapterid']
        .'&onebook='.$onebook.'&chaptername='.$chapter[$i]['chaptername'].'&section='.$chapter[$i]['section'].'">'.$chapter[$i]['chaptername'].'</a></li>';
    }
    echo '</ul>';
  }
  /********End show nav*********/
?>
</ul>
<script language='javascript'>
function unhidden(id){
  var name = navigator.appName;

  if(status==true || status=="true"){  //opera use true, chrome use 'true'
    document.getElementById(id).style.display="block";
    if(name == "Opera")
        status=false;
    else
        status="false";
    //document.getElementById("test").innerHTML=status; 
  }
  else{
   document.getElementById(id).style.display="none";
   if(name == "Opera")
        status=false;
    else
        status="false";
   //document.getElementById("test").innerHTML=status;
  }
}
</script>
<p id='test'></p>
</nav>

<div id='chapter'>
<?php
  /**********show content**************/
  //Edit this chapter variable
  $edit=false;
  if(!empty($_GET['edit'])){
    if($_GET['edit']=='true'){
       $edit=true;
    }
  }
  //dealing variable
  if(!empty($_GET['chaptername']) && !empty($_GET['chapterid'])){
    $chaptername=$_GET['chaptername'];
    $chapterid=$_GET['chapterid'];
    if (!get_magic_quotes_gpc()){
      $chaptername=addslashes($chaptername);
      $chapterid=addslashes($chapterid);
    }
  }  
  if(!empty($_GET['chaptername']) && !empty($_GET['chapterid']) && !$edit){
    $chaptername=htmlspecialchars($chaptername);    //avoid script
    echo '<h1 align="center">'.stripslashes($chaptername).'</h1>';
    $chapter_content=$book->show_chapter($db,$chapterid);
    //hapter_content=stripslashes($chapter_content);
    //you can choose if show \n by use nl2br
    echo $chapter_content;
    //echo nl2br($chapter_content);

    
    /********Bottom nav***************/
    if(!empty($_GET['section'])){
       $section_name=$_GET['section'];
       if (!get_magic_quotes_gpc()){
         $section_name=addslashes($section_name);
       }
       $next_chap=$book->navi_chapter($db,$chapterid,$section_name,$onebook,'next');
       $pre_chap=$book->navi_chapter($db,$chapterid,$section_name,$onebook,'previous');
       //display
       echo '<div id="btmnav">';
       if(!$pre_chap){ echo '<table><tr><td>','Beginning','</td>';}
       else{
         echo '<table><tr><td><a href="chapter.php?chapterid='
           .$pre_chap[0].'&chaptername='.$pre_chap[1].'&section='.$pre_chap[2]
           .'&onebook='.$pre_chap[3].'">Previous Chapter&lt;&lt;</a></td>';
       }
       
       if($editable){
         echo '<td><a href="chapter.php?edit=true'.'&chapterid='
           .$chapterid.'&chaptername='.$chaptername.'&section='.$section_name
           .'&onebook='.$onebook.'" target="_blank">Edit</a></td>';
       }
       else{echo '<td></td>';}
       
       if(!$next_chap){ echo '<td>','End','</td></tr></table>';}
       else{
         echo '<td><a href="chapter.php?chapterid='
           .$next_chap[0].'&chaptername='.$next_chap[1].'&section='.$next_chap[2]
           .'&onebook='.$next_chap[3].'">&gt;&gt;Next Chapter</a></td>'.'</tr></table>';
       }
       echo '</div>';       
    }
  }
  else if($edit){
     /*******Edit this chapter***********/
?>
<form id = "myform" action="" method='post' enctype='utf-8'>
<script language='javascript' src='../include/js/mybookeditor.js'></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#btn2").click(function(){
    $("#msg").text("updating ...");
    $.post("update.php",
      $('#myform').serialize(),
      function(data,status){
        if(status=="success"){
            $("#msg").text("update successfully~");
            $("#upcontent").text(data);
        }
        else{
              $("#msg").text("Sorry, there is an error");
        }
    });
  });
});
</script>
<p id="msg">When finished, Press the button left.</p>
<p><input type='hidden' name='chapterid' id='chapterid' value=<?php echo $chapterid; ?>></p>
<textarea type='text' name='upcontent' id='upcontent' rows='50' cols='80' wrap='soft'>
<?php
    $chapter_content=$book->show_chapter($db,$chapterid);
    echo $chapter_content;
?>
</textarea>
<input type='button' id='btn2' value='Update'>
</form>
<div id='edittool'>
<ul>
<li><input type='button' value='b' class='ed_button' onClick='dotext("b")'></li>
<li><input type='button' value='h2' class='ed_button' onClick='dotext("h2")'></li>
<li><input type='button' value='p' class='ed_button' onClick='dotext("p")'></li>
<li><input type='button' value='imath' class='ed_button' onClick='dotext("im")'></li>
<li><input type='button' value='math' class='ed_button' onClick='dotext("mt")'></li>
<li><input type='button' value='ul' class='ed_button' onClick='dotext("ul")'></li>
<li><input type='button' value='ol' class='ed_button' onClick='dotext("ol")'></li>
<li><input type='button' value='theory' class='ed_button' onClick='dotext("thry")'></li>
<li><input type='button' value='br' class='ed_button' onClick='dotext("br")'></li>
<li><input type='button' value='li' class='ed_button' onClick='dotext("li")'></li>
</ul>
</div>
<?php
  /********End edit chapter***************/
  }
  else{
     echo '<h1 align="center">The Road Not Taken</h1>'
      ,'<p align="center">Robert Frost</p>'
      ,'<p align="center">Two roads diverged in a yellow wood,</p>'
      ,'<p align="center">And sorry I could not travel both</p>'
      ,'<p align="center">And be one traveler, long I stood</p>'
      ,'<p align="center">And looked down one as far as I could</p>'
      ,'<p align="center">To where it bent in the undergrowth;</p>'
      ,'<p align="center">Then took the other, as just as fair,</p>'
      ,'<p align="center">And having perhaps the better claim,</p>'
      ,'<p align="center">Because it was grassy and wanted wear;</p>'
      ,'<p align="center">Though as for that the passing there</p>'
      ,'<p align="center">Had worn them really about the same,</p>'
      ,'<p align="center">And both that morning equally lay</p>'
      ,'<p align="center">In leaves no step had trodden black.</p>'
      ,'<p align="center">Oh, I kept the first for another day!</p>'
      ,'<p align="center">Yet knowing how way leads on to way,</p>'
      ,'<p align="center">I doubted if I should ever come back.</p>'
      ,'<p align="center">I shall be telling this with a sigh</p>'
      ,'<p align="center">Somewhere ages and ages hence:</p>'
      ,'<p align="center">Two roads diverged in a wood,and I—</p>'
      ,'<p align="center">I took the one less traveled by,</p>'
      ,'<p align="center">And that has made all the difference.</p>';
  } 
  /******End show Content****************/
  
  /********Final step***************/
  $db->close();
?>
</div>
</article>
<?php require('../include/lib/footer.inc'); ?>
</html>
<?php
}
else{
  session_destroy();
  header('location:http://'.$$cuki_dm);
}
?>
