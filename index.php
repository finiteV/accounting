<!DOCTYPE html>
<html>
<head>
<title>site map</title>
<link href='http://fonts.useso.com/css?family=Open+Sans:300,400,600&subset=latin,latin-ext' rel='stylesheet'>
<style>
body{
	background-image:url(bk/include/picture/128-165.jpg);
	background-repeat:repeat;
}
#navi{
	height:350px;
	width:600px;
	margin:auto;
	padding:100px 100px;
}
a{
	color:#000;
	text-decoration:none;
	font-family:'Open Sans';
}
a:hover{
	color:#f00;
	}
#navi ul{
	list-style:none;
	margin:0px;
	padding:0px;
}	
#navi ul li{
  width:200px; 
  float:left; 
  margin:20px 0 0px 20px; 
  display:inline; 
  text-align:center;
  padding:10px;

  text-shadow:5px 5px 5px gray;
  font-weight:bold;
  border:solid 3px blue;
  border-radius:40px 20px;
  -moz-border-radius:40px 20px;
  -o-border-radius:40px 20px;
  -webkit-border-radius:40px 20px;
  background:skyblue;
  word-wrap:break-word;
  box-shadow: 10px 10px 5px #888888;
}
footer{
    margin:0px 160px;
    border-top:2px solid HoneyDew ;
    border-bottom:2px solid HoneyDew;
    font-size:16px
    position:fixed;
    left: 10px;
    bottom: 10px;
    font-family:'Open Sans';
}
</style>
</head>
<body>
<script language='javascript'>
	//var name = navigator.appName;
	//document.writeln(name);
</script>
<div id='navi'>
<ul>
<!--
if no subdomain avaliable
<li><a href="wp/index.php">Goto Blog</a></li>
<li><a href="bk/index.php">Enter Bk</a></li>
-->
<li><a href="http://gamwg.tk">Goto Blog</a></li>
<li><a href="http://twinds.cf/bk">Enter Bk</a></li>
</ul>
</div>
<?php
  require_once("bk/include/functions.php");
  require_once("visitors.php");
  //inite the class
  $db = my_connection();
  $visitor = new visitors($db);
        
  $remote_ip = $_SERVER['REMOTE_ADDR'];
  $remote_port = $_SERVER['REMOTE_PORT'];
  $info =  $_SERVER['HTTP_USER_AGENT']; 
  $url=$_SERVER['PHP_SELF'];
  $r = $visitor->add_visitor($remote_ip,$remote_port,$info,$url);
  if(!$r){
    echo "errors";
  }
  $db->close();
?>
<footer>
<p align="right" style="padding:0; margin:0;">
<a href='http://terrytao.wordpress.com/' target='_blank'>Terry's</a>|
<a href='#' target='_blank'>suggestion</a>|
<a href='http://www.dytt8.net/' target='_blank'>dygod</a>|
<a href='http://www.linuxeden.com/' target='_blank'>linuxeden</a>|
<a href='http://www.google.com' target='_blank'>Google</a>|
<a href='http://www.hostinger.com.hk/' target='_blank'>hostinger</a>|
<a href="http://my.dot.tk/cgi-bin/amb/landing.dottk?nr=762840::13246856::1" target="_blank" style="text-decoration: none">DOT.TK</a>  
</p>
</footer>
</body>
</html>
