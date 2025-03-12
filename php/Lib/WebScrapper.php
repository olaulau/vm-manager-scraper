<?php
namespace Lib;

use Dom\HTMLDocument;
use Dom\Node;


abstract class WebScrapper
{

	public static function clean_dirty_json (string $json) : string
	{
		$json = str_replace("{body: '", '{"body": "', $json);
		$json = str_replace("\\'", "'", $json);
		$json = str_replace("\t", " ", $json);
		$json = str_replace("'}", '"}', $json);
		return $json;
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
	
	
	public static function display_html_tree (Node $node, int $depth = 0): void
	{
		echo str_pad("", $depth, "\t") . $node->nodeName . PHP_EOL;
		foreach ($node->childNodes as $child) {
			self::display_html_tree($child, $depth + 1);
		}
	}

}
