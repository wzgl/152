<?php


error_reporting(E_ALL);
include_once("/var/www/html/webdata/func.php");


/*

DROP DATABASE IF EXISTS ASM_Update;

CREATE DATABASE ASM_Update DEFAULT CHARACTER SET 'GBK';

USE ASM_Update;

SET NAMES GBK;

DROP TABLE TUpdateFile ; 

SELECT '创建升级包表' AS ACTION;
CREATE TABLE  IF NOT EXISTS `TUpdateFile` (
`RID`  int(11) NOT NULL AUTO_INCREMENT COMMENT '流水号' ,
`UpdateName`  varchar(1024) NOT NULL COMMENT '升级包名称' ,
`UpdateFile`  varchar(4096) NOT NULL COMMENT '升级包文件' ,
`Md5Path`  varchar(128) NOT NULL COMMENT 'Md5路径' ,
`InsertTime`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '入库时间' ,
PRIMARY KEY (`RID`)
)ENGINE=InnoDB DEFAULT CHARSET=gbk;


*/


function fileSort(&$a, $key, $IsAsc)
{
	
	if(isset($a))
	{
			
			for($i = 0; $i<count($a); $i++)
			{
				for($j=0; $j<count($a);$j++)
				{
					if(!isset($a[$i][$key]) || !isset($a[$j][$key]))
						continue;
					$t = $a[$i];
					if(
					 //$a[$j]["isdir"]
					($IsAsc && 	($a[$i][$key] < $a[$j][$key]))
					|| (!$IsAsc && 	($a[$i][$key] > $a[$j][$key]))
					
					)
					{
						$a[$i] = $a[$j];
						$a[$j] = $t;
					}
					
					// 强制就爱那个目录排到前面
					if($a[$i]["isdir"])
					{
						$t = $a[$i];
						$a[$i] = $a[$j];
						$a[$j] = $t;
					}
				}
			}
	
	}

}


//调用方法getDir("./dir")……可以是绝对路径也可以是相对路径
$rootdir = $_SERVER["DOCUMENT_ROOT"];
$dir="";
if(isset($_REQUEST['dir']))
	$dir = $_REQUEST['dir'];

$dirstart="?=";	
//if(!strstr($_SERVER["REQUEST_URI"],"_dir"))
{
	$dir = $_SERVER["REQUEST_URI"];
	if(strstr($dir, "?"))
		$dir = substr($dir,0,strrpos($dir,"/?"));
	else
		$dir = substr($dir,0,strrpos($dir,"/"));
		
		$dirstart="";
}

$dir=urldecode($dir);


str_replace(".","",$dir);	
//$dir="newasmpublish";
$fs = getDir($rootdir."/".$dir);

$key="name";
$bIsAsc=1;

if(isset($_REQUEST['C']))
	$key=$_REQUEST['C'];
	
if(isset($_REQUEST['D']))
	$bIsAsc=$_REQUEST['D'];
	

//var_dump($fs);
fileSort($fs, $key, $bIsAsc);


$bIsAsc = $bIsAsc ? 0 : 1;

//var_dump($_SERVER)
//var_dump($fs);
//exit();
//echo getreadme("/var/www/html/webdata/newasmpublish/v5.2.6037.1925/ASMPatch_v5.2.6037.1925_20130918_调用断网接口后开机网络被恢复.exe.rar");
//exit();
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
 <head>
  <title>Index of <?php echo $dir;?></title>
  <script src="/_dir/jquery-1.6.4.js"></script>
	<script src="/_dir/jquery.bpopup.js"></script>
	<style>
	.button:hover {
background-color: #1e1e1e;
}
		 .button {
background-color: #2b91af;
border-radius: 10px;
box-shadow: 0 2px 3px rgba(0,0,0,0.3);
color: #fff;
cursor: pointer;
display: inline-block;
padding: 10px 20px;
text-align: center;
text-decoration: none;
}

		.button.b-close, .button.bClose
		 {
				border-radius: 7px 7px 7px 7px;
				box-shadow: none;
				font: bold 131% sans-serif;
				padding: 0 6px 2px;
				position: absolute;
				right: -7px;
				top: -7px;
		}
	</style>
 </head>
 <body>
<script>
function showdsc(id)
{
	
	 $('#main_dsc').html($('#'+id).text());
	 //$('#main_dsc').
	 $('#nobaidu_dlg').bPopup();
}

function showfilelist(id)
{
	var md5path = $('#'+"file_md5Path_" + id).val();
	
	$("#listfile_dlg_if").attr("src", "/webdata/showfilelist.php?md5path=");
	$('#main_dsc2').html($('#desc_'+id).text());
	$("#listfile_dlg_if").attr("src", "/webdata/showfilelist.php?md5path=" + md5path);
	 $('#listfile_dlg').bPopup();
	
}
function delfile(id)
{
	$('#delfilename').html($('#'+"file_name_" + id).val());
	$('#delfilepath').val($('#'+"file_path_" + id).val());
	$('#delfilename2').val($('#'+"file_name_" + id).val());
	 $('#delete_dlg').bPopup();
}

