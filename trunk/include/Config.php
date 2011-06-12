<?php
$mmConfig = array(
	'db' => array(
		'host' => 'localhost',
		'user' => 'root',
		'pass' => '',
		'dbName' => 'facebook',
		'port' => '',
		'charset' => 'utf8',
	),
	'plugins' => array( //系统自带的插件
		'firephp'	=>	array(
			'include'	=>	INCLUDE_DIR .'/FirePHPCore/fb.php', //插件路径
			'open'		=>	1, //是否打开
		),
	),
	'debug' => array(
		'sql'	=>	1,//是否打印sql
	),
	'path'	=>	array(
		'upload' => ROOT_DIR.'/image/upload/',
	),
);
$mmHtml = array(
	'Css'			=>	'',
	'Javascript'	=>	'',
	'Meta'			=>	'',
	'Body'			=>	'',
	'Footer'		=>	'',
	'Title'			=>	'',
	'Form'			=>	array(),
);
$mmUser = array();