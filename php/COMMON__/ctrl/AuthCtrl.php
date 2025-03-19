<?php
namespace COMMON__\ctrl;

use Base;
use Lib\VmScraper;
use Lib\VmCached;

class AuthCtrl extends Ctrl
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
	
	
	public static function loginGET (Base $f3, array $url, string $controler)
	{
		$page = [
			"module"	=>	"COMMON__",
			"layout"	=>	"simple",
			"name"		=>	"login",
			"title"		=>	"Accueil",
			"breadcrumbs" => static::breadcrumbs(),
		];
		
		self::renderPage($page);
	}
	
	
	public static function loginPOST (Base $f3, array $url, string $controler)
	{
		$login = $f3->get("POST.login");
		$password = $f3->get("POST.password");
		
		$vmc = new VmCached();
		$res = $vmc->authenticate($login, $password);
		if($res === false) {
			$f3->clear("SESSION.user");
			$f3->reroute(["login"]);
		}
		else {
			$hashed_password = hash("sha256", $password);
			$f3->set("SESSION.user", [
				"login"				=> $login,
				"hashed_password"	=> $hashed_password,
			]);
			$f3->reroute(["data"]);
		}
		die;
	}
	
	
	public static function logoutGET (Base $f3, array $url, string $controler)
	{
		$f3->clear("SESSION.user");
		$f3->reroute(["homepage"]);
		die;
	}
	
}
