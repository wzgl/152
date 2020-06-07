
 <script src="/_dir/jquery-1.6.4.js"></script> 
 <script src="/highlight/highlight.pack.js"></script>
 <link rel="stylesheet" href="/highlight/styles/xcode.css">


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE>ASM补丁包信息</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gbk">
<script src="/tp/mod/networkinfo/js/ajax2.js"></script>
<script src="/tp/js/jquery-1.6.4.min.js"></script>

<style type="text/css">
body{ font-size:14px;}
table{ font-size:16px;}
table .head{text-align:center; background-color:#0080C0; font-weight:bold; color:#FFF;}
table tr{height:24px; line-height:24px;}
table tr td{vertical-align:middle; padding-left:5px; padding-top:5px;}
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



<?php

error_reporting(E_ALL);
include_once("func.php");

$md5paths=$_REQUEST["md5path"];
$isHideFile=$_REQUEST['hidefile'];
file_put_contents("./1.log",print_r($md5paths,true));

function GetFList($md5path)
{
	$filelist = file_get_contents("/var/www/html/_dir/patchreadme/$md5path/flist");
	return $filelist;
}

$md5pathA = explode(",", $md5paths);


?>

<table width="100%" id="file_table" cellpadding="0" cellspacing="0" bordercolorlight="#9FB9CE" bordercolordark="#FFFFFF" border="0" style=" left:0px; top:0px; border:1px #000000 solid ; " >
	
	
	<tr class='head'>
		<td width='200'>升级包名称</td>
		<td>冲突</td>
		<td>文件路径</td>
		
	</tr>
	
<?php

		$id = 1;
		foreach($md5pathA as $md5path)
		{
			$filelist = GetFList($md5path);
			$fs = explode("\n",$filelist);
			$fname=file_get_contents("/var/www/html/_dir/patchreadme/$md5path/name");
			$fname = getShortName($fname);
			$n=0;
			
			foreach($fs as $v)
			{
				
				$v = substr($v, 1);
				if(strlen($v)>2)
				{
					echo "<tr>";
					if($n>0)
						echo "<td>&nbsp;</td>";
					else
					{
						$id++;
						echo "<td id=\"fname_$id\">$fname</td>";
					}
					
					echo "<td>&nbsp;</td>";
					echo "<td  class=patchfile fname=$id>$v</td>";
					
					echo "</tr>";
					$n++;
				}
				
			}
			
		}

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


<script>
function isRepeatFile( file)
{
	var r = 0;
	
	$(".patchfile").each(
		function ()
		{
			if( $(this).text() === file)
			{
				r++;
				if(r>1)
				{
					//alert(r + file);
					//return false;
				}
			}
		}
		
	);
	
	return r;
	
}

function checkRepeatFile()
{
	
	$(".patchfile").each(
	function ()
	{
		var r = isRepeatFile($(this).text());
		if(r>1)
		{
			//alert($(this).text());
			$(this).parent().css("color", "red");
			$(this).prev().html("&nbsp;["+r+"]");
			var fnameid = $(this).attr("fname");
			$("#fname_"+fnameid).css("color", "red");
			//$(this).text($(this).text() + "["+r+"]" );
		}else{
			<?php
				if($isHideFile){
			?>
			if($(this).prev().prev().text().replace(/(^\s+)|(\s+$)/g,'') == ""){
				$(this).parent().hide();	
			}
			<?php
			}
			?>
		}
		
	}
	);

	// hide row

}
$(document).ready(function() {
  checkRepeatFile();
});
</script>

</body>
</html>
