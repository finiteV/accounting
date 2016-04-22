<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<style>
body{
  margin:5px 30px;
  background:Snow;  
}
article{
  padding:10px;
  border:1px outset OldLace;
  word-wrap:break-word;
}
.txt{
  background: #F3F3F3;
  border:1px solid AntiqueWhite;
}
</style>
</head>
<?php
try{
if(!empty($_POST['suggest']) && !empty($_POST['email_addr'])){
  require_once('../include/functions.php');
  $suggest=$_POST['suggest'];
  $email=$_POST['email_addr'];
  $email=valid_email($email);
  if(!$email){
    throw new Exception('Invalid email address!');
  }
  if (!get_magic_quotes_gpc()){
     $suggest=addslashes($suggest);
     $email=addslashes($email);
   }
  $toaddress="tzterryz@gmail.com";
  $mailcontent=htmlspecialchars($suggest);
  $subject = "Feed back from web site";
  $fromaddress = "From:".$email;
  mail($toaddress, $subject, $mailcontent, $fromaddress);
  //echo $email.'<br/>'.$subject.'<br/>'.$content.'<br/>'.$fromaddress;
  //in the end
  echo 'Information has been sended successfully. Thanks for your time!';
}
}
catch(Exception $e){
  require_once('../include/lib/domain.inc');
  $error=$e->getMessage();
  goto_url($suggest_domain,$error);
  exit;
}
?>
<body>
<?php
//display message
if(!empty($_GET['message'])){
  echo htmlspecialchars($_GET['message']).'<br>';
}
?>
<article>
<div id='suggestion'>
<p>Please leave your comment below.</p>
<form name='suggest_form' action='suggestion.php' method='post' enctype='utf-8'>
<p><label>Your Email:<br/><input type='email' name='email_addr' placeholder='Your email' class='txt'></label></p>
<p><label>Feed back:<br/><textarea name='suggest' class='txt' rows='15' placeholder='Feed back' cols='50' wrap='physical'>
</textarea></label></p>
<p><input type='submit' value='Punch Me' class='submitBt'></p>
</form>
</div>
<article>
<?php
require_once('../include/lib/footer.inc');
?>
</body>
</html>