<?php
namespace Lib;


class WebsiteTalk
{
	
	public array $cookie_headers=[];
	public int $queries_count = 0;
	public float $queries_duration = 0;
	
	
	public function createQuery (string $url, array $post_fields=[]) : WebQuery
	{
		$wq = new WebQuery($url, $post_fields, $this);
		return $wq;
	}
	
	
	public function get_cookies_str () : string
	{
		$cookies = [];
		foreach ($this->cookie_headers as $cookie_header) {
			$cookies [] = substr($cookie_header, 0, strpos($cookie_header, "; "));
		}
		$cookies_str = implode("; ", $cookies);
		return $cookies_str;
	}
	
}
