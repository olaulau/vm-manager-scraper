<?php
namespace COMMON__\ctrl;

use Base;
use COMMON__\mdl\Coach;
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
		//////////////////////////////////
		// sqlite in memory												// 50 ms
		$sqlite_filename = ":memory:";
		
		// sqlite in file												// 55 ms
		// $sqlite_filename = __DIR__ . "/../../../tmp/test.sqlite";
		// if(file_exists($sqlite_filename)) {
		// 	unlink($sqlite_filename);
		// }
		
		$dsn = "sqlite:{$sqlite_filename}";
		$options = array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		);
		$db = new SQL($dsn, null, null, $options);
		$f3->set('db',$db);
		//////////////////////////////////
		
		$db = $f3->get("db"); /** @var SQL $db */
		//////////////////////////////////
		// mysql memory engine											// 70 ms
		// $sql = "SET default_storage_engine=MEMORY;";
		// $db->exec($sql);
		
		// mysql innodb engine											// 70 ms
		//////////////////////////////////
		
		
		// create coach table
		
		Coach::setdown();
		Coach::setup();
		
		$db->begin();
		
		// load my coaches data
		$vms = new VmScraper(VmCached::auth_from_session());
		$coaches_data = $vms->get_coaches_data();
		array_shift($coaches_data); // skip headers
		foreach ($coaches_data as $coach_data) {
			$coach_data = array_values($coach_data); // easier num keys
			$my_coach = new Coach();
			$my_coach->type 					= $coach_data [0];
			$my_coach->name 					= $coach_data [1];
			$my_coach->entrainement_physique	= $coach_data [2];
			$my_coach->travail_junior			= $coach_data [3];
			$my_coach->entrainement_technique	= $coach_data [4];
			$my_coach->adaptabilite				= $coach_data [5];
			$my_coach->psychologie				= $coach_data [6];
			$my_coach->niveau_discipline		= $coach_data [7];
			$my_coach->motivation				= $coach_data [8];
			$my_coach->age						= $coach_data [9];
			$my_coach->salaire					= $coach_data [10];
			$my_coach->id						= $coach_data [11];
			$my_coach->user_login				= $f3->get("SESSION.user.login");
			$my_coach->save();
		}
		
		// load coaches change data
		$coach_wrapper = new Coach();
		$my_coaches = $coach_wrapper->find();
		$coaches = [];
		foreach ($my_coaches as $my_coach) {
			$data = $vms->get_coach_change_data_pages($my_coach->id, 4);
			array_shift($data); // remove headers
			
			foreach ($data as $coach_data) {
				if(empty($coaches [$coach_data ["id"]])) {
					$coach = new Coach();
				}
				else {
					$coach = $coaches [$coach_data ["id"]];
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
				$coaches [$coach_data ["id"]] = $coach;
			}
		}
		
		// finally bulk store into db
		foreach ($coaches as $coach) {
			$coach->save();
		}
		
		$db->commit();
		
		$logs = $db->log();
		echo count(explode(PHP_EOL, $logs)) . " queries <br/>" . PHP_EOL;
		echo "<pre>" . $logs . "</pre>";
		die;
	}
	
}
