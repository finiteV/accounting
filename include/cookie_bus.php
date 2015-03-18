<?php
if(isset($_COOKIE['mystur'])){
   //print_r($_COOKIE);
  $userid=$_COOKIE['mystur'];
  $ip=$_SERVER['REMOTE_ADDR'];
  if (!get_magic_quotes_gpc()){
    $userid=addslashes($userid);
    //$ip=addslashes($ip);
  }
   //判断cookie所在地址是否和上次一致
   if(addr_check($userid,$ip)){
     //echo $userid.'<br/>'.$ip;
     $_SESSION['valid_user']=$userid;
   }
   else{
    session_destroy();
    require_once('lib/domain.inc');
    header('location:http://'.$$cuki_dm);
   }
}
?>