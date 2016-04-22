<!DOCTYPE html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" charset='utf-8'/>
<?php

session_start();
require_once('../include/functions.php');

if(isset($_SESSION['valid_user'])){
  //display map
?>
<link href='../include/css/location1.css' rel='stylesheet' type='text/css'/>
<script type="text/javascript"  src="https://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript" src='../include/js/location.js'> </script>
</head>

<body>
<nav><ul>
<li><a href="../details.php">Main</a></li>
<li><a href="../search.php">Search</a></li>
<li><a href="admin/location.php" id='current'>Location</a></li>
<li><a href="admin/profile.php">Profile</a></li>
<li><a href="../mybook/" target='blank'>MyBook</a></li>
<li><a href="../logout.php">Log Out</a></li>
</ul></nav>
<div id='control'>
<button id='demo' onclick='startwatch()'>Start Watch</button>
<button id='demo' onclick='stopwatch()'>Stop Watch</button>
</div>
<div id="map_canvas" style="width:220px;box-shadow: 10px 10px 5px #888888;">
<p id='map_expr'>Sometimes it is as precise as a GPS while sometime it is not.<br/>当点击start按钮时,出现类似于提示:xx要使用您计算机所在位置,选择允许.当然前提是你的浏览器对HTML 5支持的很好.<br/>注意:我没有采集你的地理位置信息,而是你的浏览器的JavaScirpt功能在采集使用它.</p>
</div>
<?php
}
else{  
  //go login page
  session_destroy();
  goto_url();
  exit;
}
?>

<?php require_once('../include/lib/footer.inc');  ?>
</body>
</html>
