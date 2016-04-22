<?php
function generate_pw($digite){
    $pw='';  
    //生产验证码字符  
    $ychar="0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,!,@,#,\$,&,%,*,(,),[,],;";
    $list=explode(",",$ychar);  
    for($i=0;$i<$digite;$i++){  
      $randchar=rand(0,count($list));  
      $pw.=$list[$randchar];  
     }  
    return $pw;
}

function send_new_pw($email,$random_pw){
  $message='You requested to reset your password,below is your new one'."\n".'<cut>'.$random_pw
     .'</cut>'.".\n".'Best Wishes!'."\n".
     'Please do not replay this email, since it is generated automaticly.';
  $subject = "Reset password";
  //echo $email.'<br/>'.$subject.'<br/>'.$message;
  mail($email,$subject,$message);
}

function check_usrerid($userid){
  $needle='/^[a-zA-Z0-9]{2,40}$/';
  if(!empty($userid) && preg_match($needle,$userid)){
    return $userid; 
  }
  else{    
    return false;
  }
}

/**
*check whether info has already exits in database
*return true if all ok,otherwise return error message
*you can change the invitation time
*/
function check_info($db,$userid,$email,$invi_code){
  //echo $userid.','.$email.','.$invi_code;
  $query[0]='select userid from users where userid='."'$userid'".';';
  $query[1]='select email_addr from email where email_addr='."'$email'".';';
  $query[2]='select time from invitation where invcode='."'$invi_code'".';';
  /****Exceptions*****/
  //user name
  $result[0]=$db->query($query[0]);
  if(!$result[0]){
     return "Sorry, Cannot run query.";
   }
  $count[0]=$result[0]->num_rows;
  if($count[0]!=0){
    return 'User Name exists.';
  }
  $result[0]->free();
  
  //email
  $result[1]=$db->query($query[1]);
  if(!$result[1]){
     return "Sorry, Cannot run query.";
   }
  $count[1]=$result[1]->num_rows;
  if($count[1]!=0){
    return 'Email address exists.';
  }
  $result[1]->free();
  
  //invi_code,delecte it according to times 
  $result[2]=$db->query($query[2]);
  if(!$result[2]){
     return "Sorry, Cannot run query.";
   }
  $count[2]=$result[2]->num_rows;
  //below you can change the invitation time
  if($count[2]==0 || $count[2]['time']>10){
    //delecte user invitation info
    if($count[2]['time']>6){
      $query[3]='delect from invitation where invcode='."'$invi_code'".';';
      $result[3]=$db->query($query[3]);
      $result[3]->free();
    }
    return 'Invalid invitation code.';
  }
  $result[2]->free();
  /********End*************/
  //all is ok, return true
  return true;
}


//send the registe info to user
function send_activite_info($db,$userid,$email,$invi_code,$password){
   require_once('lib/domain.inc');
   $info=$userid.'#'.$email.'#'.$invi_code.'#'.sha1($password);
   $message=$activate_domin.'?msg='.sha1($info);
   /********not allow duplication***********/
   $query[0]='select info from activation where info like "'.$userid.'#'.$email.'#%" or info like "'.$userid.'#%" or info like "%'.$email.'#%";';
   $result[0]=$db->query($query[0]);
   if(!$result[0]){
    return 'An error has occurred. Failed to query.';
   } 
   $num_results=$result[0]->num_rows;
   $result[0]->free();
   
   if($num_results>0){
    return 'User name or email adrress has been registed.';
   }
   /**************end*************/
   $msg=sha1($info);
   $query[1]='insert into activation value('."'$msg'".','."'$info'".','.'curdate()'.');';
   /***debuge***/
   //echo $query[1].'<br/>';
   /*******/
   $result[1]=$db->query($query[1]);
   if(!$result[1]){
    return 'An error has occurred. Failed to query.';
   }
   //echo $message;
   //send it to user via email
   $subject = "Activation Message from web site";
   $mailcontent = "Thanks for registe this site! To finish the final process, Please go to the link below."."\n"
    .$message."\n".
    "Note: This link will become valid in two day.\n"
    ."Please do not reply this email.";
    $fromaddress = "From: webserver@".$domain."\r\n"."Reply-To:".$email;
   mail($email, $subject, $mailcontent, $fromaddress);
   //echo $email.'<br/>'.$subject.'<br/>'.$mailcontent.'<br/>'.$fromaddress;
   return true;
}

//verify register info
//return username,password,email,inivitation code
function verify_activite_msg($db,$msg){
  //delete offdate invitation
  $query[0]='delete from activation where datediff(curdate(),activation.date)>2'.';';
  $result[0]=$db->query($query[0]);
  
  /****do the activation*****/
  $query[1]='select info from activation where message='."'$msg'".';';

  $result[1]=$db->query($query[1]);
  if(!$result[1]){
     die('An error has occurred. Failed to query.');
  } 
  $num_results[1]=$result[1]->num_rows;
  if($num_results[1]==1){
    $row=$result[1]->fetch_row();
    $info_array=explode('#',$row[0]);
    //all done,delete this activation info
    $query[3]='delete from activation where message='."'$msg'".';';
    $result[3]=$db->query($query[3]);
    return $info_array;
  }
  else{
   return false;
  }
  //free the connection need to be free if in the last
  $result[1]->free();  
}

/**
*to add a new use to database
*$info is a array contains user info,if sucessfully add a new user
*the invite person's invite time should +1
*$userid=$info[0];$email=$info[1];$invi_code=$info[2];$pw=$info[3];
*return true or error message
*change state ment in for loop to disenable common invitation privillege
*/
function add_new_user($db,$info){
  //create item in table users,email,invitation,questions,ip
  $query[0]='insert ignore into users (userid,pwd) value('."'$info[0]'".','."'$info[3]'".');';
  $query[1]='insert ignore into email (userid) value('."'$info[0]'".');';
  $query[2]='insert ignore into ip (userid) value('."'$info[0]'".');';
  $query[3]='insert ignore into questions (userid) value('."'$info[0]'".');';
  $query[4]='insert ignore into invitation (invcode,userid,time) value('."md5('$info[0]')".','."'$info[0]'".','.'0);';
  /**********Add info****************/
  //below change state ment in for loop to disenable common invitation privillege
  for($i=0;$i<count($query)-1;$i++){

    $result[$i]=$db->query($query[$i]);
    if(!$result[$i]){
      return 'Failed to add user infomation.';
    }
  }
  //modify the the invitation table
  $query[5]='update invitation set time=time+1 where invcode='."'$info[2]'".';';

  $result[5]=$db->query($query[5]);
  if(!$result[5]){
    return 'Faild to modify the invitation code.';
  }
  /************End********************/
  //all is done,return ok
  return true;
}
?>