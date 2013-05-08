var $j = jQuery.noConflict();

$j(function(){
	$j('ul#filter a').click(function() {
		$j(this).css('outline','none');
		$j('ul#filter .current').removeClass('current');
		$j(this).parent().addClass('current');
		
		var filterVal = $j(this).text().toLowerCase().replace(/ /g,'-');
		var AllCategories = $j('.all_cats').text().toLowerCase().replace(/ /g,'-');
				
		if(filterVal == AllCategories) {
			$j('ul.portfolio-one li, ul.portfolio-two li, ul.portfolio-three li, ul.portfolio-four li').fadeTo('slow', 1.00).removeClass('hiden');
		} else {
			
			$j('ul.portfolio-one li, ul.portfolio-two li, ul.portfolio-three li, ul.portfolio-four li').each(function() {
				if(!$j(this).hasClass(filterVal)) {
					$j(this).fadeTo('slow', 0.25);
				} else {
					$j(this).fadeTo('slow', 1.00);
				}
			});
		}
		
		return false;
	});
});