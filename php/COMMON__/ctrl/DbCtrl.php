<?php
namespace COMMON__\ctrl;

use Base;
use COMMON__\mdl\Coach;
use DB\Cortex;
use DB\SQL;
use Lib\VmCached;
use Lib\VmScraper;

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
		
		// load coaches data
		$vms = new VmScraper(VmCached::auth_from_session());
		$coaches_data = $vms->get_coaches_data();
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
		
		// load coach change data
		$coach_wrapper = new Coach();
		$coaches = $coach_wrapper->find();
		foreach ($coaches as $coach) {
			$data = $vms->get_coach_change_data_pages($coach->id, 4);
			array_shift($data); // remove headers
			
			foreach ($data as $coach_data) {
				$coach = $coach_wrapper->findone(["id = ?", $coach_data ["id"]]);
				if(empty($coach)) {
					$coach = new Coach();
				}
				
				$coach->name 					= $coach_data ["Coach"];
				$coach->age 					= $coach_data ["Age"];
				$coach->entrainement_physique	= $coach_data ["Phy"];
				$coach->entrainement_technique	= $coach_data ["Tch"];
				$coach->psychologie				= $coach_data ["Psy"];
				if(!empty($coach_data ["Mot"])) {
					$coach->motivation			= $coach_data ["Mot"];
				}
				if(!empty($coach_data ["Ada"])) {
					$coach->adaptabilite		= $coach_data ["Ada"];
				}
				if(!empty($coach_data ["Jun"])) {
					$coach->travail_junior		= $coach_data ["Jun"];
				}
				$coach->niveau_discipline		= $coach_data ["Dis"];
				$coach->salaire					= $coach_data ["Salaire"];
				$coach->prix					= $coach_data ["Prix"];
				$coach->id						= $coach_data ["id"];
				$coach->save();
			}
		}
	}
	
}
