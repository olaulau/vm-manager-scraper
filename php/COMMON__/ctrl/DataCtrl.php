<?php
namespace COMMON__\ctrl;

use Base;
use ErrorException;
use Lib\Matrix;
use Lib\VmQueryCached;
use Lib\VmScraperCached;


class DataCtrl extends PrivateCtrl
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
			"name"		=>	"data",
			"title"		=>	"Data",
			"breadcrumbs" => static::breadcrumbs(),
		];
		
		self::renderPage($page);
	}
	
	
	public static function teamGET (Base $f3, array $url, string $controler)
	{
		// get team data
		$vsc = new VmScraperCached (VmQueryCached::auth_from_session());
		$data = $vsc->get_team_data ();
		$f3->set("data", $data);
		$f3->set("wt", $vsc->wt);
		
		$page = [
			"module"	=>	"COMMON__",
			"layout"	=>	"default",
			"name"		=>	"matrix",
			"title"		=>	"Team",
			"breadcrumbs" => static::breadcrumbs(),
		];
		self::renderPage($page);
	}
	
	
	public static function leagueGET (Base $f3, array $url, string $controler)
	{
		// get league data
		$vsc = new VmScraperCached (VmQueryCached::auth_from_session());
		$data = $vsc->get_league_data ();
		$f3->set("data", $data);
		$f3->set("wt", $vsc->wt);
		
		$page = [
			"module"	=>	"COMMON__",
			"layout"	=>	"default",
			"name"		=>	"matrix",
			"title"		=>	"League",
			"breadcrumbs" => static::breadcrumbs(),
		];
		self::renderPage($page);
	}
	
	
	public static function transfertsGET (Base $f3, array $url, string $controler)
	{
		// get transfert data
		$vsc = new VmScraperCached (VmQueryCached::auth_from_session());
		$data = $vsc->get_transfert_data_pages(4);
		$f3->set("data", $data);
		$f3->set("wt", $vsc->wt);
		
		$page = [
			"module"	=>	"COMMON__",
			"layout"	=>	"default",
			"name"		=>	"matrix",
			"title"		=>	"Transferts",
			"breadcrumbs" => static::breadcrumbs(),
		];
		self::renderPage($page);
	}
	
	
	public static function coachesGET (Base $f3, array $url, string $controler)
	{
		// get coaches data
		$vsc = new VmScraperCached (VmQueryCached::auth_from_session());
		$data = $vsc->get_coaches_data();
		$f3->set("data", $data);
		$f3->set("wt", $vsc->wt);
		
		$page = [
			"module"	=>	"COMMON__",
			"layout"	=>	"default",
			"name"		=>	"coaches",
			"title"		=>	"Coaches",
			"breadcrumbs" => static::breadcrumbs(),
		];
		self::renderPage($page);
	}
	
	
	public static function coachChangeGET (Base $f3, array $url, string $controler)
	{
		// params
		$coach_id = intval($f3->get("PARAMS.id"));
		if(empty($coach_id) || $coach_id < 0) {
			throw new ErrorException("invalid parameters");
		}
		
		// get coachChange data
		$vsc = new VmScraperCached (VmQueryCached::auth_from_session());
		$data = $vsc->get_coach_change_data_pages($coach_id, 4);
		$f3->set("data", $data);
		$f3->set("wt", $vsc->wt);
		
		$page = [
			"module"	=>	"COMMON__",
			"layout"	=>	"default",
			"name"		=>	"matrix",
			"title"		=>	"Coaches",
			"breadcrumbs" => static::breadcrumbs(),
		];
		self::renderPage($page);
	}
	
}
