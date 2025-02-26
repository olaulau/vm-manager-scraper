<?php
require_once __DIR__ . "/functions.inc.php";


// load JSON content from a file
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


// load html directly from a file
/*
$test_html_file_name = "data/html_table.html";
$html = file_get_contents($test_html_file_name);
*/


// browse HTML v4
/*
$doc = new DOMDocument();
// var_dump($doc);
$res = $doc->loadHTML($html, LIBXML_NOERROR);
if($res === false) {
	throw new ErrorException("html load error");
}
// echo $doc->saveHTML();
$children = $doc->childNodes;
// var_dump($children);
*/


// browse HTML v5
$dom = Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);

$data = [];
$rows = $dom->querySelectorAll('body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)');
foreach ($rows as $row) {
	$row_data = [];
	$cells = $row->querySelectorAll('td.second');
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
*/


// browse XML
/*
$xml = new SimpleXMLElement($html, LIBXML_NOERROR);
var_dump($xml); die;
*/


// output data into proper JSON
/*
header('Content-Disposition: attachment; filename="test.csv";');
header('Content-Type: application/csv; charset=UTF-8');
output_csv_table ($data);
*/

// output data into proper JSON
$data = array_remove_empty_columns($data);
output_html_table (array_remove_empty_columns($data));
?>
<style>
	table,tr,td {
		border: 1px grey solid;
	}
</style>
<?php
