<?php

/**
 * ��ȡ�м��ַ��� 
 * @param  string $strSrc="aabbcc", $strFirst="aa", $strLast="cc" 
 * @return "bb"
 * 
 */
function GetSubStr($strSrc, $strFirst, $strLast) {
    $p1 = stripos($strSrc, $strFirst);
    if ($p1 === false) {
        return "";
    }
    $len = strlen($strFirst);
    $p2 = stripos($strSrc, $strLast, $p1 + $len);
    if ($p2 == false) {
        return "";
    }
    return substr($strSrc, $p1 + $len, $p2 - $p1 - $len);
}

function urlencode2($str)
{
	$a = explode("/",$str);
	foreach($a as &$v)
	{
		$v = urlencode($v);
	}
	return   implode("/",$a);
}


/**
 * @brief cutil_exec_wait
 *
 * @Param: $cmd
 * @Param: $TimeOut
 * @Param: $ip
 *
 * Returns:
 */
 
function sendSocketFunc_new($ip, $port, $cmd) 
//function cutil_exec_cmd($cmd, $flag = false, $ip = "127.0.0.1", $TimeOut = 10)
{
	$flag = false;
    $head_flag = 'system_no_wait';
    if ($flag) {
        $head_flag = 'system_wait';
    }
    $len = 4 + 32 + strlen($cmd);
    $tlen = 32 - strlen($head_flag);
    $cmdtype = $head_flag . sprintf("%${tlen}s", "");
    $head = pack("L", $len) . $cmdtype;
    $bufOut = "";
    $buf = $head . $cmd;
    $port = 36532;

    if (!$sock = @socket_create(AF_INET, SOCK_DGRAM, 0))
        throw new Exception("socket create failure", -2);

    $timeout = array(
        'sec' => $TimeOut,
        'usec' => 100000
    );
    socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout);

    if (!@socket_sendto($sock, $buf, $len, 0, $ip, $port)) {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        throw new Exception("socket sendto failure: [$errorcode] $errormsg IP:[$ip] Port:[$port] buf:[$buf]", -2);
    }

    if ($flag) {
        if (!(@$recret = socket_recvfrom($sock, $buf, 64 * 1024, MSG_WAITALL, $ip, $port))) {
            $buf = "ִ�г�ʱ!"; // timeout val
            //echo "$recret\n";
        }
    }

    @socket_close($sock);
    return $buf;
}

function sendSocketFunc($ip, $port, $buf) 
{
	
	if (!$sock = @socket_create(AF_INET, SOCK_DGRAM, 0))
		throw new Exception("socket create failure", - 2);

	$timeout = array(
			'sec' => 10,
			'usec' => 100000
			);
	socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout);

	if (!@socket_sendto($sock, $buf, strlen($buf), 0, $ip, $port)) {
		$errorcode = socket_last_error();
		$errormsg = socket_strerror($errorcode);
		throw new Exception("socket sendto failure: [$errorcode] $errormsg IP:[$ip] Port:[$port] buf:[$buf]", - 2);
	}

	if (!(@$recret = socket_recvfrom($sock, $buf, 4, 0, $ip, $port))) {
		$buf = "10000"; // timeout val
	}
	@socket_close($sock);
	
	return $buf;
	
}



function MailAuth($user, $pass)
{

	return true;

	$pass = sha1(iconv("GBK", "UCS-2", $pass));

	$ret = file_get_contents("http://mail.infogo.com.cn/AuthUserByEmail.php?key=infogo&user=$user&password=$pass");


	if(strstr($ret, "SUCCESS"))
	{
		return true;

	}
	else
	{
		return false;
	}
}

function cutil_sys_cmd($cmd)
{
//	echo "<br>$cmd<br>";
	 return sendSocketFunc("127.0.0.1", '36532', $cmd);
}

function getMd5Name($fileName)
{
	$name = basename($fileName);
	$name2 = str_replace(".exe.rar",".exe",$name);	
	$name2 = str_replace(".exe.zip",".exe",$name2);	
	$name2 = md5($name2);
	return $name2;
}

function getShortName($fileName)
{
	// ASMPatch_v5.2.6038.3506_20150911_�豸�޷�ע������.exe.rar
	if(strstr($fileName, "ASMPatch_v5.2.603"))
	{
		return substr($fileName,24, -8);
	}
	else
	{
		return $fileName;
	}
}


//echo urlencode2("aaa/����/���");
//exit();
function byteFormat($bytes, $unit = "", $decimals = 2)
{ 
		$units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8); 
		
		$value = 0; 
		if ($bytes > 0) { 
		// Generate automatic prefix by bytes 
		// If wrong prefix given 
		if (!array_key_exists($unit, $units)) { 
		$pow = floor(log($bytes)/log(1024)); 
		$unit = array_search($pow, $units); 
		} 
		
		// Calculate byte value by prefix 
		$value = ($bytes/pow(1024,floor($units[$unit]))); 
		} 
		
		// If decimals is not numeric or decimals is less than 0 
		// then set default value 
		if (!is_numeric($decimals) || $decimals < 0 ) 
		{ 
			$decimals = 2; 
		} 
		if($unit == 'B')
			$decimals=0;

