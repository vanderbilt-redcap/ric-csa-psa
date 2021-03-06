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
		
		<?php
			$printCSArow = true;
			$printPSArow = true;
			if (count($reportData) == 3) {
				$name = $reportData[0]['study_name'];
				if ($reportData[0]['csa'] == 0) $printCSArow = false;
				if ($reportData[0]['psa'] == 0) $printPSArow = false;
				echo("
		<h1>$name Metrics Report</h1>
		");
			}
			if ($printCSArow) {
				echo("
		<div class='statRow'>
			<div class='statBox'>
				<span>" . $reportData['totals']['csas']['count'] . "</span>
				<span>CSAs</span>
			</div>
			<div class='statBox'>
				<span>" . $reportData['totals']['csas']['hits'] . "</span>
				<span>Hits</span>
			</div class='statBox'>
			<div class='statBox'>
				<span>" . $reportData['totals']['csas']['contacts'] . "</span>
				<span>User Contact Submissions</span>
			</div>
			<div class='statBox'>
				<span>" . $reportData['totals']['csas']['locations'] . "</span>
				<span>Locations</span>
			</div>
			<div class='statCircle' data-value='84'>
				<span>84/100</span>
			</div>
		</div>
		");
			}
			if ($printPSArow) {
				echo("
		<div class='statRow'>
			<div class='statBox'>
				<span>" . $reportData['totals']['psas']['count'] . "</span>
				<span>PSAs</span>
			</div>
			<div class='statBox'>
				<span>" . $reportData['totals']['psas']['hits'] . "</span>
				<span>Hits</span>
			</div class='statBox'>
			<div class='statBox'>
				<span>" . $reportData['totals']['psas']['contacts'] . "</span>
				<span>User Contact Submissions</span>
			</div>
			<div class='statBox'>
				<span>" . $reportData['totals']['psas']['locations'] . "</span>
				<span>Locations</span>
			</div>
			<div class='statCircle' data-value='84'>
				<span>84/100</span>
			</div>
		</div>
		");
			}
		?>
		<!-- heatmap -->
		<div id='choropleth'></div>
		<script type='text/javascript'>
			var reportData = JSON.parse(`<?php echo(json_encode($reportData)); ?>`);
		</script>
		<script src="js/states.js"></script>
		<script src="js/map.js"></script>
		<!-- drilldown tables -->
		<div id='tableContainer'>
<?php
foreach($reportData as $key => $study) {
	if (!is_numeric($key)) continue;
	$studyType = 'N/A';
	if ($study['csa'] == 1) $studyType = '(CSA)';
	if ($study['psa'] == 1) $studyType = '(PSA)';
	echo("
			<table>
				<thead class='tableCollapsible'>
					<tr>
						<th></th>
						<th>Study Name</th>
						<th>Hits</th>
						<th>User Contact Submissions</th>
						<th>% Conversion</th>
						<th>Study Type (CSA/PSA)</th>
					</tr>
					<tr>
						<th><img src='images/caret-down-solid.svg' onerror=\"this.onerror=null; this.src='images/caret-down-solid.png'\" class='tableCaret rotated'></th>
						<th>{$study['study_name']}</th>
						<th>{$study['totals']['hits']}</th>
						<th>{$study['totals']['contacts']}</th>
						<th>{$study['totals']['conversionRate']}%</th>
						<th>$studyType</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><small>Location Detail</small></td>
					</tr>");
	foreach ($study['locations'] as $locationName => $locationCounts) {
		echo("
					<tr>
						<td></td>
						<td>$locationName</td>
						<td>{$locationCounts['hits']}</td>
						<td>{$locationCounts['contacts']}</td>
						<td></td>
						<td></td>
					</tr>");
	}
	echo("
					<tr>
						<td><small>Page Detail</small></td>
					</tr>");
	foreach ($study['pages'] as $pageName => $pageHits) {
		echo("
					<tr>
						<td></td>
						<td>$pageName</td>
						<td>$pageHits</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>");
	}
	echo("
				</tbody>
			</table>");
}
?>
		</div>
	</body>
</html>