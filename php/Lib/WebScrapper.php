<?php
namespace Lib;

use DateTime;
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
		if($depth === 0) {
			?><pre><?php
		}
		echo str_pad("", $depth*4, " ") . $node->nodeName . " " . $node->nodeValue . " " . PHP_EOL;
		foreach ($node->childNodes as $child) {
			self::display_html_tree($child, $depth + 1);
		}
		if($depth === 0) {
			?></pre><?php
		}
	}
	
	
	public static function parse_value(mixed $val, string $format)
	{
		switch ($format) {
			case "int" :
				$val = preg_replace('/[^\d]+/', '', $val);
				$val = intval($val);
				break;
			case "DateTime" :
				$val = DateTime::createFromFormat("d.m h:i", $val);
				break;
			case "string" :
			default :
				break;
		}
		return $val;
	}
	
	
	public static function format_value (mixed $val) : string
	{
		$type = get_debug_type ($val);
		switch ($type) {
			case "int" :
				return number_format ($val, 0, ",", " ");
				break;
			case "DateTime" :
				return $val->format("d/m h:i");
				break;
			case "string" :
			default :
				return "".$val;
				break;
		}
	}

}
