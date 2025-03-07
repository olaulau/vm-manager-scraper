<?php
namespace Lib;

use ErrorException;

class WebQuery
{
	
	private int $response_status;
	private array $response_headers;
	private string $response_body;
	
	
	public function __construct (private string $url, private array $post_fields, private WebsiteTalk $wt = new WebsiteTalk()) {}
	
	
	public function get_website_talk ()
	{
		return $this->wt;
	}
	
	
	public function send () : bool|string
	{
		$ch = curl_init();

		// intercept response headers
		$headers = [];
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,
			function ($curl, $header) use (&$headers)
			{
				$len = strlen ($header);
				$header = explode (':', $header, 2);
				if (count( $header) < 2) // ignore invalid headers
					return $len;
				$headers [strtolower (trim ($header [0]))] [] = trim ($header [1]);
				return $len;
			}
		);

		// set curl options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_COOKIE, $this->wt->get_cookies_str());
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept-Language: fr"]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post_fields);
		
		// send http query
		$res = curl_exec($ch);
		if (curl_errno($ch)) {
			throw new ErrorException(curl_errno($ch) . " : " . curl_error($ch));
		}

		// show query infos
		/*
		$info = curl_getinfo($ch);
		echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
		var_dump($info);
		echo $res;
		die;
		*/

		// store cookies extracted from header (if any)
		if(!empty($headers ["set-cookie"])) {
			$this->wt->cookie_headers = $headers ["set-cookie"];
		}

		return $res;
	}
	
	
}
