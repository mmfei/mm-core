<?php
/**
 * 后台管理
 * @author mmfei<wlfkongl@163.com>
 *
 */
class Admin extends Html
{
	/**
	 * 增加管理工具
	 * 
	 * @param string $sidebarTitle
	 * @param string $linkText
	 * @param string $linkUrl
	 * @param array $linkAttr
	 * @param array $sidebarAttr
	 * @param integer $sortIndex		排序规则,降序排序
	 * @return Html
	 */
	public function AppendSidebar($sidebarTitle , $linkText , $linkUrl , $linkAttr = null , $sidebarAttr = null , $sortIndex = 10)
	{
		if(isset($GLOBALS['mmHtml']['sidebar']))
		{
			$GLOBALS['mmHtml']['sidebar'][$sidebarTitle]['list'][$linkText] = array(
				'url' => $linkUrl,
				'text' => $linkText,
				'attr' => $linkAttr,
			);
			$GLOBALS['mmHtml']['sidebar'][$sidebarTitle]['attr'] = $sidebarAttr;
			$GLOBALS['mmHtml']['sidebar'][$sidebarTitle]['sortIndex'] = $sortIndex;
		}
		else 
		{
			$GLOBALS['mmHtml']['sidebar'][$sidebarTitle] = array();
			$GLOBALS['mmHtml']['sidebar'][$sidebarTitle]['list'][$linkText] = array(
				'url' => $linkUrl,
				'text' => $linkText,
				'attr' => $linkAttr,
			);
			$GLOBALS['mmHtml']['sidebar'][$sidebarTitle]['attr'] = $sidebarAttr;
			$GLOBALS['mmHtml']['sidebar'][$sidebarTitle]['sortIndex'] = $sortIndex;
		}
		return $this;
	}
	public function Show($isReturn = false)
	{
		Plugins::RunAllAction('Admin');
		if(isset($GLOBALS['mmHtml']['sidebar']))
		{
			$html = <<<EOT
				<div class='AdminLeft'>
EOT;
			$sortMap = array();
			foreach($GLOBALS['mmHtml']['sidebar'] as $title => $arr)
			{
				$sortMap[$arr['sortIndex']][] = $title;
			}
			ksort($sortMap , SORT_NUMERIC);
			$sortMap = array_reverse($sortMap);
			foreach($sortMap as $index => $arrList)
			{
				foreach($arrList as $title)
				{
					$arr = $GLOBALS['mmHtml']['sidebar'][$title];
					$attrValue = '';
					if(isset($arr['attr']))
					{
						foreach($arr['attr'] as $attr => $value)
						{
							$attrValue.=" {$attr} = '{$value}'";
						}
					}
					$html.=<<<EOT
					
						<dl{$attrValue}>
							<dt>{$title}</dt>		
EOT;
						foreach($arr['list'] as $a)
						{
							$appendValue = '';
							if(isset($a['attr']))
								foreach($a['attr'] as $attr => $value)
									$appendValue.=" {$attr} = '{$value}'";
							
							$html.=<<<EOT
							<dd><a href="{$a['url']}"{$appendValue} target='rightContent'>{$a['text']}</a></dd>					
EOT;
						}
					$html.=<<<EOT
	
						</dl>			
EOT;
				}
			}
			$url = Controller::ParamToUrl(array(__CLASS__,'ShowContent'));
			$html.=<<<EOT

				</div>
				<div class='AdminContent'>
					<iframe name="rightContent" src="{$url}" frameborder="0" width="100%" height="100%" style="border-width: 0px;"></iframe>
				</div>
EOT;
			$this->AppendBody($html);
		}
		self::_Init();
		return parent::Show($isReturn);
	}
	public function ShowContent()
	{
		$this->AppendBody('管理后台');
		return parent::Show();
	}
	private function _Init()
	{
		$css=<<<EOT
			body{margin:0px;font-size:12px;}
			.AdminLeft{
				width:20%;
				height:100%;
				float:left;
				background-color:#ccc;
			}
			.AdminLeft dl ,.AdminLeft dd{
				margin:0px;padding:0px;
			}
			.AdminLeft dl dt{
				height:30px;
				line-height:30px;
				font-size:14px;
				padding:0px 5px;
				text-align:center;
				background-color:#333;
				color:#fff;
			}
			.AdminLeft dl dd{
				height:25px;
				line-height:25px;
			}
			.AdminLeft dl dd a{
				display:block;
				padding-left:20px;
			}
			.AdminLeft dl dd a:hover,.AdminLeft dl dd a.cur{
				background-color:#fff;
				color:#333;
			}
			.AdminContent{
				width:80%;
				float:right;
				height:100%;
			}
EOT;
		$this->AppendCss($css);
		$js=<<<EOT
			$(document).ready(function(){
				$('.AdminLeft dd a').click(function(){
//					$('.AdminContent').load($(this).attr('href'));
					$('.cur').removeClass('cur');
					$(this).addClass('cur');
					$('.left').height($(this).contents().find(".content").height());
				});
			});
EOT;
		$this->AppendJavascript($js);
	}

	public function Index()
	{
		echo('后台管理插件');
	}
	 /**
	  * 处理安装业务(如果此插件需要安装,则需要在此方法实现)
	  * @return boolean | array 返回安装结果 | 需要附加到安装标志的数据
	  */
	 public function Install()
	 {
        return true;
    }
    /**
     * 卸载插件
     * @return boolean
     */
    public function UnInstall()
    {
        return true;
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
    public function GetPluginData()
    {
        return array(
            'pluginName'    =>    '后台管理',
            'author'        =>    'mmfei',
            'email'         =>    'wlfkongl@163.com',
            'url'           =>    'www.mmfei.com',
            'desc'          =>    '后台管理插件,管理后台代码',
        );
    }
}
