<?php
namespace Lib;

use Cache;
use ErrorException;


class VM
{
	
	function __construct (public WebsiteTalk $wt = new WebsiteTalk ()) {}
	
	
	public function authenticate (string $login, string $password) : bool
	{
		$cache = Cache::instance();
		$cache_key = "Login_{$login}_{$password}";
		
		// check if we don't have cookies in cache, to avoid remote auth
		$cookies = $cache->get ($cache_key);
		if (!empty ($cookies)) {
			$this->wt->cookie_headers = $cookies;
			return true;
		}
		
		$url = "http://vm-manager.org/index.php?view=Login";
		$query = $this->wt->createQuery($url, ["login" => $login, "pass" => $password]);
		$query->send();
		$query_res = $query->response_body;
		$res = !str_contains($query_res, "Vous avez entré un login ou un mot de passe incorrect."); // we should see this string only on failed auth
		if($res === true) { // store cookies in cache for 1 day
			$cookies = $query->response_headers ["set-cookie"];
			$cache->set ($cache_key, $cookies, 86400);
		}
		return ($res);
	}
	
	
	public function get_team_data () : array
	{
		$url = "http://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Squad";
		$query = $this->wt->createQuery($url);
		$raw_content = $query->send();

		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);

		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException("JSON error " . json_last_error() . " : " . json_last_error_msg());
		}

		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];
		if(str_starts_with($html, "session Error")) {
			throw new ErrorException("unexpected error : " . $html);
		}

		// browse HTML v5
		$dom = \Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);

		// headers
		$data_headers = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::array_remove_empty_columns($data_headers);
		array_unshift($data_headers[0], "Poste"); // add missing header for first column

		// rows
		$data = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.second');
		$data = Matrix::array_remove_empty_columns($data);

		return array_merge($data_headers, $data);
	}
	
	
	public function get_league_data () : array
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=League";
		$query = $this->wt->createQuery($url);
		$raw_content = $query->send();
		
		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);
		
		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
		}
		
		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];

		// browse HTML v5
		$dom = \Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
		
		// headers
		$data_headers = WebScrapper::extract_data_from_dom($dom, 'body > form#postform > table > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::array_remove_empty_columns($data_headers);

		// rows
		$data = WebScrapper::extract_data_from_dom($dom, 'body > form#postform > table > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.second:not(:nth-child(3)):not(:nth-child(6))');
		$data = Matrix::array_remove_empty_columns($data);

		return array_merge($data_headers, $data);
	}
	
	
	public function get_transfert_data (int $num_page=1, bool $include_headers=true) : array
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=TransferList&site=$num_page";
		$query = $this->wt->createQuery($url);
		$raw_content = $query->send();
		
		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);
		
		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
		}
		
		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];
		// echo $html; die;

		// browse HTML v5
		$dom = \Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
		// display dom as HTML tree
		/*
		WebScrapper::display_html_tree($dom);
		die;
		*/
		
		// headers
		if($include_headers === true) {
			$data_headers = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.fourth');
			$data_headers = Matrix::array_remove_empty_columns($data_headers);
			array_splice($data_headers[0], 1, 0, ["Poste"]);
		}
		else {
			$data_headers = [];
		}

		// rows
		$data = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr:not(:nth-last-child(2)) > td > table > tbody > tr:nth-child(2)', 'td.second:not(:nth-child(2)):not(:nth-child(3))');
		$data = Matrix::array_remove_empty_columns($data);

		return array_merge($data_headers, $data);
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
			$data = $this->get_transfert_data ($page_num, false);
			$res = array_merge($res, $data);
			$cpt ++;
			$page_num ++;
		}
		while ($cpt <= $nb_pages);
		return $res;
	}
	
}
