<?php
/**
 * 测试
 * @author mmfei<wlfkongl@163.com>
 *
 */
class Test
{
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
