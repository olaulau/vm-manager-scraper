<?php
namespace COMMON__\ctrl;

use Base;
use COMMON__\mdl\Coach;
use DB\Cortex;
use DB\SQL;
use Lib\VmCached;

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
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		);
		$db = new SQL("sqlite:{$sqlite_filename}", null, null, $options);
		$f3->set('db',$db);
		
		// create coach table
		Coach::setup();
		
		$vmc = new VmCached(VmCached::auth_from_session());
		$coaches_data = $vmc->get_coaches_data();
		array_shift($coaches_data); // skip headers
		foreach ($coaches_data as $coach_data) {
			$coach_data = array_values($coach_data); // easier num keys
			$coach = new Coach();
			$coach->type 					= $coach_data [0];
			$coach->name 					= $coach_data [1];
			$coach->entrainement_physique	= $coach_data [2];
			$coach->travail_junior			= $coach_data [3];
			$coach->entrainement_technique	= $coach_data [4];
			$coach->adaptabilite			= $coach_data [5];
			$coach->psychologie				= $coach_data [6];
			$coach->niveau_discipline		= $coach_data [7];
			$coach->motivation				= $coach_data [8];
			$coach->age						= $coach_data [9];
			$coach->salaire					= $coach_data [10];
			$coach->id						= $coach_data [11];
			$coach->user_login				= $f3->get("SESSION.user.login");
			$coach->save();
		}
		
	}
	
}
