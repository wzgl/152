
<?php

error_reporting(E_ALL);

include_once("upload.php");
include_once("func.php");

//$_REQUEST["mailuser"]
//var_dump($_REQUEST);
$user=$_REQUEST["mailuser"];
$pass=$_REQUEST["mailpass"];
$file=$_REQUEST["delfilename2"];
$filepath=$_REQUEST["delfilepath"];



function uploadfile()
{
	global $filepath;

	 $fileArr['file'] = $_FILES[$id];
    	// �������ϴ����ļ�����
    	$filetypes = array('gif','jpg','jpge','png');
    	// �ļ��ϴ�Ŀ¼
    	$savepath = "/tmp/logs/";
    	// û��������� 0 ������
    	$maxsize = 0;
    	// ���� 0 ������  1 ����
    	$overwrite = 0;
	$new_path="/var/www/html".str_replace("http://".$_SERVER["SERVER_NAME"], "", $_SERVER["HTTP_REFERER"]).  $_FILES[$id]["name"];

    	//$upload = new upload($fileArr, $_FILES[$id]["name"], "/var/www/html".str_replace($_SERVER["HTTP_ORIGIN"], "", $_SERVER["HTTP_REFERER"]), $filetypes);
    	
	/*
	$upload = new upload($fileArr, $_FILES[$id]["name"], "/tmp/logs/", $filetypes);
    	if (!$upload->run())
    	{
     		echo   $upload->errmsg();
    	}
	return;
	*/

	$id="upfile";

	//var_dump($_REQUEST);
	//var_dump($_FILES);
	//var_dump($_SERVER);
	if (1)
  	{
  		if ($_FILES[$id]["error"] > 0)
    		{
    			return "Error: " . $_FILES[$id]["error"] . "<br />";
    		}
  		else
    		{
			/*
   			echo "Upload: " . $_FILES[$id]["name"] . "<br />";
    			echo "Type: " . $_FILES[$id]["type"] . "<br />";
    			echo "Size: " . filesize( $_FILES[$id]["tmp_name"]). " Kb<br />";
    			echo "Stored in: " . $_FILES[$id]["tmp_name"]. "<BR>";
			*/
			$new_path="/var/www/html".str_replace($_SERVER["HTTP_ORIGIN"], "", $_SERVER["HTTP_REFERER"]).  $_FILES[$id]["name"];
			$new_path="/var/www/html".str_replace("http://".$_SERVER["SERVER_NAME"], "", $_SERVER["HTTP_REFERER"]). "/" . $_FILES[$id]["name"];
			/*��ȡURL������֧�ָ�ʽ:/webdata/newasmpublish/MVG/?C=etime&D=0&dir=/webdata/newasmpublish*/
			/*chenqin 2018-12-04*/
			$ret_array = explode('dir=', $new_path);
			if(count($ret_array) > 1)
			{
				$new_path="/var/www/html" . end(explode('dir=', $new_path));
			}
			$filepath = $new_path;
			//return $filepath;

			$new_path2="/tmp/logs/".  $_FILES[$id]["name"];
                        /*chenqin 2018-11-15 ����ļ���׺��*/
                        $file = $_FILES[$id]["name"];
			//if(strlen($_FILES[$id]["name"]) >0 && (strstr($_FILES[$id]["name"], ".exe.rar") || strstr($_FILES[$id]["name"], ".bin")) )
			if(strlen($file) > 0)
                  	{
				/*chenqin 2018-11-15 �޸�֧�ֲ�ͬ�ĸ�ʽ*/
				$ext_array = array('pdf', 'rar','zip','xls','xlsx','doc','docx','ppt','pptx', 'exe', 'png', 'jpeg', 'gif', 'jpg');
                        	$ext_name = pathinfo($file, PATHINFO_EXTENSION);
				if(in_array($ext_name, $ext_array))
				{
					//var_dump($_FILES[$id]["name"]);
					//echo $new_path;
					copy($_FILES[$id]["tmp_name"], $new_path2);
				
					cutil_sys_cmd("mv $new_path2 $new_path");
					return "�ϴ��ɹ�!!!";
				}
				else
				{
					return '��֧�ֵ��ļ�����';
				}
			}
			else
			{
				return "�ϴ�ʧ��!";
			}
			//move_uploaded_file($_FILES[$id]["tmp_name"], $new_path);
    		}
  	}
	else
  	{
  		return "Invalid file";
  	}
}
/*
if(!strstr($filepath, "/webdata/"))
{
	$filepath="";
	$pass="";
}
$filepath = str_replace("..", ".");
*/

//var_dump($_REQUEST);
//var_dump($_FILES);
//print_r(getallheaders());
echo "<script>";
if( MailAuth($user, $pass))
{
	//echo "alert('ɾ��[$file]�ɹ�!');"; 
	$ret =  uploadfile();
	file_put_contents("up_file_log.txt", date("F j, Y, g:i a"). "\t�û�[$user]�ϴ�[$filepath]\n", FILE_APPEND);
	echo "alert('$ret');";
	echo "parent.location.reload();";
	
	
}
else
{
	echo "alert('1�����������![$user|$pass]');";
	
}
echo "</script>";
?>
