<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
<style type="text/css">
table{
	width: 1024px;
  border-collapse:collapse;
}
tr{
  border-bottom:1px solid  #F8F8FF;
}
th{
  text-align:center;
  background:#32CD32;
  border-bottom:2px solid #1874CD;
}
td{
  padding:3px;
  text-align:left;
}
</style>
<script type="text/javascript" src="bk/include/js/sha1.js"></script>
<script language="javascript">
    function SubmitForm() {
        document.getElementById("pw").value = 
            hex_sha1(document.getElementById("pw").value);
        //document.getElementById("myform").submit();
    }
</script>
</head>

<body>
<?php
require_once("bk/include/lib/config.php");
if(!empty($_POST['pw']) && $_POST['pw']==sha1($pw)){
	require_once("bk/include/functions.php");
    require_once("visitors.php");
    //inite the class
    $db = my_connection();
    $visitor = new visitors($db);
        
	$visitor->get_record();
	$db->close();
}
else{

?>
<form id='myform' action='statistic.php' method='post' enctype='utf8'>
	<label>Please input verfication password:</label>
	<input type='text' id='pw' name='pw' >
	<input type='submit' value='submit' onclick="SubmitForm();">
</form>
<?php	
}
?>
</body>