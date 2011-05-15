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
	public static function index()
	{
		echo('<pre>');
		echo(md5('mb'));
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