// Format output 
	return sprintf('%.' . $decimals . 'f '.$unit, $value); 
} 


function checkLogin()
{
	if(isset( $_SESSION['username']))
	{
		return true;
	}
	else
	{
		return false;
	}
}


function getpatchreadme($file, &$buguser, &$exename, &$fileobj)
{
	$ret = "";
	/* ����250M����ʾ */
	if(filesize($file) > 262144000)
		return $ret;
	
	if( (strstr($file, "ASMPatch") || strstr($file, "SMPPatch") ) && strstr($file, ".exe"))
	{
			
		$name2 = getMd5Name($file);
		$fileobj["md5"] = $name2;

		if(!file_exists("/var/www/html/_dir/patchreadme/$name2")
			|| !file_exists("/var/www/html/_dir/patchreadme/$name2/flist")
			|| 		(	strstr($file, "newasmpublish")
					&& !strstr($file, "v5.2.6037.1925")
					&& !strstr($file, "v5.2.6037.1642")
					&& (!file_exists("/var/www/html/_dir/patchreadme/$name2/Readme.txt") && !file_exists("/var/www/html/_dir/patchreadme/$name2/�������.txt"))
					)

			)
		{



			shell_exec("rm -fr /var/www/html/_dir/patchreadme/$name2");
			sendSocketFunc("127.0.0.1", '36532', "rm -fr /var/www/html/_dir/patchreadme/$name2");
			mkdir("/var/www/html/_dir/patchreadme/$name2");
		//	echo "/var/www/html/_dir/patchreadme/$name2\n";
		//	shell_exec("/var/www/html/_dir/patchreadme/$name2");
			//echo $cmd."\n";
			//exit();
			$cmd="rar x -pinfogo $file -y  /var/www/html/_dir/patchreadme/$name2 2>&1 >/dev/null";
			//$d = shell_exec($cmd);

			sendSocketFunc("127.0.0.1", '36532', $cmd);

			shell_exec("ls /var/www/html/_dir/patchreadme/$name2/ | grep .exe > /var/www/html/_dir/patchreadme/$name2/exename");

			$cmd = "/root/showpatch.sh /var/www/html/_dir/patchreadme/$name2";

			sendSocketFunc("127.0.0.1", '36532', $cmd);
			// sleep(1);
			//echo $d."\n";

			//$cmd = "cp /root/showpatch.sh /var/www/html/_dir/showpatch1.sh";
			//sendSocketFunc("127.0.0.1", '36532', $cmd);
		}

		file_put_contents("/var/www/html/_dir/patchreadme/$name2/name", $fileobj["name"]);
		$readme="/var/www/html/_dir/patchreadme/$name2/�������.txt";


		if(file_exists("/var/www/html/_dir/patchreadme/$name2/exename"))
		{
			$exename = file_get_contents("/var/www/html/_dir/patchreadme/$name2/exename");
			$exename = substr($exename, 33);
		}


		if(file_exists("/var/www/html/_dir/patchreadme/$name2/Readme.txt"))
		{
			$ret = file_get_contents("/var/www/html/_dir/patchreadme/$name2/Readme.txt");
			$buguser = GetSubStr($ret, "������Ա: ", "\r\n");
		}
		else if( file_exists("/var/www/html/_dir/patchreadme/$name2/�������.txt"))
		{
			$ret = file_get_contents("/var/www/html/_dir/patchreadme/$name2/�������.txt");

		}
		else if(file_exists("/var/www/html/_dir/patchreadme/$name2/�������.doc"))
		{
			$ret = "&nbsp;&nbsp;<a target=\"_blank\" href=\""."/_dir/patchreadme/".urlencode($name2). "/".urlencode("�������.doc")."\">DOC</a>";
		}
		else if(file_exists("/var/www/html/_dir/patchreadme/$name2/�������.docx"))
		{
			$ret = "&nbsp;&nbsp;<a target=\"_blank\" href=\""."/_dir/patchreadme/".urlencode($name2). "/".urlencode("�������.docx")."\">DOC</a>";
		}
		else
		{

			$ret="&nbsp;";//."/var/www/html/_dir/patchreadme/$name2/";
		}

		//20180416 add by baiyf, ����������Ƿ���Ҫ�������ݿ⣬˫����������Ϣ
		$upversion_file = "/var/www/html/_dir/patchreadme/$name2/patch_tmp/files/upversion.ini";
		$upversion_info = "";
		if(file_exists($upversion_file))
		{
			$upversion_config = parse_ini_file($upversion_file);
			if($upversion_config !== false)
			{
				if(array_key_exists('isbackupdb', $upversion_config))
				{
					$upversion_info = "�������ݿ�: ".(($upversion_config['isbackupdb'] == 1)?"��":"��");
					$upversion_info .= "<br>";
				}
				else
				{
					if(array_key_exists('backupdb', $upversion_config))
					{
						$upversion_info = "�������ݿ�: ".(($upversion_config['backupdb'] == 1)?"��":"��");
						$upversion_info .= "<br>";
					}
				}

				if(array_key_exists('isrestartservice', $upversion_config))
				{
					$upversion_info .= ("�������з���: ".(($upversion_config['isrestartservice'] == 1)?"��":"��"));
					$upversion_info .= "<br>";
				}
				else
				{
					if(array_key_exists('restartservice', $upversion_config))
					{
						$upversion_info .= ("�������з���: ".(($upversion_config['restartservice'] == 1)?"��":"��"));
						$upversion_info .= "<br>";
					}
				}
			}
		}
		$upversion_file = "/var/www/html/_dir/patchreadme/$name2/patch_tmp/config.ini";
		if(file_exists($upversion_file))
		{
			$config = parse_ini_file($upversion_file);
			if($config !== false)
			{
				if(array_key_exists('EnableUpdateOnHa', $config))
				{
					$upversion_info .= ("����˫��ʱ����: ".(($config['EnableUpdateOnHa'] == 1)?"��":"��"));
					$upversion_info .= "<br>";
				}
			}
		}

		$upversion_file = "/var/www/html/_dir/patchreadme/$name2/patch_tmp/�汾��.txt";
		if(file_exists($upversion_file))
		{
			$file_content = file_get_contents($upversion_file);
			if($file_content)
			{
				$upversion_info .= ("�����豸: ".(strstr($file_content, "(Ҫ������)")?"��":"��"));
				$upversion_info .= "<br>";
			}
		}

		if(strlen($upversion_info) > 0)
		{
			$ret = str_replace("��Ҫ����", $upversion_info."��Ҫ����", $ret);
		}

		$ret =str_replace("\r\n","<br>",$ret);
		$ret =str_replace("\n","<br>",$ret);

			
	}

	return $ret;
	
}


