var $j = jQuery.noConflict();
$j(function(){

	// Frame
	$j('.su-frame-align-center, .su-frame-align-none').each(function() {
		var frame_width = $j(this).find('img').width();
		$j(this).css('width', frame_width + 14);
	});

	// Spoiler
	$j('.su-spoiler .su-spoiler-title').click(function() {
		$j(this).parent('.su-spoiler').find('.su-spoiler-content').toggle(300);
		$j(this).parent('.su-spoiler').toggleClass('su-spoiler-open');

	});

	// Tabs
	$j('.su-tabs-nav').delegate('span:not(.su-tabs-current)', 'click', function() {
		$j(this).addClass('su-tabs-current').siblings().removeClass('su-tabs-current')
		.parents('.su-tabs').find('.su-tabs-pane').hide().eq($j(this).index()).show();
	});
	$j('.su-tabs-pane').hide();
	$j('.su-tabs-nav span:first-child').addClass('su-tabs-current');
	$j('.su-tabs-panes .su-tabs-pane:first-child').show();

	// Tables
	$j('.su-table tr:even').addClass('su-even');

});

function mycarousel_initCallback(carousel) {

	// Disable autoscrolling if the user clicks the prev or next button.
	carousel.buttonNext.bind('click', function() {
		carousel.startAuto(0);
	});

	carousel.buttonPrev.bind('click', function() {
		carousel.startAuto(0);
	});

	// Pause autoscrolling if the user moves with the cursor over the clip.
	carousel.clip.hover(function() {
		carousel.stopAuto();
	}, function() {
		carousel.startAuto();
	});
}