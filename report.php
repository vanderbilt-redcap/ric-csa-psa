<?php

require_once "../../redcap_connect.php";
require_once "config.php";

class Report {
	
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
					$pids[] = $potentialPID;
				}
			}
			if (empty($pids)) {
				exit("Found project ID list in master project repository but couldn't find any project IDs in the text file.");
			}
			return $pids;
		}
	}
	
	public static function getReportData($projectIDs) {
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
		foreach ($projectIDs as $pid) {
			$project = new \Project($pid);
			$appTitle = $project->project['app_title'];
			
			$eid = $project->firstEventId;
			$project = \REDCap::getData($pid);
			if ($project[1][$eid]['published'] == true) {
				// we're going to re-organize the project data so it'll be easier to use on the front-end
				$locations = [];
				$pages = [];
				
				$contactLocationLabels = \Report::getFieldLabels(['pid' => $pid, 'field' => 'ct_location']);
				$contacts = $project[1]['repeat_instances'][$eid]['contacts'];
				$hits = $project[1]['repeat_instances'][$eid]['hits'];
				$totals = [
					'hits' => 0,
					'contacts' => 0,
					'actual' => 0
				];
				foreach ($contacts as $contact) {
					$totals['contacts']++;
					if (isset($locations[$contact['ct_location']])) {
						$locations[$contact['ct_location']]++;
					} else {
						$locations[$contact['ct_location']] = 1;
					}
				}
				foreach ($hits as $hit) {
					$totals['hits']++;
					if (isset($locations[$hit['hit_location']])) {
						$locations[$hit['hit_location']]++;
					} else {
						$locations[$hit['hit_location']] = 1;
					}
					
					preg_match('/page(\d+)/', $hit['hit_url'], $match);
					if (empty($match)) {
						$page = $hit['hit_url'];
					} else {
						$page = 'Page ' . $match[1];
					}
					if (isset($pages[$page])) {
						$pages[$page]++;
					} else {
						$pages[$page] = 1;
					}
				}
				
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
	
	public static function getFieldLabels($args) {
		$project = new \Project($args['pid']);
		$csv = $project->metadata[$args['field']]['element_enum'];
		$csv = explode('\n', $csv);
		$labels = [];
		foreach ($csv as $line) {
			preg_match_all('/(\d+),\s+(.*)/', $line, $matches);
			if (!empty($matches) and isset($matches[1][0])) {
				$labels[$matches[1][0]] = $matches[2][0];
			}
			unset($matches);
		}
		return $labels;
	}
}

$pids = \Report::getProjectIDs();
$reportData = \Report::getReportData($pids);

// echo("<pre>");
// print_r($reportData);
// echo("</pre>");
// exit();

// \Report::makeGeneralReport();
include 'generalReport.html';