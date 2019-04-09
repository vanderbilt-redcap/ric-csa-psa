<?php
if(!defined("ENVIRONMENT")) {
	// echo('1');
	if (is_file('/app001/victrcore/lib/Victr/Env.php')) include_once('/app001/victrcore/lib/Victr/Env.php');
	if (class_exists("Victr_Env")) {
		$envConf = Victr_Env::getEnvConf();

		if ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_PROD) {
			define("ENVIRONMENT", "PROD");
			define("MASTER_PID", 91748);	// real project on prod");
		} elseif ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_DEV) {
			define("ENVIRONMENT", "TEST");
			define("MASTER_PID", 1297);
		}
	} elseif (gethostname() == 'VICTRWD-83SJHQ2') {
		define("ENVIRONMENT", "DEV");
		define("MASTER_PID", 28);
	} else {
		define("ENVIRONMENT", "DEV");
		define("MASTER_PID", 19);
	}
}

// published* projects on redcap (vanderbilt)
// ValEAR: 90287
// target: 73477

// pid list label: CSA PSA project id list
// pid list fname: csa_psa_project_ids.txt