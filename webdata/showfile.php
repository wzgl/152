
 <script src="/_dir/jquery-1.6.4.js"></script> 
 <script src="/highlight/highlight.pack.js"></script>
 <link rel="stylesheet" href="/highlight/styles/rainbow.css">
<?php

error_reporting(E_ALL);
include_once("func.php");

if(!checkLogin())
{
	//echo "请登录";
	//exit();
}
$md5path=$_REQUEST["md5path"];

$file=$_REQUEST["file"];


$md5path = str_replace(".", "", $md5path);
$md5path = str_replace("/", "", $md5path);
$file=str_replace("..", "",$file);

$filePath = "/var/www/html/_dir/patchreadme/$md5path/patch_tmp/files/root$file";

//echo $filePath;
$file_buf = file_get_contents($filePath);
//echo $file_buf;
//exit();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE>ASM补丁包信息</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gbk">
<script src="/tp/mod/networkinfo/js/ajax2.js"></script>
<script src="/tp/js/jquery-1.6.4.min.js"></script>

<style type="text/css">
body{ font-size:14px;}
table{ font-size:14px;}
table .head{text-align:center; background-color:#0080C0; font-weight:bold; color:#FFF;}
table tr{height:24px; line-height:24px;}
table tr td{vertical-align:middle; padding-left:5px}
table tr td.bottomborder{ border-bottom:dashed #CCC 1px; padding-left:4px;}
table tr td.time{ color:#004080;}
table tr td.c{ color:#004080;border-left:1px #9FB9CE solid ;}
table tr td.time,table tr td.type{ text-align:center;}

div.submit{
   width:100px;
   height:35px;
   line-height:35px;
   text-align:center;
   background-color:#0066CA; 
   border:0px;
   cursor:pointer;
   float: right; 
   margin-left: 1px;
}


div.info{
border: 0px solid  #0066CA;
width:100px;
height:35px;
line-height:35px;
	font-size:18px;
	float: left; 
	margin-left: 1px;
}
input.text{
	border: 1px solid  #DDE8FF;
	width:200px;
	height:33px;
	line-height:20px;
	font-size:18px;
	outline:none;
	color: #727272;
	vertical-align:middle;
/*	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius:5px;*/
	 float: right; 
	 
}

</style>
</HEAD>
<body style="margin-left:0px; margin-top:0px">
<div id="outdiv">



<table width="100%" id="file_table" cellpadding="0" cellspacing="0" bordercolorlight="#9FB9CE" bordercolordark="#FFFFFF" border="0" style=" left:0px; top:0px; border:1px #000000 solid ; " >
	
	<!--
	<tr class='head'>
		<td width='300'>文件路径</td>
		<td width='130'>升级包名称</td>
	</tr>
	-->
<?php


	
		$file_buf = str_replace("<", "&lt;",$file_buf);
		$file_buf = str_replace(">", "&gt;",$file_buf);
		$file_buf = str_replace("\"", "&quot;",$file_buf);
		//$file_buf = str_replace(" ", "&nbsp;",$file_buf);
		$filetype = substr(strrchr($file, '.'), 1);
		echo "<tr>";
		echo "<td><pre><code class=\"$filetype\">$file_buf</code></pre></td>";
		echo "</tr>";	

	
	
?>
</table>



	
<?php

	
	
?>


<div>



<script>

function SetParentHieght()
{
	//var h = $(document.body).height() + $(document.body).get(0).scrollHeight;
	var h=$("#outdiv").height()+40;
	//alert($(document.body).height());
	if(h >0)
		parent.ResetIframeSize(h );
}
setTimeout("SetParentHieght()",1);

$(document).ready(function() {
  $('pre code').each(function(i, block) {
    hljs.highlightBlock(block);
  });
});

</script>

</body>
</html>
