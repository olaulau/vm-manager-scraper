<?php

// load content from file
$test_json_file_name = "data/html_table.json";
$content = file_get_contents($test_json_file_name);


// adjust JSON format
$content = str_replace("{body: '", '{"body": "', $content);
$content = str_replace("\\'", "'", $content);
// $content = str_replace('\\"', '"', $content);
$content = str_replace("\t", " ", $content);
$content = str_replace("'}", '"}', $content);
// echo $content; die;


// validate JSON
$valid = json_validate($content);
// var_dump($valid);
if($valid === false) {
	throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
}


// decode JSON
$decoded = json_decode($content, true);
// var_dump($decoded);
$html = $decoded ["body"];
// echo $html; die;


///////////////////////
// $test_html_file_name = "data/html_table.html";
// $html = file_get_contents($test_html_file_name);
///////////////////////


// browse HTML v4
$doc = new DOMDocument();
// var_dump($doc);
$res = $doc->loadHTML($html, LIBXML_NOERROR);
if($res === false) {
	throw new ErrorException("html load error");
}
// echo $doc->saveHTML();

$children = $doc->childNodes;
// var_dump($children);


// browse HTML v5
$dom = Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);

$data = [];
$rows = $dom->querySelectorAll('body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)');
foreach ($rows as $row) {
	$row_data = [];
	$cells = $row->querySelectorAll('td'); // .fourth, .second
	foreach ($cells as $cell) {
		$val = $cell->textContent;
		$row_data [] = $val;
	}
	$data [] = $row_data;
}
// var_dump($data);
// die;

/*
display_html_tree($dom);
die;
function display_html_tree(Dom\Node $node, int $depth = 0)
{
	echo str_pad("", $depth, "\t") . $node->nodeName . PHP_EOL;
	foreach($node->childNodes as $child) {
		display_html_tree($child, $depth + 1);
	}
}
*/

// browse XML
// $xml = new SimpleXMLElement($html, LIBXML_NOERROR);
// var_dump($xml); die;


// output data into proper JSON
header('Content-Disposition: attachment; filename="test.csv";');
header('Content-Type: application/csv; charset=UTF-8');
output_csv_table ($data);
function output_csv_table (array $data)
{
	$out = fopen('php://output', 'w');
	foreach ($data as $fields) {
		fputcsv($out, $fields, ',', '"', '');
	}
	fclose($out);
}
