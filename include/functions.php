<?php
//此页需要修改how_long中$gap=getdate($stamp_gap)['yday'];
//-->$gap_arr=getdate($stamp_gap);$gap=$gap_arr['yday'];来兼容5.2版本.
//以及goto_url参数的默认值
//以及my_connection()建议将sql连接部分存为php文件使用include
//main function to work
function add_search_fun($post1,$post2,$post3='',$post4='',$post5='',$type,$sessionid){
  $db=my_connection();
  switch($type){
    case 'insert':{
      if(($post1!=0 || $post2!=0) && $post3!=''){  
        //add record to record table
        $query='insert ignore into record (userid,income,outcome,date,reason) 
           values('."'$sessionid'".','.$post1.','.$post2.','.
           "'$post3'".','."'$post5'".');';
        //add record to reason table
        //echo $query;
        $result=$db->query($query);
        if(!$result){
          echo 'An error has occurred. The item was not added';
          exit;
        }      
        $db->close();
      }
      break;
    }
    case 'select':{
      if(($post1!=0)||($post2!=0)||($post3!='')||($post4!='')){
	//当为无效值时,使用通用值替换
	$post1=($post1==0)?0:$post1;
	$post2=($post2==0)?0:$post2;

	if($post3!=''){
	  $post4=($post4=='')?date('Y-m-d'):$post4;
	  //echo $post4;
	  $query='select income,outcome,date,reason from record where income >='.
             $post1.' and outcome >='.$post2.' and userid='."'$sessionid'".
             ' and datediff(record.date,'."'$post3'".')>=0'.
             ' and datediff(record.date,'."'$post4'".')<=0'.';';
	}
	else if($post3=='' && $post4!=''){
	  $query='select income,outcome,date,reason from record where income >='.
             $post1.' and outcome >='.$post2.' and userid='."'$sessionid'".
             ' and datediff(record.date,'."'$post4'".')<=0'.';';
	}
	else{
          $query='select income,outcome,date,reason from record where income >='.
             $post1.' and outcome >='.$post2.' and userid='."'$sessionid'".';';
	}
	//echo $query;
	$result=$db->query($query);
	if(!$result){
          echo 'An error has occurred, failed to query.<br/>';
          echo $query;
          exit; 
        }
        $num_result=$result->num_rows;
        //display results
        echo '<p>Number of records found:'.$num_result.'.</p>';
        echo '<table><tr><th>Date</th><th>Income</th><th>Outcome</th><th>Details</th></tr>';
        for($i=0;$i<$num_result;$i++){
          $row=$result->fetch_assoc();
          echo '<tr>';
          echo '<td>'.stripslashes($row['date']).'</td>';
          echo '<td>'.stripslashes($row['income']).'</td>';
          echo '<td>'.stripslashes($row['outcome']).'</td>';
          echo '<td>'.htmlspecialchars(stripslashes($row['reason'])).'</td>';
          echo '</tr>';
        }
          echo '</table>';
          $result->free();
          $db->close(); 
      }
    }
  } 
} 



function display_total($sessionid){
  $db=my_connection();
  //display total
  $query='select sum(income),sum(outcome) from record where userid='."'$sessionid'".';';
  $result=$db->query($query);
  if(!$result){
      echo 'An error has occurred, failed to query.<br/>';
      exit; 
  }
  $row=$result->fetch_row(); 
  $Incomes=$row[0];
  $Outcomes=$row[1];
  $Remains=$Incomes-$Outcomes;
  echo '<p class="mark_pa">Your total Income, Outcome and Remain are:'.$Incomes.','
       .$Outcomes.','.$Remains.' so far.</p>';
  $result->free();
    /******show the invicode message*******/
  $query_inv='select invcode,time from invitation where userid='."'$sessionid'".';';
  $result_inv=$db->query($query_inv);
  if(!$result_inv){
      echo 'An error has occurred, failed to query.<br/>';
      exit; 
  }
  $row_inv=$result_inv->fetch_row(); 
  if(count($row_inv)>0){
    $invcode=$row_inv[0];
    $time=$row_inv[1];
    echo '<p class="mark_pa">Your Invicode is '.$invcode.', it has been used '.$time
     .' times. You may invite SIX person.</p>';
  }  
  $result_inv->free();
  /*************End*****************/
  $db->close();
}

/**
*display all results,specified by year month
*@param array:income,coutcom
*/
function display($sessionid,$year){
  $db=my_connection();
  //display a year's budget
  echo '<div id="'.$year.'">';
  //display whole year's budget
  $query_year='select sum(income),sum(outcome) from record where userid='."'$sessionid'"
      .' and date regexp '."'^$year\-[0-9]{2}\-[0-9]{2}$'".';';
  
//  echo $query_year.'<br>';
  
  $result_year=$db->query($query_year);
  if(!$result_year){
      echo 'An error has occurred, failed to query.<br/>';
      exit; 
  }
  $row_year=$result_year->fetch_row(); 
  $YearIncome=$row_year[0];
  $YearOutcome=$row_year[1];
  $YearRemain=$YearIncome-$YearOutcome;
  echo '<p class="mark_pa">Year'."'".'s total Income, Outcome and Remain are: '.$YearIncome.','
       .$YearOutcome.','.$YearRemain.' by now.</p>';
  $result_year->free();
  
  //display six month result  
  date_default_timezone_set('UTC');
  $currentMonth=getdate();
  $currentMonth=$currentMonth['mon'];
  if($currentMonth>6){
    $beginMonth=$currentMonth-6;
  }
  else{
    $beginMonth=1;
  }
  for($month=$currentMonth;$month>=$beginMonth;$month--){

    //query1 returns all incom outcome date and reason records at specified year
    //month
    $query1='select date,income,outcome,reason,recordid from record where userid='.
        "'$sessionid'".' and date regexp '."'^$year\-(0){0,1}$month-[0-9]{2}'".' order by date desc;';
    //query total income,outcome and average outcome
    $query2='select sum(income),sum(outcome) from record where userid='."'$sessionid'"
      .' and date regexp '."'^$year\-(0){0,1}$month-[0-9]{2}'".';';
    $result=$db->query($query1);
    $result2=$db->query($query2);
//    echo $query1.'<br/>'.$query2.'<br/>';
    if(!$result || !$result2){
      echo 'An error has occurred, failed to query.<br/>';
      echo $query1.'<br/>'.$query2;
      exit; 
    }

    $num_result=$result->num_rows;
    $row2=$result2->fetch_row();  
    //display results
    //echo $num_result.'<br/>';
    if($num_result>0){
      //month div tag
      echo '<div id="'.$year.'-'.$month.'">';
      echo '<label onclick="hidde('.$year.','.$month.')"><strong>'.$year.'-'.$month.
          '</strong></label>';
      echo '<p>Number of records found:'.$num_result.'.</p>';
      echo '<table><thead><tr><th>Date</th><th>Income</th><th>Outcome</th><th>Details</th><th></th></thead><tbody>';
      for($i=0;$i<$num_result;$i++){
        $row=$result->fetch_assoc();
        echo '<tr>';
        echo '<td>'.stripslashes($row['date']).'</td>';
        echo '<td>'.stripslashes($row['income']).'</td>';
        echo '<td>'.stripslashes($row['outcome']).'</td>';
        echo '<td>'.htmlspecialchars(stripslashes($row['reason'])).'</td>';
        echo '<td><a href="?action=delete&recordid='.$row['recordid'].'">del</a></td>';
        echo '</tr>';
        }
       $remain=$row2[0]-$row2[1];
       $avg_cospt=$row2[1]/how_long($year,$month);
       $avg_cospt=round($avg_cospt,3);
       echo '</tbody><tfoot><tr><td>Remain:'.$remain.'</td>';
       echo '<td>Total-In:'.$row2[0].'</td>';
       echo '<td>Total-Out:'.$row2[1].'</td>';
       echo '<td>Avg-Usage:'.$avg_cospt.'</td>';
       echo '<td></td></tr></tfoot></table><br/>';
       //month
       echo '</div><br/>';  
    }
    //free resource
    $result->free();
    $result2->free();
  }
  //year
  echo '</div>';
  $db->close(); 
}

/**
*@param year,month
*@return day_gap
*/
function how_long($year,$month){
  date_default_timezone_set('UTC');
  $year=intval($year);
  $month=intval($month);
  $past=mktime(0,0,0,$month,0,$year);
  //time() equals mktime()
  $now=mktime();
  $stamp_gap=$now-$past;
  $gap_arr=getdate($stamp_gap);
  $gap=$gap_arr['yday'];
  switch($month){
    case 1:{
      $gap=($gap>31)?31:$gap;
      break;
    }
    case 2:{
      $febuary=($year%4==0)?29:28;
      $gap=($gap>$febuary)?$febuary:$gap;
      break;
    }
    case 3:{
      $gap=($gap>31)?31:$gap;
      break;
    }
    case 4:{
      $gap=($gap>30)?30:$gap;
      break;
    }
    case 5:{
      $gap=($gap>31)?31:$gap;
      break;
    }
    case 6:{
      $gap=($gap>30)?30:$gap;
      break;
    }
    case 7:{
      $gap=($gap>31)?31:$gap;
      break;
    }
    case 8:{
      $gap=($gap>31)?31:$gap;
      break;
    }
    case 9:{
      $gap=($gap>30)?30:$gap;
      break;
    }
    case 10:{
      $gap=($gap>31)?31:$gap;
      break;
    }
    case 11:{
      $gap=($gap>30)?30:$gap;
      break;
    }
    case 12:{
      $gap=($gap>31)?31:$gap;
      break;
    }
  }
  return $gap;
}
//@param to_url ,replace default to your url,此页面只有一处
function goto_url($to_url='../index.php',$message=''){
   if($to_url == '../index.php'){
        $to_url = "index.php";
        echo '<script language="javascript">'
       ."window.location.replace('$to_url?message=$message')".'</script>';
   }
   else{
       echo '<script language="javascript">'
       ."window.location.replace('$to_url?message=$message')".'</script>';
   }
   
   //make sure nothing happen next
   die("Oops,Your brower's JavaScripte seems not working.");       
}

/**
*检查正在使用cookie的用户的地址和上次是否一致,主要通过javascript实现
*不一致,删除cookie,进入log in page.注意要放在session开始前,进行检查
*使用javascript获得ip
*/
function addr_check($userid,$ip){
  if(!empty($userid) && !empty($ip)){
    $now_addr=ip_look_up($ip);
    //result fetch from mysql contains no slashes
    $now_addr=stripslashes($now_addr);
    $db=my_connection();
    $query='select ipaddr from ip where userid='."'$userid'".';';
    $result=$db->query($query);
    if(!$result){
      die('Sorry,an error occured.Failed to query.');
    }
    //因该只有一行一列,即ipaddr记录上次登录地址
    $row=$result->fetch_row();
  
    //判断开始
    //echo $now_addr.'<br/>'.$row[0];    
    if($now_addr!=$row[0]){
      //清楚cookie,连接到登录页面
      $cuki_dm='bookeeper.cz.cc';
      setcookie("mystur","",time()-1000,"/",$cuki_dm);
      setcookie("mystur_ip","",time()-1000,"/",$cuki_dm);      
      return false;
    }
    return true;
    $db->close();
  }
  else{
    return false;
  }
}

function ip_look_up($ip){
  //you may want to format ip in the first place
  $look_up_url='https://ipdb.at/ip/'.$ip;
  /*****get ip detail*******/
  $data=get_data($look_up_url);
  if(strlen($data)==0){
    die("Failed to connect to https://ipdb.at");
  } 
  /********disable it*************/ 
  //$start_key='<ul id="ip-info">';
  //$start=strpos($data,$start_key)+17;
  //$end=strpos($data,'</ul>',$start);
  //$ip_addr=substr($data,$start,$end-$start);
  //get details
  //$ip_addr=explode('</li>',$ip_addr);
  //$ip_addr=str_replace('<li>','',$ip_addr[0].$ip_addr[1].$ip_addr[2].$ip_addr[5]);  
  //format details for storage
  //$ip_addr=str_replace(' ','',$ip_addr);
  //$ip_addr=str_replace("\r\n",'',$ip_addr);
  $ip_addr = 'None';
  /************disable it****************/
  return addslashes($ip_addr);
}
//get source code of given url
function get_data($url) 
{   
  $ch = curl_init();
  $timeout = 5; 
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  //$data = curl_exec($ch);
  $data = 'diable ip check';
  curl_close($ch);
  return $data;
}
//check float type
function check_float($value){
  if(!empty($value) && preg_match('/^[0-9]+(.[0-9]+)?$/',$value)){
    return floatval($value);
  }
  else{
    return 0;
  }
}
//check date type
function check_date($date){
  $needle='/^20\d{2}-(01|02|03|04|05|06|07|08|09|10|11|12)-(01|02|03|04|05|06|07|08|09|
  10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31)$/';
  if(!empty($date) && preg_match($needle,$date)){
    return $date; 
  }
  else{
        return date('Y-m-d');
  }
}

