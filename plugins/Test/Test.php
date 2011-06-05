<?php
/**
 * 测试
 * @author mmfei<wlfkongl@163.com>
 *
 */
class Test
{
	public static function getAppList()
	{
		Database::IncrementBy('fbImage', array('voteCount'=>1),array('imageId = 1'));
	}
	public static function FB()
	{
		$html=<<<EOT
			<html>
				<head>
					<meta http="equiv-content" content="text/html;charset=utf-8"/>
					<title>投票</title>
					<style>
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
					</style>
					<script type="text/javascript" src="/include/jquery.js"></script>
					<script type="text/javascript" language="javascript">
									$(document).ready(function(){
				$('tr').hover(
					function(){
						$(this).css({'backgroundColor':'#EAF5F7'});
					},
					function()
					{
						$(this).css({'backgroundColor':'#fff'});
					}
				);
			});			$(document).ready(function(){
				$('form.ajaxForm').submit(function(){
					
					$.ajax({
						type : $(this).attr('method'),
						url : $(this).attr('action'),
						cache : false,
						data : $(this).serialize(),
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
					</script>

				</head>
				<body>
					<div class='main'>
										<form name="form1" enctype="multipart/form-data" class="ajaxForm1" action="http://facebook.mobilebrother.net/index.php/Vote/Upload" method="post">
						<table>
							<thead>
								<tr><th colspan=2>上传测试</th></tr>
							</thead>

							<tbody>	<tr><td>图片名</td><td>								<input type="text" name="imageName" value="" id="imageName_"  />
								<label for="imageName_"></label>				</td></tr><tr><td>文件</td><td>								<input type="file" name="file" value="" id="file_" /></td></tr><tr><td>应用ID</td><td>								<input type="radio" name="appId" value="1" id="appId_1"  />
								<label for="appId_1">头像</label>				</td></tr>					
						<tr><td colspan=2 style="text-align:center;"><input type="submit" value="上传" /></td></tr>							
					</tbody>

				</table>
			</form>
					</div>
				</body>
			</html>
EOT;
		echo($html);
	}
	public static function phpinfo()
	{
		phpinfo();
	}
	public static function getFbUser()
	{
		$param = Controller::GetParam();
		$id = isset($param[2]) ? $param[2] : 0;
		$graph_url = "https://graph.facebook.com/". $id;
		$user = json_decode(self::fetch_page('facebook',$graph_url));
		print_r($user);
	}
	function fetch_page($site,$url,$params=false)
	{
	    $ch = curl_init();
	    $cookieFile = $site . '_cookiejar.txt';
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
	    curl_setopt($ch, CURLOPT_COOKIEFILE,$cookieFile);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch,   CURLOPT_SSL_VERIFYPEER,   FALSE);
	    curl_setopt($ch, CURLOPT_HTTPGET, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
	    if($params)
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
	    curl_setopt($ch, CURLOPT_URL,$url);
	
	    $result = curl_exec($ch);
	    //file_put_contents('jobs.html',$result);
	    return $result;
	}	
	public static function index()
	{
		echo('<pre>');
//		print_r(Page);
		$page = 12;
		$pageSize = 2;
		$url = Controller::ParamToUrl(array(__CLASS__,__FUNCTION__));
		$config = array(
			'template'	=>	array(
				'numeric'	=>	'<a href="{url}{flag}{name}/{page}">{page}</a>',
				'prev'		=>	'<a href="{url}{flag}{name}/{page}" class="prev">{text}</a>',
				'next'		=>	'<a href="{url}{flag}{name}/{page}" class="next">{text}</a>',
			),
			'special'=>array('flag'=>'/')
		);
		Page::SetConfig($config);
		$s = Page::GetPageList(100 , $url , $page , $pageSize , Page::TYPE_NUMERIC);
		$html = new Html();
		$html->AppendCss(Page::GetCss());
		$html->AppendBody($s);
		$html->Show();
//		$sql = 'select * from fbrole';
//		$data = Database::GetListBy('fbRole' , 'roleId' , null ,array('1'=>'1'));
//		print_r($data);
	}
    /**
     * 处理安装业务(如果此插件需要安装,则需要在此方法实现)
     * @return boolean | array 返回安装结果 | 需要附加到安装标志的数据
     */
    public static function Install()
    {
//        $sql = <<<EFT
//            
//EFT;
//        Database::execute($sql);
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
            'pluginName'    =>    '测试',
            'author'        =>    'mmfei',
            'email'            =>    'wlfkongl@163.com',
            'url'            =>    'www.mmfei.com',
            'desc'            =>    '测试插件',
        );
    }
}
