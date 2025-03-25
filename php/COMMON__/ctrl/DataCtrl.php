<?php
namespace COMMON__\ctrl;

use Base;
use ErrorException;
use Lib\Matrix;
use Lib\VmCached;
use Lib\VmScraper;


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
		// load FFF
		$f3 = Base::instance();
		$f3->config('conf/index.ini');
		
		// get team data
		?>
		<h2>team</h2>
		<?php
		$vms = new VmScraper (VmCached::auth_from_session());
		$data = $vms->get_team_data ();
		Matrix::display_html_table ($data);
		// Matrix::send_csv_table ($data);
		
		// display talk stats
		echo "<hr> {$vms->wt->queries_count} quer" . ($vms->wt->queries_count>1 ? "ies" : "y") . " (" . number_format ($vms->wt->queries_duration, 3, ",", " ") . " s) <br/>" . PHP_EOL;
		return;
	}
	
	
	public static function leagueGET (Base $f3, array $url, string $controler)
	{
		// load FFF
		$f3 = Base::instance();
		$f3->config('conf/index.ini');
		
		// get league data
		?>
		<h2>league</h2>
		<?php
		$vms = new VmScraper (VmCached::auth_from_session());
		$league_data = $vms->get_league_data ();
		Matrix::display_html_table ($league_data);
		
		// display talk stats
		echo "<hr> {$vms->wt->queries_count} quer" . ($vms->wt->queries_count>1 ? "ies" : "y") . " (" . number_format ($vms->wt->queries_duration, 3, ",", " ") . " s) <br/>" . PHP_EOL;
		return;
	}
	
	
	public static function transfertsGET (Base $f3, array $url, string $controler)
	{
		// load FFF
		$f3 = Base::instance();
		$f3->config('conf/index.ini');
		
		// get transfert data
		?>
		<h2>transferts</h2>
		<?php
		$vms = new VmScraper (VmCached::auth_from_session());
		$transferts_data = $vms->get_transfert_data_pages(4);
		Matrix::display_html_table ($transferts_data);
		
		// display talk stats
		echo "<hr> {$vms->wt->queries_count} quer" . ($vms->wt->queries_count>1 ? "ies" : "y") . " (" . number_format ($vms->wt->queries_duration, 3, ",", " ") . " s) <br/>" . PHP_EOL;
		return;
	}
	
	
	public static function coachesGET (Base $f3, array $url, string $controler)
	{
		// load FFF
		$f3 = Base::instance();
		$f3->config('conf/index.ini');
		
		
		// get coaches data
		?>
		<h2>coaches</h2>
		<?php
		$vms = new VmScraper (VmCached::auth_from_session());
		$coaches_data = $vms->get_coaches_data();
		Matrix::display_html_table ($coaches_data);
		
		array_shift($coaches_data); // remove headers
		foreach ($coaches_data as $coach) {
			?>
			<a href="<?= $f3->get("BASE") . $f3->alias("coachChange", ["id" => $coach ["id"]]) ?>">changer <?= $coach ["id"] ?></a>
			<?php
		}
		
		// display talk stats
		echo "<hr> {$vms->wt->queries_count} quer" . ($vms->wt->queries_count>1 ? "ies" : "y") . " (" . number_format ($vms->wt->queries_duration, 3, ",", " ") . " s) <br/>" . PHP_EOL;
		return;
	}
	
	
	public static function coachChangeGET (Base $f3, array $url, string $controler)
	{
		// load FFF
		$f3 = Base::instance();
		$f3->config('conf/index.ini');
		
		// params
		$coach_id = intval($f3->get("PARAMS.id"));
		if(empty($coach_id) || $coach_id < 0) {
			throw new ErrorException("invalid parameters");
		}
		
		// get coachChange data
		?>
		<h2>coach change (<?= $coach_id ?>)</h2>
		<?php
		$vms = new VmScraper (VmCached::auth_from_session());
		$coach_change_data = $vms->get_coach_change_data_pages($coach_id, 4);
		Matrix::display_html_table ($coach_change_data);
		
		// display talk stats
		echo "<hr> {$vms->wt->queries_count} quer" . ($vms->wt->queries_count>1 ? "ies" : "y") . " (" . number_format ($vms->wt->queries_duration, 3, ",", " ") . " s) <br/>" . PHP_EOL;
		return;
	}
	
}
