<?php
namespace COMMON__\ctrl;

use Base;
use ErrorException;
use Lib\VM;


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
		
		// prepare talk
		$conf = $f3->get("conf");
		$vm = new VM ();
		
		// auth
		$auth_res = $vm->authenticate ($conf ["auth"] ["login"], $conf ["auth"] ["pass"]);
		if($auth_res !== true) {
			throw new ErrorException("authentication failed");
		}
		
		// do stuff
		//TODO
		
		// display talk stats
		echo "<hr> {$vm->wt->queries_count} quer" . ($vm->wt->queries_count>1 ? "ies" : "y") . " (" . number_format ($vm->wt->queries_duration, 3, ",", " ") . " s) <br/>" . PHP_EOL;
		die;
	}
	
}
