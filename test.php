<?php
require_once __DIR__ . "/functions.inc.php";


// load JSON content from a file
$test_json_file_name = "data/html_table.json";
$content = file_get_contents($test_json_file_name);

// adjust JSON format
$content = clean_dirty_json($content);

// validate JSON
$valid = json_validate($content);
if($valid === false) {
	throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
}


// decode JSON
$decoded = json_decode($content, true);
$html = $decoded ["body"];

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
/*
display_html_tree($dom);
die;
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
