<?php

function getreadme($file)
{
	$ret = "";
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

function getFileInfo($file,$dir)
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
	if($f["isdir"])
	{
		$f["size2"]="-";
	}
	// chenqin 2019-01-23 ����100M�ļ�����ѹ
        else
        {
                $size = filesize($f["path"]);
                /*���������С�ڵ���100M�Ž�ѹ chenqin 2018-10-16*/
                if($size != FALSE && $size <= 104857600)
                {
                        $f["desc"] = getreadme($f["path"]);
                        $name = basename($f["path"]);
                        $name2 = str_replace(".exe.rar",".exe",$name);
                        $name2 = str_replace(".exe.zip",".exe",$name2);
                        $f["md5"] = md5($name2);
                }
        }
/*
	else
	{
		$f["desc"] = getreadme($f["path"]);
		$name = basename($f["path"]);
		$name2 = str_replace(".exe.rar",".exe",$name);
		$name2 = str_replace(".exe.zip",".exe",$name2);
		$f["md5"] = md5($name2);
	}
*/
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

if($argc > 1)
{

	$path_parts = pathinfo($argv[1]);
	echo $path_parts["dirname"] . "\n";
	echo $path_parts["basename"] . "\n";
	echo $path_parts["extension"] . "\n";


	$fs = getFileInfo($path_parts["basename"], $path_parts["dirname"] );
	var_dump($fs);
}
else
{
//	$fs = get_Dir("/var/www/html/webdata");
//
//	var_dump($fs);

//	echo "==================================================================================";
	//���������ļ��б����Ժ�����ʹ��
	file_put_contents( "/var/www/html/webdata/123test.txt", "qqqqqqqqqqqqqqqqqqqqq");
	if(0)
	{
		require_once("/var/www/html/webdata/search_comm_func.php");
		$files = enum_file_info_from_dir("/var/www/html/webdata/newasmpublish");

		var_dump($files);
	//	saveArrayToFile($files,"/var/www/html/webdata/fileinfo");
	}
}
?>
