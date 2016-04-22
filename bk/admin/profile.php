<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<?php
//You should  with your url for twice.
session_start();
require_once('../include/functions.php');
require_once('include/config.php');

if(isset($_SESSION['valid_user'])){
?>
<link href="../include/css/profile1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<nav><ul>
<li><a href="details.php">Main</a></li>
<li><a href="search.php">Search</a></li>
<li><a href="admin/location.php" target="blank">Location</a></li>
<li><a href="admin/profile.php" id='current'>Profile</a></li>
<li><a href="mybook/" target='blank'>MyBook</a></li>
<li><a href="logout.php">Log Out</a></li>
</ul></nav>

<article>
<?php
if(!empty($_POST['pwd_old']) || !empty($_POST['pwd_new'])
  || !empty($_POST['answer']) || !empty($_POST['question'])
  || !empty($_POST['new_email'])){
    
    $db=my_connection();
    $userid=addslashes($_SESSION['valid_user']);
//for changing question
if(!empty($_POST['answer']) && !empty($_POST['question'])){
  $question=trim($_POST['question']);
  $answer=$_POST['answer'];
  if (!get_magic_quotes_gpc()){
    $question=addslashes($question);
    $answer=addslashes($answer);
  }
  //echo $question.','.$answer;
  $qa_query='update questions set question = '."'$question'"
	    .', answer='."'$answer'".' where userid='."'$userid'".';';
        //echo $pw_query;
  $pw_result=$db->query($qa_query);
  echo 'Question and Answer are Successfull updated!';
}

//for changing password
if(!empty($_POST['pwd_old']) && !empty($_POST['pwd_new']) 
    && !empty($_POST['pwd_cfm']) 
    && $_POST['pwd_new']==$_POST['pwd_cfm']){
      //setting varible
      $pwd_old=$_POST['pwd_old'];
      $pwd_new=$_POST['pwd_new'];
      if (!get_magic_quotes_gpc()){
        $pwd_old=addslashes($pwd_old);
        $pwd_new=addslashes($pwd_new);
      }    
    
      //query desired from database
      $query='select * from users where
        userid='."'$userid'".' and 
        pwd=sha1('."'$pwd_old+$token'".');';
      //echo $query.'<br/>';
      $result=$db->query($query);
      if(!$result){
        echo "Cannot run query.";
        exit;
      }
    
      //get the result number,count equels 1 means it is all right
      $count=$result->num_rows;
      if($count==1){
	//add user ip address
        $pw_query='update users set pwd = sha1('."'$pwd_new+$token'"
	    .') where userid='."'$userid'".';';
        //echo $pw_query;
        $pw_result=$db->query($pw_query);
        echo 'Password Successfull updated!';
      }
      else{//destroy everything
	echo 'Please check passwords your have just entered.';
      }
      $result->free();
  }
  
//change email
if(!empty($_POST['new_email']) && (valid_email($_POST['new_email'])!=false)){
  $new_email=trim($_POST['new_email']);
  if (!get_magic_quotes_gpc()){
    $new_email=addslashes($new_email);
  }   
  //echo $old_email.','.$new_email;
  $eml_query='update email set email_addr = '."'$new_email'"
	    .' where userid='."'$userid'".';';
  //echo $eml_query;
  $eml_result=$db->query($eml_query);
  echo 'Email are Successfull updated!';
}
  $db->close();
}

?>
<ul>
<li>

<p><strong>Update Secrete Question</strong></p>
<form name='change_question' action='profile.php' method='post' enctype='utf-8'>
<p><label>Question:
<select id="Question" name='question'>
<option value="0">Select...</option>
<option value="Mother's birthplace">Mother's birthplace</option>
<option value="Best childhood friend">Best childhood friend</option>
<option value="Name of first pet">Name of first pet</option>
<option value="Favorite teacher">Favorite teacher</option>
<option value="Favorite historical person">Favorite historical person</option>
<option value="Grandfather's occupation">Grandfather's occupation</option>
</select></label></p>
<p><label>Answer:&ensp;<input type='text' class='txt'  name='answer' maxlength='30'></label></p>
<p><input type='submit' value='OK' class='bt'></p>
</form>
</li>

<li>

<p><strong>Please choose your favorate theme.</strong></p>
<form name='change_theme' action='profile.php' method='post' enctype='utf-8'>
<p><label>Theme:<select name='theme'>
<option value='pink'>Pink</option>
</select></label></p>
<p><input type='submit' value='OK' class='bt'></p>
</form>
</li>

<li>

<p><strong>Change your password below.</strong></p>
<form name='change_pw' action='profile.php' method='post' enctype='utf-8'>
<p><label>Old password:<br/><input type='password' name='pwd_old' class='txt'></label></p>
<p><label>New password:<br/><input type='password' name='pwd_new' class='txt'></label></p>
<p><label>Confirm password:<br/><input type='password' name='pwd_cfm' class='txt'></label></p>
<p><input type='submit' value='OK' class='bt'></p>
</form>
</li>

<li>
<p><strong>Modify Your Email Below</strong></p>
<form class='change_email' action='profile.php' method='post' enctype='utf-8'>
<p><label>New email address:<br/><input type='email' name='new_email' class='txt'></label></p>
</select></label><input type='submit' value='OK' class='bt'></p>
</from>
</li>
</article>
<?php require_once('../include/lib/footer.inc');  ?>

<?php
}
else{
  session_destroy();
  goto_url();
  exit;
}
?>

</body>
</html>
