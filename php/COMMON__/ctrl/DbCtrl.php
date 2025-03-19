<?php
namespace COMMON__\ctrl;

use Base;
use COMMON__\mdl\Coach;
use DB\Cortex;
use DB\SQL;


class DbCtrl extends PrivateCtrl
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
	
	
	public static function testGET (Base $f3, array $url, string $controler)
	{
		// create sqlite in memory DB
		// $sqlite_filename = ":memory:";
		$sqlite_filename = __DIR__ . "/../../../tmp/test.sqlite";
		if(file_exists($sqlite_filename)) {
			unlink($sqlite_filename);
		}
		////////////////
		
		$options = array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // generic attribute
		);
		$db = new SQL("sqlite:{$sqlite_filename}", null, null, $options);
		$f3->set('db',$db);
		
		//////
		Coach::setup();
		
		
	}
	
}
