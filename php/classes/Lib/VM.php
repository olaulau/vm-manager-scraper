<?php
namespace Lib;

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
		$data_headers = [];
		$rows = $dom->querySelectorAll('body > table:nth-child(2) > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)');
		foreach ($rows as $row) {
			$row_data = [];
			$cells = $row->querySelectorAll('td.fourth');
			foreach ($cells as $cell) {
				$val = trim($cell->textContent);
				$row_data [] = $val;
			}
			$data_headers [] = $row_data;
		}
		$data_headers = Matrix::array_remove_empty_columns($data_headers);
		array_unshift($data_headers[0], "Poste");


		// rows
		$data = [];
		$rows = $dom->querySelectorAll('body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)');
		foreach ($rows as $row) {
			$row_data = [];
			$cells = $row->querySelectorAll('td.second');
			foreach ($cells as $cell) {
				$val = trim($cell->textContent);
				$row_data [] = $val;
			}
			$data [] = $row_data;
		}
		$data = Matrix::array_remove_empty_columns($data);

		// merge
		$data = $data_headers + $data;
		return $data;
	}
	
}
