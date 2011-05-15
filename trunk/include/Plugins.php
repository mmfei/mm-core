<?php
/**
 * 插件处理类
 * @author mmfei<wlfkongl@163.com>
 *
 */
class Plugins
{
	/**
	 * 所有激活的插件
	 * @var array
	 */
	static $arrActivedPlugins = array();
	/**
	 * 获取激活的插件列表
	 * @return array(
	 * 		'插件名称' => array(
	 * 			'pluginName'	=>	'插件名称',
	 * 			'instance'		=>	'插件实例',
	 * 		),
	 * 		...
	 * )
	 */
	public static function GetActivedPlugins()
	{
		if(self::$arrActivedPlugins) return self::$arrActivedPlugins;
		
		$dir = new DirectoryIterator(PLUGINS_DIR);
		$fileList = array();
		foreach ($dir as $obj)
		{
			if($obj->isDot() || $obj->isFile() || !Plugins::IsInstall($obj->getFilename())) continue;
			$fileList[$obj->getFilename()] = array(
				'pluginName' 	=> $obj->getFilename(),
				'instance'		=> Controller::LoadPluginByName($obj->getFilename()),
			);
		}
		self::$arrActivedPlugins = $fileList;
		return $fileList;
	}
	/**
	 * 运行所有的插件的制定方法
	 * @param string $actionName
	 * @return void
	 */
	public static function RunAllAction($actionName)
	{
		$list = self::GetActivedPlugins();
		foreach($list as $plugins)
		{
			$reflectionClass = $plugins['instance'];
			if($reflectionClass->hasMethod($actionName))
			{
				$reflectionMethod = $reflectionClass->getMethod($actionName);
				if ($reflectionMethod->isStatic()) {
		      		$reflectionMethod->invoke(null);
				} else {
					$reflectionMethod->invoke($reflectionClass->newInstance());
				}
			}
		}
	}
	/**
	 * 
	 * @param string $pluginName	插件名称
	 * @param string $action		插件方法
	 * @param array $args			附加参数
	 * @return mixed
	 */
	public static function GetDataBy($pluginName , $action , array $args = null)
	{
		
	}
	/**
	 * 插件运行入口
	 */
	public static function Run()
	{
		self::AutoIncludePlugin();
		$param = Controller::GetParam();
		$pluginName = isset($param[0]) ? $param[0] : null;
		$action = isset($param[1]) ? $param[1] : null;
		if($pluginName)
		{
			if($pluginName == __CLASS__ || self::IsActived($pluginName))
			{
				$data = Controller::Action($pluginName);
			}
			else 
			{
				echo('插件'.$pluginName.'被禁用或者尚未安装');
				return self::Complete();
			}
		}
		else 
		{
			$data = self::Index();
		}
		self::Complete();
		return $data;
	}
	/**
	 * 自动载入配置的插件
	 */
	public static function AutoIncludePlugin()
	{
		$mmConfig = $GLOBALS['mmConfig'];
		if(isset($mmConfig['plugins']))
		{
			foreach($mmConfig['plugins'] as $arr)
			{
				if(isset($arr['open']) && $arr['open'])
				{
					if(isset($arr['include']))
					{
						include_once $arr['include'];
					}
				}
			}
		}
	}
	/**
	 * 判断是否开启了自动载入的插件
	 * @param string $pluginName
	 * @return boolean
	 */
	public static function IsOpenAutoPlugin($pluginName)
	{
		return (bool)self::GetMmConfigBy(array('plugins',$pluginName,'open'));
	}
	/**
	 * 获取系统配置内容
	 * @param array | string $key
	 * @reutrn mixed
	 */
	public static function GetMmConfigBy($key = null)
	{
		$data = Controller::_GetCache('Plugins', __FUNCTION__ , $key);
		if(isset($data)) return $data;
		$mmConfig = $GLOBALS['mmConfig'];
		if(!isset($key)) return $mmConfig;
		if(is_array($key))
		{
			$data = $mmConfig;
			foreach($key as $k)
			{
				if(isset($data[$k]))
				{
					$t = $data[$k];
					$data = $t;
				}
				else 
				{
					return null;
				}
			}
			Controller::_SetCache('Plugins', __FUNCTION__ , $key, $data);
			return $data;
		}
		else
		{
			$data = isset($mmConfig[$key]) ? $mmConfig[$key] : null;
			Controller::_SetCache('Plugins', __FUNCTION__ , $key, $data);
			return $data;
		}
		Controller::_SetCache('Plugins', __FUNCTION__ , $key, null);
		return null;
	}
	/**
	 * 插件默认入口(无插件入口时)
	 */
	public static function Index()
	{
		//默认插件页面
		Controller::LoadPluginsAction('Vote', 'Facebook');
	}
	/**
	 * 判断插件是否已经安装
	 * 
	 * @param string $pluginName
	 * @return boolean
	 */
	public static function IsInstall($pluginName)
	{
		return self::GetInstallFile($pluginName);
	}
	/**
	 * 安装插件
	 * 
	 * @param string $pluginName
	 * @return boolean
	 */
	public static function Install()
	{
		$param = Controller::GetParam();
		
		$pluginName = isset($param[2]) ? $param[2] : null;
		if(empty($pluginName))
		{
			throw new Exception('请指定插件名称');
		}
		if(self::GetInstallFile($pluginName))
		{
			throw new Exception('插件已经安装');
		}
		$data = array();
		if($pluginName != __CLASS__)
		{
			$array = Controller::LoadPluginsAction($pluginName, 'install');
			if(is_array($array))
				$data = array_merge(
					$array , 
					Controller::LoadPluginsAction($pluginName, 'GetPluginData')
				);
			else 
				$data = Controller::LoadPluginsAction($pluginName, 'GetPluginData');
		}
		if($data)
			$return = self::SetInstallFile($pluginName , $data);
		else 
			$return = self::SetInstallFile($pluginName);
		if($return)
		{
			echo('插件'.$pluginName.'安装成功!');
		}
		else
		{
			echo('插件'.$pluginName.'安装失败!');
		}
	}
	/**
	 * 获取安装信息配置
	 * 
	 * @param string $pluginName
	 * @reutrn array(
	 * 		'installTime'	=>	'安装时间',
	 * 		'pluginName'	=>	'插件名称',
	 * )
	 */
	public static function GetInstallFile($pluginName)
	{
		$file = self::GetInstallFileName($pluginName);
		if(file_exists($file))
			return include($file);
	}
	/**
	 * 获取安装文件路径
	 * @param string $pluginName
	 * @return string
	 */
	public static function GetInstallFileName($pluginName)
	{
		return PLUGINS_INSTALL_DIR . '/'.$pluginName.'.installed.php';
	}
	/**
	 * 写入安装文件
	 * 
	 * @param string $pluginName	插件名称
	 * @param array $appendData		附加数据
	 * @return boolean
	 */
	public static function SetInstallFile($pluginName ,array $appendData = null)
	{
		$data = array(
			'installTime'	=>	time(),
			'pluginName'	=>	$pluginName,
			'actived'		=>	1,
		);
		if(isset($appendData) && $appendData)
		{
			$data = array_merge($appendData , $data);
		}
		return file_put_contents(self::GetInstallFileName($pluginName), "<?php\nreturn ".var_export($data , true).';');
	}
	/**
	 * 安装界面
	 * @param string $pluginName
	 * @return void
	 */
	public static function InstallForm($pluginName)
	{
		$url= Controller::ParamToUrl(array(__CLASS__,'Install',$pluginName));
		$append = "<a href='{$url}'>安装插件? {$pluginName}</a>";
		die('插件尚未安装'.$append);
	}
	/**
	 * 卸载插件
	 * @return void
	 */
	public static function UnInstall()
	{
		$param = Controller::GetParam();
		$pluginName = isset($param[2]) ? $param[2] : null;
		Controller::LoadPluginsAction($pluginName, __FUNCTION__);
		$filename = self::GetInstallFileName($pluginName);
		if(file_exists($filename) && unlink($filename))
		{
			echo('插件'.$pluginName.'卸载成功!');
		}
		else {
			echo('插件'.$pluginName.'卸载失败!');
		}
	}
	/**
	 * 禁用插件
	 */
	public static function DeActived()
	{
		$param = Controller::GetParam();
		$pluginName = isset($param[2]) ? $param[2] : null;
		$filename = self::GetInstallFileName($pluginName);
		$data = self::GetInstallFile($pluginName);
		$data['actived'] = 0;
		if(file_exists($filename) && file_put_contents($filename, "<?php\nreturn ".var_export($data , true).';'))
		{
			echo('插件'.$pluginName.'禁用成功!');
		}
		else {
			echo('插件'.$pluginName.'禁用失败!');
		}
	}
	/**
	 * 激活插件
	 */
	public static function Actived()
	{
		$param = Controller::GetParam();
		$pluginName = isset($param[2]) ? $param[2] : null;
		$filename = self::GetInstallFileName($pluginName);
		$data = self::GetInstallFile($pluginName);
		$data['actived'] = 1;
		if(file_exists($filename) && file_put_contents($filename, "<?php\nreturn ".var_export($data , true).';'))
		{
			echo('插件'.$pluginName.'激活成功!');
		}
		else {
			echo('插件'.$pluginName.'激活失败!');
		}
	}
	/**
	 * 插件是否被禁用
	 * @param string $pluginName
	 * @return boolean
	 */
	public static function IsActived($pluginName)
	{
		$data = self::GetInstallFile($pluginName);
		return isset($data['actived']) && $data['actived'] ? true : false;
	}
	/**
	 * 系统最后一次调用
	 */
	public static function Complete()
	{
		if(self::GetMmConfigBy(array('debug','sql')) && self::IsOpenAutoPlugin('firephp'))
		{
			FB::log(DatabaseBase::$sql);
		}
	}
}