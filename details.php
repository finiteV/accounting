<!--
file name:details.php
-->
<!DOCTYPE html>
<head>
<title>Your recent details</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='include/css/details1.css' rel='stylesheet' type='text/css'/>
</head>
<?php
/**
*@param income,outcome,date,month,year
*@param array:incomes,outcomes,dates,ect.
*@param from_url,to_url
*/
session_start();
//details.php';   source url where data comes from
//search.php';      url dealing the data
require_once('include/functions.php');

//check whether user has loged in,if not go to login page

if(isset($_SESSION['valid_user'])){
?>
<nav><ul>
<li><a href="details.php" id='current'>Main</a></li>
<li><a href="search.php">Search</a></li>
<li><a href="admin/location.php" target="blank">Location</a></li>
<li><a href="admin/profile.php">Profile</a></li>
<li><a href="mybook/" target='blank'>MyBook</a></li>
<li><a href="logout.php">Log Out</a></li>
</ul></nav>

<div id='content'>
<div id='s_record'> 
<form name='s_record_form' action='search.php' method='POST' enctype='utf-8'> 
<p><label>MinIncome:<br/><input type='number' class='txt' name='minincome' min='0' max='99999' value='50' step='1'></label></p>
<p><label>MaxOutcome:<br/><input type='number' class='txt' name='maxoutcome' min='0' max='99999' value='50' step='1'></label></p>
<p><label>From:<br/><input type='date' name='startdate' class='txt' size='10' maxlength='12'></label></p>
<p><label>To:<br/><input type='date' name='enddate' class='txt' size='10' maxlength='12'></label></p>
<p><input type='submit' name='button' id='button1' value='Search'></p>
</form>
<?php
//display totals
display_total($_SESSION['valid_user']);
?>
</div>

<div id='a_record'>
<form name='as_record_form' action='details.php' method='POST' enctype='utf-8'>
<p><label>Income:<br/><input type='number' name='income' class='txt' min='0' max='99999' value='50' step='1'></label></p>
<p><label>Outcome:<br/><input type='number' name='outcome' class='txt' min='0' max='99999' value='50' step='1'></label></p>
<p><label>Date:<br/><input type='date' name='date' size='10' class='txt' maxlength='20'></label></p>
<p><label>Reasons:<br/><textarea name='reasons' cols='18' placeholder='Your Excuse:(less than 50 character)' id='txtarea' rows='8' warp=virtual></textarea></label></p>
<p><input type='submit' name='submit' id='button2' value='Add'></p>
</form>
<p>日期框格式:2012-09-18,可留空</p>
</div>
<?php
  /***********delete a record********************/
  //use get
  if(!empty($_GET['action']) && $_GET['action']=='delete' && !empty($_GET['recordid']) ){
    $recordid=$_GET['recordid'];
    $sessionid=addslashes($_SESSION['valid_user']);
    if (!get_magic_quotes_gpc()){
      $recordid=addslashes($recordid);     
    }
    $recordid=positive_number($recordid);
    if(!$recordid){
      die('Invalid Information. Please donnot try to mess up my site.');
    }
    delete_record($sessionid,$recordid);
    /***********Second option************/
    //disable echo in above function
    //goto_url($detail_domain);
  }
  
  /************display info**********************/
  //connect to database to query,first be caution with data to store
  if(!empty($_POST['income']) || !empty($_POST['outcome']) || !empty($_POST['date'])){
    $sessionid=addslashes($_SESSION['valid_user']);
    //specified user data 
    $income=check_float($_POST['income']);
    $outcome=check_float($_POST['outcome']);
    $reasons=$_POST['reasons'];
    if(!get_magic_quotes_gpc()){
      $reasons=addslashes($reasons);
    }
    if(strlen($reasons)>50){
      die('Why you need a so so long reason? Please MAKE IT SIMPLE.');
    }    
    //if no data given use system date
    date_default_timezone_set('UTC');
    $date=check_date($_POST['date']);
    //echo $income.'<br/>'.$outcome.'<br/>'.$date.','.$reasons;
    add_search_fun($income,$outcome,$date,'',$reasons,'insert',$sessionid);   
  }    


  //show all record in current year
  echo '<div id="main">';
  date_default_timezone_set('UTC');// 设定要用的默认时区
  $year = date('Y');
//  echo $year.'<br>';

  display($_SESSION['valid_user'],$year);

  echo '</div>';
}
else{//go login page
  session_destroy();
  goto_url();
  exit;
}
?>
</div>

<?php require_once('include/lib/footer.inc');  ?>
</body>
</html>
