<?php
try{
  if(!empty($_GET['msg'])){
   $msg=$_GET['msg'];
   if (!get_magic_quotes_gpc()){
     $msg=addslashes($msg);
   } 
   require_once('../include/functions.php');
   require_once('../include/user_manage_fns.php');
   require_once('../include/lib/domain.inc');
   /*******verify the meesage*****/
   $db=my_connection();
   $info=verify_activite_msg($db,$msg);
   if(!$info){
    throw new Exception('Invalid activation link.');
   }
   /****test****/
   //print_r($info);
   /******test*****/
   /******add user*****/
   //$userid=$info[0];$email=$info[1];$invi_code=$info[2];$pw=$info[3];
   $add_user_error=add_new_user($db,$info);
   if($add_user_error!==true){
    throw new Exception($add_user_error);
   }
   $db->close();
   /*****End********/
   echo 'Congratulations!You have finished the registation.<br/><a href="'.$domain.'">Go to Login Page</a>';
  }
}
catch(Exception $e){
  $message=$e->getMessage();
  echo $message;
  //it has to end every here if error occurs
  exit;
} 
?>