<?php
include "/var/www/html/webdata/func.php";

function getreadme($file,$flag=0)
{
	$ret = "";
	file_put_contents("/var/www/html/_dir/123.log","getreadme>>>>>>".$file."\n",8);
	if(strstr($file, "ASMPatch") && strstr($file, ".exe"))
	{
		$name = basename($file);

		$name2 = str_replace(".exe.rar",".exe",$name);	
		$name2 = str_replace(".exe.zip",".exe",$name2);	
		$name2 = md5($name2);


		if(!file_exists("/var/www/html/_dir/patchreadme/$name2")
				|| 		(	strstr($file, "newasmpublish") 
					&& !strstr($file, "v5.2.6037.1925")  
					&& !strstr($file, "v5.2.6037.1642") 
					&& (!file_exists("/var/www/html/_dir/patchreadme/$name2/Readme.txt") && !file_exists("/var/www/html/_dir/patchreadme/$name2/�������.txt"))
					)

		  )
		{
			$cmd="rar x -pinfogo $file -y  /var/www/html/_dir/patchreadme/$name2 �������.* Read*";
			$cmd="rar x -pinfogo $file -y  /var/www/html/_dir/patchreadme/$name2 ";
			//echo "/var/www/html/_dir/patchreadme/$name2"."\n";
			echo "$cmd\n";

			shell_exec("rm -fr /var/www/html/_dir/patchreadme/$name2");
			mkdir("/var/www/html/_dir/patchreadme/$name2");
			$d = shell_exec($cmd);

			// ����exe���ļ���
			shell_exec("ls /var/www/html/_dir/patchreadme/$name2/ | grep .exe > /var/www/html/_dir/patchreadme/$name2/exename");
			
			shell_exec("/root/showpatch.sh /var/www/html/_dir/patchreadme/$name2");
				
		}

		$readme="/var/www/html/_dir/patchreadme/$name2/�������.txt";

		//echo "$readme\n";
		if( file_exists($readme))
		{
			$ret = file_get_contents($readme);
			$ret =str_replace("\r\n","<br>",$ret);
			$ret =str_replace("\n","<br>",$ret);
		}
		else if(file_exists("/var/www/html/_dir/patchreadme/$name2/�������.doc"))
		{
			$ret = "<a target=\"_blank\" href=\""."/_dir/patchreadme/".urlencode($name2). "/".urlencode("�������.doc")."\">�������.doc</a>";
		}
		else
		{
			$ret="&nbsp;";
		}

		//echo $ret;

	}

	return $ret;

}

function getFileInfo($file,$dir,$flag = 0)
{
	$f="";

	$f["name"]=$file;
	if(is_dir($dir."/".$file))
	{
		$f["isdir"]=true;
	}
	else
		$f["isdir"]=false;

	$f["path"] = $dir."/".$file;

	if(strstr($f["path"], "dokuwiki"))
		return false;

	$f["link"] = urlencode($f["name"]);	
	$f["ext"] = pathinfo($dir."/".$file, PATHINFO_EXTENSION);
	$f["ico"] = "/icons/compressed.gif";
	if($f["isdir"])
		$f["ico"] = "/icons/folder.gif";
	$f["size"] = filesize($dir."/".$file);

	//	$f["size2"] = byteFormat($f["size"] );
	//("/var/www/html/_dir/123.log","1111>>>>>>isdir::".$f["isdir"]."\n",8);
	if($f["isdir"])
	{
		$f["size2"]="-";
	}
	else
	{
		$size = filesize($f["path"]);
		//file_put_contents("/var/www/html/_dir/123.log","1111>>>>>>size::".$size."\n",8);
		/*���������С�ڵ���100M�Ž�ѹ chenqin 2018-10-16*/
		if($size != FALSE && $size <= 262144000)
		{
			$f["desc"] = getreadme($f["path"],$flag);
			//file_put_contents("/var/www/html/_dir/123.log","1111>>>>>>desc::".$f["desc"]."\n",8);
			$name = basename($f["path"]);
			$name2 = str_replace(".exe.rar",".exe",$name);
			$name2 = str_replace(".exe.zip",".exe",$name2);
			$f["md5"] = md5($name2);
			//file_put_contents("/var/www/html/_dir/123.log","1111>>>>>>name2::".$f["md5"]."\n",8);
		}
	}
	return $f;
}
/**
 * Goofy 2011-11-30
 * getDir()ȥ�ļ����б�getFile()ȥ��Ӧ�ļ���������ļ��б�,���ߵ����������ж���û�С�.����׺���ļ���������һ��
 */

//��ȡ�ļ�Ŀ¼�б�,�÷�����������
function getDir_sub($dir, &$dirArray) 
{
	if (false != ($handle = opendir ( $dir ))) 
	{

		while ( false !== ($file = readdir ( $handle )) )
		{

			//ȥ��"��.������..���Լ�����.xxx����׺���ļ�
			if ($file != "." && $file != "..") 
			{
				$f="";

				$f = getFileInfo($file, $dir);
				if($f===false)
					continue;
				//	$f["etime"] = date ("Y-m-d H:i:s", filemtime($dir."/".$file));
				$dirArray[]=$f;
				if($f["isdir"])
				{
					getDir_sub($dir."/".$file, $dirArray);
				}
			}
		}
		//�رվ��
		closedir ( $handle );
	}
	return $dirArray;
}

function get_Dir($dir)
{
	$dirArray="";
	return getDir_sub($dir, $dirArray);
}
file_put_contents("/var/www/html/_dir/123.log",var_dump($argv,true)."<<<<<\n",8);
//if($argc > 1 and $argv[2]!=1)
if(false)
{
	$argv[1] = str_ireplace('"',"",$argv[1]);
	$argv[1] = str_ireplace("'","",$argv[1]);
	$path_parts = pathinfo($argv[1]);
	echo $path_parts["dirname"] . "\n";
	echo $path_parts["basename"] . "\n";
	echo $path_parts["extension"] . "\n";
	$flag = 1 ;
	$fs = getFileInfo($path_parts["basename"], $path_parts["dirname"],$flag );

}
else
{

	$fs = get_Dir("/var/www/html/webdata");
	$argv[1] = str_ireplace('"',"",$argv[1]);
	$argv[1] = str_ireplace("'","",$argv[1]);
	$name = basename($argv[1]);
	$name2 = str_replace(".exe.rar",".exe",$name);
	$name2 = str_replace(".exe.zip",".exe",$name2);
	$name2 = md5($name2);
	if ( trim($name2) != "" && $argv[2] == 1)
	{
	        file_put_contents("/var/www/html/_dir/123.log","1111>>>>name2>>".$name2."\n",8);
		/*�ϴ���ѹ�õ���upload�˻������޷�ֱ��ɾ���ļ��ģ���Ҫ��Ȩ chenqin 2020-05-29*/
		sendSocketFunc("127.0.0.1", 36532, "rm -fr /var/www/html/_dir/patchreadme/$name2");
		//shell_exec("rm -fr /var/www/html/_dir/patchreadme/$name2");
		file_put_contents("/var/www/html/_dir/123.log","rm -fr /var/www/html/_dir/patchreadme/$name2"."\n",8);
	}


	//var_dump($fs);

	//���������ļ��б����Ժ�����ʹ��
	{
		require_once("/var/www/html/webdata/search_comm_func.php");
		$files = enum_file_info_from_dir("/var/www/html/webdata/newasmpublish");
		saveArrayToFile($files,"/var/www/html/webdata/fileinfo");
	}
}
?>
