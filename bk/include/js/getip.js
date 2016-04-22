/*
Copyright (c) 2012 Zhang Chufan. All Right reserved.
*/
function getip(){
  try{
    document.getElementById('iphidden').value=ILData[0];
  }
  catch(err){
    document.getElementById('Msg').innerHTML='Failed to connect to Sina!';
  }

}
