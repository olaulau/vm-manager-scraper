<?php
namespace COMMON__\ctrl;

use Base;


class IndexCtrl extends Ctrl
{

	public static function beforeRoute ()
	{
		parent::beforeRoute();
	}
	
	
	public static function afterRoute ()
	{
		parent::afterRoute();
	}

	
	public static function breadcrumbs ()
	{
		$res = [];
		return $res;
	}
	
	public static function indexGET (Base $f3, array $url, string $controler)
	{
		$page = [
			"module"	=>	"COMMON__",
			"layout"	=>	"default",
			"name"		=>	"index",
			"title"		=>	"Accueil",
			"breadcrumbs" => static::breadcrumbs(),
		];
		
		self::renderPage($page);
	}
	
	
	public static function testGET (Base $f3, array $url, string $controler)
	{
		// load FFF
		$f3 = Base::instance();
		$f3->config('conf/index.ini');
		
		// do stuff
		
		die;
	}
	
}
