
 <script src="/_dir/jquery-1.6.4.js"></script> 
 <script src="/highlight/highlight.pack.js"></script>
 <link rel="stylesheet" href="/highlight/styles/xcode.css">
<?php

error_reporting(E_ALL);
include_once("func.php");

$md5path=$_REQUEST["md5path"];
$exe_path = "";
$exe_name = file_get_contents("/var/www/html/_dir/patchreadme/$md5path/exename");
$sql_base=file_get_contents("/var/www/html/_dir/patch_base/update.sql");
$sh_base= file_get_contents("/var/www/html/_dir/patch_base/before-update.sh");
$sh2_base=file_get_contents("/var/www/html/_dir/patch_base/after-update.sh");

if($exe_name)
{
	$exe_path = "/var/www/html/_dir/patchreadme/$md5path/$exe_name";
	$exe_path = trim($exe_path);
}

$filelist = file_get_contents("/var/www/html/_dir/patchreadme/$md5path/flist");
$sqlBuf =  file_get_contents("/var/www/html/_dir/patchreadme/$md5path/patch_tmp/files/update.sql");
if($sql_base == $sqlBuf)
{
	$sqlBuf ="-- 没有数据库升级";
}


//$sqlBuf = str_replace("\n", "<BR>", $sqlBuf);

$shBuf =  file_get_contents("/var/www/html/_dir/patchreadme/$md5path/patch_tmp/files/before-update.sh");
if($sh_base == $shBuf)
{
	$shBuf ="## 没有升级前脚本";
}


$shBuf2 =  file_get_contents("/var/www/html/_dir/patchreadme/$md5path/patch_tmp/files/after-update.sh");
if($sh2_base == $shBuf2)
{
	$shBuf2 ="## 没有升级后脚本";
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE>ASM补丁包信息</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gbk">
<script src="/_dir/ajax2.js"></script>
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
		if(strlen($exe_path)>0 && file_exists($exe_path))
		{
			$exe_md5 = md5_file($exe_path);
			echo "<tr>";
			echo "<td>升级包MD5: ".$exe_md5."</td>";
			echo "</tr>";
		}
	
		$fs = explode("\n",$filelist);
		foreach($fs as $v)
		{
			
			$v = substr($v, 1);
			//if(strlen($v)>2)
			{
				echo "<tr>";
				echo "<td><a target=\"_blank\" href=\"/webdata/showfile.php?md5path=$md5path&file=$v\">$v</a></td>";
				//echo "<td>&nbsp;</td>";
				echo "</tr>";
			}
			
		}
		echo "<tr>";
		
		echo "<td><pre><code class=\"sql\">$sqlBuf</code></pre></td>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td><pre><code class=\"bash\">$shBuf</code></pre></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><pre><code class=\"bash\"><code>".str_replace('</','&lt;',$shBuf2)."</code></pre></td>";
		echo "</tr>";
	
	
?>
</table>


<!--
<table width="100%" id="sql_table" cellpadding="0" cellspacing="0" bordercolorlight="#9FB9CE" bordercolordark="#FFFFFF" border="0" style="left:0px; top:0px; border:1px #000000 solid ; " >
	
<?php

	//echo "<tr>";
	//echo "<td>&nbsp;$sqlBuf<BR>$shBuf</td>";
	//echo "</tr>";		
?>
</table>
-->
<div>
<div style="position:fixed;right:10px;top:2px;z-index:9999">


<a href="#" target="_blank">
<img width="40px" style="border:0px;width:32px;"src="/newmax.png" /></a>
</div>


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
