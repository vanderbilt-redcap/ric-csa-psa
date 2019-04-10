<?php
define("NOAUTH", true);
require_once "../../redcap_connect.php";
require_once "config.php";

// $oldcwd = getcwd();
// $geocodesPath = str_replace("temp", "plugins" . DIRECTORY_SEPARATOR . "ric-csa-psa", APP_PATH_TEMP . "geocodes.json");
// $logPath = str_replace("temp", "plugins" . DIRECTORY_SEPARATOR . "ric-csa-psa", APP_PATH_TEMP . "log.txt");
// $geocodes = json_decode(file_get_contents($geocodesPath), true);
// $geocodingKey = file_get_contents('geocodingKey.txt');
// $missingMarkers = 0;

class RICReport {
	static public function printHi() {
		echo('hi');
	}
}

if (!defined('MASTER_PID')) {
	echo("<h3>Missing Master Project</h3>");
	echo("<span>No master RIC CSA/PSA project has been configured for this server. Please contact your REDCap administrator.</span>");
} else {
	// $pids = \RICReport::getProjectIDs();
	// $reportData = \RICReport::getReportData($pids);
	// $reportData['missingMarkers'] = $missingMarkers;
	// save geocode info
	// file_put_contents($geocodesPath, json_encode($geocodes));
	
	// print report using reportData
	include 'report.php';
}