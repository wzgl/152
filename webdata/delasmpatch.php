
<?php


//$_REQUEST["mailuser"]
//var_dump($_REQUEST);
$user=$_REQUEST["mailuser"];
$pass=$_REQUEST["mailpass"];
$file=$_REQUEST["delfilename2"];
$filepath=$_REQUEST["delfilepath"];


function sendSocketFunc($ip, $port, $buf) {
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

/*
if(!strstr($filepath, "/webdata/"))
{
	$filepath="";
	$pass="";
}
$filepath = str_replace("..", ".");
*/

$pass = sha1(iconv("GBK", "UCS-2", $pass));

//$ret = file_get_contents("http://mail.infogo.com.cn/AuthUserByEmail.php?key=infogo&user=$user&password=$pass");
$ret = "SUCCESS";
echo "<script>";
if(1 or strstr($ret, "SUCCESS"))
{
	//echo "ok";
	//unlink ($filepath);
	$dir = dirname("/var/www/html/del_patch$filepath");
	sendSocketFunc("127.0.0.1", '36532', "mkdir -p $dir && mv -f /var/www/html$filepath /var/www/html/del_patch$filepath");
	//echo "rm -f $filepath";
	
	// É¾³ý»º´æÎÄ¼þ
	$name = basename($filepath);
	$name2 = str_replace(".exe.rar",".exe",$name);	
	$name2 = str_replace(".exe.zip",".exe",$name2);	
	$name2 = md5($name2);
	shell_exec("rm -fr /var/www/html/_dir/patchreadme/$name2");
	
	file_put_contents("del_file_log.php", date("F j, Y, g:i a"). "\tÓÃ»§[$user]É¾³ý[$filepath]\n", FILE_APPEND);
	echo "alert('É¾³ý[$file]³É¹¦!');"; 
	echo "parent.location.reload();";
	
	
}
else
	echo "alert('ÓÊÏäÃÜÂë´íÎó!');";
	
	echo "</script>";
?>
