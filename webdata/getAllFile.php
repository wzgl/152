<?php 
/**
 * 用于获取当前所有补丁文件
 * 用于分析冲突的补丁包
 * 用于显示重复的补丁包
 * @author zhangkb 20170103
 * */
/* 递归返回指定的文件路径
*$path 指定的文件或目录
*$extension后缀名如php/exe/txt 注意不要点.如果为空则全部返回
*/
function SercheFile($path,$extension="",$pathtype=""){
	$tempfile_path_arr=array();
	if (is_dir($path))
		{
			$dir=$path;
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					$file_path = substr($dir, - 1) == '/' ? $dir . $file : $dir . '/' . $file;
					if(is_dir($file_path)){
						if($file!="." && $file!=".." && $file!=".svn")
						{
								if($pathtype!=""&&stripos($file_path,$pathtype)===false)
								{
									continue ;
								}
								$childFile=SercheFile($file_path);
								if(is_array($childFile) && count($childFile)>0)
									$tempfile_path_arr=array_merge($tempfile_path_arr,$childFile);
						}
					}else{
						if($pathtype!=""&&stripos($file_path,$pathtype)!==false)
						{
							continue ;
						}
						if($extension!="")
						{
							if (get_file_extend($file_path) == $extension)
								$tempfile_path_arr[]=$file_path;
						}else{
							$tempfile_path_arr[]=$file_path;
						}
					}
				}
				closedir($dh);
				return $tempfile_path_arr;
			}
	}
	if(is_file($path) && file_exists($path))
	{
		if($extension!="")
		{
			if (get_file_extend($path) == $extension)
				$tempfile_path_arr[]=$path;
		}else{
			$tempfile_path_arr[]=$path;
		}
		return $tempfile_path_arr;
	}
}
$file = "/var/www/html/webdata/newasmpublish/";
$file_read = "/var/www/html/_dir/patchreadme/";
$aPatchName = SercheFile($file,"","v5.2.6");//获取补丁文件名称
$i = 0 ;
$aAllFile = array();
foreach ($aPatchName as $patch)
{
	$patchs = str_ireplace(".exe.rar",".exe",$patch);
	$patchs = str_ireplace(".exe.zip",".exe",$patchs);
	$aPName = explode("/",$patchs);
	$num = count($aPName) ;
	if($num>1&&stripos($patch,"SP/")===false&&stripos($patch,".iso")===false)
	{
		$pname = $aPName[$num-1];
		$md5path = md5($pname);
		$pathpath = $file_read.$md5path."/patch_tmp/files/root/";
		if(is_dir($pathpath))
		{
			$aFile = SercheFile($pathpath);
			foreach ($aFile as $item)
			{
				$pathfile = str_ireplace($pathpath,"",$item);
				//$aAllPatchFile[$pname][] = $pathfile;
				$aAllFile[$aPName[6]][$pathfile][] = str_ireplace(".exe","",iconv("gbk","utf-8",$pname));
			}
		}
	}
	else if(stripos($patch,"SP/")!==false&&stripos($patch,".log")!==false)
	{
		$aPatchSps[$aPName[6]][] = $patch;
	}
}
$aPatchSp = "";
foreach ($aPatchSps as $key => $aItem)
{
	asort($aItem);
	$num =count($aItem);
	$key = str_ireplace("v5.2.","",$key);
	$aPatchSp[$key] = $aItem[($num-1)];
}
$aPatchName = "";
if(is_array($aAllFile))
{
	foreach ($aAllFile as $keys => $aPatchAll)
	{
		if(is_array($aPatchAll))
		{
			foreach ($aPatchAll as $aPatch)
			{
				$num = count($aPatch);
				if($num > 1)
				{
					$aPatchs = $aPatch ;
					foreach ($aPatchs as $p_name)
					{
						if(!isset($aAllPatch[$p_name]))
						{
							$a = array($p_name);
							$aAllPatch[$p_name] = array_diff($aPatch,$a);
						}
						else 
						{
							$a = array($p_name);
							$b = array_diff($aPatch,$a);
							$c = array_merge($aAllPatch[$p_name],$b);
							$c = array_unique($c);
							$aAllPatch[$p_name] = $c;
						}
					}
				}
			}
		}
	}
}
$aAllPatch['PatchSps'] = $aPatchSp;
echo json_encode($aAllPatch);

?>
