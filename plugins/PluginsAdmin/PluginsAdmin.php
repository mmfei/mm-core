<?php
/**
 * 插件管理
 * @author mmfei<wlfkongl@163.com>
 *
 */
class PluginsAdmin
{
	public static function Admin()
	{
		$admin = new Admin();
		$admin->AppendSidebar('系统配置', '插件管理', Controller::ParamToUrl(array(__CLASS__,'PluginsList')));
	
	}
	public static function PluginsList()
	{
		$dir = new DirectoryIterator(PLUGINS_DIR);
		$fileList = array();
		foreach ($dir as $obj)
		{
			if($obj->isDot() || $obj->isFile()) continue;
			$fileList[] = array(
				'folder' => $obj->getFilename(),
				'data' => Controller::LoadPluginsAction($obj->getFilename(), 'GetPluginData'),
			);
		}
		
		$html = '';
		foreach($fileList as $folder)
		{
			$installUrl = Controller::ParamToUrl(array('Plugins' , 'Install' ,$folder['folder'] ,));
			$unInstallUrl = Controller::ParamToUrl(array('Plugins' , 'UnInstall' ,$folder['folder'] ,));
			$activedUrl = Controller::ParamToUrl(array('Plugins' , 'Actived' ,$folder['folder'] ,));
			$deActivedUrl = Controller::ParamToUrl(array('Plugins' , 'DeActived' ,$folder['folder'] ,));
			$name = isset($folder['data']['pluginName']) ? $folder['data']['pluginName'] : $folder['folder'];
			$text = isset($folder['data']['author']) ? '<dd><b>作者:</b>'.$folder['data']['author'].'</dd>' : '';
			$text .= isset($folder['data']['email']) ? '<dd><b>邮箱:</b>'.$folder['data']['email'].'</dd>' : '';
			$text .= isset($folder['data']['url']) ? '<dd><b>主页:</b>'.$folder['data']['url'].'</dd>' : '';
			$text .= isset($folder['data']['desc']) ? '<dd><b>说明:</b>'.$folder['data']['desc'].'</dd>' : '';
			$folder['data']['actived'] = Plugins::IsInstall($folder['folder']);
			$text .= isset($folder['data']['actived']) ? "<dd><b>激活:</b>是<a href='{$unInstallUrl}'>卸载?</a></dd>":"<dd><b>激活:</b>否<a href='{$installUrl}'>现在安装?</a></dd>";
			if($folder['data']['actived'])
			{
				$pluginData = Plugins::GetInstallFile($folder['folder']);
				$text .= isset($pluginData['installTime']) ? '<dd><b>安装时间:</b>'.date('Y-m-d H:i:s',$pluginData['installTime']).'</dd>' : '';
				
				$actived = isset($pluginData['actived']) && $pluginData['actived'] ? true : false;
				if($actived)
				{
					$text .= "<dd><b>状态:</b>激活<a href='{$deActivedUrl}'>禁用?</a></dd>";
				}
				else 
				{
					$text .= "<dd><b>状态:</b>禁用<a href='{$activedUrl}'>激活?</a></dd>";
				}
			}
			
			$html.=<<<EOT
				<dl>
					<dt><h3>{$name}</h3></dt>
					{$text}
				</dl>
EOT;
		}
		$objHtml = new Html();
		$objHtml->AppendBody($html)->Title('插件列表')->Show();
	}
    /**
     * 获取插件信息
     * @return array(
     *         'pluginName'    =>    '插件名称',
     *         'author'        =>    '插件作者',
     *         'email'            =>    '作者邮箱',
     *         'url'            =>    '插件官方网站',
     *         'desc'            =>    '插件描述',
     * )
     */
    public static function GetPluginData()
    {
        return array(
            'pluginName'    =>    '插件管理器',
            'author'        =>    'mmfei',
            'email'            =>    'wlfkongl@163.com',
            'url'            =>    'www.mmfei.com',
            'desc'            =>    '插件管理器,管理系统的所有插件',
        );
    }
}