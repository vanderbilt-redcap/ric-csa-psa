var rads = [];
$(function() {
	// var choropleth = L.map('choropleth').setView([51.505, -0.09], 13);
	
	// set radial indicators
	$(".statCircle").each(function(i, e) {
		var rad = radialIndicator(e, {
			barColor: '#004cc6',
			barWidth: 6,
			roundCorner: true,
			percentage: true
		});
		rads[i] = rad;
		rad.animate($(e).attr("data-value"));
	})
});