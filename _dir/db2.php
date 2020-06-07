<?php

echo 111;
error_reporting(E_ALL);
//include_once("function.php");
//define ( "SQL", "|:::|" );
echo 111;

class DBCLASS extends PDO {

    public $_hostname; // 数据库地址
    private $_dbname = ""; // 数据库名称
    private $_username = ""; // 用户名
    private $_password = ""; // 密码
    private $_charset = "gbk"; // 编码方式
    private $debug=false;
    static public $langArr = array(
       
// *** Multi-language configuration array start ***
'zh' => array(
'linkError' => '对不起,数据库连接错误,请联系管理员或技术支持！',
'optError1' => '对不起,您本次交易操作失败,错误流水号:【',
'optError2' => '】,请联系管理员或技术支持!',
'p_3_d' => '协同服务器连接失败,请检查协同服务器运行是否正常！'
),
'en' => array(
'linkError' => 'Sorry, database connection error, please contact the administrator or technical support!',
'optError1' => 'Sorry, the transaction fails, the error serial number: [',
'optError2' => '】, Please contact your administrator or technical support!'
)
// *** Multi-language configuration array end ***
    );
    private $_Forbit = array(' and if(Length(', ' or if(Length(', "ID = '999999.9'", "ID = 999999.9", ' union all select 0x', ' select concat(0x7e,0x27,database(),0x27,0x7e)', ' and if(exists(select', 'SELECT statements have a different number of columns', '`information_schema`', 'concat((select (select (select concat', 'select null,', ' or exists(select', 'ascii(substring', 'database()'); //防止SQL注入

    /**
     * 连接数据库
     */

    public function __construct() {
        
        global $gLang;
        $langConfFile = "/etc/version.ini";
		$gLang = "zh";
       

        $this->_hostname ="127.0.0.1";

        try {
           // $DBInfo = parse_ini_file('/etc/AsmDb.ini');
            $this->_dbname = "ASM_Update"
            $this->_username = "root";
            $this->_password = "InfogoAsmPass168";

            parent::__construct("mysql:host=$this->_hostname;dbname=$this->_dbname", $this->_username, $this->_password, 
            array(
            	PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true
            ));
            //try{self::$instance = 
            //new PDO("mysql:host=localhost;dbname=myDB;charset=utf8",'myUsername','myPassword',array(PDO::ATTR_EMULATE_PREPARES=>false,PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true,PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC));}
           //, array(                PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true,PDO::ATTR_EMULATE_PREPARES=>false            )
        }
        catch (Exception $e) 
        {
            $path_dblb = "/etc/dblb.ini";
            if(file_exists($path_dblb))
            {
        		$aDbLB = parse_ini_file($path_dblb);
				$aLink = shell_exec("/asm/sbin/asmnmap -n ".$aDbLB['DBLBIp']." -p 3306 | grep open");
				if(stristr($aLink,"open")===false)
				{
					echo "<script>art.dialog.alert(".LG(DBCLASS::$langArr, 'p_3_d').");</script>";
					throw new Exception(LG(DBCLASS::$langArr, 'p_3_d'), - 2);
				}
            }
        	throw new Exception(LG(DBCLASS::$langArr, 'linkError') . trim(strstr($cfg, "devtype=asc")), - 2);
        }
        $this->query("set names " . $this->_charset);
    }

    /**
     * 处理一条SQL语句，并返回影响行数
     * 
     * @return $affectRows 执行SQL语句影响的行数
     *        
     */
    public function command($sql, $debugs = false) {
        $sql = str_ireplace($this->_Forbit, "", $sql);
        if (($affectRows = $this->exec($this->formatSql($sql))) === false) {
            $db_date_list = "100".time();
            //			cutil_php_log ( "###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n###错误信息:" . var_export ( $this->errorInfo (), true ) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", "/tmp/logs/db_debug" );
            file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
            throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
        }

        if ($debugs == true || $this->debug == true) {
            file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
        }
        return $affectRows;
    }

    // 处理一条SQL语句，并返回一个PDOStatement
    public function queryfunc($sql, $debugs = false) {
        $sql = str_ireplace($this->_Forbit, "", $sql);
        if (!($query = $this->query($this->formatSql($sql)))) {
            $db_date_list = "100".time();;
            file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
            throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
        }
        if ($debugs == true || $this->debug == true) {
            file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
        }
        return $query;
    }

