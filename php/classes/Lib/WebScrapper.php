<?php
namespace Lib;

use ErrorException;


class WebScrapper
{

	public static array $cookies_headers = [];


	public function construct__ () : void
	{

	}


	public static function query_with_curl (string $url, array $post_fields) : bool|string
	{
		$ch = curl_init();

		// intercept response headers
		$headers = [];
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,
			function($curl, $header) use (&$headers)
			{
				$len = strlen($header);
				$header = explode(':', $header, 2);
				if (count($header) < 2) // ignore invalid headers
					return $len;
				$headers[strtolower(trim($header[0]))][] = trim($header[1]);
				return $len;
			}
		);

		// set curl options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIE, self::get_cookies_str());
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept-Language: fr"]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		
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

		// extract cookies from header
		self::$cookies_headers = $headers ["set-cookie"] ?? [];

		return $res;
	}


	public static function get_cookies_str () : string
	{
		$cookies = [];
		foreach (self::$cookies_headers as $cookie_header) {
			$cookies [] = substr($cookie_header, 0, strpos($cookie_header, "; "));
		}
		$cookies_str = implode("; ", $cookies);
		return $cookies_str;
	}
	
	
	public static function clean_dirty_json (string $json) : string
	{
		$json = str_replace("{body: '", '{"body": "', $json);
		$json = str_replace("\\'", "'", $json);
		$json = str_replace("\t", " ", $json);
		$json = str_replace("'}", '"}', $json);
		return $json;
	}

}
