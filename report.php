<?php

require_once "../../redcap_connect.php";
require_once "config.php";

class Report {
	public static function makeReport($args) {
		$report = '';
		
		return $report;
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
					$pids[] = $potentialPID;
				}
			}
			if (empty($pids)) {
				exit("Found project ID list in master project repository but couldn't find any project IDs in the text file.");
			}
			return $pids;
		}
	}
}

print_r(Report::getProjectIDFile());