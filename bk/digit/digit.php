<html>
<head>
<title>Search test</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
<center>
<form method="get" action="index.php" enctype="UTF-8">
<input type="text" name="keywords" size="30">
<input type="hidden" name="pn" value="0"/>
<input type="submit" value="Search"><br/>
<input type="radio" name="engine" value="google" />Google
<input type="radio" name="engine" value="baidu"/>Baidu
<input type="radio" name="engine" value="php"/>php
<input type="radio" name="engine" value="mysql"/>mysql
<input type="radio" name="engine" value="java"/>Java
<input type="radio" name="engine" value="html"/>html
</form>
</center>
</div>


<?php
$keywords=$_GET['keywords'];
$pn=$_GET['pn'];
$engine=$_GET['engine'];
if(empty($keywords))
	die('Sorry,no keywords.');
//添加$keyword中加号
$keywords=str_replace(' ','+',$keywords);
//支持特殊字符
$keywords=urlencode($keywords);
/**
 *切换搜索引擎
 */
if(($engine=='google')||(empty($engine))||($engine=='html')||($engine=='php')){

header("Content-type:text/html;charset=big5");//加个头
require('google.sty');
//temp variables
//$keywords='google pig';
//$pn=20;
if(($engine=='html')){
  $keywords=$keywords.'+site:www.w3schools.com';
}
//远程有效
if(($engine=='php')){
  $keywords=$keywords.'+site:www.php.net';
}

$url='http://www.google.com/search?q='.$keywords.'&start='.$pn;
//echo $url;

//$contents=file_get_contents($url);
//try fopen() with google
//$fp=fopen($url,'r');
//while(!feof($fp)){
//$contents=$contents.fgets($fp,999);}

//try with curl
$contents=get_data($url);

$start=strpos($contents,'<div id="ires">');
//echo $start.'start<br/>';
$end=strpos($contents,'<div id="foot"');
//echo $end.'end';

$len=$end-$start;
//echo $len.'lenght<br/>';
$inf=substr($contents,$start,$len);
//过滤掉google伪链接,替换搜索链接,尾部链接
$inf=str_replace('/url?q=','',$inf);
$inf=str_replace('search?q=','index.php?engine=google&keywords=',$inf);
//过滤google js带来的无用后缀
//$pattern='&sa=([a-z0-9_\-]){1}&ei=([a-z0-9_\-]){22}&ved=([a-z0-9_\-]){9,13}&usg=([a-z0-9_\-]){34}';
$pattern2='&amp;sa=([a-z0-9_\-]){1}&amp;ei=([a-z0-9_\-]){18,22}&amp;ved=([a-z0-9_\-]){9,13}(&amp;usg=([a-z0-9_\-]){34}){0,1}';
//test regular expression below
$bottom='http://www.google.com/search?';
$inf=eregi_replace($pattern2,' ',$inf);
$inf=str_replace($bottom,'http://inneraspire.co.cc/digit/index.php?',$inf);
$inf=preg_replace('/^(&amp;q)|(&q)$/','&ampkeywords',$inf);

//$write='test writing ability';
//file_put_contents('/home/mageia/search_test',$inf);
//替换类似内容链接无法打开链接
$inf=str_replace('/search?','http://www.google.com/search?',$inf);
//替换google对链接的转换
$inf=urldecode($inf);
echo $inf;
$guid='<p id="page"><a href="?keywords='.$keywords.'&amp;pn=0&engine=google">1</a>
        <a href="?keywords='.$keywords.'&amp;pn=10&engine=google">2</a>
        <a href="?keywords='.$keywords.'&amp;pn=20&engine=google">3</a>
        <a href="?keywords='.$keywords.'&amp;pn=30&engine=google">4</a>
        <a href="?keywords='.$keywords.'&amp;pn=40&engine=google">5</a>
        <a href="?keywords='.$keywords.'&amp;pn=50&engine=google">6</a>
        <a href="?keywords='.$keywords.'&amp;pn=60&engine=google">7</a>
        <a href="?keywords='.$keywords.'&amp;pn=70&engine=google">8</a>
        </p>';
//$currentpage当前所在页
$currentpage=$pn/10+1;
$currentpos=strpos($guid,$currentpage.'<');
//$guid为最低下的导航页内容
$guid=substr_replace($guid,'O',$currentpos,($currentpage<10)?1:2);
echo $guid;
}


