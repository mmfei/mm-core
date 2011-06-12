<?php
class Database extends DatabaseBase
{
	/**
	 * 根据条件获取一行数据
	 * 
	 * @param string $table		表名
	 * @param array $arrSelect	选择列
	 * @param array $arrWhere	条件
	 * @return array			一条记录
	 */
	public static function GetRowBy($table , array $arrSelect = null ,array $arrWhere = null)
	{
		$fields = isset($arrSelect) ? join(',' , $fields) : '*';
		$where = isset($arrWhere) ? ' Where ' .join(' And ',$arrWhere) : '';
		$sql = 'Select '.$fields.' From '.$table .$where;
		$rows = self::Query($sql);
		if($rows)
		{
			return $rows[0];
		}
		else 
		{
			return null;
		}
	}
	/**
	 * 获取数据列表
	 * @param string $table				表名
	 * @param string $key				返回数组的索引的字段key名称
	 * @param array $arrSelect			选择列
	 * @param array $arrWhere			条件 , And连接
	 * @param array $arrOrder			排序规则 , 如 : col1 desc
	 * @param integer $page				当前页
	 * @param integer $pageSize			每页显示多少记录
	 * @param integer $count			记录总数
	 * @return array(
	 * 		key => array(),
	 * 		key => array(),
	 * 		key => array(),
	 * 		...
	 * )
	 */
	public static function GetListBy($table , $key = null , array $arrSelect = null ,array $arrWhere = null , array $arrOrder = null , $page = null , $pageSize = 10 , &$count = null)
	{
		$fields = isset($arrSelect) ? join(',' , $fields) : '*';
		$where = isset($arrWhere) ? ' Where ' .join(' And ',$arrWhere) : '';
		$order = isset($arrOrder) ? ' Order By '.join(',',$arrOrder) : '';
		$start = max(($page - 1) * $pageSize , 0);
		$limit = isset($page) ? ' Limit '. $start .' , '.$pageSize : '';
		if(isset($count))
		{
			$sql = 'Select count(1) as c From '.$table .$where;
			$row = self::Query($sql);
	
			if($row)
			{
				$count = $row[0]['c'];
			}
			else 
			{
				$count = 0;
			}
		}
		else
		{			
			$count = true;
		}
		$data = array();
		if($count)
		{
			$sql = 'Select '.$fields.' From '.$table .$where.$order.$limit;
			$data = self::Query($sql , $key);
		}
		return $data;
	}
	/**
	 * 根据条件更新
	 * 
	 * @param string $table		表名
	 * @param array $data		更新数据 array(字段名=>需要更新的数值,...)
	 * @param array $arrWhere	更新条件
	 * @return integer			影响行数
	 */
	public static function UpdateBy($table ,array $data , array $arrWhere = null)
	{
		$set = '';
		$flag = '';
		foreach($data as $key => $value)
		{
			$set.= $flag . $key . ' = \''.$value.'\'';
			$flag = ' , ';
		}
		$where = isset($arrWhere) ? ' Where ' .join(' And ',$arrWhere) : '';
		$sql = 'Update '.$table.' Set '.$set.$where;
		return self::Execute($sql);
	}
	/**
	 * 累加
	 * 
	 * @param string $table			表名
	 * @param array $data			更新数据 array(字段名=>需要更新的数值,...)
	 * @param array $arrWhere		更新条件
	 * @param boolean $notMinus		是否允许负数
	 * @return integer
	 */
	public static function IncrementBy($table , array $data , array $arrWhere = null , $notMinus = false)
	{
		$set = '';
		$flag = '';
		foreach($data as $key => $value)
		{
			if($notMinus)
				$set.= $flag . $key . ' = '.$key.' + '.$value;
			else 
				$set.= $flag . $key . ' = Case When '.$key.' + '.$value.' > 0 Then '.$key.' + '.$value.' Else 0 End';
			$flag = ' , ';
		}
		$where = isset($arrWhere) ? ' Where ' .join(' And ',$arrWhere) : '';
		$sql = 'Update '.$table.' Set '.$set.$where;
		return self::Execute($sql);
	}
	/**
	 * 根据条件删除
	 * @param string $table		表名
	 * @param array $arrWhere	更新条件
	 * @return integer			影响行数
	 */
	public static function DeleteBy($table , array $arrWhere = null)
	{
		$where = isset($arrWhere) ? ' Where ' .join(' And ',$arrWhere) : '';
		$sql = 'Delete From '.$table.$where;
		return self::Execute($sql);
	}
	/**
	 * 插入一条记录
	 * @param string $table		表名
	 * @param array $data		更新数据 array(字段名=>需要更新的数值,...)
	 * @param integer $insertId	返回的自增列ID
	 * @return integer			影响行数
	 */
	public static function InsertBy($table , array $data , &$insertId = null)
	{
		$fields = join(',' , array_keys($data));
		$values = $char = '';
		foreach($data as $key => $value)
		{
			$values .= $char . '\''.mysql_escape_string($value).'\'';
			$char = ',';
		}
		$sql = 'Insert Into '.$table.'('.$fields.') values('.$values.')';
		$affect = self::Execute($sql);
		if($affect)
		{
			$insertId = self::GetInsertId();
			return $affect;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 插入一条记录
	 * @param string $table		表名
	 * @param array $data		更新数据 array(字段名=>需要更新的数值,...)
	 * @param integer $insertId	返回的自增列ID
	 * @return integer			影响行数
	 */
	public static function InsertIgnoreBy($table , array $data , &$insertId = null)
	{
		$fields = join(',' , array_keys($data));
		$values = $char = '';
		foreach($data as $key => $value)
		{
			$values .= $char . '\''.mysql_escape_string($value).'\'';
			$char = ',';
		}
		$sql = 'Insert Ignore Into '.$table.'('.$fields.') values('.$values.')';
		$affect = self::Execute($sql);
		if($affect)
		{
			$insertId = self::GetInsertId();
			return $affect;
		}
		else
		{
			return 0;
		}
	}
	
}