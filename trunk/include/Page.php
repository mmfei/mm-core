<?php
class Page
{
	static $config = array();
	static $defaultConfig = array(
		'value'		=>	array(
			'perPageNumeric' => 10,
		),
		'template'	=>	array(
			'numeric'	=>	'<a href="{url}{flag}{name}={page}">{page}</a>',
			'prev'		=>	'<a href="{url}{flag}{name}={page}" class="prev">{text}</a>',
			'next'		=>	'<a href="{url}{flag}{name}={page}" class="next">{text}</a>',
			'jump'		=>	'<input type="text" name="{name}" value="{page}" />',
			'current'	=>	'<span>{page}</span>',
			'wrap'		=>	'<div class="showpage">{text}</div>',
			'name'		=>	'page',
		),
		'text'		=>	array(
			'prev'		=>	'上一页',
			'next'		=>	'下一页',
		),
		'special'	=>	array(
//			'flag'	=>	'/',
			'theme'	=>	'default',
		),
	);
	/**
	 * 无特殊的内容(只有上一页和下一页的内容)
	 * @var integer
	 */
	const TYPE_NONE = 1;
	/**
	 * 包含数字链接
	 * @var integer
	 */
	const TYPE_NUMERIC = 2;
	/**
	 * 包含数字链接和跳转链接
	 * @var integer
	 */
	const TYPE_NUMERIC_JUMP = 3;
	/**
	 * 只有跳转链接
	 * @var integer
	 */
	const TYPE_JUMP = 4;
	
	public static function SetConfig(array $config)
	{
		self::$config = $config;
	}
	/**
	 * 设置默认配置
	 */
	private static function SetDefaultConfig()
	{
		return self::$defaultConfig;
	}
	/**
	 * 显示分页链接
	 * @param integer $total
	 * @param string $url
	 * @param integer $page
	 * @param integer $pageSize
	 * @param integer $type
	 * @return string
	 */
	public static function GetPageList($total , $url = '' , $page = 1 , $pageSize = 10, $type = 1 , array $config = null)
	{
		if(!isset($config)) self::SetDefaultConfig();
		else self::SetConfig($config);
		
		$pageTotal = ceil($total / $pageSize);//总页数
		$page = $page > $pageTotal ? $pageTotal : ((int)$page < 1 ? 1 : (int)$page);
		
		$return = '';
		switch($type)
		{
			case self::TYPE_NONE:
				$return .= self::GetListByNone($url, $page , $pageTotal);
				break;
			case self::TYPE_NUMERIC:
				$return .= self::GetListByMumeric($url, $page , $pageTotal);
				break;
			case self::TYPE_NUMERIC_JUMP:
				$return .= self::GetListByMumericJump($url, $page , $pageTotal);
				break;
			case self::TYPE_JUMP:
				$return .= self::GetListByJump($url, $page , $pageTotal);
				break;
			default:
				
		}
		$template = self::GetTemplate('wrap');
		$return = strtr($template , array('{text}'=>$return));
		return $return;
	}
	
	private static function GetListByNone($url, $page , $pageTotal)
	{
		$return = self::GetPrevNumeric($url, $page);
		$return .= self::GetNextNumeric($url, $page , $pageTotal);
		return $return;
	}
	
	private static function GetListByMumeric($url, $page , $pageTotal)
	{
		$return = self::GetPrevNumeric($url, $page);
		$return .= self::GetNumeric($pageTotal, $url , $page);
		$return .= self::GetNextNumeric($url, $page , $pageTotal);
		return $return;
	}
	
	private static function GetListByMumericJump($url, $page , $pageTotal)
	{
		$return = self::GetPrevNumeric($url, $page);
		$reutrn .= self::GetNumeric($pageTotal, $url , $page);
		$return .= self::GetJump($url, $page);
		$return .= self::GetNextNumeric($url, $page , $pageTotal);
		return $return;
	}
	
	private static function GetListByJump($url, $page , $pageTotal)
	{
		$return = self::GetPrevNumeric($url, $page);
		$return .= self::GetJump($url, $page);
		$return .= self::GetNextNumeric($url, $page , $pageTotal);
		return $return;
	}
	
	private static function GetNumeric($pageTotal , $url , $page = 1)
	{
		$template = self::GetTemplate('numeric');//'<a href="{url}{flag}page={page}">{page}</a>'
		$templateCurrent = self::GetTemplate('current');
		$name = self::GetTemplate('name');
		
		$perPageNumeric = self::GetValue('perPageNumeric');
		if($pageTotal <= $perPageNumeric)
		{
			$i = 1;
			$perPageNumeric = $pageTotal;
		}
		else 
		{
			$bit = floor($perPageNumeric / 2);
			$i = $page - $bit > 0 ? ($page - $bit < $pageTotal ? $bit : $pageTotal) : 1;
		}
		$return = '';
		$defaultFlag = self::getSpecial('flag');
		$flag = $defaultFlag ? $defaultFlag : ((false === strpos($url, '?')) ? '?' : '&');
		while($perPageNumeric)
		{
			if($i == $page)
			{
				$return .= str_replace(array('{url}','{flag}','{page}','{name}'),array($url , $flag , $i , $name),$templateCurrent);
			}
			else 
			{
				$return .= str_replace(array('{url}','{flag}','{page}','{name}'),array($url , $flag , $i , $name),$template);
			}
			
			$i++;
			$perPageNumeric--;
		}
		
		return $return;
	}

