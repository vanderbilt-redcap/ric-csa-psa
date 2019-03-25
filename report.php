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
		$data = [];
		foreach ($projectIDs as $pid) {
			$project = new \Project($pid);
			$eid = $project->firstEventId;
			$project = \REDCap::getData($pid);
			if ($project[1][$eid]['published'] == true) {
				echo("<pre>");
				$labels = \Report::getFieldLabels(['pid' => $pid, 'field' => 'ct_location']);
				print_r($labels);
				echo("</pre>");
				exit();
				// we're going to re-organize the project data so it'll be easier to use on the front-end
				$locations = [];
				$pages = [];
				
				$contacts = $project;
				$hits = $project[$pid][1]['repeat_instances'][$eid]['hits'];
				
				echo("<pre>");
				print_r($contacts);
				echo("</pre>");
				exit();
				foreach ($contacts as $contact) {
					$location = $contact['ct_location'];
					if (isset($locations[$location])) {
						
					} else {
						
					}
				}
				foreach ($hits as $hit) {
					
				}
				
				$data[] = [
					'study_name' => $project->project['app_title'],
					'locations' => $locations,
					'pages' => $pages
				];
			} else {
				unset($data[$pid]);
			}
		}
		return $data;
	}
	
	public static function makeGeneralReport($args) {
		include 'generalReport.html';
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
$data = \Report::getReportData($pids);
// \Report::makeGeneralReport($data);
// echo($genReport);