elseif($engine=='baidu'){

header("Content-type:text/html;charset=utf-8");//加个头
require('baidu.sty');
$url='http://www.baidu.com/s?wd='.$keywords.'&pn='.$pn;
//please check the url
//echo $url;
//$contents=file_get_contents($url);

//try with curl
$contents=get_data($url);
//echo $contents;
$start=strpos($contents,'<div id="container">');
//echo $start.'<br/>';
//real start position
$start=strpos($contents,'</table>',$start);
//real end position
$end=strpos($contents,'<p id="page">',$start);
$len=$end-$start;
//echo $end.'<br/>';
$inf=substr($contents,$start,$len);
//替换一些失效链接
$inf=str_replace('/s?wd=','index.php?engine=baidu&keywords=',$inf);
echo $inf;

$guid='<p id="page"><a href="?keywords='.$keywords.'&amp;pn=0&engine=baidu">1</a>
        <a href="?keywords='.$keywords.'&amp;pn=10&engine=baidu">2</a>
        <a href="?keywords='.$keywords.'&amp;pn=20&engine=baidu">3</a>
        <a href="?keywords='.$keywords.'&amp;pn=30&engine=baidu">4</a>
        <a href="?keywords='.$keywords.'&amp;pn=40&engine=baidu">5</a>
        </p>';
//$currentpage当前所在页
$currentpage=$pn/10+1;
$currentpos=strpos($guid,$currentpage.'<');
//$guid为最低下的导航页内容
$guid=substr_replace($guid,'O',$currentpos,($currentpage<10)?1:2);
echo $guid;
}


//本地有效,远程无效
//elseif($engine=='php'){

/*it will only search php functions within http://cn2.php.net/ . If the function name you *give does not exist, it returns nothing but a empty page.
*/
//require('php_print.css');
//require('php_site.css');
//require('php_mirror.css');
//$url='http://php.net/search.php?&pattern='.$keywords.'&show=quickref';

//try with file_get_contents,failed remote in us server
//$phpcontents=file_get_contents($url);

//try with curl,failed local
//$phpcontents=get_data($url);
//echo $phpcontents;
//$start=strpos($phpcontents,'<div id="content"');
//echo $start.'<br/>';
//actual start position
//$start+=8;
//echo $start.'<br/>';
//$start=strpos($phpcontents,'<div id=',$start);
//echo $start;
//$end=strpos($phpcontents,'<div id="footnav">');
//$phpinf=substr($phpcontents,$start,$end-$start);
//echo $phpinf;
//}



elseif(($engine=='mysql')||($engine=='java')){

header("Content-type:text/html;charset=utf-8");//加个头
require('search-core.sty');
require('search.sty');
require('results.sty');
require('grids-ses.sty');
require('tabview-ses.sty');
require('treeview-ses.sty');

//enable page links to work
$pgstart=$pn+1;
$pgend=$start+9;

//url中不能有回车符
if($engine=='mysql'){
$url='http://search.oracle.com/search/search?&q='.$keywords.'&group=MySQL&search_startnum='.$pgstart.'&search_endnum='.$pgend.'&num=10';}
else{
$url='http://search.oracle.com/search/search?search_p_main_operator=all&group=Documentation&q='.$keywords.'+url:/javase'.'&search_startnum='.$pgstart.'&search_endnum='.$pgend.'&num=10';
}
//echo $url;
//$mysqlcontents=file_get_contents($url);

//try with curl
$mysqlcontents=get_data($url);

$start=strpos($mysqlcontents,'<div id="bd">');
$end=strpos($mysqlcontents,'<div id="ft">');
$mysqlinf=substr($mysqlcontents,$start,$end-$start);
echo $mysqlinf;
if($engine=='mysql'){
$guid='<p id="page"><a href="?keywords='.$keywords.'&amp;pn=0&engine=mysql">1</a>
        <a href="?keywords='.$keywords.'&amp;pn=10&engine=mysql">2</a>
        <a href="?keywords='.$keywords.'&amp;pn=20&engine=mysql">3</a>
        <a href="?keywords='.$keywords.'&amp;pn=30&engine=mysql">4</a>
        <a href="?keywords='.$keywords.'&amp;pn=40&engine=mysql">5</a>
        </p>';
}else{
$guid='<p id="page"><a href="?keywords='.$keywords.'&amp;pn=0&engine=java">1</a>
        <a href="?keywords='.$keywords.'&amp;pn=10&engine=java">2</a>
        <a href="?keywords='.$keywords.'&amp;pn=20&engine=java">3</a>
        <a href="?keywords='.$keywords.'&amp;pn=30&engine=java">4</a>
        <a href="?keywords='.$keywords.'&amp;pn=40&engine=java">5</a>
        </p>';
}
 
$currentpage=$pn/10+1;
$currentpos=strpos($guid,$currentpage.'<');
//$guid为最低下的导航页内容
$guid=substr_replace($guid,'O',$currentpos,($currentpage<10)?1:2);
echo $guid;
}
else{
echo 'Sorry,This search engine will be supported soon.';
}


/* gets the data from a URL */
function get_data($url) 
{   
  $ch = curl_init();
  $timeout = 5; 
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
} 
?>
</body>
</html>
