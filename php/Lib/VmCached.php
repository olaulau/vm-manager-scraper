<?php
namespace Lib;

use Base;
use Cache;
use ErrorException;


class VmCached
{
	
	public static function auth_from_session () : ?VM
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
			$vm = new VM(new WebsiteTalk($cookies));
			return $vm;
		}
		else {
			return null;
		}
	}
	
	
	function __construct (public ?VM $vm = new VM ()) {
		if(empty($this->vm)) {
			$this->vm = new VM ();
		}
	}
	
	
	public function authenticate (string $login, string $password) : bool
	{
		$hashed_password = hash("sha256", $password);
		$cache_key = "Login_{$login}_{$hashed_password}";
		$cache_duration = 3600;
		
		// check if we don't have cookies in cache, to avoid remote auth
		$cache = Cache::instance();
		$cookies = $cache->get ($cache_key);
		if (!empty ($cookies)) {
			return true;
		}
		else {
			$this->vm = new VM ();
			$data = $this->vm->authenticate ($login, $password);
			if(!empty($data)) {
				$cache->set ($cache_key, $data, $cache_duration);
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	
	public function get_team_data () : array
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
			$data = $this->vm->get_team_data ();
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
	
	public function get_league_data () : array
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
			$data = $this->vm->get_league_data ();
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
	
	public function get_transfert_data (int $num_page=1) : array
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
			$data = $this->vm->get_transfert_data ($num_page);
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
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
			$data = $this->vm->get_coaches_data ();
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
	}
	
	
	public function get_coach_change_data (int $coach_id, int $num_page=1) : array
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
			$data = $this->vm->get_coach_change_data ($coach_id, $num_page);
			$cache->set ($cache_key, $data, $cache_duration);
			return $data;
		}
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
