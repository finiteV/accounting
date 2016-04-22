<?php
class myShare extends mybook{
  /*****initialization********/
  function __construct(){
    $this->book=null;
  }
  
  /*******Get shared book************/
  function fetch_share($db,$userid=''){
     $i=0;$row=array();
     foreach($this->category as $cat){
       if($userid!=''){
         $query='select book,category from books where shared= true'
          .' and category='."'$cat'".' and userid='."'$userid'".' order by category;';
        }
       else{
         $query='select book,category from books where shared= true'
          .' and category='."'$cat'".' order by category;';       
       }

        $result=$db->query($query);
	if(!$result){
          $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
          $this->logerror($error);
          exit; 
        }
        $num_result=$result->num_rows;
        for($j=0;$j<$num_result;$j++){
          $row[$i][$j]=$result->fetch_assoc();
        }
        
        $result->free();
        if($num_result>0){
          $i++;
        }
     }   
      $this->book=$row;    
  }
    /*******Get private book************/
  function fetch_private($db,$userid=''){
     $i=0;$row=array();
     foreach($this->category as $cat){
       if($userid!=''){
         $query='select book,category from books where shared= false'
          .' and category='."'$cat'".' and userid='."'$userid'".' order by category;';
        }
       else{
         $query='select book,category from books where shared='.$shared
          .' and category='."'$cat'".' order by category;';       
       }

        $result=$db->query($query);

	if(!$result){
          $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
          $this->logerror($error);
          exit; 
        }
        $num_result=$result->num_rows;
        for($j=0;$j<$num_result;$j++){
          $row[$i][$j]=$result->fetch_assoc();
        }
        
        $result->free();
        if($num_result>0){
          $i++;
        }
     }   
      $this->book=$row;    
  }
  /**
  *dealing with books to be shared/private one by one
  */
  function share_or_not($db,$bookname,$shared){
        /******check condition*********/
        $check_query='select shared from books where book='
          ."'$bookname'".' and shared!='.(int)$shared.';';
        //echo $check_query.'<br/>'; //debuge
        $check_result=$db->query($check_query);
        if(!$check_result){
          $error=$check_query.' in '.$_SERVER['SCRIPT_FILENAME'];
          $this->logerror($error);
          exit; 
        }
        /************/
        //echo $check_query;
        /*********/
        $num_result=$check_result->num_rows;
        $row=$check_result->fetch_row();
        if($num_result==0){
         return false;
        }
        /***if book is legal,do query*******/
        $check_result->free();
        $query='update books set shared='.(int)$shared.' where book='."'$bookname'".';';
        /************/
        //echo $query;
        /*********/
        $result=$db->query($query);
        if(!$result){
          $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
          /*******/
          echo $error;
          /*****/    
          //$this->logerror($error);
          exit; 
        }
        return true;
  }
}
?>