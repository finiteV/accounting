<?php
//echo 'error: '.$_FILES['userfile']['error'];
require_once("../bk/include/lib/config.php");
if(!empty($_FILES) && !empty($_POST['pw']) 
	&& $_POST['pw']==sha1($pw)){
	$upfile = __DIR__.'/'.basename($_FILES['userfile']['name']);
    //echo $upfile;
	if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
		if(!move_uploaded_file($_FILES['userfile']['tmp_name'],$upfile)){
			echo 'error: '.$_FILES['userfile']['error'];
			die('Could not move file to destination directory');
		}
	}
	else{
		$msg = 'Possible file upload attact '.$_FILES['userfile']['name'];
		die($msg);
	}
	echo $_FILES['userfile']['name'].' Upload Successful!';
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">	
	<title>upload files</title>
<script type="text/javascript" src="../bk/include/js/sha1.js"></script>
<script language="javascript">
    function SubmitForm() {
        document.getElementById("pw").value = 
            hex_sha1(document.getElementById("pw").value);
    }
</script>	
</head>

<body>
<form enctype="multipart/form-data" action="upload.php" method="post">
	<label>Choose File:</label>
	<input type="file" name="userfile">
	<label>Your verfication password:</label>
	<input type='text' id='pw' name='pw' >
	<input type='submit' value='Upload' onclick="SubmitForm();">
</form>
</body>
</html>
