<?php
define("NOAUTH", true);
require_once "../../redcap_connect.php";
require_once "config.php";

$geocodesPath = str_replace("temp", "plugins" . DIRECTORY_SEPARATOR . "ric-csa-psa", APP_PATH_TEMP . "geocodes.json");
$logPath = str_replace("temp", "plugins" . DIRECTORY_SEPARATOR . "ric-csa-psa", APP_PATH_TEMP . "log.txt");
$geocodes = json_decode(file_get_contents($geocodesPath), true);
$geocodingKey = file_get_contents('geocodingKey.txt');
$missingMarkers = 0;

class RICReport {
	public static function geocode($place) {
		global $geocodes;
		global $missingMarkers;
		global $geocodingKey;
		
		// attempt to retrieve geocode info from cache
		if (isset($geocodes[$place])) {
			return $geocodes[$place];
		}
		
		// not in cache so use Google Geocoding API
		try {
			$name = urlencode($place);
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=$name&key=$geocodingKey";
			$json = file_get_contents($url);
			$response = json_decode($json, true);
			
			// // use Nominatim OSM api:
			// $url = "https://nominatim.openstreetmap.org/search?q=$name&format=json&limit=1";
			// // create stream with HTTP UserAgent info
			// $opts = [
				// 'http' => [
					// 'header' => "User-Agent: redcap-ric-csa-psa-report-plugin 1.0\r\n",
					// 'method' => "POST"
				// ]
			// ];
			// $context = stream_context_create($opts);
			// $json = file_get_contents($url, false, $context);
			
			if ($response['status'] != 'OK') throw new Exception('non-ok response from geocoding API: ' . $response['status']);
			$lat = $response['results'][0]['geometry']['location']['lat'];
			$lng = $response['results'][0]['geometry']['location']['lng'];
			$state = '';
			foreach ($response['results'][0]['address_components'] as $i => $part) {
				if ($part['types'][0] == 'administrative_area_level_1') {
					$state = $part['long_name'];
				}
			}
			if (!is_float($lat) or !is_float($lng)) {
				throw new Exception('retrieved non-float lat/long coords');
			}
		} catch (Exception $e) {
			// TODO log geocode failure
			$missingMarkers++;
			// file_put_contents('log.txt', "Failed to geocode location with place name: '$place'\r\nException text: $e\r\n", FILE_APPEND | LOCK_EX);
			return null;
		}
		$geocodes[$place] = [
			"lat" => $lat,
			"lng" => $lng,
			"state" => $state
		];
		return $geocodes[$place];
	}
	
	private static function getFieldLabels($args) {
		$project = new \Project($args['pid']);
		$csv = $project->metadata[$args['field']]['element_enum'];
		$csv = explode('\n', $csv);
		$labels = [];
		foreach ($csv as $line) {
			preg_match_all('/(\d+),\s+(.*)/', $line, $matches);
			if (!empty($matches) and isset($matches[1][0])) {
				// preg_match('/(\d+)[\s]+(.*)/', $matches[2][0], $match);
				$labels[$matches[1][0]] = $matches[2][0];
			}
			unset($matches);
		}
		return $labels;
	}
	
