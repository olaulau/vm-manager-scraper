<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config.inc.php";

use Lib\Matrix;
use Lib\VM;


// auth
VM::authenticate ($conf ["auth"] ["login"], $conf ["auth"] ["pass"]);

// get team data
?>
<h2>team</h2>
<?php
$players_data = VM::get_team_data ();
Matrix::display_html_table ($players_data);
// Matrix::send_csv_table ($players_data);


// get league data
?>
<h2>league</h2>
<?php
$league_data = VM::get_league_data ();
Matrix::display_html_table ($league_data);


// get transfert data
?>
<h2>transferts</h2>
<?php
$transferts_data = VM::get_transfert_data ();
Matrix::display_html_table ($transferts_data);
$transferts_data = VM::get_transfert_data (2);
Matrix::display_html_table ($transferts_data);
