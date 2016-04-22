<html>
<head>
<?php
require_once('include/functions.php');
try{
if(!empty($_POST['userid']) && !empty($_POST['email_addr'])
   && !empty($_POST['random_code']) && !empty($_POST['invi_code'])
   && !empty($_POST['pwd']) && !empty($_POST['pwdcfm']) 
   && !empty($_POST['term'])){
    if($_POST['term']!='term'){
        throw new Exception('You have not agree the terms.');
    }
    session_start();
    /*********check*************/
    if(!isset($_SESSION['random_code']) || 
       strtoupper($_POST['random_code'])!=$_SESSION['random_code']){
        session_destroy();
        throw new Exception('The symbols you entered is not correct.');
    }
    require_once('include/user_manage_fns.php');
    /***get post info*********/
    $userid=$_POST['userid'];
    $email=$_POST['email_addr'];
    $invi_code=$_POST['invi_code'];
    if (!get_magic_quotes_gpc()){
     $userid=addslashes($userid);
     $email=addslashes($email);
     $invi_code=addslashes($invi_code);
    }
    
    $userid=check_usrerid($userid);
    if(!$userid){
      throw new Exception("The name you entered is valid.");
    }

    if($_POST['pwd']!=$_POST['pwdcfm']){
      throw new Exception('The password you enterd do not match.');
    }
    if(strlen($_POST['pwd'])<6 && strlen($_POST['pwd'])>20){
      throw new Exception("Your password if too short or very long.");
    }
    $password=$_POST['pwd'];
    $password_cfm=$_POST['pwdcfm'];

   //check email
   $email=valid_email($email);
   if(!$email){
    throw new Exception('Sorry,Invalid email address.');
   }
   /******End check********/

   /******Not allow duplication*******/
   $db=my_connection();
   $error=check_info($db,$userid,$email,$invi_code);
   if($error!==true){
    throw new Exception($error);
   }
   $send_error=send_activite_info($db,$userid,$email,$invi_code,$password);
   if($send_error!==true){
    throw new Exception($send_error);
   }
   $db->close();
   /*********End************/
   echo 'Thanks for your register. Go check your email for more info';
 }

}
catch(Exception $e){
  $message=$e->getMessage();
  require_once('include/lib/domain.inc');
  goto_url($registe_domain,$message);
  //it has to end every here if error occurs
  exit;
} 
?>
<meta charset='utf-8'>
<title>Registaion</title>
<link href="include/css/registe1.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
//display message
if(!empty($_GET['message'])){
  echo htmlspecialchars($_GET['message']).'<br>';
}
?>
<article>
<div id='registe'>
<form name='registe_form' action='registe.php' method='POST' enctype='utf-8'>
<table>
<tr><td>User Name:</td><td><input type='text' class='txt' name='userid' placeholder='use numbers and alphabet' maxlength='10'></td></tr>
<tr><td>Password:</td><td><input type='password' class='txt' name='pwd' value=''></td></tr>
<tr><td>Confirm Password:</td><td><input type='password' class='txt' name='pwdcfm' value=''></td></tr>
<tr><td>Invitation Code:</td><td><input type='text' class='txt' name='invi_code' maxlength='40'></td></tr>
<tr><td>Email:</td><td><input type='email' class='txt' name='email_addr' maxlength='30'></td></tr>
<tr><td><img src="../include/verify.php" id='refresh' onclick="document.getElementById('refresh').src='../include/verify.php?t='+Math.random()"/></td>
<td><input type="text" class='txt' name="random_code" placeholder='请输入五位验证码' maxlength="5" autocomplete="off"></td></tr>
<tr><td><input type="checkbox" name="term" value="term" checked>I gree the term</td><td>(<a href='#'>See terms</a>)</td>
<tr><td></td><td><input type='submit' name='submit' id='bt' value='Registe'></td></tr>
</table>
</form>
</div>
</article>
<?php require_once('include/lib/footer.inc');  ?>
</body>
</html>