    // 获取单条记录
    public function getFetch($sql, $debugs = false) {
        $sql = str_ireplace($this->_Forbit, "", $sql);
        try {
            if ($debugs == true || $this->debug == true) {
                file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
            }
            $query = $this->queryfunc($sql);
            $query->setFetchMode(parent::FETCH_ASSOC);
            return $query->fetch();
        } catch (Exception $e) {
            $db_date_list = "100".time();;
            file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
            throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
        }
    }

    // 获取所有记录
    public function getFetchAll($sql, $debugs = false) {
        $sql = str_ireplace($this->_Forbit, "", $sql);
        try {
            if ($debugs == true || $this->debug == true) {
                file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
            }

            $query = $this->queryfunc($sql);
            $query->setFetchMode(parent::FETCH_ASSOC);
            return $query->fetchAll();
        } catch (Exception $e) {
            $db_date_list = "100".time();;
            file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
            // echo "失败";
            throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
        }
    }

    // 获取记录总数select count(*)语句
    public function getFetchColumn($sql, $debugs = false) {
        $sql = str_ireplace($this->_Forbit, "", $sql);
        try {
            if ($debugs == true || $this->debug == true) {
                file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
            }
            $query = $this->queryfunc($sql);
            return $query->fetchColumn();
        } catch (Exception $e) {
            $db_date_list = "100".time();;
            file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
            throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
        }
    }    
    /**
     * 执行获取一条记录信息的命令
     * @param sql语句 $sql
     * @param 参数数组 $aInfo
     * @param $debug 默认: false
     * @return 获取到的所有的数据记录
     * */
    public function getFetch2(&$sql,$aInfo="")
    {
    	$this->setAttribute(PDO::ATTR_PERSISTENT, false);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    	$db_date_list = "100".time();;
    	if($sql=="")
    	{
    		file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
    		throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
    	}
    	if ($this->debug == true) 
    	{
			file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
        }
        try {
        $numargs = func_num_args();
		if($numargs>2)
		{
			for($i = 2; $i< $numargs ;$i++)
			{
				//var_dump(func_get_arg($i));
				$tmp_arg = func_get_arg($i);
				$aInfo = array_merge($aInfo, $tmp_arg);
			}
		}
			$aSqlInfo = $this->FuncSqlLog($sql,$aInfo);
            $query = $this->prepare($sql);
            if(!$query)
            {
		        throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
            }
	    	if(is_array($aInfo))
				$isR = $query->execute($aSqlInfo['Info']);
			else 
				$isR = $query->execute();
			return  $query->fetch(PDO::FETCH_ASSOC);
        }
		catch (Exception $e){
			$this->funcWLog($sql,$aInfo);
		}
    }
	/**
	 * 执行获取一条记录信息的命令
     * @param sql语句 $sql
     * @param 参数数组 $aInfo
     * @param $debug 默认: false
     * @return 获取到的所有的数据记录 
     * */
    public function getFetchAll2(&$sql,$aInfo="")
    {
    	$this->setAttribute(PDO::ATTR_PERSISTENT, false);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    	$db_date_list = "100".time();;
    	if($sql=="")
    	{
    		$this->funcWLog($sql,$aInfo);
    	}
    	if ($this->debug == true)
    	{
			file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
        }
        try {
        	$numargs = func_num_args();
			if($numargs>2)
			{
				for($i = 2; $i< $numargs ;$i++)
				{
					$tmp_arg = func_get_arg($i);
					$aInfo = array_merge($aInfo, $tmp_arg);
				}
			}
			$aSqlInfo = $this->FuncSqlLog($sql,$aInfo);
            $query = $this->prepare($sql);
            if(!$query)
            {
		        throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
            }
			if(is_array($aInfo))
				$isR = $query->execute($aSqlInfo['Info']);
			else 
				$isR = $query->execute();
			$sql = $aSqlInfo['sql'];
			return  $query->fetchAll(PDO::FETCH_ASSOC);
        }
		catch (Exception $e){
			$this->funcWLog($sql,$aInfo);
		}
    }
    /**
     * 执行update,insert 语句
     * @param sql语句 $sql
     * @param 参数数组 $aInfo
     * @param $debug 默认: false 是否记录日志
     * @return update:更新的记录数 insert: 本次插入的记录ID
     * */
    public function command2(&$sql,$aInfo="")
    {
    	$this->setAttribute(PDO::ATTR_PERSISTENT, false);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    	$db_date_list = "100".time();
    	if($sql=="")
    	{
    		$this->funcWLog($sql,$aInfo);
    	}
    	if ($this->debug == true) 
    	{
			file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
        }
        try {
        	$numargs = func_num_args();
			if($numargs>2)
			{
				for($i = 2; $i< $numargs ;$i++)
				{
					$tmp_arg = func_get_arg($i);
					$aInfo = array_merge($aInfo, $tmp_arg);
				}
			}
			$aSqlInfo = $this->FuncSqlLog($sql,$aInfo);
            $query = $this->prepare($sql);
            if(!$query)
            {
		        throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
            }
			if(is_array($aInfo))
				$isR = $query->execute($aSqlInfo['Info']);
			else 
				$isR = $query->execute();
			if($isR===false)
			{
				throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
			}
			$sql = $aSqlInfo['sql'];
			if(stristr(trim($sql),"insert ")!==false&&stristr(trim($sql)," into ")!==false)
				return $this->lastInsertId();
			else 
				return $query->rowCount();
        }
		catch (Exception $e){
			$this->funcWLog($sql,$aInfo);
		}
    }
        // 获取记录总数select count(*)语句
    public function getFetchColumn2(&$sql,$aInfo="") {
        $this->setAttribute(PDO::ATTR_PERSISTENT, false);
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        try {
            if ($this->debug == true) {
                file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n" . $sql . "\n>>>>>>>>>>>>>>>>>调试语句<<<<<<<<<<<<\n", 8);
            }
            $numargs = func_num_args();
			if($numargs>2)
			{
				for($i = 2; $i< $numargs ;$i++)
				{
					$tmp_arg = func_get_arg($i);
					$aInfo = array_merge($aInfo, $tmp_arg);
				}
			}
			$aSqlInfo = $this->FuncSqlLog($sql,$aInfo);
            $query = $this->prepare($sql);
            if(!$query)
            {
		        throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
            }
			if(is_array($aInfo))
				$isR = $query->execute($aSqlInfo['Info']);
			else 
				$isR = $query->execute();
			$sql = $aSqlInfo['sql'];
			return  $query->fetchColumn();
        } catch (Exception $e) {
            $this->funcWLog($sql,$aInfo);
        }
    }
    private function funcWLog($sql,$aInfo)
    {
    	$db_date_list = "100".time();
        $aSqlInfo = $this->FuncSqlLog($sql,$aInfo);
        $sql = $aSqlInfo['sql'];
        file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
        throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
    }
    /**
    * 处理数组中的|:::| SQL
    * 重组sql 用于记录日志
    */
    private  function FuncSqlLog($sql,$aInfo)
    {
    	$sql = $this->formatSql($sql);
    	
    	if(is_array($aInfo))
    	{
    		$snum = substr_count($sql,'?');
    		$anum = count($aInfo);
    		foreach ($aInfo as &$item)
    		{
    			$item = str_ireplace("|:::|","",$item);
//    			if(strstr($item,"|:::|")===false)
//    				$item = "|:::|".$item."|:::|";
//    			$item = $this->formatSql($item);
				$pos = stripos($sql,"?");
				$ss = substr($sql,0,$pos+1);
				$ss_1 = $item==""?str_ireplace("?","''",$ss):str_ireplace("?","'".$item."'",$ss);
				$sql = str_ireplace($ss,$ss_1,$sql);
    		}
    		if($anum!=$snum&&$anum>0)
    		{
    			$db_date_list = "100".time();;
		        file_put_contents("/tmp/logs/db_debug-" . date("Ymd", time()) . ".log", date("Y-m-d H:i:s", time()) . "   ###错误流水号:【" . $db_date_list . "】错误sql语句:>>>" . $sql . "\n错误传递数组:" .var_export($aInfo,true). "\n" . date("Y-m-d H:i:s", time()) . "   ###错误信息:" . var_export($this->errorInfo(), true) . "\n\n>>>>>>>>>>>>>>>>>>######结束#####<<<<<<<<<<<<<<<\n\n", FILE_APPEND);
		        throw new Exception(LG(DBCLASS::$langArr, 'optError1') . $db_date_list . LG(DBCLASS::$langArr, 'optError2'), - 2);
    		}
    		
    	}
    	return array("sql"=>$sql,"Info"=>$aInfo);
    }
    /**
     * 自动生成where条件语句
     * 当条件不固定时使用
     * 如果变量为空 ，则不传递对应的列值
     * @param $aInfo = array("DeviceID"=>$adevice_id, //默认等于，可以不写
    						"DeviceID > ? "=>$adevice_id, // 大于小于类似
    						"instr(Content, ?)>0"=>$mac,//查找是否存在字符串
    						"IP like ? "=>"%".$ip."%"//like语句
    						);
     * @return $aWhere('whsql'=>" DeviceID=? AND DeviceID>? and instr(Content, ?)>0 and IP like ? ","wharr"=>array($IP,$deviceid,$mac,"%".$ip."%"));
     * */
    public function formatWhere($aInfo)
    {
    	$aWhere = false;
    	if(is_array($aInfo))
    	{
    		$sql_add = "";
    		foreach ($aInfo as $key => $item)
    		{
    			if(strlen(trim($item))>0)
    			{
    				$find_num = stripos($key,'?');
    				if($find_num==false)
    				{
    					$sql_add .= $sql_add ==""?" ".$key." = ? ":" and ".$key." = ? ";
    				}
    				else 
    					$sql_add .= $sql_add ==""?" ".$key." ":" and ".$key." ";
    				$aSql[] = $item;
    			}
    		}
    	}
    	if($sql_add!="")
    	{
	    	$aWhere['whsql'] = $sql_add;
	    	$aWhere['wharr'] = $aSql;
    	}
    	return $aWhere;
    }
        /**
     * 自动生成update条件语句
     * 当更新条件不固定时使用
     * 如果变量为空 ，则不传递对应的列值
     * @param $aInfo = array("IP"=>$IP,"DeviceID"=>$deviceid);
     * @return $aWhere('upsql'=>" IP=? , DeviceID=? ","uparr"=>array($IP,$deviceid));
     * */
    public function formatUpdate($aInfo)
    {
    	$aWhere = false;
    	if(is_array($aInfo))
    	{
    		$sql_add = "";
    		foreach ($aInfo as $key => $item)
    		{
    			if(strlen(trim($item))>0)
    			{
    				$sql_add .= $sql_add ==""?" ".$key." = ? ":" , ".$key." = ? ";
    				$aSql[] = $item;
    			}
    		}
    	}
    	if($sql_add!="")
    	{
	    	$aWhere['upsql'] = $sql_add;
	    	$aWhere['uparr'] = $aSql;
    	}
    	return $aWhere;
    }
    /**
     * * 自动格式化sql语句，自动转义sql语句中的特殊符号 **
     */
    public function formatSql($sql) {
        if (($pos = strpos($sql, SQL)) === false) {
            return $sql;
        }
        $str = substr($sql, $pos + strlen(SQL));
        $value = substr($str, 0, strpos($str, SQL));
        $remain_str = substr($str, strlen($value . SQL));
        $sql = str_ireplace($this->_Forbit, "", $sql);
        switch ($this->getSqlRelation($sql)) {   // 判断当前位置的逻辑条件
            case '=' :
            case ',' :
            case '(' :
            case ')' :
				if(strpos($value,"\\")!==false)
				{
					$value = iconv("GBK","UTF-8",$value);
                	$value = str_replace('\\', '\\\\', $value);
					$value = iconv("UTF-8","GBK",$value);
				}
                $value = str_replace('"', '\"', $value);
                $value = str_replace("'", "\'", $value);
                break;
            case 'like' :
                $value = str_replace('\\', '\\\\\\\\', $value);
                $value = str_replace('%', '\%', $value);
                $value = str_replace('"', '\"', $value);
                $value = str_replace("'", "\'", $value);
                $value = str_replace("_", "\_", $value);
                break;
        }

        $sql = substr($sql, 0, $pos) . $value . $remain_str;
        return $this->formatSql($sql);
    }

    public function getSqlRelation($sql) {  // 获取条件关系
        $pos = strpos($sql, SQL);
        $str = substr($sql, 0, $pos);
        for ($i = 1; $i <= $pos; $i++) {
            $s = substr($str, - $i, 1);
            switch ($s) {
                case '=' :
                    return '=';
                    break;
                case 'e' :
                    return 'like';
                    break;
                case ',' :
                    return ',';
                    break;
                case '(' :
                    return '(';
                    break;
                case ')' :
                    return ')';
                    break;
            }
        }
    }

}

echo 222;
$DB = new DBCLASS ();
?>