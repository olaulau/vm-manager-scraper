<?php
namespace COMMON__\ctrl;

use Base;


abstract class PrivateCtrl extends Ctrl
{
	
	public static function beforeRoute ()
	{
		parent::beforeRoute();
		
		$f3 = Base::instance();
		if(empty($f3->get("SESSION.user"))) {
			$f3->reroute(["login"]);
			die;
		}
	}
	
	
	public static function afterRoute ()
	{
		parent::afterRoute();
	}
	
}
