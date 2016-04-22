<!DOCTYPE html>
<head>
<meta charset='utf-8'>
<title>Forget Your Password</title>
<link href='../include/css/forget_pw1.css' rel='stylesheet' type='text/css'/>
</head>
<?php
if(!empty($_POST['user']) && !empty($_POST['question']) 
   && !empty($_POST['answer']) && !empty($_POST['email_addr'])
   && !empty($_POST['random_code'])){
   session_start();  
   require_once('../include/functions.php');
   require_once('include/config.php');
   require_once('../include/user_manage_fns.php');
   $url='admin/forget_pw.php';
   if(!isset($_SESSION['random_code']) || 
       strtoupper($_POST['random_code'])!=$_SESSION['random_code']){
        goto_url($url);
        exit;
    }
    
   $userid=trim($_POST['user']);
   $question=trim($_POST['question']);
   $email_addr=trim($_POST['email_addr']);
   $answer=$_POST['answer'];
   $email_addr=valid_email($email_addr);
   if(!$email_addr){
    die('Please Check email formate.<br/>
	  <a href="admin/forget_pw.php">Go Back</a>');
   }
   if (!get_magic_quotes_gpc()){
     $userid=addslashes($userid);
     $question=addslashes($question);
     $answer=addslashes($answer);
     $email_addr=addslashes($email_addr);
   }
   //check userid,question,email_addr
   //echo $userid.','.$question.','.$answer.','.$email_addr;
   $db=my_connection();
   /********start***********/

   $query[0]='select userid from users where
        userid='."'$userid'".';';
   $query[1]='select * from questions where
        userid='."'$userid'".' and question='."'$question'"
        .' and answer='."'$answer'".';';
   $query[2]='select * from email where
        userid='."'$userid'".' and email_addr='."'$email_addr'".';';
   
   //loop to determine conditions
   for($i=0;$i<count($query);$i++){
     //echo $query[$i].'<br/>';
     $result[$i]=$db->query($query[$i]);
      if(!$result[$i]){
        echo "Cannot run query.";
        exit;
      }
      $count[$i]=$result[$i]->num_rows;
      if($count[$i]!=1){
	die('Please Check the information again.<br/>
	  <a href="admin/forget_pw.php">Go Back</a>');
      }
      $result[$i]->free();
   }
   
   //all is satisfied, let's do it
   $random_pw=generate_pw(8);
   //refresh password
   $refresh_query='update users set pwd=sha1('."'$random_pw+$token'"
     .') where userid='."'$userid'".';';
   //echo $refresh_query.'<br/>';
   $refresh_result=$db->query($refresh_query);
   
   //notify user with her new pw
   send_new_pw($email_addr,$random_pw);
   echo 'Your new password has send to your email. Please check!';
    /***************end**********************/
   //exit session and close mysql connection
   $db->close();
   unset($_SESSION['random_code']);
   session_destroy();
   //goto_url();
}
?>
<body>
<article>
<p>Please fill in the Password Reset Form below to reset your password.</p>
<div id='forget'>
<form id='forget_form' action='forget_pw.php' method='post' enctype='utf-8'>
<table>
<tr>
<td>User Name:</td>
<td><input type='text' name='user' class='txt'></td>
</tr>
<td>Question:</td>
<td><select id="Question" name='question'>
<option value="0">Select...</option>
<option value="Mother's birthplace">Mother's birthplace</option>
<option value="Best childhood friend">Best childhood friend</option>
<option value="Name of first pet">Name of first pet</option>
<option value="Favorite teacher">Favorite teacher</option>
<option value="Favorite historical person">Favorite historical person</option>
<option value="Grandfather's occupation">Grandfather's occupation</option>
</select></td>
</tr>
<tr>
<td>Answers:</td>
<td><input type='text' name='answer' class='txt'></td>
</tr>
<tr>
<td>Email:</td>
<td><input type='email' class='txt' name='email_addr'></td>
</tr>
<tr>
<td><img src="../include/verify.php" id='refresh' onclick="document.getElementById('refresh').src='../include/verify.php?t='+Math.random()"/></td>
<td><input type="text" class='txt' name="random_code" size="5"></td>
</tr>
</table>
<input type='submit' id='sub_button' value='Submit'>
</form>
</div>
</article>

<?php require_once('../include/lib/footer.inc');  ?>
</body>
</html>
