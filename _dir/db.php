<?php
	
	error_reporting ( E_ALL );

	// 报告所有 PHP 错误
	error_reporting (- 1 );

	// 和 error_reporting(E_ALL); 一样
	ini_set ( 'error_reporting' ,  E_ALL );



	class DBCLASS 
	{
	
		 public $conn;
		 public $mysql_database="ASM_Update"; // 数据库的名字
		 public function __construct() {
			
			 $mysql_server_name="localhost"; //数据库服务器名称
			$mysql_username="root"; // 连接数据库用户名
			$mysql_password="InfogoAsmPass168"; // 连接数据库密码
			
			
			 // 连接到数据库
			$this->conn=mysql_connect( $mysql_server_name, $mysql_username,
							$mysql_password);
							
			mysql_select_db($this->mysql_database,$this->conn);  
			mysql_set_charset("gbk");
		}
		
	   
		public function getFetch(&$sql)
		{
		
			
			 $result=mysql_db_query($this->mysql_database , $sql, $this->conn);
			
			$row=mysql_fetch_array($result, MYSQL_ASSOC);
		
			mysql_free_result ( $result );
			
			 return $row;
		}
	   
		
		
		public function command22(&$sql)
		{
		
			echo $sql;
			// $result=mysql_query( $sql, $this->conn);
			

			
			//mysql_free_result ( $result );
			
		}
		
		public function getFetchAll(&$sql)
		{
		
			
			 $result=mysql_db_query($this->mysql_database , $sql, $this->conn);
	
			$rowA = array();
			
			while($row=mysql_fetch_array($result, MYSQL_ASSOC))
			 {
				$rowA[]=$row;
				
			 }
			mysql_free_result ( $result );

			return $rowA;
		}
		
		public function exec($sql)
		{
			
			$result=mysql_db_query($this->mysql_database , $sql, $this->conn);
			mysql_free_result ( $result );
			//echo $sql;
		}
                        
	}
	
	$DB = new DBCLASS ();
	   // 从表中提取信息的sql语句

	   
	echo 111;  
	$DB->exec("insert into TUpdateFile (UpdateName,UpdateFile,Md5Path) values ('111', '222', '333')");
	//var_dump($DB->getFetchAll);
//	$DB->command22("insert into TUpdateFile (UpdateName,UpdateFile,Md5Path) values ('111', '222', '333')");
	
	
	
	$strsql="SELECT * FROM `TUpdateFile`";
    // 执行sql查询
   

	
    $row=$DB->getFetchAll($strsql);
	
	var_dump($row);
?>