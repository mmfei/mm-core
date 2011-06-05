<?php
/**
 * 框架开发助手插件
 * 		协助开发
 * @author mmfei<wlfongl@163.com>
 *
 */
class Helper
{
	public function Admin()
	{
		$arr = array(
			'根据表单创建表格' => 'CodeByTableForm',
			'生成插件代码' => 'PluginsForm',
		);
		$admin = new Admin();
		foreach($arr as $name => $f)
		{
			$admin->AppendSidebar('开发助手', $name, Controller::ParamToUrl(array(__CLASS__,$f)) , null , null , 1);
		}
	}
	public static function Index()
	{
		$arr = array(
			'获取插件信息' => 'GetPluginData',
			'根据表单创建表格' => 'CodeByTableForm',
//			'创建数据' => 'CodeByTable',
			'生成插件代码' => 'PluginsForm',
		);
		$body = '<ul>';
		foreach($arr as $name => $f)
		{
			$url = Controller::ParamToUrl(array(__CLASS__ , $f));
			$body.=<<<EOT
				<li>
					<a href="{$url}">{$name}</a>
				</li>		
EOT;
		}
		$body.="</ul>";
		$html = new Html();
		$html->AppendBody($body);
		$html->Show();
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
		$action = Controller::ParamToUrl(array(__CLASS__ , 'CodeByTableForm'));
		$caption = '根据数据表创建代码';
		$html->Form($method, $formName , $action ,$caption , null , null ,2 , '生成');
		$sql = 'show tables ';
		$tables = Database::query($sql);
		foreach($tables as $arr)
		{
			foreach($arr as $table)
				$html->AppendInput($formName, 'table' , '选择表', $table , 'select' , $table , array(Html::PG('table')));
		}
		$html->InitDefaultCss()->InitDefaultJs()->AppendFooter(self::PrintData(self::CodeByTable()))->Show();
		
	}
	private static function CodeByTable()
	{
		$table = Html::PG('table');
		if($table)
		{
			$sql = 'show full fields from `'.$table.'`';
			$data = Database::query($sql);
//			self::PrintData($data);
			$code = "<?php\n";
			$codeList = array(
				'th'	=>	array(),
				'tr'	=>	array(),
				'data'	=>	array(),
				'data2'	=>	array(),
				'data1'	=>	array(),
			);
			foreach($data as $index => $row)
			{
				$name = $row['Field'];
				$type = $row['Type'];
				$null = $row['Null'];
				$default = $row['Default'];
				$key = $row['Key'];
				$extra = $row['Extra'];
				$comment = $row['Comment'];
				$codeList['th'][] =<<<EOT
				<th>{$comment}</th>
EOT;
				$codeList['tr'][] =<<<EOT
				<td>
					{\$data['{$name}']}
				</td>
EOT;
				$codeList['data'][] =<<<EOT
				'{$name}'	=>	\$data['{$name}'],
EOT;
				$codeList['data1'][] =<<<EOT
				\$data['{$name}']	=	\${$name};
EOT;
				$codeList['data2'][] =<<<EOT
				'{$name}'	=>	\${$name};
EOT;
			}
			foreach($codeList as $type => $arr)
			{
				$code.= "\n\n".join("\n" , $arr);
			}
			return highlight_string($code , true);
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
		echo('<pre style="text-align:left;">');
		print_r($data);
		echo('</pre>');
	}
}