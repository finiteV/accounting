<?php
/*
 * Created on Dec 28, 2013
 */
 /*
  * this class is used to list a dir in some way
  */
 class directory_man{
 	var $dir;
 	
 	function __construct($dir){
 		$this->dir = $dir;
 	}
 	
 	//the first way to echo a dir using table
 	function list_dir(){
 		$dir = $this->dir;
 		if(is_dir($dir)){         //only if it is dir
 			$dir_handle=opendir($dir);
 			//echo '<table>';
                        $i=0;
                        date_default_timezone_set('UTC');// 设定要用的默认时区
 			while(false !== ($file = readdir($dir_handle))){
                                //读取文件信息多维数组
 				if(!is_dir($file) && $file!='.' && $file!='..' && strpos($file,'.php')==false){
                                        $files[$i]["name"] = $file;
                                        $files[$i]["size"] = round(filesize($file)/1024,2);
                                        $files[$i]["time"] = fileatime($file);
                                        $files[$i]["type"] = mime_content_type($file);
                                        $i++;	
 				}
 			}
                        //取出列用来对多维数组进行排序
                        foreach($files as $k=>$v){
                            //$size[$k] = $v['size'];
                            $time[$k] = $v['time'];
                            //$name[$k] = $v['name'];
                            //$type[$k] = $v['type'];
                        }
 			array_multisort($time,SORT_DESC,SORT_NUMERIC, $files);//按时间排序
                        //array_multisort($name,SORT_DESC,SORT_STRING, $files);//按名字排序
                        //array_multisort($size,SORT_DESC,SORT_NUMERIC, $files);//按大小排序
                        //print_r($files);
                        foreach($files as $k=>$v){
                            echo '<tr>'
 					.'<td><a href="'.$v['name'].'">'.$v['name'].'</a></td>'
 					.'<td>'.$v['type'].'</td>'
 					.'<td>'.$v['size'].'kb</td>'
 					.'<td>'.date('j F Y H:i',$v['time']).'</td>'
 					.'</tr>';
                        }
 		}
 	}
 }
?>
<!DOCTYPE html>
<head>
<meta charset="UTF-8">	
<title>docs</title>
<style>
body{
    font-family: Helvetica, Arial, sans-serif;
    background:-webkit-gradient(linear, 0 0, 0 100%, from(＃2074af), to(＃2c91d2));
    background:-moz-linear-gradient(top, ＃FFC3C8,＃FF9298);
    background: filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=＃00ffff,endColorstr=＃9fffff,grandientType=1);
}
table {
  font-family: verdana,arial,sans-serif;
  font-size:11px;
  color:#333333;
  border-width: 1px;
  border-color: #666666;
  border-collapse: collapse;
}
table th {
  border-width: 1px;
  padding: 8px;
  border-style: solid;
  border-color: #666666;
  background-color: #dedede;
}
table td {
  border-width: 1px;
  padding: 8px;
  border-style: solid;
  border-color: #666666;
  background-color: #ffffff;
}
</style>
</head>
<body>
<?php
require_once("../bk/include/lib/config.php");
if(!empty($_POST['pw']) && $_POST['pw']==$pw){
?>	
<table>
	<tr>
	<th>Name</th>
	<th>Type</th>
	<th>Size</th>
	<th>Date</th>
	</tr>
	<?php
	//output the files in the current directory
	$dir_man = new directory_man(__DIR__);
	$dir_man->list_dir();
	?>
</table>
<?php
}
else{
?>	
<form enctype="multipart/form-data" action="index.php" method="post">
	<label>Your verfication password:</label>
	<input type='text' name='pw' >
	<input type="submit" value="submit">
</form>

<?php
}
  require_once("../bk/include/functions.php");
  require_once("../visitors.php");
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
</body>
</html>
