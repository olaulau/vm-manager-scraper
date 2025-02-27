<?php

function clean_dirty_json (string $json) : string
{
	$json = str_replace("{body: '", '{"body": "', $json);
	$json = str_replace("\\'", "'", $json);
	$json = str_replace("\t", " ", $json);
	$json = str_replace("'}", '"}', $json);
	return $json;
}


function display_html_tree(Dom\Node $node, int $depth = 0): void
{
	echo str_pad("", $depth, "\t") . $node->nodeName . PHP_EOL;
	foreach ($node->childNodes as $child) {
		display_html_tree($child, $depth + 1);
	}
}