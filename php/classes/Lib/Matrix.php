<?php
namespace Lib;

class Matrix
{

	public static function display_html_table(array $data): void
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
		<style>
			table, tr, td {
				border: 1px grey solid;
			}
		</style>
		<?php
	}


	private static function output_csv_table(array $data): void
	{
		$out = fopen('php://output', 'w');
		foreach ($data as $fields) {
			fputcsv($out, $fields, ',', '"', '');
		}
		fclose($out);
	}

	public static function send_csv_table(array $data): void
	{
		header('Content-Disposition: attachment; filename="data.csv";');
		header('Content-Type: application/csv; charset=UTF-8');
		self::output_csv_table($data);
		die;
	}


	public static function array_transpose(array $data): array
	{
		$res = [];
		foreach ($data as $y => $row) {
			foreach ($row as $x => $val) {
				$res[$x][$y] = $val;
			}
		}
		return $res;
	}


	public static function array_remove_empty_columns(array $data): array
	{
		$res = self::array_transpose($data);

		foreach ($res as $y => $row) {
			$empty = true;
			foreach ($row as $x => $val) {
				if ($val !== null && $val !== "") {
					$empty = false;
					break;
				}
			}
			if ($empty === true) {
				unset($res[$y]);
			}
		}

		return self::array_transpose($res);
	}

}
