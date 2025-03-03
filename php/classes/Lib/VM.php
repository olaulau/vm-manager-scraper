<?php
namespace Lib;

use Dom\HTMLDocument;
use ErrorException;


class VM
{
	
	public static function display_html_tree(\Dom\Node $node, int $depth = 0): void
	{
		echo str_pad("", $depth, "\t") . $node->nodeName . PHP_EOL;
		foreach ($node->childNodes as $child) {
			self::display_html_tree($child, $depth + 1);
		}
	}
	
	public static function authenticate (string $login, string $password) : void
	{
		$url = "http://www.vm-manager.org/index.php?view=Login";
		WebScrapper::query_with_curl($url, ["login" => $login, "pass" => $password]);
	}
	
	
	public static function extract_data_from_dom (HTMLDocument $dom, string $row_selector, string $cell_selector) : array
	{
		$res = [];
		$rows = $dom->querySelectorAll($row_selector);
		foreach ($rows as $row) {
			$row_data = [];
			$cells = $row->querySelectorAll($cell_selector);
			foreach ($cells as $cell) {
				$val = trim($cell->textContent);
				$row_data [] = $val;
			}
			$res [] = $row_data;
		}
		return $res;
	}
	
	
	public static function get_players_data () : array
	{
		$url = "http://www.vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Squad";
		$players_raw_content = WebScrapper::query_with_curl($url, []);

		// adjust JSON format
		$players_raw_content = WebScrapper::clean_dirty_json($players_raw_content);

		// validate JSON
		$valid = json_validate($players_raw_content);
		if($valid === false) {
			throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
		}

		// decode JSON
		$decoded = json_decode($players_raw_content, true);
		$html = $decoded ["body"];

		// browse HTML v5
		$dom = \Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
		// display dom as HTML tree
		/*
		display_html_tree($dom);
		die;
		*/

		// headers
		$data_headers = self::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::array_remove_empty_columns($data_headers);
		array_unshift($data_headers[0], "Poste"); // add missing header for first column

		// rows
		$data = self::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.second');
		$data = Matrix::array_remove_empty_columns($data);

		return $data_headers + $data;
	}
	
}
