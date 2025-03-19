<?php
namespace Lib;

use Dom\HTMLDocument;
use Dom\HTMLElement;
use ErrorException;


class VmScraper
{
	
	function __construct (public WebsiteTalk $wt) {}
	
	
	public function get_team_data () : array
	{
		$vmq = new VmQuery($this->wt);
		$raw_content = $vmq->get_team_data();

		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);

		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException("JSON error " . json_last_error() . " : " . json_last_error_msg());
		}

		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];
		if(str_starts_with($html, "session Error")) {
			throw new ErrorException("unexpected error : " . $html);
		}

		// browse HTML v5
		$dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);

		// headers
		$data_headers = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::remove_empty($data_headers);
		array_unshift($data_headers[0], "Poste"); // add missing header for first column

		// rows
		$data = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.second');
		$data = Matrix::remove_empty($data);
		$data = Matrix::pack($data);
		$data = Matrix::parse_values($data, ["string", "string", "int", "int", "int", "int", "int", "int"]);

		return array_merge($data_headers, $data);
	}
	
	
	public function get_league_data () : array
	{
		$vmq = new VmQuery($this->wt);
		$raw_content = $vmq->get_league_data();
		
		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);
		
		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
		}
		
		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];

		// browse HTML v5
		$dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);
		
		// headers
		$data_headers = WebScrapper::extract_data_from_dom($dom, 'body > form#postform > table > tbody > tr > td > table:first-child > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::remove_empty($data_headers);

		// rows
		$data = WebScrapper::extract_data_from_dom($dom, 'body > form#postform > table > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.second:not(:nth-child(3)):not(:nth-child(6))');
		$data = Matrix::remove_empty($data);
		$data = Matrix::pack($data);
		$data = Matrix::parse_values($data, ["string", "int", "int", "int", "int", "string", "string", "string"]);

		return array_merge($data_headers, $data);
	}
	
	
	public function get_transfert_data (int $num_page=1) : array
	{
		$vmq = new VmQuery($this->wt);
		$raw_content = $vmq->get_transfert_data($num_page);
		
		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);
		
		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
		}
		
		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];

		// browse HTML v5
		$dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);
		
		// headers
		$data_headers = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::remove_empty($data_headers);
		array_splice($data_headers[0], 1, 0, ["Poste"]);

		// rows
		$data = WebScrapper::extract_data_from_dom($dom, 'body > table:nth-child(2) > tbody > tr:not(:nth-last-child(2)) > td > table > tbody > tr:nth-child(2)', 'td.second:not(:nth-child(2)):not(:nth-child(3))');
		$data = Matrix::remove_empty($data);
		$data = Matrix::pack($data);
		$data = Matrix::parse_values($data, ["DateTime", "string", "string", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int", "int"]);

		return array_merge($data_headers, $data);
	}
	
	
	public function get_coaches_data () : array
	{
		$vmq = new VmQuery($this->wt);
		$raw_content = $vmq->get_coaches_data();
		
		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);
		
		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
		}
		
		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];

		// browse HTML v5
		$dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);
		
		$tr1_list = $dom->querySelectorAll("body > table:nth-child(1) > tbody > tr");
		$tr1_array = iterator_to_array($tr1_list);
		$tr1_splited = array_chunk ($tr1_array, 7);
		
		$coaches = [];
		foreach ($tr1_splited as $tr1_array) {
			$coach = [];
			
			$node = $tr1_array [0]; /** @var HTMLElement $node */
			$element = $node->querySelector("tr > td > table > tbody > tr:nth-child(2) > td:nth-child(3) > b > span");
			$coach ["type"] = $element->textContent;
			
			$element = $node->querySelector("tr > td > table > tbody > tr:nth-child(2) > td:nth-child(4) > b > span > b");
			$coach ["name"] = $element->textContent;
			
			$node = $tr1_array [2]; /** @var HTMLElement $node */
			$tr2_list = $node->querySelectorAll("tr > td > table > tbody > tr:nth-child(2) > td:nth-child(3) > table > tbody > tr");
			
			$node = $tr2_list->item(0);
			$element = $node->querySelector("tr > td:nth-child(3)");
			$coach ["Entraînement physique"] = WebScrapper::parse_value($element->textContent, "int");
			
			$element = $node->querySelector("tr:nth-child(1) > td:nth-child(6)");
			$coach ["Travail avec les juniors"] = WebScrapper::parse_value($element->textContent, "int");
			
			$node = $tr2_list->item(1);
			$element = $node->querySelector("tr > td:nth-child(2)");
			$coach ["Entraînement technique"] = WebScrapper::parse_value($element->textContent, "int");
			
			$element = $node->querySelector("tr > td:nth-child(5)");
			$coach ["Adaptabilité"] = WebScrapper::parse_value($element->textContent, "int");
			
			$node = $tr2_list->item(2);
			$element = $node->querySelector("tr > td:nth-child(2)");
			$coach ["Psychologie"] = WebScrapper::parse_value($element->textContent, "int");
			
			$element = $node->querySelector("tr > td:nth-child(5)");
			$coach ["Niveau de discipline"] = WebScrapper::parse_value($element->textContent, "int");
			
			$node = $tr2_list->item(3);
			$element = $node->querySelector("tr > td:nth-child(2)");
			$coach ["Motivation"] = WebScrapper::parse_value($element->textContent, "int");
			
			$node = $tr2_list->item(4);
			$element = $node->querySelector("tr > td:nth-child(1)");
			$coach ["age"] = WebScrapper::parse_value($element->textContent, "int");
			
			$element = $node->querySelector("tr > td:nth-child(2)");
			$coach ["salaire"] = WebScrapper::parse_value($element->textContent, "int");
			
			$node = $tr1_array [4]; /** @var HTMLElement $node */
			$changer = $node->querySelector("tr > td > table > tbody > tr:nth-child(2)");
			$attributes = $changer->attributes;
			$onclick_attribute = $attributes->getNamedItem("onclick");
			$onclick_attribute->value;
			$res = preg_match("/javascript:callGetViewPanelBody\('CoachChange&coachId=(\d+)'\);/", $onclick_attribute->value, $matches);
			if($res === false) {
				throw new ErrorException("regex error while looking for coach id");
			}
			if(empty($matches[1])) {
				throw new ErrorException("coach id not found");
			}
			$coach ["id"] = $matches[1];
			
			$coaches [] = $coach;
		}
		
		$headers = array_keys ($coaches [0]);
		// $coaches = Matrix::pack($coaches);
		
		$data = array_merge([$headers], $coaches);
		return array_merge([$headers], $coaches);
	}
	
	
	public function get_coach_change_data (int $coach_id, int $num_page=1) : array
	{
		$vmq = new VmQuery($this->wt);
		$raw_content = $vmq->get_coach_change_data($coach_id, $num_page);
		
		// clean JSON
		$raw_content = WebScrapper::clean_dirty_json($raw_content);
		
		// validate JSON
		$valid = json_validate($raw_content);
		if($valid === false) {
			throw new ErrorException(json_last_error() . " : " . json_last_error_msg());
		}
		
		// decode JSON
		$decoded = json_decode($raw_content, true);
		$html = $decoded ["body"];
		// echo $html; die;

		// browse HTML v5
		$dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);
		// WebScrapper::display_html_tree($dom); die;
		
		// headers
		$data_headers = WebScrapper::extract_data_from_dom($dom, 'body > table > tbody > tr > td > table > tbody > tr:nth-child(2)', 'td.fourth');
		$data_headers = Matrix::remove_empty($data_headers);
		
		// rows
		$data = WebScrapper::extract_data_from_dom($dom, 'body > table > tbody > tr:not(:nth-last-child(-n+7)) > td > table > tbody > tr:nth-child(2)', 'td.second:not(:has(> span))');
		$data = Matrix::remove_empty($data);
		$data = Matrix::pack($data);
		$data = Matrix::parse_values($data, ["string", "int", "int", "int", "int", "int", "int", "int", "int", "int", "string"]);

		return array_merge($data_headers, $data);
	}
	
}
