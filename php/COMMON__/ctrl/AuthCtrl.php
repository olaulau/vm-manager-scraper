<?php
namespace COMMON__\ctrl;

use Base;
use Cache;
use Lib\VmQueryCached;
use Lib\WebsiteTalk;


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
		
		$vqc = new VmQueryCached(new WebsiteTalk());
		$res = $vqc->authenticate($login, $password);
		if($res === null) {
			sleep(3);
			$f3->clear("SESSION.user");
			$f3->reroute(["login"], [], ["message" => "auth failed"]);
		}
		else {
			$hashed_password = hash("sha256", $password);
			$f3->set("SESSION.user", [
				"login"				=> $login,
				"hashed_password"	=> $hashed_password,
			]);
			
			$redirect_url = $f3->get("GET.redirect_url");
			if(!empty($redirect_url)) {
				$f3->reroute($f3->get("GET.redirect_url"));
			}
			else {
				$f3->reroute(["data"]);
			}
		}
		die;
	}
	
	
	public static function logoutGET (Base $f3, array $url, string $controler)
	{
		$cache = Cache::instance();
		
		$user = $f3->get("SESSION.user");
		if(!empty($user)) {
			$cache_key = "{$user ["login"]}_{$user ["hashed_password"]}_auth_cookies__http";
			$cache->clear($cache_key);
		}
		
		$f3->clear("SESSION.user");
		$f3->reroute(["login", [], ["message" => "explicit logout"]]);
		die;
	}
	
}
