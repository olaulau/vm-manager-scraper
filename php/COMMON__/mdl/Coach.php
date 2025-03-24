<?php
namespace COMMON__\mdl;

use DB\Cortex;
use DB\SQL\Schema;


class Coach extends Cortex
{
	
	protected $fieldConf = [
		"user_login" => [
			"type"		=> Schema::DT_VARCHAR128,
			"nullable"	=> true,
			"index"		=> true,
			"unique"	=> false,
			"default"	=> null,
		],
		"id" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> false,
			"index"		=> true,
			"unique"	=> true,
			// "default"	=> null,
		],
		"type" => [
			"type"		=> Schema::DT_VARCHAR128,
			"nullable"	=> true,
			"index"		=> true,
			"unique"	=> true,
			"default"	=> null,
		],
		"name" => [
			"type"		=> Schema::DT_VARCHAR128,
			"nullable"	=> false,
			"index"		=> false,
			"unique"	=> true,
			// "default"	=> null,
		],
		"entrainement_physique" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> false,
			"index"		=> false,
			"unique"	=> false,
			// "default"	=> null,
		],
		"travail_junior" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> true,
			"index"		=> false,
			"unique"	=> false,
			"default"	=> null,
		],
		"entrainement_technique" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> false,
			"index"		=> false,
			"unique"	=> false,
			// "default"	=> null,
		],
		"adaptabilite" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> true,
			"index"		=> false,
			"unique"	=> false,
			"default"	=> null,
		],
		"psychologie" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> false,
			"index"		=> false,
			"unique"	=> false,
			// "default"	=> null,
		],
		"niveau_discipline" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> false,
			"index"		=> false,
			"unique"	=> false,
			// "default"	=> null,
		],
		"motivation" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> true,
			"index"		=> false,
			"unique"	=> false,
			"default"	=> null,
		],
		"age" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> false,
			"index"		=> false,
			"unique"	=> false,
			// "default"	=> null,
		],
		"salaire" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> false,
			"index"		=> false,
			"unique"	=> false,
			// "default"	=> null,
		],
		"prix" => [
			"type"		=> Schema::DT_INT,
			"nullable"	=> true,
			"index"		=> false,
			"unique"	=> false,
			"default"	=> null,
		],
	];
	
	protected $db = "db";
	protected $table = "coach";
	protected $primary = "id";
	
}