	public static function getProjectIDs() {
		// fetches the txt file in the master project's file repository that contains list of other study (CSA/PSA) project IDs
		
		$pids = [];
		$query = "SELECT * FROM redcap_edocs_metadata
			WHERE project_id=" . MASTER_PID . " AND doc_name LIKE '%csa_psa_project_ids%'
			ORDER BY stored_date DESC
			LIMIT 1";
		$result = db_query($query);
		$info = db_fetch_assoc($result);
		if (isset($info['stored_name'])) {
			$pidsFilename = EDOC_PATH . $info['stored_name'];
			if(!file_exists($pidsFilename)){
				exit("Couldn't find list of CSA/PSA project IDs in the master project file repository. Please ensure the text file with project IDs is uploaded to the master project file repository with the name 'csa_psa_project_ids.txt'. The master project ID is " . MASTER_PID);
			}
			$pidsFile = file_get_contents($pidsFilename);
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $pidsFile) as $line){
				preg_match_all("/(\d+)/", $line, $matches);
				$potentialPID = intval($matches[0][0]);
				if (gettype($potentialPID) == 'integer') {
					if ($potentialPID > 0) $pids[] = $potentialPID;
				}
			}
			if (empty($pids)) {
				exit("Found project ID list in master project repository but couldn't find any project IDs in the text file.");
			}
			return $pids;
		}
	}
	
	public static function getReportData($projectIDs) {
		// file_put_contents('log.txt', "Called getReportData function\r\n", FILE_APPEND | LOCK_EX);
		$pids = 
		$data = [];
		$data['totals'] = [
			'csas' => [
				'count' => 0,
				'hits' => 0,
				'contacts' => 0,
				'locations' => 0,
				'actual' => 0
			],
			'psas' => [
				'count' => 0,
				'hits' => 0,
				'contacts' => 0,
				'locations' => 0,
				'actual' => 0
			]
		];
		
		function sortLocationsByHits($a, $b) {
			$a = $a['hits'];
			$b = $b['hits'];
			if ($a < $b) return 1;
			if ($a > $b) return -1;
			return 0;
		}
		function sortPagesByHits($a, $b) {
			if ($a < $b) return 1;
			if ($a > $b) return -1;
			return 0;
		}
		
		foreach ($projectIDs as $pid) {
			$project = new \Project($pid);
			$appTitle = $project->project['app_title'];
			
			$eid = $project->firstEventId;
			$project = \REDCap::getData($pid);
			if ($project[1][$eid]['published'] == true) {
				// we're going to re-organize the project data so it'll be easier to use on the front-end
				$locations = [];
				$pages = [];
				
				$contactLocationLabels = \RICReport::getFieldLabels(['pid' => $pid, 'field' => 'ct_location']);
				$contacts = $project[1]['repeat_instances'][$eid]['contacts'];
				$hits = $project[1]['repeat_instances'][$eid]['hits'];
				$totals = [
					'hits' => 0,
					'contacts' => 0,
					'actual' => 0
				];
				
				foreach ($contacts as $contact) {
					$totals['contacts']++;
					
					$locationID = $contact['ct_location'];
					if (isset($contactLocationLabels[$locationID])) {
						// determine location name
						preg_match('/(?:\d+)?\s?(.*)/', $contactLocationLabels[$locationID], $match);
						$locationName = $match[1];
						
						// add location to locations if not present
						if (!isset($locations[$locationName])) {
							$locations[$locationName] = [
								'hits' => 0,
								'contacts' => 0
							];
							$coords = \RICReport::geocode($locationName);
							
							if (gettype($coords) == 'array') {
								$locations[$locationName]['lat'] = $coords['lat'];
								$locations[$locationName]['lng'] = $coords['lng'];
								$locations[$locationName]['state'] = $coords['state'];
							}
						}
						// increment contacts counter
						$locations[$locationName]['contacts']++;
					}
				}
				
				foreach ($hits as $hit) {
					$totals['hits']++;
					
					$locationID = $hit['hit_location'];
					if (isset($contactLocationLabels[$locationID])) {
						// determine location name
						preg_match('/(?:\d+)?\s?(.*)/', $contactLocationLabels[$locationID], $match);
						$locationName = $match[1];
						
						// add location to locations if not present
						if (!isset($locations[$locationName])) {
							$locations[$locationName] = [
								'hits' => 0,
								'contacts' => 0
							];
							$coords = \RICReport::geocode($locationName);
							if (gettype($coords) == 'array') {
								$locations[$locationName]['lat'] = $coords['lat'];
								$locations[$locationName]['lng'] = $coords['lng'];
								$locations[$locationName]['state'] = $coords['state'];
							}
						}
						// increment contacts counter
						$locations[$locationName]['hits']++;
					}
					
					preg_match('/page(\d+)/', $hit['hit_url'], $match);
					if (!empty($match)) {
						$page = 'Page ' . $match[1];
						if (isset($pages[$page])) {
							$pages[$page]++;
						} else {
							$pages[$page] = 1;
						}
					}
				}
				
				$totals['conversionRate'] = round(100 * $totals['actual'] / $totals['hits']);
				
				if ($project[1][$eid]['csa_psa'][1] == true) {
					$data['totals']['csas']['count']++;
					$data['totals']['csas']['hits'] += $totals['hits'];
					$data['totals']['csas']['contacts'] += $totals['contacts'];
					$data['totals']['csas']['locations'] += count($locations);
				}
				if ($project[1][$eid]['csa_psa'][2] == true) {
					$data['totals']['psas']['count']++;
					$data['totals']['psas']['hits'] += $totals['hits'];
					$data['totals']['psas']['contacts'] += $totals['contacts'];
					$data['totals']['psas']['locations'] += count($locations);
				}
				
				uasort($pages, 'sortPagesByHits');
				uasort($locations, 'sortLocationsByHits');
				
				$data[] = [
					'study_name' => $appTitle,
					'csa' => $project[1][$eid]['csa_psa'][1],
					'psa' => $project[1][$eid]['csa_psa'][2],
					'locations' => $locations,
					'pages' => $pages,
					'totals' => $totals
				];
			} else {
				unset($data[$pid]);
			}
		}
		return $data;
	}
}

if (!defined('MASTER_PID')) {
	echo("<h3>Missing Master Project</h3>");
	echo("<span>No master RIC CSA/PSA project has been configured for this server. Please contact your REDCap administrator.</span>");
} else {
	// fetch report data from REDCap projects
	$pids = \RICReport::getProjectIDs();
	$reportData = \RICReport::getReportData($pids);
	$reportData['missingMarkers'] = $missingMarkers;
	
	// save geocode info
	file_put_contents($geocodesPath, json_encode($geocodes));
	
	// print report using reportData
	include 'report.php';
}