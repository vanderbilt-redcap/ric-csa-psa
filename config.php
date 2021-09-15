<?php
use Vanderbilt\Victrlib\Env;
if(!defined("ENVIRONMENT")) {
	# Define the environment: options include "DEV", "TEST" or "PROD"
	if (is_file('/app001/www/redcap/plugins/victrlib/src/Env.php'))
		include_once('/app001/www/redcap/plugins/victrlib/src/Env.php');

	if (class_exists("\\Vanderbilt\\Victrlib\\Env")) {
		if (Env::isProd()) {
			define("ENVIRONMENT", "PROD");
			define("MASTER_PID", 91748);	// real project on prod");
		} else if (Env::isStaging()) {
			define("ENVIRONMENT", "TEST");
			define("MASTER_PID", 1297);
		}
	} else {
		define("ENVIRONMENT", "DEV");
		if (gethostname() == 'VICTRWD-83SJHQ2') {
			define("ENVIRONMENT", "DEV");
			define("MASTER_PID", 28);
		} else {
			define("ENVIRONMENT", "DEV");
			define("MASTER_PID", 19);
		}
	}
}

// published* projects on redcap (vanderbilt)
// ValEAR: 90287
// target: 73477

// pid list label: CSA PSA project id list
// pid list fname: csa_psa_project_ids.txt