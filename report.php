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
		<div id='choropleth'>
			<img src='images/cheapmap.png' alt='CSA PSA metrics choropleth'>
		</div>
		<!-- drilldown tables -->
		<div>
			<div class='columnNames'>
				<span>CSA Name</span>
				<span>Unique Hits</span>
				<span>Contacts</span>
				<span>% Conversion</span>
			</div>
<?php
foreach($reportData as $key => $study) {
	// echo("<pre>");
	// print_r($reportData);
	// echo("</pre>");
	
	if (!is_numeric($key)) continue;
	echo("
			<table>
				<thead class='tableCollapsible'>
					<th><img src='images/caret-down-solid.svg' onerror=\"this.onerror=null; this.src='images/caret-down-solid.png'\" class='tableCaret rotated'></th>
					<th>{$study['study_name']}</th>
					<th>{$study['totals']['hits']}</th>
					<th>{$study['totals']['contacts']}</th>
					<th>{$study['totals']['conversionRate']}%</th>
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
					</tr>");
	}
	echo("
				</tbody>
			</table>");
}
?>
		
		</div>
		<br />
		<br />
		<br />
		<br />
		<br />
	</body>
</html>