function onupfile()
{
	 $('#upload_dlg').bPopup();
}
function showallfile()
{
	 $("#md5path").val("");
	$('.file_md5Path').each(
	function(){
   // alert($(this).val())
   var old =  $("#md5path").val();
   $("#md5path").val(old + $(this).val() + "," );
  });
  $("#submit_md5path").click();
  //alert($("#md5path").val());
  
}
</script>

<div style="float:right">
<a  href="###" onclick="showallfile()" title="显示所有升级文件" >
<img width="40px" style="border:0px;"src="/flist.png" /></a>

<a href="###" onclick="onupfile()" title="上传文件" ><img width="40px" style="border:0px;"src="/upload.png" /></a></div>
<h1>Index of <?php echo $dir;?></h1>
<table >
<tr>
	<th><img src="/icons/blank.gif" alt="[ICO]"></th>
	<th><a href="<?php echo "?C=link&D=$bIsAsc&dir=$dir";?>">Name</a></th>
	<th width="200px"><a href="<?php echo "?C=etime&D=$bIsAsc&dir=$dir";?>">Last modified</a></th>
	<th  width="100px"><a href="<?php echo "?C=size&D=$bIsAsc&dir=$dir";?>">Size</a></th>
	
	<th width="70px"><a href="<?php echo "?C=user&D=$bIsAsc&dir=$dir";?>">User</a></th>
	<th   align="left"><a href="<?php echo "?C=exename&D=$bIsAsc&dir=$dir";?>">ExeName</a></th>
	<th  align="right">Action</th>
</tr>

<tr><th colspan="8"><hr></th></tr>

<tr>
	<td valign="top"><img src="/icons/back.gif"  alt="[DIR]"></td>
	<td><a href="<?php echo $dirstart.substr($dir,0,strrpos($dir,"/"));?>">Parent Directory</a>
	</td><td>&nbsp;</td>
	<td align="right">  - </td>
	<td>&nbsp;</td>
	<td>  - </td>
	<td>  - </td>
</tr>

<?php


	$i=0;
	$fileNum = 0;
	$dirNum = 0;
	foreach($fs as $f)
	{
		$i++;
		$id="desc_$i";
		
		echo "<input type=hidden id=file_name_$i value=\"{$f["name"]}\" />";
		echo "<input type=hidden id=file_path_$i value=\"$dirstart$dir/{$f["name"]}\" />";
		echo "<input type=hidden class=file_md5Path id=file_md5Path_$i value=\"{$f["md5Path"]}\" />";
		echo "<tr>\n";
		
		if($f["isdir"] )
		{

			echo "<td><img width=\"32px\" src=\"{$f["ico"]}\" alt=\"[   ]\"  ></td>\n";
					
		}
		else
		{
			echo "<td>&nbsp;</td>\n"; 
		}
		
		if($f["isdir"])
		{
			$dirNum ++;
			
			echo "	<td><a href=\"$dirstart$dir/{$f["link"]}\">{$f["name"]}</a></td>\n";
		}
		else
		{
			$fileNum++;
			$shortName = getShortName($f["name"]) ;
			$link = urlencode2($dir)."/".$f["link"] ;
			echo "	<td><a href=\"$link\"  title=\"{$f["name"]}\">$shortName</a></td>\n";
		}	
		echo "	<td align=\"right\">{$f["etime"]}</td>\n";
		echo "	<td align=\"right\">{$f["size2"]}</td>\n";
		$desc = "";
		if($f["desc"] == "&nbsp;" || strlen($f["desc"] )<2)
		{
			$desc=$f["desc"];
		}
		else if(strstr($f["desc"], "<a target="))
		{
			$desc=$f["desc"];
		}
		else
		{
			$desc = "<textarea id=$id style=\"display:none\">{$f["desc"]}</textarea>";
		}
		echo "	$desc\n";
		echo "	<td align=\"left\">{$f["user"]}</td>\n";
		echo "	<td align=\"left\">{$f["exename"]}<textarea style=\"display:none\">{$f["md5"]}</textarea></td>\n";
		//操作列
		echo "	<td align=\"right\" width=\"100px\"  >";
		
		
		if(!$f["isdir"] && strstr($f["name"], ".exe.rar" ))
		{
			echo "	<img width=\"32px\" title=\"显示升级包内容\" src=\"/flist.png\" alt=\"[   ]\"  onclick=showfilelist('$i') style=\"cursor:hand;\">";
			if(stripos($dirstart,"del_patch")===false)
			{
				echo "	<img width=\"32px\" title=\"删除\" src=\"/del.png\" alt=\"[   ]\"  onclick=delfile('$i') style=\"cursor:hand;\">\n";
			}
			
		}
		
		
		echo  "</td>\n";
		echo "</tr>\n";
	
	}

