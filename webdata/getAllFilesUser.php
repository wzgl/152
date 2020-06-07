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
$aPatchNames = array();
foreach ($aPatchName as $patch)
{
	$patchs = str_ireplace(".exe.rar",".exe",$patch);
	$patchs = str_ireplace(".exe.zip",".exe",$patchs);
	$aPName = explode("/",$patchs);
	$num = count($aPName) ;
	$i ++;
	if($num>1&&stripos($patch,"SP/")===false&&stripos($patch,"_SP")===false&&stripos($patch,".iso")===false)
	{
		if(stripos($patch,"2017")!=false || stripos($patch,"2018")!=false ||stripos($patch,"2019")!=false ||stripos($patch,"2020")!=false ||stripos($patch,"2021")!=false )
		{
			$pname = $aPName[$num-1];
			$md5path = md5($pname);
			$pathpath = $file_read.$md5path."/patch_tmp/files/root/";
			$pathReadMe = $file_read.$md5path."/Readme.txt";
			$pathName = $file_read.$md5path."/name";
			$name = "";
			$userkey = "";
			$mod = stripos($patch,"custom/")!==false ? 'custom' : (stripos($patch,"production/")!==false ? 'production':'normal');
//			if(file_exists($pathReadMe))
//			{
//				$File = file_get_contents($pathReadMe);
//				$aFile = explode("\n",$File);
//				foreach ($aFile as $item)
//				{
//					if(stripos($item,'制作时间')!==false && (stripos($item,'2017')==false && stripos($item,'2018')==false&& stripos($item,'2019')==false&& stripos($item,'2020')==false))
//					{
//						break ;
//					}
//					if(stripos($item,'制作人员')!==false)
//					{
//						//张克彬 白彦芳 晏成 陈勤 刀梦欢
//						$userkey = str_ireplace("制作人员: ","",$item);
//						$userkey = str_ireplace("\n","",$userkey);
//						$userkey = str_ireplace("\t","",$userkey);
//						$userkey = iconv("gbk","utf-8",$userkey);
//						break;
//					}
//				}
//				if(file_exists($pathName))
//				{
//					$name = file_get_contents($pathName);
//					$name = str_ireplace(".exe.rar","",$name);
//					$name = str_ireplace("\n","",$name);
//					$name = str_ireplace("\t","",$name);
//					$name = iconv("gbk","utf-8",$name);
//				}
//			}
			if($name == "" && $pname!="")
				$name = iconv("gbk","utf-8",$pname);
			if($name!="")
			{

				if(!isset($aPatchNames[$name] ))
					$aPatchNames[$name] = array(0=>"",1=>$mod,2=>"1");
				else
				{
					$nums = intval($aPatchNames[$name][2]+1);
					$aPatchNames[$name] = array(0=>"",1=>$mod,2=>intval($nums));
				}
			}
		}


	}
}
$nums = 0 ;
echo json_encode($aPatchNames);
?>
