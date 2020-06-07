<?php 
/**
 * 用于分析bug包是否有冲突
 * @author zhangkb 20170103
 * */
function iconvsall($aInfo)
{
	$aInfos = "";
	$aPatchs = "";
	foreach ($aInfo as $key => $aPatch)
	{
		$key = iconv("UTF-8","GBK",$key);
		if(is_array($aPatch))
		{
			foreach ($aPatch as $kkey => $patch)
			{
				$kkey = iconv("UTF-8","GBK",$kkey);
				$patch = iconv("UTF-8","GBK",$patch);
				$aPatchs[$kkey] = $patch;
			}
		}
		else 
		{
			$aPatchs = iconv("UTF-8","GBK",$patch);
		}
		$aInfos[$key] = $aPatchs;
	}
	return $aInfos;
}
/**
 * 用于分析补丁包信息
 * */
function getPatchInfo($path,$filepatch,$aVer)
{
	if(!file_exists($filepatch))
	{
		echo "补丁升级包文件不存在,不进行分析!\n";
		return "";
	}
	$userfile = file_get_contents($filepatch);
	if($userfile=="\n"||$userfile=="")
	{
		echo "补丁升级包文件为空,不进行分析!\n";
		return "";
	}
	foreach ($aVer as $item)
	{
		if(stripos($userfile,$item)!==false)
		{
			$version = $item;
			break ;
		}
	}
	$fpath = "/asm/sh/asm_tools/asmpatchlog/asmdiffpatchinfo.log";
	shell_exec("wget -O $fpath -q 'http://192.168.46.152/webdata/getAllFile.php'");
	if(!file_exists($fpath))
	{
		echo "补丁升级包文件不存在,不进行分析!!\n";
		return "";
	}
	$aFile = file_get_contents($fpath);
	$aFile = json_decode($aFile,true);
	$aFile = iconvsall($aFile);
	$aPatch = $aFile['PatchSps'];
	$aPatchInfo = "";
	if(is_array($aPatch))
	{
		foreach ($aPatch as $key => $item)
		{
			if($version==$key)
			{
				$patchlog = str_ireplace("/var/www/html/","",$item);
				$pathpatchlog = "/asm/sh/asm_tools/asmpatchlog/patchinfo.".$key.".log";
				shell_exec("wget -O $pathpatchlog -q http://192.168.46.152/".$patchlog);
				$aPatchInfo = file_get_contents($pathpatchlog);
				$aPatchInfo = explode("\n",$aPatchInfo);
			}
		}
	}
	if(is_array($aPatchInfo)&&$userfile!="")
	{
		$aUserPatchInfo = explode("\n",$userfile);
		foreach ($aUserPatchInfo as $patch)
		{
			if(!in_array($patch,$aPatchInfo))
			{
				$aNoInPatch[] = $patch;
			}
		}
	}
	$aDiffPatch = $aFile[$version];
	var_dump($aDiffPatch);
	foreach ($aNoInPatch as $item)
	{
		if(isset($aDiffPatch[$item]))
		{
			var_dump($aDiffPatch[$item]);
		}
	}
}

if($argc < 4 )
{
	echo ("警告,参数错误!\n");
	echo "eg: ./log_asmpatchinfo.php 目录(/tmp/logs/) 日期(20160803) 版本(6038.3506)\n";
	exit();
}
$path = $argv[1];
$dates = $argv[2];
$ujson = $argv[3];
$ujson = json_decode($ujson,true);
if(isset($ujson['version'])&&$ujson['version']!="")
	$aVer = array($ujson['version']);
else 
	$aVer = array("6038.4630","6038.3506","6038.2839");

getPatchInfo($path,$path."asmpatchinfo.log",$aVer);

?>
