<?php
/*class useed by mybook prg
change the myConnect details*/ 
class mybook{
  var $userid;
  var $book;
  var $category=array('文学','语言','IT资料','专业资料','办公文书','自然科学','综合性图书');
  
  function __construct($user){
    $this->userid=$user;
    $this->book=null;
  }
  
  //connect database
  function my_connect(){
    require('lib/database.php');//do not use require_once
    @$db=new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_database);
    //set connection charset
    if(!$db->set_charset("utf8")){
       die('failed to set connection characterset');
    }
    if(mysqli_connect_error()){
      die('Cannot connect to database.');
    }
    else{
      return $db;
    }
  }
  //disconnect from databse
  function my_disconnect($db){
    $db->close();
  }
  
  /*fetch books of a user
  set books
  */
  function fetch_books($db){
     $i=0;$row=array();
     foreach($this->category as $cat){
        $query='select book,category from books where userid='."'$this->userid'"
        .' and category='."'$cat'".' order by category;';
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
   * change string encoding to uft-8,this is designed specially for different broweer default encode.
   * since different brower send GET/POST parameter to server using its default encode if not using form encoding
   * property, it must convert if encoding is not given
   * to UTF-8 to make database query successfull.
   * return false on failure
   * @param type $str
   */
  function chang_default_encode($str){
      //support encodings
      $ary[] = 'UTF-8';
      $ary[] = "gb2312";
      $ary[] = "EUC-CN";
      //detect the default encoding
      $encode = mb_detect_encoding($str,$ary);
      //echo $encode.'<br>';
      $str = iconv($encode,"utf-8",$str);
      if(!$str)
        return false;
    else {
        return $str;
    }
  }
  
  /**
  *fetch user section
  *return sections of the particular book or false if there's none
  */
  function fetch_section($db,$onebook){
     $query='select section from books,sections where books.book=sections.book'.' and books.book='."'$onebook'".' order by sectionid;';
     $result=$db->query($query);
	if(!$result){
          $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
          $this->logerror($error);
          exit;
        }
        /*******debuge********/
        //echo $query.'<br/>';
        //echo $onebook;
        /***********/
        $num_result=$result->num_rows;
        for($i=0;$i<$num_result;$i++){
          $section[$i]=$result->fetch_assoc();
        }
        $result->free();  
        if(isset($section)){
          return $section;
        }
        else{
	  return false;
        }
  }
  
  /**
  *fetch chapter names and chapterid or false if none found
  */
  function fetch_chapter($db,$section_name,$onebook){
    //$size=count($section);
     $query='select chapters.chapterid,chapters.section, chapters.chaptername from books,chapters,sections where books.book=sections.book and books.book=chapters.book and sections.section=chapters.section'
     .' and sections.section='."'$section_name'".' and books.book='."'$onebook'".' order by chapters.chapterid;';
     $result=$db->query($query);
	if(!$result){
          $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
          $this->logerror($error);
          exit;
        }
        $num_result=$result->num_rows;
        for($i=0;$i<$num_result;$i++){
          $chapter[$i]=$result->fetch_assoc();
        }
        $result->free(); 
        /***********/
        //echo $query;
        //echo $section_name;
        /************/
        if(empty($chapter)){
          return false;
        }
        return $chapter;    
  }
  
  /**
  *fetch chapter value
  */
  function show_chapter($db,$chapter_id){
     //$size=count($section);
     //$query='select chapters.chapterid,chapters.chapter from chapters,sections,books where books.book=sections.book and sections.section=chapters.section and'
     //  .'books.book='.$onebook.' and sections.section='.$section_name.' and chapters.chaptername='.$chapter_name.' group by chapters.chapterid;';
     $query='select chapter from chapters where chapterid='.$chapter_id.';';
     $result=$db->query($query);
	if(!$result){
          $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
          $this->logerror($error);
          exit;
        }
        $chapter=$result->fetch_row();
        $result->free();  
        return $chapter[0];    
  }
  
  /**
  *fetch the next/previous chapter's chapterid,section name,book name
  *return chapter array contains chapterid, onebook,section,chaptername
  */
  function navi_chapter($db,$chapterid,$section_name,$onebook,$type){
     switch($type){
      case 'next':
        $query='select chapterid,chaptername,section,book from chapters where '
         .'book='."'$onebook'".' and section='."'$section_name'".' and chapterid >'.$chapterid.' order by chapters.chapterid asc limit 1;';
         break;
     case 'previous':
       $query='select chapterid,chaptername,section,book from chapters where '
       .'book='."'$onebook'".' and section='."'$section_name'".' and chapterid <'.$chapterid.' order by chapters.chapterid desc limit 1;';
     }
     
     $result=$db->query($query);
	if(!$result){
          $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
          $this->logerror($error);
          exit; 
        }
        $chapter=$result->fetch_row();
        $result->free(); 
        /******debuge**********/
        //echo $type.$query.'<br/>';
        /****************/
        if(empty($chapter)){
	  return false;
        }
        return $chapter;      
  }
  
  /**
  *add record
  */
  function add_new($db,$chapter_name,$chapter,$section_name,$book_name,$category,$userid){
    /*******Handle dupicate chapter******/
    $dup_query[0]='select sectionid from sections where section='."'$section_name'"
      .' and book='."'$book_name'".';';
    $dup_query[1]='select chapterid from chapters where chaptername='."'$chapter_name'"
      .' and book='."'$book_name'".';';
      /***********fatal one************/
      $dup_result[1]=$db->query($dup_query[1]);
      if(!$dup_result[1]){
        die('An error has occurred, failed to query.');
        echo $dup_query[1];
      }
      $num_result[1]=$dup_result[1]->num_rows;
      $dup_result[1]->free();
      if($num_result[1]>0){
        return false;
      }
      /***********caution one***********/
      $dup_result[0]=$db->query($dup_query[0]);
      if(!$dup_result[0]){
        die('An error has occurred, failed to query.');
      }
      $num_result[0]=$dup_result[0]->num_rows;
      $dup_result[0]->free();
      //querys
      $query[0]='insert IGNORE into books (userid,book,category) values('."'$userid'".','."'$book_name'".','."'$category'".');';
      $query[1]='insert IGNORE into chapters (chapter,chaptername,section,book) values('."'$chapter'".','."'$chapter_name'".','
         ."'$section_name'".','."'$book_name'".');';
      if($num_result[0]==0){
        $query[2]='insert IGNORE into sections (section,book) values('."'$section_name'".','."'$book_name'".');';
      }
   
    /**********Add record******************/
    //$query[0]='insert IGNORE into books (userid,book,category) values('."'$userid'".','."'$book_name'".','."'$category'".');';
    //$query[1]='insert IGNORE into chapters (chapter,chaptername,section) values('."'$chapter'".','."'$chapter_name'".','."'$section_name'".');';
    $query_num=count($query);
    for($i=0;$i<$query_num;$i++){
      $result[$i]=$db->query($query[$i]);
      /******debuge************/
      //echo $query[$i].'<br/>';
      /******************/
      if(!$result[$i]){
          die('An error has occurred, failed to query.<br/>');
        }  
    }
    return true;   
  }

  /**
  *distiguish whether certain book shared
  *return true or false
  */
  function is_editable($sessionid,$onebook){
    $book_arr=explode('@',$onebook);
    $author=$book_arr[0];
    if(empty($author) || $sessionid!=$author){
      return false;
    }
    //all ok
    return true;
  }
  /**
  *edit special chapter,return false if no such chapter or chapter
  *with such contents
  */
  function edit_chapter($db,$chapterid,$chapter){
    /****check correction of h=chapterid and chapter***/
    $check_query='select chapterid from chapters where chapterid='
      .$chapterid.';';
    $check_result=$db->query($check_query);
    if(!$check_result){
        $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
        $this->logerror($error);
        exit; 
    }
    $num_result=$check_result->num_rows;
    /******************/
    //echo $check_query;
    /****************/
    if($num_result==0){
      return false;
    }
    $check_result->free();
    /********add result*********/ 
    $query='update chapters set chapter='."'$chapter'".
      ' where chapterid='.$chapterid.';';
    $result=$db->query($query);
    if(!$result){
        $error=$query.' in '.$_SERVER['SCRIPT_FILENAME'];
        $this->logerror($error);
        exit; 
    }
    return true;
  }

  /**
  *log errors to file,to replace die() message
  */
  function logerror($error){
    date_default_timezone_set('UTC');  //do not forget this
    $error=date('"F j, Y, g:i a"').':'.$error."\r\n";
    file_put_contents('../sitelog',$error);//FILE_APPEND if needed
    echo 'A Error occurs! It has been documented and will be fixed soon.<br/>';
  }
}
?>
