<?php
//start session , this session end in index.php successfully login
session_start();

header ("Content-type: image/png");
$code1=generate_random_code(2);
$code2=generate_random_code(3);
//传给index.php
$_SESSION['random_code']=$code1.$code2;

/*deal with image*/
  $im = @imagecreate (60, 30)
	    or die ("Cannot initialize new GD image stream");
  $background_color = imagecolorallocate ($im, 193, 205, 193);
  $blue = imagecolorallocate ($im, 0, 0, 64);
  $text_color = imagecolorallocate ($im, 0,0,0);
  imageline($im,0,15,60,5,$text_color);
  imagestring ($im, 5, 5, 6,  $code1, $text_color);
  imagestring ($im, 5, 30, 1,  $code2, $text_color);
  //imagesetpixel($im,5,5,$blue);
  imagepng ($im);
  imagedestroy($im);
/*end*/ 

function generate_random_code($number){
    $authnum='';  
    //生产验证码字符  
    $ychar="1,2,3,4,5,6,7,9,A,B,C,D,E,F,G,H,I,J,K,L,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
    $list=explode(",",$ychar);  
    for($i=0;$i<$number;$i++){  
      $randnum=rand(0,32);  
      $authnum.=$list[$randnum];  
     }  
    return $authnum;
}
?>
