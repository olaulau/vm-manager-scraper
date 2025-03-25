<?php
namespace COMMON__\mdl;

use Base;
use DB\SQL\Mapper;


class Coach extends Mapper
{
	
	protected static $_db;
	protected $table = "coach";
	
	
	function __construct ()
	{
		if(empty(self::$_db)) {
			$f3 = Base::instance();
			self::$_db = $f3->get("db");
		}
		parent::__construct(self::$_db, $this->table);
	}
	
}
