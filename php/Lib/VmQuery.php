<?php
namespace Lib;

use Base;


class VmQuery
{
	
	function __construct (public WebsiteTalk $wt = new WebsiteTalk ()) {}
	
	
	public function authenticate (string $login, string $password) : ?array
	{
		$url = "http://vm-manager.org/index.php?view=Login";
		$query = $this->wt->createQuery($url, ["login" => $login, "pass" => $password]);
		$query->send();
		
		$query_res = $query->response_body;
		$res = !str_contains($query_res, "Vous avez entré un login ou un mot de passe incorrect."); // we should see this string only on failed auth
		if($res === true) {
			$cookies = $query->response_headers ["set-cookie"];
			return $cookies;
		}
		else {
			return null;
		}
	}
	
	
	public static function has_session_error (string $raw_content) : bool
	{
		return str_contains($raw_content, "{body: 'session Error 2");
	}
	
	public static function check_session_error (string $raw_content) : void
	{
		if (self::has_session_error($raw_content)) {
			$f3 = Base::instance();
			$f3->reroute(["login", [], ["redirect_url" => $f3->get("PATH")]]);
		}
	}
	
	
	public function get_team_data () : string
	{
		$url = "http://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Squad";
		$query = $this->wt->createQuery($url);
		$query->send();
		$raw_content = $query->response_body;
		self::check_session_error ($raw_content);
		return $raw_content;
	}
	
	
	public function get_league_data () : string
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=League";
		$query = $this->wt->createQuery($url);
		$query->send();
		$raw_content = $query->response_body;
		self::check_session_error ($raw_content);
		return $raw_content;
	}
	
	
	public function get_transfert_data (int $num_page=1) : string
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=TransferList&site=$num_page";
		$query = $this->wt->createQuery($url);
		$query->send();
		$raw_content = $query->response_body;
		self::check_session_error ($raw_content);
		return $raw_content;
	}
	
	
	public function get_coaches_data () : string
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=Coaches";
		$query = $this->wt->createQuery($url);
		$query->send();
		$raw_content = $query->response_body;
		self::check_session_error ($raw_content);
		return $raw_content;
	}
	
	
	public function get_coach_change_data (int $coach_id, int $num_page=1) : string
	{
		$url = "https://vm-manager.org/Ajax_handler.php?phpsite=view_body.php&action=CoachChange&coachId=$coach_id&site=$num_page";
		$query = $this->wt->createQuery($url);
		$query->send();
		$raw_content = $query->response_body;
		self::check_session_error ($raw_content);
		return $raw_content;
	}
	
}