?>
<!--
<tr>
	<td valign="top"><img src="/icons/compressed.gif" alt="[   ]"></td>
	<td><a href="ASMUpdate_ASM-v5.2.6037.1642-v5.2.6037.1925(%d2%aa%c7%f3%d6%d8%c6%f4).exe.zip">ASMUpdate_ASM-v5.2.6037.1642-v5.2.6037.1925(要求重启).exe.zip</a></td>
	<td align="right">28-Oct-2013 11:15  </td><td align="right">142M</td>
	<td>&nbsp;</td>
</tr>
-->
<tr><th colspan="5"><hr></th></tr>

</table>

<div>文件数量:&nbsp; <?php echo $fileNum;?> &nbsp;&nbsp; &nbsp; &nbsp;  目录数量:&nbsp;  <?php echo $dirNum;?></div>


<div id="nobaidu_dlg" style="border:1px solid #2B91AF; background-color:#fff; border-radius:15px;color:#000;display:none;padding:20px;min-width:450px;min-height:180px;">
   	<span class="button b-close"><span>X</span></span>
     <p style="margin-left:10px;margin-top: 20px; line-height: 30px;">
    	
    	<span id="main_dsc"></span>
     </p>
</div>
<div id="delete_dlg" style="border:1px solid #2B91AF; background-color:#fff; border-radius:15px;color:#000;display:none;padding:20px;min-width:450px;min-height:180px;">
   	<span class="button b-close"><span>X</span></span>
     <p style="margin-left:10px;margin-top: 20px; line-height: 30px;">
    	
    	确认删除 &nbsp;[<span id="delfilename" style="color:red" name=delfilename ></span>]&nbsp;?
		<br>
		<form id="delfile" target="deliframe" method="post" action="/webdata/delasmpatch.php">
		<input id="delfilepath" name="delfilepath" type="hidden" />
		<input id="delfilename2" name="delfilename2" type="hidden" />
		<br>
		<center>
		<table>
		<tr>
		<td>邮箱用户:</td>
		<td><input id="mailuser"  style="width:200px" name="mailuser" type="text" /></td>
		</tr>
		<tr>
		<td>邮箱密码:</td>
		<td><input  name="mailpass"   style="width:200px" id="mailpass" type="password" /></td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input id="delsubmit"    style="width:200px" name="delsubmit" type="submit"  value="&nbsp;&nbsp;删&nbsp;&nbsp;除&nbsp;&nbsp;"/></td>
		</tr>
		</table>
		</center>
		
		</form>

		<iframe id="deliframe" name="deliframe"  style="display:none"> </iframe>
		
     </p>
</div>

<div id="listfile_dlg" style="border:1px solid #2B91AF; background-color:#fff; border-radius:15px;color:#000;display:none;padding:20px;min-width:450px;min-height:180px;">
   	<span class="button b-close"><span>X</span></span>
    <p style="margin-left:10px;margin-top: 20px; line-height: 30px;">
    	<span id="main_dsc2"></span>
		<br>
		<iframe id="listfile_dlg_if" name="listfile_dlg_if"  style="border:0px solid; width:700px;height:50px" src="/webdata/showfilelist.php" > </iframe>
		
     </p>
</div>

<div id="upload_dlg" style="border:1px solid #2B91AF; background-color:#fff; border-radius:15px;color:#000;display:none;padding:20px;min-width:450px;min-height:180px;">
       
<span class="button b-close"><span>X</span></span>
     <p style="margin-left:10px;margin-top: 20px; line-height: 30px;">

       
                
                <form id="upfileform" target="upiframe" method="post" action="/webdata/uploadasmpatch.php" enctype="multipart/form-data" >

<input id="delfilepath" name="delfilepath" type="hidden" />
                <input id="delfilename2" name="delfilename2" type="hidden" />
                <br>
                <center>
                <table>
		<tr>
			<td><label for="upfile">选择文件:</label></td>
			<td><input type="file" name="upfile" id="upfile" /></td>
			
                <tr>
                	<td>邮箱用户:</td>
                	<td><input id="mailuser"  style="width:200px" name="mailuser" type="text" /></td>
                </tr>
                <tr>
                <td>邮箱密码:</td>
                <td><input  name="mailpass"   style="width:200px" id="mailpass" type="password" /></td>
                </tr>
                <tr>
                <td>&nbsp;</td>
                <td><input id="delsubmit"    style="width:200px" name="delsubmit" type="submit"  value="&nbsp;&nbsp;上&nbsp;&nbsp;传&nbsp;&nbsp;"/></td>
                </tr>
                </table>
                </center>

		
                </form>
                <iframe id="upiframe" name="upiframe"  style="display:none"></iframe>
     </p>

</div>

<div style="display:none">
	<form method="post" target="_blank" action="/webdata/showallfilelist.php">
		<input id="md5path" name="md5path" type="text" value="" />
		<input id="submit_md5path" type="submit" />
	</form>
</div>

<script>
function ResetIframeSize(childHieght)
{
	$("#listfile_dlg_if").height(childHieght);
	//alert($("#listfile_dlg_if").height());

}
 </script>
</body>
</html>

