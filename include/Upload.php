<?php
class Upload
{
	static $path = '';
	public static function SetUploadDir($path)
	{
		self::$path = $path;
	}
	/**
	 * 上传文件
	 * @param string $inputName	需要上传的表单名称
	 * @param string $filePath	指定上传文件路径(包括文件名)
	 * @return integer			上传返回代码 (注意,没有-5的返回选项)
	 * 		1 : 上传成功
	 * 		0 : 上传失败
	 * 		-1: 上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值
	 * 		-2: 上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值
	 * 		-3: 文件只有部分被上传
	 * 		-4: 没有文件被上传
	 * 		-6: 找不到临时文件夹
	 * 		-7: 文件写入失败
	 * 		-8: 文件已经存在
	 */
	public static function DoUpload($inputName , $filePath = null)
	{
		if(isset($_FILES[$inputName]))
		{
			if(!isset($filePath))
			{
				$filePath = self::$path.'/'.$_FILES[$inputName]['name'];
			}
			if($_FILES[$inputName]['error'])
			{
				return (-1)*$_FILES[$inputName]['error'];
			}
			elseif(file_exists($filePath))
			{
				return -8;
			}
			elseif(move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
	}
	
	public static function ErrorCodeMapping($code = null)
	{
		$arr = array(
//			UPLOAD_ERR_OK 			=> '',
			UPLOAD_ERR_INI_SIZE 	=> '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
			UPLOAD_ERR_FORM_SIZE 	=> '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
			UPLOAD_ERR_PARTIAL 		=> '文件只有部分被上传',
			UPLOAD_ERR_NO_FILE 		=> '没有文件被上传',
			UPLOAD_ERR_NO_TMP_DIR 	=> '找不到临时文件夹',
			UPLOAD_ERR_CANT_WRITE 	=> '文件写入失败',
			8					 	=> '文件已经存在',
		);
		if(isset($code))
		{	
			if($code < 0) $code = -1 * $code;
			return $arr[$code];
		}
		return $arr;
	}
}