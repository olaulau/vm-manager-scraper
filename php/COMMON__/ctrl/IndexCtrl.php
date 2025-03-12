<?php
namespace COMMON__\ctrl;

use ErrorException;
use Lib\Matrix;
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
	
	public static function indexGET (\Base $f3, $url, $controler)
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
	
	
	public static function testGET (\Base $f3, $url, $controler)
	{
		// load FFF
		$f3 = \Base::instance();
		$f3->config('conf/index.ini');
		
		// prepare talk
		$conf = $f3->get("conf");
		$vm = new VM ();
		
		// auth
		$auth_res = $vm->authenticate ($conf ["auth"] ["login"], $conf ["auth"] ["pass"]);
		if($auth_res !== true) {
			throw new ErrorException("authentication failed");
		}
		
		// get team data
		?>
		<h2>team</h2>
		<?php
		$players_data = $vm->get_team_data ();
		Matrix::display_html_table ($players_data);
		// Matrix::send_csv_table ($players_data);
		
		// get league data
		?>
		<h2>league</h2>
		<?php
		$league_data = $vm->get_league_data ();
		Matrix::display_html_table ($league_data);
		
		// get transfert data
		?>
		<h2>transferts</h2>
		<?php
		$transferts_data = $vm->get_transfert_data_pages(4);
		Matrix::display_html_table ($transferts_data);
		
		// display talk stats
		echo "<hr> {$vm->wt->queries_count} quer" . ($vm->wt->queries_count>1 ? "ies" : "y") . " (" . number_format ($vm->wt->queries_duration, 3, ",", " ") . " s) <br/>" . PHP_EOL;
		
		die;
	}
	
}
