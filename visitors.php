<?php
class visitors{
	var $db;

	function __construct($db){
		if($db!=null)
			$this->db = $db;
		else
			die("Error,not connect to database~");
	}
	//query first and update table
	function add_visitor($ip,$port,$info,$url){
		$sql1 = 'select id,freq from visitors where ip ="'.$ip.'"'
		.' and url = "'.$url.'"';
		$result = $this->db->query($sql1);
		if(!$result){
			echo $sql1;
			return false;
		}
		//the result number
		$num = $result->num_rows;

		if($num > 0){//an old customer
			#echo "-------".$num;
			$row = $result->fetch_row();
			#print_r($row);
			$id = $row[0];
			$freq = $row[1] + 1;
			$sql2 = "update visitors set lvisitt = ".time().",freq = ".$freq.
					",info = '".$info."',port = '".$port."'".
					" where id = ".$id;
			#$sql2 = 'insert into visitors(lvisitt,freq) values('.time().",".$freq.
			#		") where id = ".$id.")";
			#$sql2 = "insert into visitors values(null,'".$ip."',".time().","."1".")";
			$result->free();
			$result = $this->db->query($sql2);
			if(!$result){
				echo $sql2;
			    return false;
			}

		}
		else{//a fresh visitor
			$sql2 = "insert into visitors values(null,'".$ip."','".$port."',"
				.time().","."1".',"'.$info.'","'.$url.'")';
			$result = $this->db->query($sql2);
			if(!$result){
				return false;
			}

		}
		return true;

	}
	
	//get the visitor record
	function get_record(){
		//三个月内记录
		$past3 = time()-2*30*24*60*60;
		//echo date('j F Y H:i',$past3);
		$sql = "select * from visitors where lvisitt>$past3 order by freq desc limit 150";	
		//echo $sql;
		$result = $this->db->query($sql);
        if(!$result){
			echo "Error A";
            return false;
		}

        //the result number
        $num = $result->num_rows;

        if($num > 0){//an old customer
			echo "<table><tr><th>Id</th><th>IP</th><th>Last Visite</th>".
					"<th>Total</th>".
					"<th>Agent</th>"."<th>URL</th></tr>";
			while($row = $result->fetch_row()){
            
            	$id = $row[0];
				$ip = $row[1];
				$ip = $ip.":".$row[2];
				$lvisitt = $row[3];
            	$freq = $row[4];
				$info = $row[5];
				$url = $row[6];
				echo "<tr><td>".$id."</td>".
						"<td>".$ip."</td>".
						"<td>".date('j F Y H:i',$lvisitt)."</td>".
						"<td>".$freq."</td>".
						"<td>$info</td>".
						"<td>$url</td></tr>";
			}
			$result->free();
			echo "</table>";
			return true;
       }
       //default is false  
       return false;
	}
}
?>
