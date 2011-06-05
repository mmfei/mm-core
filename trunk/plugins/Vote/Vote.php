<?php
class Vote
{
	const _FACEBOOK_SESSION_ = '_fb_mb_';
	public function __construct(array $args = array())
	{
		
	}
	public function AddAppForm()
	{
		$html = new Html();
		$method = 'post';
		$formName = 'form1';
		$action = Controller::ParamToUrl(array(__CLASS__ , 'AddAppForm'));
		$caption = '增加应用';
		
		$html->Form($method, $formName , $action , $caption , null , null , 2 , '添加' , array('class'=>'ajaxForm1'));
		$html->AppendInput($formName, 'appName' ,'应用名称' ,'','text');
		$html->InitDefaultCss()->InitDefaultJs()->InitAjaxSubmit();
		
		$appName = Html::PG('appName');
		if($appName)
		{
			$table = 'fbApp';
			$data = array(
				'appName'	=>	$appName,
				'isActived'	=>	1,
			);
			Database::InsertBy($table, $data);
			$html->AppendBody('添加应用成功');
		}
		$html->Title('添加应用')->Show();
	}
	public function AppListForm()
	{
		$appDataList = self::GetAllAppList();

		$html = new Html();
		$body = '';
		foreach($appDataList as $data)
		{
			$url = Controller::ParamToUrl(array(__CLASS__ , 'EditAppForm' , $data['appId']));
			$body.=<<<EOT
				<tr>
	                <td> 
	                    {$data['appId']} 
	                </td>
	                <td> 
	                    {$data['appName']} 
	                </td>
	                <td> 
	                    {$data['isActived']} 
	                </td>
	                <td> 
	                    {$data['startTime']} 
	                </td>
	                <td> 
	                    {$data['endTime']} 
	                </td>
	                <td> 
	                    <a href="{$url}">编辑</a>
	                </td>
				</tr>
EOT;
		}
		$table=<<<EOT
			<captain>应用列表</captain>
			<table class='table'>
				<thead>
					<tr>
		                <th>应用id</th>
		                <th>应用名称</th>
		                <th>是否激活(1:有效;0:无效)</th>
		                <th>活动开始时间</th>
		                <th>活动结束时间</th>
		                <th>编辑?</th>
					</tr>
				</thead>
				<tbody>
					{$body}
				</tbody>
			</table>
EOT;
		$html->Title('应用列表')->AppendBody($table)->InitDefaultCss()->InitDefaultJs()->Show();
	}
	public function EditAppForm()
	{
		$appId = Html::PG('appId');
		if(empty($appId))
		{
			$param = Controller::GetParam();
			if(isset($param[2])) 
			{
				$appId = $param[2];
			}
			else
			{
				return self::AppListForm();
			}
		}
		
	
		$html = new Html();
	
		$appData = self::GetAppByAppId($appId);
		$appName = Html::PG('appName');
		$startTime = Html::PG('startTime' , 0);
		$endTime = Html::PG('endTime' , 0);
		$isActived = Html::PG('isActived' , 0);
		if($appName && $appData)
		{
			if($startTime)
			{
				$startTime = strtotime($startTime);
			}
			if($endTime)
			{
				$endTime = strtotime($endTime);
			}
			$table = 'fbApp';
			$data = array(
				'appName'	=>	$appName,
				'startTime'	=>	$startTime,
				'endTime'	=>	$endTime,
				'isActived'	=>	$isActived ? 1 : 0,
			);
			foreach($data as $key => $value)
				$appData[$key] = $value;
			Database::UpdateBy($table, $data , array('appId = '.$appId));
			$html->AppendBody('修改应用成功');
		}
		
		if($appData)
		{
			if($appData['startTime'])
			{
				$appData['startTime'] = date('Y-m-d H:i:s' , $appData['startTime']);
			}
			if($appData['endTime'])
			{
				$appData['endTime'] = date('Y-m-d H:i:s' , $appData['endTime']);
			}
		}
		else 
		{
			return self::AppListForm();
		}
		
		$method = 'post';
		$formName = 'form1';
		$action = Controller::ParamToUrl(array(__CLASS__ , 'EditAppForm'));
		$caption = '编辑应用';
		$html->Form($method, $formName , $action , $caption , null , null , 2 , '编辑' , array('class'=>'ajaxForm1'));
		$html->AppendInput($formName, 'appId' ,'应用名称' ,$appData['appId'],'hidden');
		$html->AppendInput($formName, 'appName' ,'应用名称' ,$appData['appName'] , 'text');
		$html->AppendInput($formName, 'isActived' ,'激活?' ,1 , 'radio' , '是' , array($appData['isActived']));
		$html->AppendInput($formName, 'isActived' ,'激活?' ,0 , 'radio' , '否' , array($appData['isActived']));
		$html->AppendInput($formName, 'startTime' ,'开始时间(留空表示不限制)' ,$appData['startTime'] , 'text');
		$html->AppendInput($formName, 'endTime' ,'开始时间(留空表示不限制)' ,$appData['endTime'] , 'text');
		$html->InitDefaultCss()->InitDefaultJs()->InitAjaxSubmit();
		
		$html->Title('编辑应用')->Show();
	}
	public function getAllAppList()
	{
		$table = 'fbApp';
		$list = Database::GetListBy($table , 'appId');
		$data = array();
		foreach($list as $index => $arr)
		{
			$data[$arr['appId']] = $arr;
		}
		return $data;
	}
	public function GetAppList()
	{
		$table = 'fbApp';
		$list = Database::GetListBy($table , 'appId' , null , array('isActived = 1'));
		$data = array();
		foreach($list as $index => $arr)
		{
			$data[$arr['appId']] = $arr;
		}
		return $data;
	}
	public function Index()
	{
		echo('投票插件');
	}
	public function Admin()
	{
		$appList = self::GetAppList();
		$admin = new Admin();
		$admin->AppendSidebar('投票', '所有应用的图片管理', Controller::ParamToUrl(array('Vote','VoteList')));
		$admin->AppendSidebar('投票', 'php测试上传', Controller::ParamToUrl(array('Vote','VoteForm')));
		$admin->AppendSidebar('投票', '管理应用', Controller::ParamToUrl(array('Vote','AppListForm')));
		$admin->AppendSidebar('投票', '添加应用', Controller::ParamToUrl(array('Vote','AddAppForm')));
		foreach($appList as $appId => $arr)
		{
			$admin->AppendSidebar('投票', '['.$arr['appName'].'] 上传的图片', Controller::ParamToUrl(array('Vote','VoteList',$appId)));
		}
	}
	public function DoVote()
	{
		$arrParam = Controller::GetParam();
		$imageId = isset($arrParam[2]) ? $arrParam[2] : 0;
		$userId = self::GetCurrentFbUserId();
		if($imageId && $userId)
		{
			if(Database::InsertBy('fbVote', array(
				'imageId'	=>	$imageId,
				'userId'	=>	$userId,
				'voteTime'	=>	time(),
			)))
			{
				Database::IncrementBy('fbImage', array('voteCount'=>1),array('imageId = '.$imageId));
				echo('投票成功!');
			}
		}
		return self::Facebook();
	}
	public function VoteForm()
	{
		$html = new Html();
		$method = 'post';
		$formName = 'form1';
		$action = Controller::ParamToUrl(array(__CLASS__ , 'Upload'));
		$caption = '上传测试';
		$appListData = Database::GetListBy('fbApp' , 'appId');
		
		$html->Form($method, $formName , $action , $caption , null , null , 2 , '上传' , array('enctype'=>"multipart/form-data" ,'class'=>'ajaxForm1'));
		$html->AppendInput($formName, 'imageName' ,'图片名' ,'','text')
		->AppendInput($formName, 'file' ,'文件' ,'','file');
		foreach($appListData as $appId => $arr)
		{
			$html->AppendInput($formName, 'appId','应用ID' ,$appId , 'radio' , $arr['appName'] , array(Html::PG('appId')));
		}
		$html->InitDefaultCss()->InitDefaultJs()->InitAjaxSubmit();
		$html->Title('投票')->Show();
	}
	public function Facebook()
	{
		//等级facebook的内容
		$facebook = self::GetFacebook();
		$fbUser = $facebook->getSession();
		if(isset($fbUser['uid']))
		{
			$userData = Database::GetRowBy('fbFacebookUser' , null , array('userId='.$fbUser['uid'],));
			if(empty($userData))
			{
				$fbUserData = self::getFacebookUserByCurl($fbUser['uid']);
				if($fbUserData)
				{
					$userData = array(
						'userId'	=>	$fbUser['uid'],
						'userName'	=>	$fbUserData['name'],
						'link'		=>	$fbUserData['link'],
					);
					Database::InsertBy('fbFacebookUser', $userData);
				}
			}
			if($userData)
				self::SetFbSession($userData);
		}

		$html = new Html();
		$arrParam = Controller::GetParam();
		$appId = isset($arrParam[2]) ? $arrParam[2] : 1;
		$page = isset($arrParam[3]) ? $arrParam[3] : 1;
		$pageSize = isset($arrParam[4]) ? $arrParam[4] : 30;
		$count = 0;
		$arrListData = Database::GetListBy('fbImage' , null , null , array('appId = '.$appId) , null , $page , $pageSize , $count);
		if(!self::GetFbSession())
		{
			$html->AppendBody('<div>尚未登陆,不能投票!</div>');
		}
		$htmlString = '<div class="MyVoteList">';
		if($arrListData)
		{
			foreach($arrListData as $arr)
			{
				$imgSrc = self::GetPath() . $arr['url'];
				$imgSrc = strtr($imgSrc , array(ROOT_DIR => ''));
				$voteUrl = Controller::ParamToUrl(array(__CLASS__,'DoVote',$arr['imageId']));
				$htmlString.=<<<EOT
				<a href="{$voteUrl}" class='VoteImage' id="{$arr['imageId']}">
					<img src="{$imgSrc}" alt="{$arr['imageName']}"/>
					<span>{$arr['imageName']}</span>
				</a>
EOT;
			}
		}
		$config = array(
			'template'	=>	array(
				'numeric'	=>	'<a href="{url}{flag}{page}/'.$pageSize.'">{page}</a>',
				'prev'		=>	'<a href="{url}{flag}{page}/'.$pageSize.'" class="prev">{text}</a>',
				'next'		=>	'<a href="{url}{flag}{page}/'.$pageSize.'" class="next">{text}</a>',
			),
			'special'=>array('flag'=>'/')
		);
		
		$htmlString.="</div><div style='clear:both;'></div>";
		$url = Controller::ParamToUrl(array(__CLASS__,__FUNCTION__,$appId));
		$htmlString.= Page::GetPageList($count , $url , $page , $pageSize , Page::TYPE_NUMERIC , $config);
		$css = <<<EOT
			body{text-align:center;}
			.MyVoteList{
				margin:0 auto;
			}
			.MyVoteList a{
				width:110px;
				height:150px;
				display:block;
				text-align:center;
				float:left;
				border:1px solid #fff;
			}
			.MyVoteList a img{
				border:0px;
				width:98px;
				height:98px;
				margin:5px;
			}
			.MyVoteList a:hover{
				background-color:#E3E4FA;
				border-color:#DDDDDD;
			}
			.MyVoteList a:hover img{}
EOT;
		
		return $html->AppendCss($css)->AppendCss(Page::GetCss())->AppendBody($htmlString)->Show();
	}
	/**
	 * 获取可投票列表
	 */
	public function VoteList()
	{
		$appId = Html::PG('appId');
		$page = Html::PG('page' , 1);
		$pageSize = Html::PG('pageSize' , 30);
		
		$facebook = self::GetFacebook();
		$session = $facebook->getSession();
		
		if(!isset($session))
		{
			
		}
		
		if(empty($appId))
		{
			$param = Controller::GetParam();
			if(isset($param[2])) 
			{
				$appId = $param[2];
			}
		}
		
		$html = new Html();
		$method = 'post';
		$formName = 'form1';
		$action = Controller::ParamToUrl(array(__CLASS__ , __FUNCTION__));
		$caption = '选择应用';
		$appListData = Database::GetListBy('fbApp' , 'appId');

		$html->Form($method, $formName , $action , $caption , null , null , 2 , '查看');
		foreach($appListData as $appId1 => $arr)
		{
			$html->AppendInput($formName, 'appId' , '应用' , $appId1 , 'radio' , $arr['appName'] , array($appId));
		}
		
		if($appId)
		{
			$count = 0;
			$imageListData = Database::GetListBy('fbImage' , 'imageId' , null , array('appId = '.$appId) , null , $page , $pageSize , $count);

			$s = '<div class="imgList">';
		
			foreach($imageListData as $imageId => $arr)
			{
				$url = strtr(self::GetPath().$arr['url'] , array(ROOT_DIR => ''));
				$s .=<<<EOT
					<a href='#'>
						<img src="{$url}" value="{$imageId}" alt="{$arr['imageName']}" />
						<span>{$arr['imageName']}</span>
					</a>			
EOT;
			}
			$s.='</div>';
			$html->AppendFooter($s);
			$css=<<<EOT
				.imgList{}
				.imgList a{text-align:center;display:block;float:left;margin:10px;width:150px;150px;}
				.imgList a:hover{background-color:#eee;}
				.imgList a img{width:120px;height:120px;display:block;margin:10px auto 5px;}
				.imgList a span{display:block;margin:5px auto 10px;;}
EOT;
			$js=<<<EOT
				$(document).ready(function(){
					$('.imgList a').click(function(){
						alert($('img',this).attr('value'));
					});
				});
EOT;
			$html->AppendCss($css)->AppendJavascript($js);
		}
		else 
		{
			$html->AppendBody('没有图片');
		}
		$html->Title('投票')->InitDefaultCss()->InitDefaultJs()->Show();
	}
	/**
	 * 获取图片
	 */
	public function Image()
	{
		$arrParam = Controller::GetParam();
		$imageId = isset($arrParam[2]) ? $arrParam[2] : '';
		if($imageId)
		{
			$imageData = self::GetImageByImageId($imageId);
			if($imageData)
			{
				header('Content-Type: image/png');
				$path = self::GetPath().$imageData['url'];
				$im = ImageCreateFromPNG($path);
				imagepng($im);
				imagedestroy($im);
			}
		}
		else
		{
			
		}
	}
	/**
	 * 根据图片ID获取图片数据
	 * @param integer $imageId
	 * @param array $fields
	 * @return array(
	 * 		...
	 * )
	 */
	public function GetImageByImageId($imageId , array $fields = null)
	{
		return Database::GetRowBy('fbImage' , $fields , array('imageId'=>$imageId));
	}
	/**
	 * 根据应用ID获取应用数据
	 * @param integer $appId
	 * @param array $fields
	 * @return array(
	 * 		...
	 * )
	 */
	public function GetAppByAppId($appId , array $fields = null)
	{
		return Database::GetRowBy('fbApp' , $fields , array('appId = '.$appId));
	}
	/**
	 * 上传图片处理
	 * @return integer
	 * 		-11 : 应用id不能为空
	 * 		-22 : 图片名称不能为空
	 * 		-33 : 上传文件数据不存在
	 * 		-44 : 保存数据失败(入库)
	 * 		-55 : 应用id错误(数据库中无数值)
	 * 		-66 : 应用被系统禁用
	 */
	public function Upload()
	{
		echo('<pre>');
		print_r($_POST);
		echo('</pre>服务器结果:');
		$appId = Html::PG('appId');
		$imageName = Html::PG('imageName');
		if(empty($appId))
		{
			echo -11;
			exit;
		}

		$appData = self::GetAppByAppId($appId);
		if(empty($appData))
		{
			echo -55;
			exit;
		}
		elseif(!isset($appData['isActived']) || $appData['isActived'] != 1)
		{
			echo -66;
			exit;
		}
		if(empty($imageName))
		{
			echo -22;
			exit;
		}
		if(empty($_FILES))
		{
			echo -33;
			exit;
		}

		$url = date('YmdHis').'.png';
		$imageData = array(
			'appId'		=>	$appId,
			'imageName'	=>	$imageName,
			'url'		=>	$url,
		);
		$return = self::UploadImg(self::GetPath().$url);
		if($return == 1)
		{
			if(Database::InsertBy('fbImage', $imageData))
			{
				echo 1;
				exit;
			}
			else 
			{
				echo -44;
				exit;
			}
		}
		else
		{
			echo $return;
			exit;
		}
		
		echo('到底了');
//		if($return == 0)
//		{
//			return r;
//		}
//		else
//		{
//			echo(Upload::ErrorCodeMapping($return));
//		}
//		return self::VoteForm();
	}
	public function UploadImg($fileFullPath)
	{
		$path = self::GetPath();
		Upload::SetUploadDir($path);
		$return = Upload::DoUpload('file' , $fileFullPath);
		return $return;
	}
	public function GetPath()
	{
		return Plugins::GetMmConfigBy(array('path','upload')).'Vote/';
	}
	public function GetCurrentFbUserId()
	{
		$data = self::GetFbSession();
		return isset($data['uid']) ? $data['uid'] : 0;
	}
	public function GetFbSession()
	{
		User::StartSession();
		return isset($_SESSION[Vote::_FACEBOOK_SESSION_]) ? $_SESSION[Vote::_FACEBOOK_SESSION_] : null;
	}
	public function SetFbSession($data)
	{
		User::StartSession();
		$_SESSION[Vote::_FACEBOOK_SESSION_] = $data;
	}
	public function UnsetFbSession()
	{
		User::StartSession();
		unset($_SESSION[Vote::_FACEBOOK_SESSION_]);
	}
    /**
     * 处理安装业务(如果此插件需要安装,则需要在此方法实现)
     * @return boolean | array 返回安装结果 | 需要附加到安装标志的数据
     */
    public static function Install()
    {
    	$sql = array();
        $sql[] = <<<EFT
create table fbApp(
    appId int not null auto_increment comment '应用id',
    appName varchar(50) not null comment '应用名称',
    isActived tinyint not null default 1 comment '是否激活(1:有效;0:无效)',
    startTime int not null default 0 comment '活动开始时间',
    endTime int not null default 0 comment '活动结束时间',
    primary key(appId)
)engine InnoDB charset utf8 comment '应用程序表';
EFT;
        $sql[] = <<<EFT
create table fbImage(
	imageId int auto_increment comment '图像ID',
	imageName varchar(255) comment '图像名称',
	url varchar(255) not null comment '图像地址',
	appId tinyint not null default 0 comment '应用程序类型ID',
	voteCount int not null default 0 comment '投票总次数',
	addTime int not null default 0 comment '添加时间',
	lastVoteTime int not null default 0 comment '最后一次投票时间',
	primary key(imageId),
    index(imageName),
	index(appId)
)engine InnoDB charset utf8 comment '投票的图片表';
EFT;
        $sql[] = <<<EFT
create table fbVote(
	imageId int not null comment '投票的图像id',
	userId varchar(255) not null comment '玩家Id',
	email varchar(255) not null default '' comment '邮箱',
	sex tinyint not null default 0 comment '性别(1:男;0:女)',
	voteTime int not null default 0 comment '投票时间',
	primary key(imageId),
	index(userId),
	index(voteTime)
)engine InnoDB charset utf8 comment '投票表';
EFT;
        $sql[] = <<<EFT
create table fbFacebookUser(
	userId int not null comment '玩家id',
	userName varchar(255) not null comment '玩家名称',
	email varchar(255) comment '邮箱',
	link varchar(255) not null comment '主页',
	sex tinyint not null default 0 comment '性别(1:男;0:女)',
	primary key(userId),
	index(userName)
)engine InnoDB charset utf8 comment '玩家表';
EFT;
		$sql[] =<<<EOT
        INSERT INTO `fbApp` SET `appName`='头像',`isActived`=1,`startTime`=0,`endTime`=0;
EOT;
		foreach($sql as $sql1)
        	Database::Execute($sql1);
        //创建文件夹
        $dir = self::GetPath();
        if(!is_dir($dir))
        {
        	mkdir($dir , true);
        }
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
            'pluginName'    =>    '投票插件',
            'author'        =>    'mmfei',
            'email'            =>    'wlfkongl@163.com',
            'url'            =>    'www.mmfei.com',
            'desc'            =>    '投票管理插件,针对每个应用进行管理对应的图片投票!',
        );
    } 
    /**
     * 
     * @return Facebook
     */
    public static function GetFacebook()
    {
    	return new Facebook(array(
		  'appId'  => '130693217005536',
		  'secret' => 'dade0330cdf5e326c6f8cc19f9646182',
		  'cookie' => true,
		));
    }
    
	public static function getFacebookUserByCurl($userId)
	{
		$graph_url = "https://graph.facebook.com/". $userId;
		$user = json_decode(self::GetCurlPage('facebook',$graph_url) , true);
		return($user);
	}
	public function GetCurlPage($site,$url,$params=false)
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
		FB::log($ch);
	    $result = curl_exec($ch);
	    return $result;
	}
	public static function Test()
	{
		$param = Controller::GetParam();
		$userId = isset($param[2]) ? $param[2] : 0;
		return self::getFacebookUserByCurl($userId);
	}
}