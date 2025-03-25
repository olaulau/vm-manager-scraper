<?php
namespace COMMON__\ctrl;

use Base;
use COMMON__\mdl\Coach;
use DB\SQL;
use Lib\VmQueryCached;
use Lib\VmScraper;
use Lib\VmScraperCached;
use PDO;


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
		// sqlite in memory												// 30 ms
		// $sqlite_filename = ":memory:";
		
		// sqlite in file												// 35 ms
		// $sqlite_filename = __DIR__ . "/../../../tmp/test.sqlite";
		// if(file_exists($sqlite_filename)) {
		// 	unlink($sqlite_filename);
		// }
		
		// $dsn = "sqlite:{$sqlite_filename}";
		// $options = array(
		// 	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		// );
		// $db = new SQL($dsn, null, null, $options);
		// $f3->set("db", $db);
		//////////////////////////////////
		
		$db = $f3->get("db"); /** @var SQL $db */
		//////////////////////////////////
		// mysql memory engine											// 45 ms
		$sql = "SET default_storage_engine=MEMORY;";
		$db->exec($sql);
		
		// mysql innodb engine (auto)									// 50 ms
		//////////////////////////////////
		
		
		// create coach table
		
		Coach::setdown();
		Coach::setup();
		
		$db->begin();
		
		// load my coaches data
		$vsc = new VmScraperCached(VmQueryCached::auth_from_session());
		$coaches_data = $vsc->get_coaches_data();
		array_shift($coaches_data); // skip headers
		foreach ($coaches_data as $coach_data) {
			$my_coach = new Coach();
			$my_coach->user_login = $f3->get("SESSION.user.login");
			$my_coach->copyfrom($coach_data);
			$my_coach->save();
			$my_coaches [$my_coach->id] = $my_coach;
		}
		
		// load coaches change data
		$coaches = [];
		foreach ($my_coaches as $my_coach) {
			$data = $vsc->get_coach_change_data_pages($my_coach->id, 4);
			array_shift($data); // remove headers
			
			foreach ($data as $coach_data) {
				if(!empty($coaches [$coach_data ["id"]])) {
					$coach = $coaches [$coach_data ["id"]];
				}
				else {
					$coach = new Coach();
				}
				$coach->copyfrom($coach_data);
				$coaches [$coach_data ["id"]] = $coach;
			}
		}
		
		// finally bulk store into db
		foreach ($coaches as $coach) {
			$coach->save();
		}
		
		$db->commit();
		
		$logs = $db->log();
		preg_match_all('/\((\d+\.\d+)ms\)/m', $logs, $matches);
		$sql_total = array_sum($matches[1]);
		echo count(explode(PHP_EOL, $logs)) . " queries ($sql_total ms) <br/>" . PHP_EOL;
		echo "<pre>" . $logs . "</pre>";
		return;
	}
	
}
