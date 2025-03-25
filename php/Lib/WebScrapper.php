<?php
namespace Lib;

use DateTime;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use Dom\Node;
use ErrorException;


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
	
	
	public static function extract_data_from_dom (HTMLDocument $dom, string $row_selector, string $cell_sub_selector, string $extrators_type = "normal") : array
	{
		$res = [];
		$rows = $dom->querySelectorAll($row_selector);
		foreach ($rows as $row) {
			$row_data = [];
			$cells = $row->querySelectorAll($cell_sub_selector);
			if($cells->count() > 0) {
				if($extrators_type === "normal") {
					$extractors = array_fill(0, $cells->count(), "textContent");
				}
				elseif($extrators_type === "coachChange") {
					$extractors = array_fill(0, ($cells->count())-2, "textContent");
					$extractors [] = "coach_change_id";
					$extractors [] = "textContent";
				}
				foreach ($cells as $x => $cell) {
					$extractor = $extractors [$x] ?? "textContent";
					$val = self::$extractor ($cell);
					$row_data [] = $val;
				}
				$res [] = $row_data;
			}
		}
		return $res;
	}
	
	private static function textContent (Element $cell)
	{
		return trim($cell->textContent);
	}
	
	private static function coach_change_id (Element $cell) : ?string
	{
		$span = $cell->firstChild; /** @var Element $span */
		if(empty($span)) {
			return null;
		}
		$onclick = $span->getAttribute("onclick");
		$regex = "/CoachChangeSave&coachId=(\d+)&newCoachId=(\d+)&countryId=(\d+)&age=(\d+)/";
		$res = preg_match($regex, $onclick, $matches);
		if($res === false) {
			throw new ErrorException("coach change regex failed");
		}
		return $matches [2];
	}
	
	
	
	
	public static function display_html_tree (Node $node, int $depth = 0): void
	{
		if($depth === 0) {
			?><pre><?php
		}
		
		$id = "";
		$class = "";
		if($node instanceof HTMLElement) {
			$id = $node->getAttribute("id");
			$id = empty($id) ? "" : " #$id";
			$class = empty($node->className) ? "" : " .$node->className";
		}
		echo str_pad("", $depth*4, " ") . $node->nodeName . $id . $class . " " . $node->nodeValue . " " . PHP_EOL;
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
				$val = DateTime::createFromFormat("d.m H:i", $val);
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
