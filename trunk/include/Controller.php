<?php
/**
 * 全局控制类
 * 
 * @author mmfei<wlfkongl@163.com>
 */
class Controller
{
	/**
	 * 缓存变量
	 * @var array
	 */
	static $classCache = array();
	
	/**
	 * 控制转向插件
	 * 
	 * @param string $pluginName	插件名称
	 */
	public static function Action($pluginName)
	{
		$param = self::GetParam();
		$actionName = isset($param[1]) ? $param[1] : 'Index';
		return self::LoadPluginsAction($pluginName, $actionName);
	}
	/**
	 * 调用
	 * @param string $pluginName
	 * @param string $actionName
	 * @return mixed
	 */
	public static function LoadPluginsAction($pluginName , $actionName)
	{
		$reflectionClass = self::LoadPluginByName($pluginName);
		if($reflectionClass->hasMethod($actionName))
		{
			$reflectionMethod = $reflectionClass->getMethod($actionName);
			if ($reflectionMethod->isStatic()) {
	      		$s = $reflectionMethod->invoke(null);
			} else {
				$s = $reflectionMethod->invoke($reflectionClass->newInstance());
			}
			return $s;
		}
	}
	/**
	 * 调用 , 获取数据 , [注意:此方法从首次创建的缓存获取数据]
	 * @param string $pluginName	插件名称
	 * @param string $actionName	插件方法
	 * @param array $args			附加参数
	 */
	public static function GetDataByPluginsAction($pluginName , $actionName , array $args = null)
	{
		$data = self::_GetCache($pluginName, $actionName, $args);
		if($data)
		{
			return $data;
		}
		$reflectionClass = self::LoadPluginByName($pluginName);
		if($reflectionClass->hasMethod($actionName))
		{
			$reflectionMethod = $reflectionClass->getMethod($actionName);
			if ($reflectionMethod->isStatic()) {
				if(isset($args))
				{
	      			$data = $reflectionMethod->invokeArgs($args);
				}
				else 
				{
					$data = $reflectionMethod->invoke(null);
				}
			} else {
				if(isset($args))
				{
	      			$data = $reflectionMethod->invokeArgs($args);
				}
				else 
				{
					$data = $reflectionMethod->invoke(null);
				}
			}
			self::_SetCache($pluginName, $actionName, $args, $data);
			return $data;
		}
		return null;
	}
	/**
	 * 加载插件
	 * 
	 * @param string $pluginName	插件名称
	 * @return mixed
	 */
	public static function LoadPluginByName($pluginName)
	{
		$file = PLUGINS_DIR .'/'.$pluginName.'/'.$pluginName.'.php';
		
		if(file_exists($file))
		{
			include_once($file);
		}
		return new ReflectionClass($pluginName);
	}
	/**
	 * 获取参数
	 */
	public static function GetParam()
	{
		static $param;
		if($param) return $param;
		if(isset($_SERVER['PHP_SELF']))
		{
			$param = explode('/', preg_replace("/.*\.php/", '', $_SERVER['PHP_SELF']));
		}
		else 
		{
			$param = array();
		}
		if($param)
		{
			array_shift($param);
		}
		return $param;
	}
	/**
	 * 数组转换为url
	 * 
	 * @param array $arr
	 * @return string
	 */
	public static function ParamToUrl($arr)
	{
		preg_match("/(\w+.php)/", $_SERVER['PHP_SELF'] , $a);
		if($a)
			$file = '/'.$a[1];
		else 
			$file = '/index.php';
		if($arr)
			return $file.'/'.join('/', $arr);
		return $file;
	}
	/**
	 * 获取缓存的key
	 * @param string $pluginName
	 * @param string $actionName
	 * @param array $args
	 * @reutrn string
	 */
	public static function _GetKey($pluginName , $actionName , array $args)
	{
		return $pluginName . '_' . $actionName . '_' . json_encode($args);
	}
	/**
	 * 缓存数据
	 * @param string $pluginName
	 * @param string $actionName
	 * @param array $args
	 * @param mixed $data
	 * @return boolean
	 */
	public static function _SetCache($pluginName , $actionName , array $args , $data)
	{
		$key = self::_GetKey($pluginName, $actionName, $args);
		return self::$classCache[$key] = $data;
	}
	/**
	 * 获取缓存数据
	 * @param string $pluginName
	 * @param string $actionName
	 * @param array $args
	 * @return boolean
	 */
	public static function _GetCache($pluginName , $actionName , array $args)
	{
		$key = self::_GetKey($pluginName, $actionName, $args);
		return isset(self::$classCache[$key]) ? self::$classCache[$key] : null;
	}
}