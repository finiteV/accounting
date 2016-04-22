function dotext(key){
  try{
    var textbox=document.getElementById("upcontent");
    var txt=textbox.value;
    var start=textbox.selectionStart;
    var newtext="";    
    //document.writeln("function works");
    //document.writeln(txt);
    //now do the string works   
    if(key=="b" ||key=="h2" ||key=="p" ||key=="li"){
      newtext=txt.substr(0,start)+"<"+key+"></"+key+">"+txt.substr(start);
    //var len=txt.length;
    //document.writeln("content length:"+len);
      textbox.value=newtext;
    }  
    else if(key=="im"){
      newtext=txt.substr(0,start)+"\\(\\)"+txt.substr(start);
      textbox.value=newtext;
    }
    else if(key=="mt"){
      newtext=txt.substr(0,start)+"$$$$"+txt.substr(start);
      textbox.value=newtext;
    }  
    else if(key=="ul"){
      newtext=txt.substr(0,start)+"<ul><li></li></ul>"+txt.substr(start);
      textbox.value=newtext; 
    }  
    else if(key=="ol"){
      newtext=txt.substr(0,start)+"<ol><li></li></ol>"+txt.substr(start);
      textbox.value=newtext; 
    }    
    else if(key=="thry"){
      newtext=txt.substr(0,start)+"<p><b></b>  </p>"+txt.substr(start);
      textbox.value=newtext; 
    }
    else if(key=="br"){
      newtext=txt.substr(0,start)+"<br>"+txt.substr(start);
      textbox.value=newtext; 
    }
    //重新定位光标位置
    var newpos=0;
    if(key=="ul" || key=="ol"){
      newpos=start + key.length + 2 + 4;
    }
    else if(key=="mt" || key=="im"){
      newpos=start + key.length;
    }
    else{
      newpos=start + key.length + 2;
    }
    textbox.selectionStart=newpos;
    textbox.focus();
  }
  catch(exception){
    document.writeln("Error occurs!");
  }
}