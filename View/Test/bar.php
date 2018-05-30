<?php 

$width = 500;                      //显示的进度条长度，单位 px 
//$total = count($users);     //总共需要操作的记录数 
//$pix = $width / $total;        //每条记录的操作所占的进度条单位长度 
$progress = 100;                   //当前进度条长度 
?> 
<html> 
<head> 
    <title></title> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <style> 
    body, div input { font-family: Tahoma; font-size: 9pt } 
    </style> 
    <script language="JavaScript"> 
    <!-- 
    function updateProgress(sMsg, iWidth) 
    {  
        //document.getElementById("status").innerHTML = sMsg; 
        document.getElementById("progress").style.width = iWidth + "px"; 
        document.getElementById("percent").innerHTML = parseInt(iWidth / <?php echo $width; ?> * 100) + "%"; 
     } 
    //--> 
    </script>     
</head> 

<body> 
<div style="margin: 4px; padding: 2px; border: 1px solid gray; background: #EAEAEA; width: <?php echo $width+1; ?>px"> 
    
    <div style="padding: 0; background-color: white; border: 1px solid navy; width: <?php echo $width; ?>px"> 
    <div id="progress" style="padding: 0; background-color: #FFCC66; border: 0; width: 0px; text-align: center;   height: 16px"></div>             
    </div> 
    <!--<div id="status">&nbsp;</div> -->
    <div id="percent" style="position: relative; top: -3px; text-align: center; font-weight: bold; font-size: 8pt">0%</div> 
</div> 
<?php 
flush();   

 $width=350;
?> 
<script language="JavaScript"> 
    updateProgress("流量使用情况", <?php echo $width; ?>); 
</script> 
<?php 
flush(); 
?> 
  

 


</body> 
</html> 