<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<link rel="stylesheet" href="css/report.css">
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
			integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
			crossorigin=""/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
		<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script src="js/report.js"></script>
		<script src="js/radialIndicator/radialIndicator.js"></script>
		<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
			integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
			crossorigin=""></script>
		<title>Recruitment Innovation Center - General CSA/PSA Web Metrics Report</title>
	</head>
	<body>
		<!-- logo -->
		<img src='images/cheaplogo.png' alt='RIC Logo' height='150' width='400'>
		<!-- csa row -->
		<div class='statRow'>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['csas']['count']); ?></span>
				<span>CSAs</span>
			</div>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['csas']['hits']); ?></span>
				<span>Hits</span>
			</div class='statBox'>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['csas']['contacts']); ?></span>
				<span>Contacts</span>
			</div>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['csas']['locations']); ?></span>
				<span>Locations</span>
			</div>
			<div class='statCircle' data-value='84'>
				<span>84/100</span>
			</div>
		</div>
		<!-- psa row -->
		<div class='statRow'>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['psas']['count']); ?></span>
				<span>PSAs</span>
			</div>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['psas']['hits']); ?></span>
				<span>Hits</span>
			</div class='statBox'>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['psas']['contacts']); ?></span>
				<span>Contacts</span>
			</div>
			<div class='statBox'>
				<span><?php echo($reportData['totals']['psas']['locations']); ?></span>
				<span>Locations</span>
			</div>
			<div class='statCircle' data-value='84'>
				<span>84/100</span>
			</div>
		</div>
		<!-- heatmap -->
		<div id='choropleth'></div>
		<script type='text/javascript'>
			var reportData = JSON.parse(`<?php echo(json_encode($reportData)); ?>`);
		</script>
		<script src="js/states.js"></script>
		<script src="js/map.js"></script>
	</body>
</html>