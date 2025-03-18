<?php
namespace Lib;

use Base;
use Cache;
use ErrorException;
 

abstract class VMHelper
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
	
}
