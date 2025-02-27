<?php
require_once __DIR__ . "/functions.inc.php";
require_once __DIR__ . "/config.inc.php";


// auth
$url = "http://www.vm-manager.org/index.php?view=Login";
$ch = curl_init();
$headers = [];
unset($res);
curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	function($curl, $header) use (&$headers)
	{
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
			return $len;
		$headers[strtolower(trim($header[0]))][] = trim($header[1]);
		return $len;
	}
);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $conf ["auth"]);
$res = curl_exec($ch);
if (curl_errno($ch)) {
	throw new ErrorException(curl_errno($ch) . " : " . curl_error($ch));
}
// $info = curl_getinfo($ch);
// echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
// var_dump($info);
// echo $res;
// die;

$cookies_headers = $headers ["set-cookie"];
$cookies = [];
foreach ($cookies_headers as $cookie_header) {
	$cookies [] = substr($cookie_header, 0, strpos($cookie_header, "; "));
}
$cookies_str = implode("; ", $cookies);


// get players data
$url = "http://www.vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Squad";
$ch = curl_init();
$headers = [];
unset($res);
curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	function($curl, $header) use (&$headers)
	{
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
			return $len;
		$headers[strtolower(trim($header[0]))][] = trim($header[1]);
		return $len;
	}
);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_COOKIE, $cookies_str);
$res = curl_exec($ch);
if (curl_errno($ch)) {
	throw new ErrorException(curl_errno($ch) . " : " . curl_error($ch));
}
// $info = curl_getinfo($ch);
// echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
// var_dump($info);
// echo $res;

$players_raw_content = $res;


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
