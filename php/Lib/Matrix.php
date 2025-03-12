<?php
namespace Lib;

use DateTime;

class Matrix
{
	
	
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

	
	public static function display_html_table (array $data): void
	{
		$headers = array_shift($data);
		?>
		<table>
			<thead>
				<tr>
					<?php
					foreach ($headers as $val) {
						?>
						<th><?= $val ?></th>
						<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($data as $row) {
					?>
					<tr>
						<?php
						foreach ($row as $val) {
							?>
							<td><?= self::format_value ($val) ?></td>
							<?php
						}
						?>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<style>
			table {
				border-collapse: collapse;
				border: 2px black solid;
			}
			td, th {
				border: 1px grey solid;
				padding: 2px;
			}
			th {
				background-color: lightgrey;
				font-size: larger;
				font-weight: normal;
			}
		</style>
		<?php
	}


	private static function output_csv_table (array $data): void
	{
		$out = fopen('php://output', 'w');
		foreach ($data as $fields) {
			fputcsv($out, $fields, ',', '"', '');
		}
		fclose($out);
	}

	public static function send_csv_table (array $data): void
	{
		header('Content-Disposition: attachment; filename="data.csv";');
		header('Content-Type: application/csv; charset=UTF-8');
		self::output_csv_table($data);
		die;
	}


	public static function transpose (array $data): array
	{
		$res = [];
		foreach ($data as $y => $row) {
			foreach ($row as $x => $val) {
				$res [$x] [$y] = $val;
			}
		}
		return $res;
	}


	public static function remove_empty_columns (array $data): array
	{
		$res = self::transpose($data);

		foreach ($res as $y => $row) {
			$empty = true;
			foreach ($row as $x => $val) {
				if ($val !== null && $val !== "") {
					$empty = false;
					break;
				}
			}
			if ($empty === true) {
				unset ($res [$y]);
			}
		}

		return self::transpose($res);
	}
	
	
	public static function pack (array $data) : array
	{
		foreach ($data as &$val) {
			if (is_array($val)) {
				$val = self::pack($val);
			}
		}
		
		$keys = range(0, count($data)-1);
		$values = array_values($data);
		$data = array_combine($keys, $values);
		return $data;
	}
	
	
	public static function format_values (array $data, array $formats): array
	{
		foreach ($data as $y => &$row) {
			foreach ($row as $x => &$val) {
				$format = $formats [$x];
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
			}
		}
		return $data;
	}

}
