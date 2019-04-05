$(function(){
	var maptoken = "pk.eyJ1IjoicmVlZGN3MSIsImEiOiJjanUwMDVyZnEzMXV2M3lwZWV6ODVkNDVwIn0.Anl3bJMpuuz1iuMs7sMxfg";
	var map = L.map('choropleth').setView([37.8, -96], 4);
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + maptoken, {
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>, State polygons shared by Mike Bostock of <a href="http://d3js.org/">D3</a> which was created with 2011 data from the <a href="http://www.census.gov/">US Census Bureau</a>',
		maxZoom: 18,
		id: 'mapbox.light',
		accessToken: maptoken
	}).addTo(map);
	
	L.geoJson(statesData).addTo(map);
	
	// add markers
	// console.log(reportData);
	Object.keys(reportData).forEach(function(project, key) {
		if ($.isNumeric(project)) {
			let locations = reportData[project].locations;
			Object.keys(locations).forEach(function(location, name) {
				let lat = locations[location].lat;
				let lng = locations[location].lng;
				if (lat && lng) {
					let marker = L.marker([lat, lng]).addTo(map);
					marker.bindPopup("<b>" + location + "</b><br>Hits: " + locations[location].hits);
				}
			});
		}
	});
});