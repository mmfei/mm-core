<?php
/**
 * 框架开发助手插件
 * 		协助开发
 * @author mmfei<wlfongl@163.com>
 *
 */
class Helper
{
	/**
	 * 获取插件信息
	 * @return array(
	 * 		'pluginName'	=>	'插件名称',
	 * 		'author'		=>	'插件作者',
	 * 		'email'			=>	'作者邮箱',
	 * 		'url'			=>	'插件官方网站',
	 * 		'desc'			=>	'插件描述',
	 * )
	 */
	public static function GetPluginData()
	{
		return array(
			'pluginName'	=>	'开发助手插件',
			'author'		=>	'mmfei',
			'email'			=>	'wlfkongl@163.com',
			'url'			=>	'www.mmfei.com',
			'desc'			=>	'开发助手插件',
		);
	}
	/**
	 * 根据表单创建表格
	 */
	public static function CodeByTableForm()
	{
		$html = new Html();
		$method = 'post';
		$formName = 'form1';
		$action = Controller::ParamToUrl(array(__CLASS__ , 'CodeByTable'));
		$caption = '根据数据表创建代码';
		$html->Form($method, $formName , $action ,$caption , null , null ,2 , '生成');
		$sql = 'show tables ';
		$tables = Database::query($sql);
		foreach($tables as $table)
		{
			$html->AppendInput($formName, 'table' , '选择表', $table[0] , 'select' , $table[0] , array(Html::PG('table')));
		}
		return $html->InitDefaultCss()->InitDefaultJs()->Show();
	}
	/**
	 * 创建数据
	 * 
	 */
	public static function CodeByTable()
	{
		$table = Html::PG('table');
		if($table)
		{
			$sql = 'show full fields from `'.$table.'`';
			$data = Database::query($sql);
			self::PrintData($data);
			$code = "<?php\n";
			
			foreach($data as $index => $row)
			{
				$name = $row['Field'];
				$type = $row['Type'];
				$null = $row['Null'];
				$default = $row['Default'];
				$key = $row['Key'];
				$extra = $row['Extra'];
				$comment = $row['Comment'];
				$code .=<<<EOT
				
EOT;
			}
			highlight_string($code);
		}
	}
	/**
	 * 插件生成
	 */
	public static function PluginsForm()
	{
		$html = new Html();
		$method = 'post';
		$formName = 'form1';
		$action = Controller::ParamToUrl(array(__CLASS__ , 'PluginsCode'));
		$caption = '插件生成代码';
		$html->Form($method, $formName , $action ,$caption , null , null , 2 , '生成插件代码')
		->AppendInput($formName, 'key','插件名称', Html::PG('key') , 'text')
		->AppendInput($formName, 'pluginName','插件中文名称', Html::PG('pluginName') , 'text')
		->AppendInput($formName, 'author','插件作者', Html::PG('author') , 'text')
		->AppendInput($formName, 'email','邮箱', Html::PG('email') , 'text')
		->AppendInput($formName, 'url','网址', Html::PG('url') , 'text')
		->AppendInput($formName, 'desc','说明', Html::PG('desc') , 'textarea')
		->InitDefaultCss()->InitDefaultJs()
		->Show();
		;
	}
	public static function PluginsCode()
	{
		$data					= array();
		$data['{{key}}']	 	= Html::PG('key');
		$data['{{pluginName}}'] 	= Html::PG('pluginName');
		$data['{{author}}'] 	= Html::PG('author');
		$data['{{email}}'] 		= Html::PG('email');
		$data['{{url}}'] 		= Html::PG('url');
		$data['{{desc}}']		= Html::PG('desc');
		
		$html=<<<EOT
<?php
/**
 * {{pluginName}}
 * @author {{author}}<{{email}}>
 *
 */
class {{key}}
{
	/**
	 * 处理安装业务(如果此插件需要安装,则需要在此方法实现)
	 * @return boolean | array 返回安装结果 | 需要附加到安装标志的数据
	 */
	public static function Install()
	{
		\$sql = array();
		
		foreach(\$sql as \$sql1)
			Database::Execute(\$sql1);
		return true;
	}
	/**
	 * 卸载插件
	 * @return boolean
	 */
	public static function UnInstall()
	{
		return true;
	}	
	/**
	 * 获取插件信息
	 * @return array(
	 * 		'pluginName'	=>	'插件名称',
	 * 		'author'		=>	'插件作者',
	 * 		'email'			=>	'作者邮箱',
	 * 		'url'			=>	'插件官方网站',
	 * 		'desc'			=>	'插件描述',
	 * )
	 */
	public static function GetPluginData()
	{
		return array(
			'pluginName'	=>	'{{pluginName}}',
			'author'		=>	'{{author}}',
			'email'			=>	'{{email}}',
			'url'			=>	'{{url}}',
			'desc'			=>	'{{desc}}',
		);
	}
}
EOT;
		$html = strtr($html , $data);
		highlight_string($html);
	}
	public static function PrintData($data)
	{
		echo('<pre>');
		print_r($data);
	}
}