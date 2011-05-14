<?php
function __autoload($className)
{
	$filename = INCLUDE_DIR .'/'.$className.'.php';
	if(file_exists($filename))
	{
		include_once $filename;
		return;
	}
	$filename = PLUGINS_DIR.'/'.$className.'/'.$className.'.php';
	if(file_exists($filename))
	{
		include_once $filename;
		return;
	}
	$filename = PLUGINS_DIR.'/'.$className.'.php';
	if(file_exists($filename))
	{
		include_once $filename;
		return;
	}
}