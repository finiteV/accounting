<html>

<?php
//以及cookie的作用域,设置新的作用域需将以前设置的cookie手动清除
//index.php,logout.php,functions.php都含有cookie设置
require_once('include/functions.php');
require_once('include/lib/domain.inc');
session_start();
//check
if(isset($_SESSION['valid_user'])){
  $userid=$_SESSION['valid_user'];
  //print_r($_COOKIE);
  //$domain='exbooks.co.cc';
  setcookie('mystur','',time()-1000,'/',$cuki_dm);
//  setcookie('mystur_ip','',time()-1000,'/',$cuki_dm);
  //撤销会话,及会话变量 
  //print_r($_COOKIE);
  unset($_SESSION['valid_user']);
  session_destroy();
} 

  goto_url();
?>
<body>
</body>
</html>