<?php
namespace Lib;

use Dom\HTMLDocument;
use ErrorException;


class VM
{
	
	public static function display_html_tree (\Dom\Node $node, int $depth = 0): void
	{
		echo str_pad("", $depth, "\t") . $node->nodeName . PHP_EOL;
		foreach ($node->childNodes as $child) {
			self::display_html_tree($child, $depth + 1);
		}
	}
	
	public static function authenticate (string $login, string $password) : void
	{
		$url = "http://vm-manager.org/index.php?view=Login";
		WebScrapper::query_with_curl($url, ["login" => $login, "pass" => $password]);
	}
	
	
	public static function extract_data_from_dom (HTMLDocument $dom, string $row_selector, string $cell_sub_selector) : array
	{
		$res = [];
		$rows = $dom->querySelectorAll($row_selector);
		foreach ($rows as $row) {
			$row_data = [];
			$cells = $row->querySelectorAll($cell_sub_selector);
			foreach ($cells as $cell) {
				$val = trim($cell->textContent);
				$row_data [] = $val;
			}
			$res [] = $row_data;
		}
		return $res;
	}
	
	
	public static function get_team_data () : array
	{
		$url = "http://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Squad";
		$raw_content = WebScrapper::query_with_curl($url, []);

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
		$data_headers = self::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::array_remove_empty_columns($data_headers);
		array_unshift($data_headers[0], "Poste"); // add missing header for first column

		// rows
		$data = self::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.second');
		$data = Matrix::array_remove_empty_columns($data);

		return array_merge($data_headers, $data);
	}
	
	
	public static function get_league_data () : array
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=League";
		$raw_content = WebScrapper::query_with_curl($url, []);
		
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
		$data_headers = self::extract_data_from_dom($dom, 'body > form#postform > table > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::array_remove_empty_columns($data_headers);

		// rows
		$data = self::extract_data_from_dom($dom, 'body > form#postform > table > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.second:not(:nth-child(3)):not(:nth-child(6))');
		$data = Matrix::array_remove_empty_columns($data);

		return array_merge($data_headers, $data);
	}
	
	
	public static function get_transfert_data (int $num_page=1, bool $include_headers=true) : array
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=TransferList&site=$num_page";
		$raw_content = WebScrapper::query_with_curl($url, []);
		
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
		self::display_html_tree($dom);
		die;
		*/
		
		// headers
		if($include_headers === true) {
			$data_headers = self::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.fourth');
			$data_headers = Matrix::array_remove_empty_columns($data_headers);
			array_splice($data_headers[0], 1, 0, ["Poste"]);
		}
		else {
			$data_headers = [];
		}

		// rows
		$data = self::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr:not(:nth-last-child(2)) > td > table > tbody > tr:nth-child(2)', 'td.second:not(:nth-child(2)):not(:nth-child(3))');
		$data = Matrix::array_remove_empty_columns($data);

		return array_merge($data_headers, $data);
	}
	
	public static function get_transfert_data_pages (int $nb_pages=1, int $start_offset=1)
	{
		if ($nb_pages < 1 || $start_offset < 1) {
			throw new ErrorException("parameter problem");
		}
		
		$res = [];
		$page_num = $start_offset;
		$cpt = 1;
		do {
			$data = VM::get_transfert_data ($page_num, false);
			$res = array_merge($res, $data);
			$cpt ++;
			$page_num ++;
		}
		while ($cpt <= $nb_pages);
		return $res;
	}
	
}
