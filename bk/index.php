<!DOCTYPE html>
<head>
<meta http-equiv='content-type' content='text/html;charset=utf-8'/>
<title>INDEX</title>

<link href='include/css/login1.css' rel='stylesheet' type='text/css'/>

<?php
require_once('include/functions.php'); 
require_once('include/lib/config.php'); 
require_once('include/lib/domain.inc');
//成功登录后前往页面
//此页面只有一处以及cookie变量的名字bookeeper.cz.cc->bookeeper.cz.cc
//和cookie的作用域
$url='details.php';
//加密因子
//设置cookie登录,cookie加密解密
//print_r($_COOKIE);
try{
  /***********Check cookie**********************/
if(isset($_COOKIE['mystur'])){
   //print_r($_COOKIE);
  $userid=$_COOKIE['mystur'];
//  $ip=$_COOKIE['mystur_ip'];
  if (!get_magic_quotes_gpc()){
    $userid=addslashes($userid);
    $ip=$_SERVER['REMOTE_ADDR'];
  }
   //判断cookie所在地址是否和上次一致
   if(addr_check($userid,$ip)){
     //echo $userid.'<br/>'.$ip;
     session_start();
     $_SESSION['valid_user']=$userid;
     goto_url($url);
   }
   else{
    echo 'Your account logged in from somewhere else last time, Please login again for safety.';
   }
}
else{
  if(!empty($_POST['userid']) && !empty($_POST['pwd'])
    && !empty($_POST['random_code']))
  {
  /*start varification process code*/
   //start a session
   session_start();  
   if(!isset($_SESSION['random_code']) || 
       strtoupper($_POST['random_code'])!=$_SESSION['random_code']){
       throw new Exception("The symbols entered is incorrect.");
    }    
    //specified user data  
    $userid=$_POST['userid'];     
    $ip=$_POST['ip'];
    $pwd=$_POST['pwd'];
    if (!get_magic_quotes_gpc()){
      $userid=addslashes($userid);     
      $ip=$_SERVER['REMOTE_ADDR'];
      $pwd=addslashes($pwd);
    }
    $ipAddr=ip_look_up($ip);
    //echo $ip.'<br/>'.$ipAddr;
    
    $pwd=sha1($pwd+$token);
    //connect to mysql,do not forget to disconnect after query
    $db=my_connection();
    //query desired from database
    $query='select * from users where
        userid='."'$userid'".' and 
        pwd='."'$pwd'".';';
    
    $result=$db->query($query);
    if(!$result){
      die("Cannot run query.");
    }
    
    //get the result number,count equels 1 means it is all right
    $count=$result->num_rows;
    switch($count){
        case 0:{
	  throw new exception('Wrong user name or password.');
        }
        case 1:{
          //add user ip address
          $ip_query='update ip set ipaddr = '."'$ipAddr'"
	      .' where userid='."'$userid'".';';
          $ip_result=$db->query($ip_query);
          //if($ip_result){
            //exit;
          //}

          /*当登录成功时,取消验证变量*/
          unset($_SESSION['random_code']);
          $_SESSION['valid_user']=$userid;
          //setcookie,better encypte cookie value
          $cookie_value=$userid;
          /*************set cookie to site**********************/
          setcookie('mystur',$cookie_value,time()+60*60*24*30,'/',$cuki_dm,false,true);
          //cookie contains user ip address
//          setcookie('mystur_ip',$ip,time()+60*60*24*30,'/',$cuki_dm,false,true);

          //go to details page
          //echo $url;
          goto_url($url);//exit in this function
          //exit;
        }
    }
    $result->free();
    $db->close();
  }
}

}
catch(Exception $e){
  $message=$e->getMessage();
  goto_url($domain,$message);
  //it has to end every here if error occurs
  exit;
}
?>
</head>

<body>
<p id='Msg'></p>
<?php
//display message
if(!empty($_GET['message'])){
  echo htmlspecialchars($_GET['message']).'<br>';
}
?>
<div id='login'>
<form name='login_form' action='index.php' method='POST' enctype='utf-8'>
<p style='font-size:10px;'><a href='registe.php' target='blank'>Not a member?</a></p>
<p><label>User:</td><td><input type='text' class='txt' name='userid' maxlength='10'></label></p>
<p><label>Password:</td><td><input type='password' class='txt' name='pwd' value=''></label></p>
<p><input type='hidden' name='ip' id='iphidden'></p>
<p><img src="include/verify.php" id='refresh' onclick="document.getElementById('refresh').src='include/verify.php?t='+Math.random()"/></td>
<input type="text" class='txt' name="random_code" size="5" placeholder='请输入五位验证码' autocomplete="off"></p>
<p></td><td><input type='submit' name='submit' id='bt' value='Go'></p>
<p style='font-size:10px;'><a href='admin/forget_pw.php' target='blank'>Forget Your Password?</a></p>
</div>
</form>
<footer style='width:300px;margin-right:400px;border-top:2px solid LightGray;font-family:Sans-serif;font-size:10px'>
<p>©2012 Zhang Chufan etc. &ensp;
<a href='help.html' target='blank'>help</a>|
<a href='admin/suggestion.php' target='blank'>suggestion</a>|
<a href='#'>about</a>
</p>
</footer>
</body>
</html>
