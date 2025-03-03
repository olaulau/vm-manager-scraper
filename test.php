<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config.inc.php";

use Lib\Matrix;
use Lib\VM;


// auth
VM::authenticate($conf ["auth"] ["login"], $conf ["auth"] ["pass"]);


// get players data
$data = VM::get_players_data();


// send data as CSV
// Matrix::send_csv_table($data);

// output data in HTML table
Matrix::display_html_table ($data);
