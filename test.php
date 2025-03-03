<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config.inc.php";

use Lib\Matrix;
use Lib\VM;


// auth
VM::authenticate ($conf ["auth"] ["login"], $conf ["auth"] ["pass"]);


// get team data
$players_data = VM::get_team_data ();
?>
<h2>team</h2>
<?php
Matrix::display_html_table ($players_data);
// Matrix::send_csv_table ($players_data);


// get league data
$league_data = VM::get_league_data ();
?>
<h2>league</h2>
<?php
Matrix::display_html_table ($league_data);
