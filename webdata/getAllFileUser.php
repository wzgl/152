<?php 
/**
 * ���ڻ�ȡ��ǰ���в����ļ�
 * ���ڷ�����ͻ�Ĳ�����
 * ������ʾ�ظ��Ĳ�����
 * @author zhangkb 20170103
 * */
/* �ݹ鷵��ָ�����ļ�·��
*$path ָ�����ļ���Ŀ¼
*$extension��׺����php/exe/txt ע�ⲻҪ��.���Ϊ����ȫ������
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
$aPatchName = SercheFile($file,"","v5.2.6");//��ȡ�����ļ�����
$i = 0 ;
$aAllFile = array();
foreach ($aPatchName as $patch)
{
	$patchs = str_ireplace(".exe.rar",".exe",$patch);
	$patchs = str_ireplace(".exe.zip",".exe",$patchs);
	$aPName = explode("/",$patchs);
	$num = count($aPName) ;
	if($num>1&&stripos($patch,"SP/")===false&&stripos($patch,"production/")===false&&stripos($patch,".iso")===false)
	{
		$pname = $aPName[$num-1];
		$md5path = md5($pname);
		$pathpath = $file_read.$md5path."/patch_tmp/files/root/";
		$pathReadMe = $file_read.$md5path."/Readme.txt";
		$pathName = $file_read.$md5path."/name";
		$name = "";
		$userkey = "";
		if(file_exists($pathReadMe))
		{
			$File = file_get_contents($pathReadMe);
			$aFile = explode("\n",$File);
			foreach ($aFile as $item)
			{
				if(stripos($item,'����ʱ��')!==false && (stripos($item,'2017')==false && stripos($item,'2018')==false&& stripos($item,'2019')==false&& stripos($item,'202')==false ))
				{
					break ;
				}
				if(stripos($item,'������Ա')!==false)
				{
					//�ſ˱� ���巼 �̳� ���� ���λ�
					$userkey = str_ireplace("������Ա: ","",$item);
					$userkey = str_ireplace("\n","",$userkey);
					$userkey = str_ireplace("\t","",$userkey);
					$userkey = iconv("gbk","utf-8",$userkey);
					break;
				}
			}
			if(file_exists($pathName))
			{
				$name = file_get_contents($pathName);
				$name = str_ireplace(".exe.rar","",$name);
				$name = str_ireplace("\n","",$name);
				$name = str_ireplace("\t","",$name);
				$name = iconv("gbk","utf-8",$name);
			}
			if($name!=""&&$userkey!="")
				$aPatchNames[$name] = $userkey;
		}
	}
}
echo json_encode($aPatchNames);
?>
