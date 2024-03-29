<?php
class Html
{
	public function __construct($content = '', $title = '', $javascript = '', $css = '')
	{
		$GLOBALS['mmHtml']['Css'] 			= $css;
		$GLOBALS['mmHtml']['Javascript'] 	= $javascript;
		$GLOBALS['mmHtml']['Body'] 			= $content;
		$GLOBALS['mmHtml']['Title'] 		= $title;
	}
	public static function Get($s , $defualt = null)
	{
		return isset($_GET[$s]) ? $_GET[$s] : $defualt;
	}
	public static function Post($s , $defualt = null)
	{
		return isset($_POST[$s]) ? $_POST[$s] : $defualt;
	}
	public static function PG($s , $defualt = null)
	{
		return isset($_POST[$s]) ? $_POST[$s] : (isset($_GET[$s]) ? $_GET[$s] : $defualt);
	}
	public function AppendCss($s)
	{
		$GLOBALS['mmHtml']['Css'].=$s;
		return $this;
	}
	public function Css($s)
	{
		$GLOBALS['mmHtml']['Css']=$s;
		return $this;
	}
	public function AppendJavascript($s)
	{
		$GLOBALS['mmHtml']['Javascript'].=$s;
		return $this;
	}
	public function Javascript($s)
	{
		$GLOBALS['mmHtml']['Javascript']=$s;
		return $this;
	}
	public function AppendBody($s)
	{
		$GLOBALS['mmHtml']['Body'].=$s;
		return $this;
	}
	public function AppendFooter($s)
	{
		$GLOBALS['mmHtml']['Footer'].=$s;
		return $this;
	}
	public function Body($s)
	{
		$GLOBALS['mmHtml']['Body']=$s;
		return $this;
	}
	public function AppendTitle($s)
	{
		$GLOBALS['mmHtml']['Title'].=$s;
		return $this;
	}
	public function Title($s)
	{
		$GLOBALS['mmHtml']['Title']=$s;
		return $this;
	}
	public function AppendInput($formName , $inputName , $inputLabel = '' , $value = '' , $type = 'text' , $label = '' , $selected = array() , $br = false , $attr = array())
	{
		if(isset($GLOBALS['mmHtml']['Form'][$formName]))
		{
			if(!isset($GLOBALS['mmHtml']['Form'][$formName]['Input'][$inputName]))
			{
				$GLOBALS['mmHtml']['Form'][$formName]['Input'][$inputName] = array(
					'Label'		=>	$inputLabel ,
					'Selected'	=>	$selected,
					'Type'		=>	$type,
					'List'		=>	array(
										array(
											'Name'		=>	$inputName,
											'Value'		=>	$value,
											'Label'		=>	$label,
											'Br'		=>	$br,
											'Attr'		=>	$attr,
										),
					),
				);
			}
			else 
			{
				$GLOBALS['mmHtml']['Form'][$formName]['Input'][$inputName]['List'][] = array(
					'Name'		=>	$inputName,
					'Value'		=>	$value,
					'Label'		=>	$label,
					'Br'		=>	$br,
					'Attr'		=>	$attr,
				);
			}
		}
		return $this;
	}
	public function AppendTextToForm($name , $string)
	{
		$GLOBALS['mmHtml']['Form'][$name]['FormAppendText'] .= $string;
		return $this;
	}
	public function AppendTextToFormBottom($name , $string)
	{
		$GLOBALS['mmHtml']['Form'][$name]['FormAppendTextBottom'] .= $string;
		return $this;
	}
	public function Form($method , $name , $action = '' , $caption = '' , $th = null , $tr = null , $columns = null , $sumbitText = null , $attr = array())
	{
		$GLOBALS['mmHtml']['Form'][$name] = array(
			'Method'	=>	$method ,
			'Action'	=>	$action ? $action : $_SERVER['REQUEST_URI'] ,
			'Caption'	=>	$caption ,
			'Th'		=>	isset($th) ? $th : '<th colspan={Columns}>{Caption}</th>' ,
			'Tr'		=>	isset($tr) ? $tr : '<tr><td>{Label}</td><td>{Text}</td></tr>' ,
			'Columns'	=>	isset($columns) ? $columns : 2,
			'FormAppendText'=> '',
			'FormAppendTextBottom'=> '',
			'SumbitText'=>	isset($sumbitText) ? $sumbitText : '确定',
			'Input'		=>	array(),
			'Attr'		=>	$attr,
		);
		return $this;
	}
	protected function ToForm()
	{
		$form = '';
		foreach($GLOBALS['mmHtml']['Form'] as $formName => $data)
		{
			$caption = str_replace(array('{Caption}','{Columns}',), array($data['Caption'],$data['Columns']), $data['Th']);
			if($data['Attr'])
			{
				$attrString = '';
				foreach($data['Attr'] as $key => $value)
					$attrString .= ' '.$key.'="'.$value.'"';
			}
			else 
			{
				$attrString = '';
			}
			if($data)
			{
				$form.=<<<EOT
					<form name="{$formName}"{$attrString} action="{$data['Action']}" method="{$data['Method']}">
						<table>
							<thead>
								<tr>{$caption}</tr>
							</thead>
							<tbody>	
EOT;
				foreach($data['Input'] as $inputName => $arrInput)
				{
					$s = '';
					if($arrInput['Type'] == 'select')
					{
						$s.=<<<EOT
							<select name='{$inputName}'>
EOT;
					}
					foreach($arrInput['List'] as $iii => $input)
					{
						$checked = '';
						if(in_array($input['Value'] , $arrInput['Selected']))
						{
							switch ($arrInput['Type'])
							{
								case 'radio':
								case 'checkbox':
									$checked = ' checked="checked"';
									break;
								case 'select':
									$checked = ' selected = "selected"';
									break;
								default:
									$checked = '';
							}
						}
						if($input['Br'])
						{
							if(is_numeric($input['Br']) && $iii &&  $iii % $input['Br'] == 0)
							{
								$br = '<br />';
							}
							else
								$br = '<br />';
						}
						else 
						{
							$br = '';
						}
						$attr = '';
						if($input['Attr'])
						{
							foreach($input['Attr'] as $key => $value)
							{
								$attr .= " {$key} => '{$value}'";
							}
						}
						if($arrInput['Type'] == 'hidden')
						{
							$s.=<<<EOT
								<input type="{$arrInput['Type']}" name="{$input['Name']}" value="{$input['Value']}" id="{$input['Name']}_{$input['Value']}" {$attr}/>
EOT;
						}
						elseif($arrInput['Type'] == 'password' || $arrInput['Type'] == 'file')
						{
							$s.=<<<EOT
								<input type="{$arrInput['Type']}" name="{$input['Name']}" value="{$input['Value']}" id="{$input['Name']}_{$input['Value']}" {$attr}/>
EOT;
						}
						elseif($arrInput['Type'] == 'label')
						{
							$s.=<<<EOT
								{$input['Value']}
EOT;
						}
						elseif($arrInput['Type'] == 'select')
						{
							$s.=<<<EOT
								<option name="{$input['Name']}"  value="{$input['Value']}" {$checked} {$attr}>{$input['Label']}</option>
EOT;
						}
						else
						{
							$s.=<<<EOT
								<input type="{$arrInput['Type']}" name="{$input['Name']}" value="{$input['Value']}" id="{$input['Name']}_{$input['Value']}" {$checked} {$attr}/>
								<label for="{$input['Name']}_{$input['Value']}">{$input['Label']}</label>{$br}				
EOT;
						}
					}
					
					if($arrInput['Type'] == 'hidden')
					{
						$form.= $s;
					}
					else 
					{
						$form.= str_replace(array(
							'{Label}',
							'{Text}',
						), array(
							$arrInput['Label'],
							$s,
						), $data['Tr']);
					}
				
					if($arrInput['Type'] == 'select')
					{
						$s.=<<<EOT
							</select>
EOT;
					}
				}
				if($data['FormAppendTextBottom'])
				{
					$form.= $data['FormAppendTextBottom'];
				}
				if(is_array($data['SumbitText']))
				{
					$attrStr = '';
					if(!isset($data['SumbitText']['type']))
					{
						$attrStr = " type='submit'";
					}
					foreach($data['SumbitText'] as $name => $value)
					{
						$attrStr.= " {$name} = '{$value}'";
					}
					$form.= <<<EOT
				
						<tr><td colspan={$data['Columns']} style="text-align:center;"><input {$attrStr}/></td></tr>			
EOT;
				}
				else
					$form.= <<<EOT
					
						<tr><td colspan={$data['Columns']} style="text-align:center;"><input type="submit" value="{$data['SumbitText']}" /></td></tr>			
EOT;
				$form.=<<<EOT
				
					</tbody>
				</table>{$data['FormAppendText']}
			</form>
EOT;
			}//end if ($data)
		}//end foreach
		return $form;
	}
	public function Show($isReturn = false)
	{
		$html=<<<EOT
			<html>
				<head>
					<meta http="equiv-content" content="text/html;charset=utf-8"/>
					<title>{Title}</title>
					{Meta}
					<style>
						{Css}
					</style>
					<script type="text/javascript" src="/include/jquery.js"></script>
					<script type="text/javascript" language="javascript">
						{Javascript}
					</script>
				</head>
				<body>
					<div class='main'>
					{Body}{Footer}
					</div>
				</body>
			</html>
EOT;
		$body = $GLOBALS['mmHtml']['Body'] . $this->ToForm();
		
		$html = str_replace(
			array(
				'{Title}',
				'{Css}',
				'{Meta}',
				'{Javascript}',
				'{Body}',
				'{Footer}',
			), 
			array(
				$GLOBALS['mmHtml']['Title'],
				$GLOBALS['mmHtml']['Css'],
				$GLOBALS['mmHtml']['Meta'],
				$GLOBALS['mmHtml']['Javascript'],
				$body,
				$GLOBALS['mmHtml']['Footer'],
			),
			$html
		);
		
		if($isReturn)
			return $html;
		echo($html);
	}
	
	
	public function InitDefaultCss()
	{
		$css =<<<EOT
			body{font-size:14px;text-align:center;}
			.main{
				margin:0 auto;
				text-align:center;
			}
			table , .table{
				border-collapse:collapse;
				margin:0 auto 15px;
				width:600px;
			}
			captain{
				font-size:bold;
				text-align: center;
				width:98%;
				margin:0 auto;
				font-weight:bold;
				font-size:16px;
				height:35px;
				line-height:35px;
				background-color: #FFDEAD;
				border: 1px solid #999999;
				display:block;
			}
			th {
				font-size:bold;
				text-align: center;
				padding: 6px 6px 6px 12px;
				background-color: #EAF5F7;
				border: 1px solid #999999;
			}
			td {
				padding: 6px 6px 6px 12px;
				border:1px solid #ccc;
			} 
			.err , #tips_temp{
				padding: 6px 6px 6px 12px;
				background-color: #FFDEAD;
			}
			#tips{
				color:#CD5C5C;
				font-size:12px;
			}
