<?php
namespace Lib;

use Base;
use Cache;
use ErrorException;


class VmCached
{
	
	public static function auth_from_session () : WebsiteTalk
	{
		$f3 = Base::instance();
		
		$user = $f3->get("SESSION.user");
		if(empty ($user ["login"]) || empty ($user ["hashed_password"])) {
			throw new ErrorException("auth user not found");
		}
		
		$login = $user ["login"];
		$hashed_password = $user ["hashed_password"];
		$cache_key = "Login_{$login}_{$hashed_password}";
		
		// check if we don't have cookies in cache, to avoid remote auth
		$cache = Cache::instance();
		$cookies = $cache->get ($cache_key);
		if (!empty ($cookies)) {
			return new WebsiteTalk($cookies);
		}
		else {
			return new WebsiteTalk();
		}
	}
	
	
	function __construct (public ?WebsiteTalk $wt)
	{
		if (empty($this->wt)) {
			$this->wt = self::auth_from_session();
		}
	}
	
	
	public function authenticate (string $login, string $password) : ?array
	{
		$hashed_password = hash("sha256", $password);
		$cache_key = "Login_{$login}_{$hashed_password}";
		$cache_duration = 60*20; // 20 minutes
		
		// check if we don't have cookies in cache, to avoid remote auth
		$cache = Cache::instance();
		$data = $cache->get ($cache_key);
		if (!empty ($data)) {
			return $data;
		}
		else {
			$vmq = new VmQuery($this->wt);
			$data = $vmq->authenticate ($login, $password);
			if(!empty($data)) {
				$cache->set ($cache_key, $data, $cache_duration);
			}
			return $data;
		}
	}
	
	
	public function get_team_data () : string
	{
		$f3 = Base::instance();
		
		$user = $f3->get("SESSION.user");
		$cache_key = "Login_{$user ["login"]}_{$user["hashed_password"]}_team";
		$cache_duration = 3600;
		
		// check if we don't have data in cache, to avoid remote query
		$cache = Cache::instance();
		$data = $cache->get ($cache_key);
		if (!empty ($data)) {
			return $data;
		}
		else {
			$vmq = new VmQuery($this->wt);
			$data = $vmq->get_team_data ();
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
	
	public function get_league_data () : string
	{
		$f3 = Base::instance();
		
		$user = $f3->get("SESSION.user");
		$cache_key = "Login_{$user ["login"]}_{$user["hashed_password"]}_league";
		$cache_duration = 3600;
		
		// check if we don't have data in cache, to avoid remote query
		$cache = Cache::instance();
		$data = $cache->get ($cache_key);
		if (!empty ($data)) {
			return $data;
		}
		else {
			$vmq = new VmQuery($this->wt);
			$data = $vmq->get_league_data ();
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
	
	public function get_transfert_data (int $num_page=1) : string
	{
		$f3 = Base::instance();
		
		$user = $f3->get("SESSION.user");
		$cache_key = "Login_{$user ["login"]}_{$user["hashed_password"]}_transfert_{$num_page}";
		$cache_duration = 3600;
		
		// check if we don't have data in cache, to avoid remote query
		$cache = Cache::instance();
		$data = $cache->get ($cache_key);
		if (!empty ($data)) {
			return $data;
		}
		else {
			$vmq = new VmQuery($this->wt);
			$data = $vmq->get_transfert_data ($num_page);
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
	
	public function get_coaches_data () : string
	{
		$f3 = Base::instance();
		
		$user = $f3->get("SESSION.user");
		$cache_key = "Login_{$user ["login"]}_{$user["hashed_password"]}_coaches";
		$cache_duration = 3600;
		
		// check if we don't have data in cache, to avoid remote query
		$cache = Cache::instance();
		$data = $cache->get ($cache_key);
		if (!empty ($data)) {
			return $data;
		}
		else {
			$vmq = new VmQuery($this->wt);
			$data = $vmq->get_coaches_data ();
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
	
	public function get_coach_change_data (int $coach_id, int $num_page=1) : string
	{
		$f3 = Base::instance();
		
		$user = $f3->get("SESSION.user");
		$cache_key = "Login_{$user ["login"]}_{$user["hashed_password"]}_coach_change_{$coach_id}_{$num_page}";
		$cache_duration = 3600;
		
		// check if we don't have data in cache, to avoid remote query
		$cache = Cache::instance();
		$data = $cache->get ($cache_key);
		if (!empty ($data)) {
			return $data;
		}
		else {
			$vmq = new VmQuery($this->wt);
			$data = $vmq->get_coach_change_data ($coach_id, $num_page);
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
}
