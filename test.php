<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/functions.inc.php";
require_once __DIR__ . "/config.inc.php";

use Lib\Matrix;
use Lib\WebScrapper;


// auth
$url = "http://www.vm-manager.org/index.php?view=Login";
$res = WebScrapper::query_with_curl($url, $conf ["auth"]);


// get players data
$url = "http://www.vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Squad";
$players_raw_content = WebScrapper::query_with_curl($url, []);


// adjust JSON format
$players_raw_content = clean_dirty_json($players_raw_content);

// validate JSON
$valid = json_validate($players_raw_content);
if($valid === false) {
	throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
}


// decode JSON
$decoded = json_decode($players_raw_content, true);
$html = $decoded ["body"];

// browse HTML v5
$dom = Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
// display dom as HTML tree
/*
display_html_tree($dom);
die;
*/

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

// clean data & merge headers
$data_headers = Matrix::array_remove_empty_columns($data_headers);
array_unshift($data_headers[0], "Poste");
$data = Matrix::array_remove_empty_columns($data);
$data = $data_headers + $data;


// download data as CSV
// Matrix::download_csv_table($data);

// output data in HTML table
Matrix::display_html_table ($data);