EOT;
		$this->AppendCss($css);
		return $this;
	}
	public function InitDefaultJs()
	{
		$js =<<<EOT
			\$(document).ready(function(){
				\$('tr').hover(
					function(){
						\$(this).css({'backgroundColor':'#EAF5F7'});
					},
					function()
					{
						\$(this).css({'backgroundColor':'#fff'});
					}
				);
			});
EOT;
		$this->AppendJavascript($js);
		return $this;
	}
	public function InitAjaxSubmit()
	{
		$js =<<<EOT
			\$(document).ready(function(){
				\$('form.ajaxForm').submit(function(){
					
					\$.ajax({
						type : \$(this).attr('method'),
						url : \$(this).attr('action'),
						cache : false,
						data : \$(this).serialize(),
						error : function(){
							alert('提交失败!');
						},
						success : function(data)
						{
							if(data == true || data == 1 || data == '1')
								alert('提交成功!');
							else
								alert('提交失败'+data);
						}
					});
					return false;
				});	
			});	
EOT;
		return $this->AppendJavascript($js);
	}
	public function appendMeta($name  , $content , $nameKey = 'name')
	{
		$GLOBALS['mmHtml']['Meta'] .= <<<EOT

		<meta {$nameKey}="{$name}" content="{$content}" />
EOT;
	}
	public function clear()
	{
		$GLOBALS['mmHtml'] = array(
			'Css'			=>	'',
			'Meta'			=>	'',
			'Javascript'	=>	'',
			'Body'			=>	'',
			'Footer'		=>	'',
			'Title'			=>	'',
			'Form'			=>	array(),
		);
	}
	
	public function WaitingToUrl($url , $title = null , $content = null , $time = 5)
	{
		self::clear();
//		self::appendMeta("refresh", "{$time};url={$url}" , "http-equiv");
		if(is_null($content))
		{
			$js =<<<EOT
			$(document).ready(function(){
				wtLoading();
			});
			function wtLoading()
			{
				unit = 1000;
				count = {$time} * 1000 / unit;
				width = $('#percent').width();
				warpWidth = $('#percent').parent().width();
				nowWidth = width + 300 / count;
				
				if(nowWidth >= warpWidth)
				{
					$('#percent').text('100%');
					window.location = "{$url}";
				}
				else
				{
					percent = parseInt((nowWidth / warpWidth) * 100);
					$('#percent').text(percent + '%').animate({
						'width':nowWidth
					}, unit);
					setTimeout('wtLoading()',unit);
				}
			}
EOT;
			$css=<<<EOT
				#loading{
					width:302px;
					height:18px;
					line-height:18px;
					border:1px solid #999999;
					margin:0 auto;
				}
				#percent{
					width:1px;
					margin:1px;
					height:16px;
					line-height:16px;
					background-color:#EAF5F7;
					text-align:right;
				}
				#loadingTips{
					
				}
				#loadingTips a{
					text-decoration:none;
					font-weight:bold;
				}
EOT;
			self::AppendCss($css)->InitDefaultCss();
			self::AppendJavascript($js);
			if(isset($title))
			{
				$css=<<<EOT
					h1{
						font-size:14px;
						height:30px;
						line-height:30px;
						text-align:center;
						margin:0 auto;
					}			
EOT;
				self::AppendCss($css);
				$content=<<<EOT
<div class="main">
	<h1>{$title}</h1>
	<div id='loading'>
		<div id='percent'></div>
	</div>
	<p id='loadingTips'> 
		如果您的浏览器不支持跳转,
		<a href="{$url}">请点这里</a>.
	</p>
</div>
EOT;
			}
			else
			{
				$content=<<<EOT
<div class="main">
	<div id='loading'>
		<div id='percent'></div>
	</div>
	<p id='loadingTips'> 
		如果您的浏览器不支持跳转,
		<a href="{$url}">请点这里</a>.
	</p>
</div>
EOT;
				
			}
		}
		if(isset($title))
		{
			self::Title($title);
			
		}
		self::AppendBody($content);
		self::Show();
		exit(0);
	}
}