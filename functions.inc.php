<?php

function display_html_tree(Dom\Node $node, int $depth = 0) : void
{
	echo str_pad("", $depth, "\t") . $node->nodeName . PHP_EOL;
	foreach($node->childNodes as $child) {
		display_html_tree($child, $depth + 1);
	}
}


function output_csv_table (array $data) : void
{
	$out = fopen('php://output', 'w');
	foreach ($data as $fields) {
		fputcsv($out, $fields, ',', '"', '');
	}
	fclose($out);
}


function output_html_table (array $data) : void
{
	?>
	<table>
		<tbody>
			<?php
			foreach ($data as $row) {
				?>
				<tr>
					<?php
					foreach ($row as $val) {
						?>
						<td><?= $val ?></td>
						<?php
					}
					?>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}


function array_transpose (array $data) : array
{
	$res = [];
	foreach ($data as $y => $row) {
		foreach ($row as $x => $val) {
			$res [$x] [$y] =  $val;
		}
	}
	return $res;
}


function array_remove_empty_columns (array $data) : array
{
	$res = array_transpose($data);
	
	foreach ($res as $y => $row) {
		$empty = true;
		foreach ($row as $x => $val) {
			if(!empty($val)) {
				$empty = false;
				break;
			}
		}
		if($empty === true) {
			unset($res [$y]);
		}
	}
	
	return array_transpose($res);
}