/**
 * getDir()ȥ�ļ����б�getFile()ȥ��Ӧ�ļ���������ļ��б�,���ߵ����������ж���û�С�.����׺���ļ���������һ��
 */

//��ȡ�ļ�Ŀ¼�б�,�÷�����������
function getDir($dir) {
	$dirArray='';
	if (false != ($handle = opendir ( $dir )))
	{
		while ( false !== ($file = readdir ( $handle )) )
		 {
			//ȥ��"��.������..���Լ�����.xxx����׺���ļ�
			if ($file != '.' && $file != '..')
			{
				$f='';
				
				$f['path'] = $dir.'/'.$file;
				$f['name']=$file;
				
				$f['md5Path'] = getMd5Name($file);
				
				$f['user']='-';
				$f['exename']='-';
				if(is_dir($f['path']))
				{
					$f['isdir']=true;
					$f['desc']='';
					$f['ico'] = '/icons/folder.gif';
					$f['size2']='-';
				}
				else
				{
					$f['isdir']=false;
					$f['desc'] = getpatchreadme($f['path'],$f['user'],$f['exename'], $f);
					$f['ico'] = '/icons/compressed.gif';
					$f['size'] = filesize($f['path']);
					$f['size2'] = byteFormat($f['size'] );
				}
				$f['link'] = urlencode($f['name']);
				$f['ext'] = pathinfo($f['path'], PATHINFO_EXTENSION);
				$f['etime'] = date ('Y-m-d H:i:s', filemtime($f['path']));

				// luozh add date attribute to sort
				$shortName = getShortName($file);
				$makeDate = substr($shortName, 0, 13);
				$f['date'] = $makeDate;
				$dirArray[]=$f;
			}
		}
		//�رվ��
		closedir ( $handle );
	}
	return $dirArray;
}

/**
 * ��ȡ��ά�����е�ĳһ��.
 * �÷���array_columnһ�£�ֻ��5.3�汾��û�и÷���
 *
 * @param array $array ����Ϊ��ά���ϵ����飬�Ҷ�ά���������������
 * @param mixed $arrayKey
 * @param mixed $indexKey
 *
 * @return array|bool
 */
function arrayColumn($array, $arrayKey, $indexKey = null)
{
    if (!is_array($array) || !$arrayKey) {
        return false;
    }

    if (PHP_VERSION > '5.3.3') {
        return array_column($array, $arrayKey, $indexKey);
    }

    $result = array();
    foreach ($array as $key => $value) {
        if (!is_array($value)) {
            return false;
        }
        if (!isset($value[$arrayKey])) {
            return false;
        }
        $newKey = $indexKey ? $value[$indexKey]: $key;
        $result[$newKey] = $value[$arrayKey];
    }

    return $result;
}
?>
