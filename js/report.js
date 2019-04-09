var rads = [];
$(function() {
	$('tbody').hide();
	
	// set radial indicators
	$(".statCircle").each(function(i, e) {
		var rad = radialIndicator(e, {
			barColor: '#BD0026',
			barWidth: 6,
			roundCorner: true,
			percentage: true
		});
		rads[i] = rad;
		rad.animate($(e).attr("data-value"));
	});
	
	$(".tableCollapsible").on('click', function() {
		$(this).next('tbody').toggle();
		let caret = $(this).find('img');
		if (caret.hasClass('rotated')) {
			caret.removeClass('rotated');
		} else {
			caret.addClass('rotated');
		}
	});
});