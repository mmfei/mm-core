<?php
/**
 * 用户类
 * 	处理网站用户业务(包括管理员)
 * 
 * @author mmfei<wlfkongl@163.com>
 *
 */
class User
{
	const USER_SESSION_KEY = 'mmUser';
	/**
	 * 系统默认进入界面
	 */
	public static function Index()
	{
		if(!Plugins::IsInstall(__CLASS__))
		{
			self::Install();
		}
		return self::LoginForm();
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
			'pluginName'	=>	'用户管理插件',
			'author'		=>	'mmfei',
			'email'			=>	'wlfkongl@163.com',
			'url'			=>	'www.mmfei.com',
			'desc'			=>	'用户管理插件',
		);
	}
	public static function LoginForm()
	{
		$html = new Html();
		$method = 'post';
		$arrParam = array(
			__CLASS__,
			'DoLogin'
		);
		$action = Controller::ParamToUrl($arrParam);
		$formName = 'form1';
		$captain = '登陆';
		$html->Form($method , $formName , $action , $captain);
		$html->AppendInput($formName, 'userName' , '用户名' , Html::PG('userName'))->Title('登陆')
		->AppendInput($formName, 'password' , '密码' , Html::PG('password'), 'password')
		->InitDefaultCss()->InitDefaultJs()
		;
		return $html->Show();
	}
	public static function DoLogin()
	{
		$userData = self::GetCurrentUser();
		if($userData)
		{
			$html = new Html();
			$html->AppendBody('你已经登陆了');
			return Controller::LoadPluginsAction('Admin','Show');
		}
		$userName = Html::PG('userName');
		$password = Html::PG('password');

		if(!$userName || !$password)
		{
			return self::LoginForm();
		}
		$userData = Database::GetRowBy('fbUser' , null , array("userName = '{$userName}'",'password=\''.md5($password).'\''));
		if(empty($userData))
		{
			$html = new Html();
			$html->AppendBody('用户名或密码错误');
			return self::LoginForm();
		}
		self::SetLoginData($userData);
		return Controller::LoadPluginsAction('Admin','Show');
	}
	/**
	 * 退出
	 */
	public static function Logout()
	{
		self::UnsetLoginData();
		return self::LoginForm();
	}
	/**
	 * 管理
	 */
	public static function Admin()
	{
		$admin = new Admin();
		$admin->AppendSidebar('玩家工具', '添加管理员', Controller::ParamToUrl(array(__CLASS__ , 'AddForm')) , null , null , -1);
		$admin->AppendSidebar('玩家工具', '退出', Controller::ParamToUrl(array(__CLASS__ , 'Logout')) , array('target'=>'') , null , -1);
	}
	public static function ListForm()
	{
		$html = new Html();
		$method = 'post';
		$arrParam = array(
			__CLASS__,
			'Edit'
		);
		$action = Controller::ParamToUrl($arrParam);
		$formName = 'form1';
		$captain = '添加管理员';
		$html->Form($method , $formName , $action , $captain , null , null ,2 ,'添加',array('class'=>'ajaxForm1'));
		$html->AppendInput($formName, 'userName' , '用户名' , Html::PG('userName'))->Title('登陆')
		->AppendInput($formName, 'password' , '密码' , Html::PG('password'), 'password')
		->InitDefaultCss()->InitDefaultJs()->InitAjaxSubmit()
		;
		return $html->Show();
	}
	public static function AddForm()
	{
		$html = new Html();
		$method = 'post';
		$arrParam = array(
			__CLASS__,
			'DoAdd'
		);
		$action = Controller::ParamToUrl($arrParam);
		$formName = 'form1';
		$captain = '添加管理员';
		$html->Form($method , $formName , $action , $captain , null , null ,2 ,'添加',array('class'=>'ajaxForm1'));
		$html->AppendInput($formName, 'userName' , '用户名' , Html::PG('userName'))->Title('登陆')
		->AppendInput($formName, 'password' , '密码' , Html::PG('password'), 'password')
		->InitDefaultCss()->InitDefaultJs()->InitAjaxSubmit()
		;
		return $html->Show();
	}
	public static function DoAdd()
	{
		$userName = Html::PG('userName');
		$password = Html::PG('password');
		if($userName && $password)
		{
			$data = array(
				'userName' => $userName ,
				'password' => md5($password),
			);
			$userData = Database::GetRowBy('fbUser' , null , array('userName = \''.$userName.'\''));
			if($userData)
			{
				echo('0');
			}
			elseif(Database::InsertBy('fbUser', $data))
			{
				echo('1');
			}
			else
			{
				echo('0');
			}
		}
		else
		{
			echo('0');
		}
	}
	/**
	 * 处理安装业务(如果此插件需要安装,则需要在此方法实现)
	 * @return boolean | array 返回安装结果 | 需要附加到安装标志的数据
	 */
	public static function Install()
	{
		$sql = array();
		$password = md5('mb');
		$sql[] = <<<EOT
create table fbUser( 
       userId int not null auto_increment comment '玩家ID', 
       userName varchar(255) not null comment '玩家名称', 
       isActived tinyint not null default 1 comment '是否激活', 
       createTime int not null comment '创建时间', 
       roleId tinyint not null default 1 comment '用户身份(0:普通会员;1:vip会员;2:管理员)', 
       password varchar(255) not null comment '管理员密码(md5加密)',
       primary key(userId) 
)engine InnoDB charset utf8 comment '会员表';
EOT;
		$sql[] = <<<EOT
create table fbRole( 
       roleId int not null auto_increment comment '角色ID', 
       roleName varchar(50) not null comment '角色名称', 
       `desc` varchar(255) not null comment '备注', 
       isActived tinyint not null default 1 comment '是否激活', 
       pluginOpenList varchar(2000) comment '只允许使用的插件列表(为空表示无此限制 , 插件名用逗号分隔)', 
       pluginDenyList varchar(2000) comment '禁止使用的插件列表(为空表示无此限制 , 插件名用逗号分隔)', 
       primary key(roleId , isActived) 
)engine InnoDB charset utf8 comment '会员表';
EOT;
		$sql[] = <<<EOT
INSERT INTO `facebook`.`fbrole` SET `roleName`='管理员',`desc`='超级管理员',`isActived`=1;
EOT;
		$sql[] = <<<EOT
INSERT INTO `facebook`.`fbrole` SET `roleName`='普通用户',`desc`='普通用户',`isActived`=1;
EOT;
		$sql[] = <<<EOT
INSERT INTO `facebook`.`fbuser` SET `userName`='admin',`isActived`=1,`createTime`=1305054814,`roleId`=1,`password`='{$password}';
EOT;

		foreach($sql as $sql1)
			Database::execute($sql1);
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
	 * 记录登陆标志
	 * @param array $arr
	 */
	public static function SetLoginData($arr)
	{
		self::StartSession();
		$_SESSION[User::USER_SESSION_KEY] = $arr;
	}
	/**
	 * 删除登陆标志
	 */
	public static function UnsetLoginData()
	{
		self::StartSession();
		unset($_SESSION[User::USER_SESSION_KEY]);
	}
	/**
	 * 需要登陆
	 */
	public static function NeedLogin()
	{
		if(!self::GetCurrentUser())
		{
			return Controller::LoadPluginsAction('User' , 'LoginForm');
		}
	}
	/**
	 * 获取当前登陆玩家的数据
	 * @param string $key
	 */
	public static function GetCurrentUser($key = null)
	{
		self::StartSession();
		if(isset($key))
		{
			return isset($_SESSION[User::USER_SESSION_KEY][$key]) ? $_SESSION[User::USER_SESSION_KEY][$key] : null;
		}
		else 
			return isset($_SESSION[User::USER_SESSION_KEY]) ? $_SESSION[User::USER_SESSION_KEY] : null;
	}
	public static function StartSession()
	{
		if (empty($_SESSION['SID'])) @session_start();
	}
}