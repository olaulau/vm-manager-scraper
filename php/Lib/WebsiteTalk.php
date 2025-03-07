<?php
namespace Lib;


class WebsiteTalk
{
	
	public array $cookie_headers;
	
	
	public function createQuery (string $url, array $post_fields) : WebQuery
	{
		$wq = new WebQuery($url, $post_fields, $this);
		return $wq;
	}
	
}