function valid_email($email){
  $needle='/^[a-zA-Z0-9\._\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]+$/';
  if(!empty($email) && preg_match($needle,$email)){
    return $email; 
  }
  else{
        return false;
  }
}

function positive_number($value){
  if(!empty($value) && preg_match('/^[0-9]+$/',$value)){
    if(intval($value)>0){
      return intval($value);
    }
    else{
      return false;
    }
  }
  else{
    return false;
  }
}
/**
*code repeat many time to connect to mysql
*donnont forget to disconnect after your query
*addslash to variables
*$db=my_connection("localhost","budgetor","pw","budget")
*/
function my_connection(){
  require('lib/database.php');
  
  @$db=new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_database);
  //set connection charset
  if(!$db->set_charset("utf8")){
       die('failed to set connection characterset');
  }
  
  if(mysqli_connect_error()){
    echo 'Cannot connect to database.';
    exit;
  }
  else{
    return $db;
  }
}

function delete_record($userid,$recordid){
  $db=my_connection();
  $query='delete from record where userid='."'$userid'".' and recordid='.$recordid.';';
  $result=$db->query($query);
  if(!$result){
     echo $query;
     die('Invalid query. Possibly you are doing something bad');
  }
  $db->close();
  echo 'Record deleted!';
}
?>
