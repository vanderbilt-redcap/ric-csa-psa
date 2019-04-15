$(function(){
	let states = {};
	statesData.features.forEach(function(state) {
		state.properties.hits = 0;
		states[state.properties.name] = state.properties;
	})
	
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
	let markerIcon = L.icon({
		iconUrl: 'images/marker.png',
		iconSize: [16, 16]
	});
	let maxHits = 0;
	Object.keys(reportData).forEach(function(project, key) {
		if ($.isNumeric(project)) {
			let locations = reportData[project].locations;
			Object.keys(locations).forEach(function(location, name) {
				let lat = locations[location].lat;
				let lng = locations[location].lng;
				if (lat && lng && locations[location].hits > 0) {
					let marker = L.marker([lat, lng], {icon: markerIcon}).addTo(map);
					marker.bindPopup("<b>" + location + "</b><br>Hits: " + locations[location].hits);
					marker.on({
						mouseover: function(e) {
							this.openPopup();
						},
						mouseout: function(e) {
							this.closePopup();
						}
					});
				}
				
				let state = states[locations[location].state];
				if (state != null) {
					state.hits += locations[location].hits;
					maxHits = Math.max(maxHits, state.hits);
				}
			});
		}
	});
	
	// change state colors based on hit count
	let legendValues = [
		maxHits * 0,
		maxHits * 0.1,
		maxHits * 0.25,
		maxHits * 0.33,
		maxHits * 0.5,
		maxHits * 0.66,
		maxHits * 0.75,
		maxHits * 0.9,
	];
	legendValues.forEach(function(val, i) {
		legendValues[i] = Math.round(val);
	});
	function getColor(hits) {
		return hits > legendValues[7] ? '#800026':
			   hits > legendValues[6] ? '#BD0026':
			   hits > legendValues[5] ? '#E31A1C':
			   hits > legendValues[4] ? '#FC4E2A':
			   hits > legendValues[3] ? '#FD8D3C':
			   hits > legendValues[2] ? '#FEB24C':
			   hits > legendValues[1] ? '#FED976':
			   '#FFEDA0';
	}
	function styleMapFeature(feature) {
		return {
			fillColor: getColor(feature.properties.hits),
			weight: 2,
			opacity: 1,
			color: 'white',
			dashArray: '3',
			fillOpacity: 0.7
		};
	}
	function highlightFeature(e) {
		var layer = e.target;
		
		layer.setStyle({
			weight: 5,
			color: '#666',
			dashArray: '',
			fillOpacity: 0.7
		});
		
		if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
			layer.bringToFront();
		}
		info.update(layer.feature.properties);
	}
	function resetHighlight(e) {
		geojson.resetStyle(e.target);
		info.update();
	}
	function zoomToFeature(e) {
		map.fitBounds(e.target.getBounds());
	}
	function onEachFeature(feature, layer) {
		layer.on({
			mouseover: highlightFeature,
			mouseout: resetHighlight,
			click: zoomToFeature
		});
	}
	
	var geoJson;
	geojson = L.geoJson(statesData, {
		style: styleMapFeature,
		onEachFeature: onEachFeature
	}).addTo(map);
	
	// add state info
	var info = L.control();

	info.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
		this.update();
		return this._div;
	};

	// method that we will use to update the control based on feature properties passed
	info.update = function (props) {
		this._div.innerHTML = '<h4>US Population Density</h4>' +  (props ?
			'<b>' + props.name + '</b><br />' + props.hits + ' hits'
			: 'Hover over a state');
	};

	info.addTo(map);
	
	
	// add legend via control
	var legend = L.control({position: 'bottomright'});

	legend.onAdd = function (map) {
		var div = L.DomUtil.create('div', 'info legend'),
			// grades = [0, 10, 20, 50, 100, 200, 500, 1000],
			grades = legendValues,
			labels = [];

		// loop through our density intervals and generate a label with a colored square for each interval
		for (var i = 0; i < grades.length; i++) {
			div.innerHTML +=
				'<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
				grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
		}

		return div;
	};

	legend.addTo(map);
});