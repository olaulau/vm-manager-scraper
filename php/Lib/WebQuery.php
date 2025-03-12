<?php
namespace Lib;

use ErrorException;


class WebQuery
{
	
	public array $response_headers;
	public array $response_infos;
	public string $response_body;
	
	
	public function __construct (public string $url, public array $post_fields, public WebsiteTalk $wt = new WebsiteTalk()) {}
	
	
	public function send () : bool|string //TODO return void, use $this->response_body instead
	{
		$ch = curl_init();

		// intercept response headers
		$headers = [];
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,
			function (/*resource*/ $curl, string $header) use (&$headers)
			{
				$len = strlen ($header);
				$header = explode (':', $header, 2);
				if (count ($header) < 2) // ignore invalid headers
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
		$this->response_body = curl_exec($ch);
		if (curl_errno($ch)) {
			throw new ErrorException(curl_errno($ch) . " : " . curl_error($ch));
		}

		// store query infos
		$this->response_infos = curl_getinfo($ch);
		
		// store cookies extracted from header (if any)
		$this->response_headers = $headers;
		if(!empty($this->response_headers ["set-cookie"])) {
			$this->wt->cookie_headers = $this->response_headers ["set-cookie"];
		}
		
		// update talk stats
		$this->wt->queries_count ++;
		$this->wt->queries_duration += $this->response_infos ["total_time"];
		
		return $this->response_body;
	}
	
}
