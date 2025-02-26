<?php

function display_html_tree(Dom\Node $node, int $depth = 0)
{
	echo str_pad("", $depth, "\t") . $node->nodeName . PHP_EOL;
	foreach($node->childNodes as $child) {
		display_html_tree($child, $depth + 1);
	}
}


function output_csv_table (array $data)
{
	$out = fopen('php://output', 'w');
	foreach ($data as $fields) {
		fputcsv($out, $fields, ',', '"', '');
	}
	fclose($out);
}


function output_html_table (array $data)
{
	?>
	<table>
		<thead>
			<th>
				<td></td>
			</th>
		</thead>
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
		<tfoot>
			<th>
				<td></td>
			</th>
		</tfoot>
	</table>
	<?php
}