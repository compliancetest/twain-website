var $j = jQuery.noConflict();
	
function doMove(panelWidth, tooFar) {
	var leftValue = $j("#mover").css("left");
	
	// Fix for IE
	if (leftValue == "auto") { leftValue = 0; };
	
	var movement = parseFloat(leftValue, 10) - panelWidth;
	
	if (movement == tooFar) {
		$j(".slide_offer img").animate({
			top: '-200',
			opacity: 0
		}, function() {
			$j("#mover").animate({
				"left": 0
			}, function() {
				$j(".slide_offer img").animate({
					top: '10',
					opacity: 1.0
				});
			});
		});
	}
	else {
		$j(".slide_offer img").animate({
			top: '-200',
			opacity: 0
		}, function() {
			$j("#mover").animate({
				"left": movement
			}, function() {
				$j(".slide_offer img").animate({
					top: '10',
					opacity: 1.0
				});
			});
		});
	}
}


$j(function(){
	

	var panelWidth = 960;
	var panelPaddingLeft = 40;
	var panelPaddingRight = 40;

	panelWidth = parseFloat(panelWidth, 10);
	panelPaddingLeft = parseFloat(panelPaddingLeft, 10);
	panelPaddingRight = parseFloat(panelPaddingRight, 10);

	panelWidth = panelWidth + panelPaddingLeft + panelPaddingRight;
	
	var numPanels = $j(".slide_offer").length;
	var tooFar = -(panelWidth * numPanels);
	var totalMoverwidth = numPanels * panelWidth;
	$j("#mover").css("width", totalMoverwidth);

	$j("#slider_offer").append('<a href="#" id="slider-stopper" class="offer_slider_buttons play_off">Pause</a>');

	sliderIntervalID = setInterval(function(){
		doMove(panelWidth, tooFar);
	}, delayLength);
	
	$j("#slider-stopper").click(function(){
		if ($j(this).text() == "Pause") {
			clearInterval(sliderIntervalID);
		 	$j(this).text("Play");
			$j(".offer_slider_buttons").removeClass("play_off");
		}
		else {
			sliderIntervalID = setInterval(function(){
				doMove(panelWidth, tooFar);
			}, delayLength);
		 	$j(this).text("Pause");
			$j(".offer_slider_buttons").addClass("play_off");
		}
		 
	});

});