	private static function GetPrevNumeric($url , $page)
	{
		$template = self::GetTemplate('prev');//'<a href="{url}{flag}page={page}">{page}</a>'
		$name = self::GetTemplate('name');
		$text = self::GetText('prev');
		$defaultFlag = self::getSpecial('flag');
		$flag = $defaultFlag ? $defaultFlag : ((false === strpos($url, '?')) ? '?' : '&');
		return str_replace(array('{url}','{flag}','{page}','{name}' , '{text}'),array($url , $flag , max($page - 1 , 1), $name , $text) ,$template);
	}
	private static function GetNextNumeric($url , $page , $pageTotal)
	{
		$template = self::GetTemplate('next');//'<a href="{url}{flag}page={page}">{page}</a>'
		$name = self::GetTemplate('name');
		$text = self::GetText('next');
		$defaultFlag = self::getSpecial('flag');
		$flag = $defaultFlag ? $defaultFlag : ((false === strpos($url, '?')) ? '?' : '&');
		return str_replace(array('{url}','{flag}','{page}','{name}' , '{text}'),array($url , $flag , min($page + 1 , $pageTotal) , $name , $text) ,$template);
	}
	
	private static function GetJump($url , $page)
	{
		$template = self::GetTemplate('jump');//'<a href="{url}{flag}page={page}">{page}</a>'
		$name = self::GetTemplate('name');
		$defaultFlag = self::getSpecial('flag');
		$flag = $defaultFlag ? $defaultFlag : ((false === strpos($url, '?')) ? '?' : '&');
		return str_replace(array('{url}','{flag}','{page}','{name}'),array($url , $flag , $page - 1 > 0 ? $page - 1 : 1, $name) ,$template);
	}

	private static function GetTemplate($key)
	{
		return self::GetConfigKey($key , 'template');
	}
	private static function GetSpecial($key)
	{
		return self::GetConfigKey($key , 'special');
	}
	private static function GetValue($key)
	{
		return self::GetConfigKey($key , 'value');
	}
	
	private static function GetText($key)
	{
		return self::GetConfigKey($key , 'text');
	}
	
	private static function GetConfigKey($key , $parentKey = 'template')
	{
		return isset(self::$config[$parentKey][$key]) ? self::$config[$parentKey][$key] : (isset(self::$defaultConfig[$parentKey][$key]) ? self::$defaultConfig[$parentKey][$key] : '');
	}
	
	public static function GetCss($key = 'default')
	{
		$css = array();
		$css['default'] =<<<EOT
		.showpage{
			margin:2px auto;
			height:30px;
			line-height:30px;
			font-size:12px;
		}
		.showpage a,.showpage span{
			margin:0px 5px;
			text-align:center;
			display:block;
			float:left;
			color:#000;
		}
		.showpage a{
		}
		.showpage a.next,.showpage a.prev{
			width:50px;
		}
		.showpage a:hover{
		}
		.showpage span{
		}
EOT;
		$css['black'] =<<<EOT
		.showpage{
			margin:2px auto;
			height:30px;
			line-height:30px;
			background-color:#010101;
			font-size:12px;
		}
		.showpage a,.showpage span{
			margin:0px 5px;
			border:1px solid #939393;
			text-align:center;
			display:block;
			float:left;
			width:25px;
			height:25px;
			line-height:25px;
			color:#fff;
		}
		.showpage a{
			text-decoration:none;
		}
		.showpage a.next,.showpage a.prev{
			width:50px;
		}
		.showpage a:hover{
			background-color:#636363;
		}
		.showpage span{
			background-color:#636363;
		}
EOT;


		$css['white'] =<<<EOT
		.showpage{
			margin:2px auto;
			height:30px;
			line-height:30px;
			background-color:#FFFFFF;
			font-size:12px;
		}
		.showpage a,.showpage span{
			margin:0px 5px;
			border:1px solid #DFDFDF;
			text-align:center;
			display:block;
			float:left;
			width:25px;
			height:25px;
			line-height:25px;
			color:#5E62AC;
		}
		.showpage a{
			text-decoration:none;
		}
		.showpage a.next,.showpage a.prev{
			width:50px;
		}
		.showpage a:hover{
			color:#BA257F;
		}
		.showpage span{
			color:#BA257F;
		}
EOT;

		$css['orange'] =<<<EOT
		.showpage{
			margin:2px auto;
			height:30px;
			line-height:30px;
			background-color:#FBFFFF;
			font-size:12px;
		}
		.showpage a,.showpage span{
			margin:0px 5px;
			border:1px solid #DA8863;
			text-align:center;
			display:block;
			float:left;
			width:25px;
			height:25px;
			line-height:25px;
			color:#C99855;
		}
		.showpage a{
			text-decoration:none;
		}
		.showpage a.next,.showpage a.prev{
			width:50px;
		}
		.showpage a:hover{
			color:#F36D0E;
			background-color:#FFBC9C;
		}
		.showpage span{
			color:#F36D0E;
			background-color:#FFBC9C;
		}
EOT;
		return isset($css[$key]) ? $css[$key] : $css[self::GetSpecial('theme')];
	}
}