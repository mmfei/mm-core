<?php
/**
 * 数据库处理类
 * 			注意:此类依赖一个全局变量
 * 			$mmConfig = array(
 * 				'host'	=>	'数据库连接(数组/域名)',
 * 				'port'	=>	'数据库端口',
 * 				'user'	=>	'数据库用户',
 * 				'dbName'=>	'数据库名称',
 * 				'pass'	=>	'数据库密码',
 * 				'charset'=>	'数据库编码',#可选
 * 			)
 * 
 * @author mmfei<wlfkongl@163.com>
 */
class DatabaseBase
{
	static $sql = array();
	/**
	 * 获取数据配置
	 */
	private static function GetInstance()
	{
		static $db = null;
		if(isset($db)) return $db;
		$mmConfig = isset($GLOBALS['mmConfig']['db']) ? $GLOBALS['mmConfig']['db'] : null;
		if(!isset($mmConfig))
		{
			die('数据库配置错误');
		}
		$host = $mmConfig['host'].($mmConfig['port'] ? ':'.$mmConfig['port'] : '');
		try{
			$db = mysql_connect($host , $mmConfig['user'] , $mmConfig['pass']);
			if($db)
			{
				mysql_select_db($mmConfig['dbName'] , $db);
				if(isset($mmConfig['charset']) && $mmConfig['charset'])
				{
					self::Execute('set names '.$mmConfig['charset']);
				}
			}
		}
		catch(Exception $e)
		{
			die('Mysql connection faile!');
		}
		return $db;
	}
	/**
	 * 查询
	 * 
	 * @param string $sql				查询语句
	 * @parma string $key				返回数组下标的字段名
	 * @param boolean $formatToObject	是否返回对象列表
	 * @return array(
	 * 		array(...),#如果$formatToObject为真,这里返回的是对象
	 * 		array(...),#如果$formatToObject为真,这里返回的是对象
	 * 		array(...),#如果$formatToObject为真,这里返回的是对象
	 * 		...
	 * )
	 */
	public static function Query($sql , $key = null ,  $formatToObject = false)
	{
		$data = array();
		self::$sql[] =$sql;
		$result = mysql_query($sql , self::GetInstance());
		if(!$result)
		{
			throw new Exception(mysql_error());
		}
		if($result)
		{
			if($formatToObject)
			{
				while($row = mysql_fetch_object($result))
				{
					if(isset($key) && isset($row[$key]))
					{
						$data[$row[$key]] = $row;
					}
					else 
					{
						$data[] = $row;
					}
				}
			}
			else 
			{
				while($row = mysql_fetch_assoc($result))
				{
					if(isset($key) && isset($row[$key]))
					{
						$data[$row[$key]] = $row;
					}
					else 
					{
						$data[] = $row;
					}
				}
			}
		}
		mysql_free_result($result);
		return $data;
	}
	/**
	 * 执行sql
	 * 
	 * @param string $sql
	 * @return integer
	 */
	public static function Execute($sql)
	{
		self::$sql[] =$sql;
		if(!mysql_query($sql , self::GetInstance()))
		{
			throw new Exception(mysql_error());
		}
		return self::GetAffectedRow();
	}
	/**
	 * 获取影响行数
	 * @return integer
	 */
	public static function GetAffectedRow()
	{
		return mysql_affected_rows(self::GetInstance());
	}
	/**
	 * 获取上次插入的自增列ID
	 * @return integer
	 */
	public static function GetInsertId()
	{
		return mysql_insert_id(self::GetInstance());
	}
}