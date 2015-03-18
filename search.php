<!DOCTYPE html>
<head>
<title>Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='include/css/search1.css' rel='stylesheet' type='text/css'/>
</head>

<?php
//You should http://localhost/ with your url for once.
session_start();
require_once('include/functions.php');

if(isset($_SESSION['valid_user'])){
?>
<body>
<nav><ul>
<li><a href="details.php">Main</a></li>
<li><a href="search.php" id='current'>Search</a></li>
<li><a href="admin/location.php" target="blank">Location</a></li>
<li><a href="admin/profile.php">Profile</a></li>
<li><a href="mybook/" target='blank'>MyBook</a></li>
<li><a href="logout.php">Log Out</a></li>
</ul></nav>
<div id='content'>

<div id='content'>
<div id='s_record'> 
<form name='s_record_form' action='search.php' method='POST' enctype='utf-8'> 
<p><label>MinIncome:<br/><input type='number' class='txt' name='minincome' min='0' max='99999' value='50' step='1'></label></p>
<p><label>MaxOutcome:<br/><input type='number' class='txt' name='maxoutcome' min='0' max='99999' value='50' step='1'></label></p>
<p><label>From:<br/><input type='date' name='startdate' class='txt' size='10' maxlength='12'></label></p>
<p><label>To:<br/><input type='date' name='enddate' class='txt' size='10' maxlength='12'></label></p>
<p><input type='submit' name='button' id='button' value='Search'></p>
</form>
<p>日期格式:2012-09-18</p>
</div>

<div id="main">
<?php
  //connect to database to query,first be caution with data to store
  if(!empty($_POST['minincome']) || !empty($_POST['maxoutcome']) || !empty($_POST['startdate']) 
     || !empty($_POST['enddate'])){
    //specified user data
    //$minincome=$_SESSION['valid_user'].'_minincome';
    //$maxoutcome=$_SESSION['valid_user'].'_maxoutcome';
    //$start_date=$_SESSION['valid_user'].'_start_date';
    //$end_date=$_SESSION['valid_user'].'_end_date';
     
    $minincome=check_float($_POST['minincome']);
    $maxoutcome=check_float($_POST['maxoutcome']);
    //check ip
    //$ip=addslashes($_POST['ip']);
    //$sessionid=addslashes($_SESSION['valid_user']);
    //if(!addr_check($sessionid,$ip)){
    //  exit;
    //}
    //if no data given use system date
    date_default_timezone_set('UTC');
    $needle='/^20\d{2}-(01|02|03|04|05|06|07|08|09|10|11|12)-(01|02|03|04|05|06|07|08|09|
      10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31)$/';
    if(!empty($_POST['startdate']) && 
        preg_match($needle,$_POST['startdate'])){
      $start_date=$_POST['startdate'];
      if (!get_magic_quotes_gpc()){
        $start_date=addslashes($start_date);
      }
    }
    else{
      $start_date='';
    }
    if(!empty($_POST['enddate']) && 
        preg_match($needle,$_POST['enddate'])){
      $end_date=$_POST['enddate'];
      if (!get_magic_quotes_gpc()){
        $end_date=addslashes($end_date);
      }      
    }
    else{
      $end_date='';
    }
    //echo $minincome.','.$maxoutcome.','.$start_date.$end_date;
    add_search_fun($minincome,$maxoutcome,$start_date,$end_date,'','select',$_SESSION['valid_user']);

  }
}
else{
  session_destroy();
  goto_url();
  exit;
}
?>

</div>

</div>
<?php require_once('include/lib/footer.inc');  ?>
</body>
</html>
