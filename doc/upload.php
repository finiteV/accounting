<?php
//echo 'error: '.$_FILES['userfile']['error'];
require_once("../bk/include/lib/config.php");
if(!empty($_FILES) && !empty($_POST['pw']) 
	&& $_POST['pw']==$pw){
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
	<title>upload files</title>
</head>

<body>
<form enctype="multipart/form-data" action="upload.php" method="post">
	<label>Choose File:</label>
	<input type="file" name="userfile">
	<label>Your verfication password:</label>
	<input type='text' name='pw' >
	<input type="submit" value="Upload">
</form>
</body>
</html>
