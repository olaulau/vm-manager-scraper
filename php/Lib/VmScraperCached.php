<?php
namespace Lib;

use Base;
use Cache;
use ErrorException;


class VmScraperCached
{
	
	function __construct (public WebsiteTalk $wt) {}
	
	
	public function get_team_data () : array
	{
		$vs = new VmScraper ($this->wt);
		return $vs->get_team_data();
	}
	
	
	public function get_league_data () : array
	{
		$vs = new VmScraper ($this->wt);
		return $vs->get_league_data();
	}
	
	
	public function get_transfert_data (int $num_page=1) : array
	{
		$vs = new VmScraper ($this->wt);
		return $vs->get_transfert_data($num_page);
	}
	
	
	public function get_transfert_data_pages (int $nb_pages=1, int $start_offset=1)
	{
		if ($nb_pages < 1 || $start_offset < 1) {
			throw new ErrorException("parameter problem");
		}
		
		$res = [];
		$page_num = $start_offset;
		$cpt = 1;
		do {
			$data = $this->get_transfert_data ($page_num);
			$headers = array_shift($data);
			
			$res = array_merge($res, $data);
			$cpt ++;
			$page_num ++;
		}
		while ($cpt <= $nb_pages);
		
		$res = array_merge([$headers], $res);
		return $res;
	}
	
	
	public function get_coaches_data () : array
	{
		$f3 = Base::instance();
		$cache = Cache::instance();
		
		$user = $f3->get("SESSION.user");
		$cache_key = "{$user ["login"]}_{$user["hashed_password"]}_coaches__data";
		$cache_duration = 60 * 60; // 1 hour
		
		// check if we have data in cache
		$data = $cache->get ($cache_key);
		if (empty ($data)) {
			$vs = new VmScraper ($this->wt);
			$data = $vs->get_coaches_data();
			$cache->set ($cache_key, $data, $cache_duration);
		}
		
		return $data;
	}
	
	
	public function get_coach_change_data (int $coach_id, int $num_page=1) : array
	{
		$f3 = Base::instance();
		$cache = Cache::instance();
		
		$user = $f3->get("SESSION.user");
		$cache_key = "{$user ["login"]}_{$user["hashed_password"]}_coach_change_{$coach_id}_{$num_page}__data";
		$cache_duration = 60 * 60; // 1 hour
		
		// check if we have data in cache
		$data = $cache->get ($cache_key);
		if (empty ($data)) {
			$vs = new VmScraper ($this->wt);
			$data = $vs->get_coach_change_data($coach_id, $num_page);
			$cache->set ($cache_key, $data, $cache_duration);
		}
			
		return $data;
	}
	
	
	public function get_coach_change_data_pages (int $coach_id, int $nb_pages=1, int $start_offset=1)
	{
		if ($nb_pages < 1 || $start_offset < 1) {
			throw new ErrorException("parameter problem");
		}
		
		$res = [];
		$page_num = $start_offset;
		$cpt = 1;
		do {
			$data = $this->get_coach_change_data ($coach_id, $page_num);
			$headers = array_shift($data);
			
			$res = array_merge($res, $data);
			$cpt ++;
			$page_num ++;
		}
		while ($cpt <= $nb_pages);
		
		$res = array_merge([$headers], $res);
		return $res;
	}
	
}
