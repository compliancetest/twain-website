(function($) {

	// vars
	var $iframe = $("#mc4wp-css-preview");
	var $form, $paragraphs, $labels, $fields, $buttons, $notices;

	// functions
	function setElements()
	{
		$form = $iframe.contents().find('.mc4wp-form');
		$paragraphs = $form.find('p');
		$labels = $form.find('label');
		$fields = $form.find('input[type="text"], input[type="email"], input[type="url"], input[type="number"], input[type="date"], select, textarea').not('[name="mc4wp_required_but_not_really"]');
		$buttons = $form.find('input[type="submit"], input[type="button"], button');
		$notices = $form.find('.mc4wp-alert');
	}

	function clearCSS()
	{
		$form.removeAttr('style');
		$paragraphs.removeAttr('style');
		$labels.removeAttr('style');
		$fields.removeAttr('style');
		$buttons.removeAttr('style');
	}

	function applyCSS()
	{

		clearCSS();

		var labelsTextStyle = $("#labels-font-style").val();

		/* form container */
		$form.css({
			"display": "block",
			"background": getColor($("#form-background-color")),
			"color": getColor($("#form-font-color")),
			"border-width": $("#form-border-width").val() + "px",
			"border-color": getColor($("#form-border-color"), "transparent"),
			"padding-top": $("#form-vertical-padding").val() + "px",
			"padding-bottom": $("#form-vertical-padding").val() + "px",
			"padding-left": $("#form-horizontal-padding").val() + "px",
			"padding-right": $("#form-horizontal-padding").val() + "px",
			"text-align": $("#form-text-align").val()
		});

		// add border style if border-width is set and bigger than 0
		if($("#form-border-width").val().length > 0 && $("#form-border-width").val() > 0) {
			$form.css('border-style', 'solid');
		}

		/* paragraphs */
		$paragraphs.css({
			"font-size": $("#paragraphs-font-size").val() + 'px',
			"color": getColor($("#paragraphs-font-color")),
			"margin-top": $("#paragraphs-vertical-margin").val() + "px",
			"margin-bottom": $("#paragraphs-vertical-margin").val() + "px"
		});


		/* labels */
		$labels.css({
			"margin-bottom": "6px",
			"color": getColor($("#labels-font-color")),
			"font-size": $("#labels-font-size").val() + 'px',
			"display": getRadioValue($("#labels-display"))
		});

		
		// only set label text style if it is set
		if(labelsTextStyle.length > 0) {
			$labels.css({
				"font-weight": (labelsTextStyle == 'bold' || labelsTextStyle == 'bolditalic') ? 'bold' : 'normal',
				"font-style": (labelsTextStyle == 'italic' || labelsTextStyle == 'bolditalic') ? 'italic' : 'normal',
			});
		}


		/* fields */
		$fields.css({
			"margin-bottom": "6px",
			"box-sizing": "border-box",
			"border-width": $("#fields-border-width").val() + "px",
			"border-color": getColor($("#fields-border-color")),
			"display": getRadioValue($("#fields-display")),
			"padding": "6px 12px",
			"width": $("#fields-width").val(),
			"height": $("#fields-height").val() + 'px',
			'line-height': (getIntValue($("#fields-height")) - 12 - getIntValue($("#fields-border-width"))) + "px"
		});

		/* buttons */
		$buttons.css({
			"text-align": "center",
			"cursor": "pointer",
			"padding": "6px 12px",
			"text-shadow": "none",
			"border-width": $("#buttons-border-width").val() + "px",
			"border-color": getColor($("#buttons-border-color")),
			"width": $("#buttons-width").val(),
			"height": $("#buttons-height").val() + 'px',
			"background-color": getColor($("#buttons-background-color")),
			"color": getColor($("#buttons-font-color")),
			"font-size": $("#buttons-font-size").val() + 'px',
			"display": getRadioValue($("#buttons-display")),
			"line-height": (getIntValue($("#buttons-height")) - getIntValue($("#buttons-border-width")) - 12) + "px"		
		});

		$buttons.hover(function() {
			$(this).css({
				"border-color": getColor($("#buttons-hover-border-color"), false),
				"background-color": getColor($("#buttons-hover-background-color"), false),
				"color": getColor($("#buttons-hover-font-color"), false)
			});
		}, function () {
			$(this).css({
				"border-color": getColor($("#buttons-border-color")),
				"background-color": getColor($("#buttons-background-color")),
				"color": getColor($("#buttons-font-color"))
			});
		});

		// add background reset only if custom background color has been set
		if($("#buttons-background-color").wpColorPicker('color') && $("#buttons-background-color").wpColorPicker('color').length > 0) {
			$buttons.css({
				"background-image": "none",
				"filter": "none",
			});
		}	

		// add border style if border-width is set and bigger than 0
		if($("#buttons-border-width").val().length > 0 && $("#buttons-border-width").val() > 0) {
			$buttons.css('border-style', 'solid');
		}

		/* notices */
		$notices.filter('.mc4wp-success').css({
			'color': getColor($("#messages-font-color-success"))
		})
		$notices.filter(".mc4wp-error").css({
			'color': getColor($("#messages-font-color-error"))
		});

		/* custom css */
		$iframe.contents().find('#custom-css').html($("#mc4wp-css-textarea").val());

	}

	function getRadioValue($parentEl, retval)
	{
		var value = $parentEl.find(":input:checked").val();
		if(value) {
			return value;
		} else {
			return (retval !== undefined) ? retval : '';
		}
	}

	function getIntValue($el, retval) 
	{
		if($el.val()) {
			return parseInt($el.val());
		} else {
			return (retval !== undefined) ? retval : 0;
		}
	}

	function getColor($el, retval)
	{
		if($el.val().length > 0) {
			return $el.wpColorPicker('color');
		} else {
			return (retval !== undefined) ? retval : '';
		}
	}

	// events
	$("#mc4wp-css-preview-form").change(function() {
		var url = $iframe.data('src-url').replace('{form_id}', $(this).val());
		$iframe.attr('src', url);
	});

	$('input.color-field').wpColorPicker({ change: function() { applyCSS() }, clear: function() { applyCSS(); } });

	$("#mc4wp-css-form :input").change(applyCSS).keydown(function() {
		poll(function() { applyCSS(); }, 500);
	});

	$('#mc4wp-css-form input[type="radio"]').mousedown(function(e){
	  var $self = $(this);
	  if( $self.is(':checked') ){
	    var uncheck = function(){
	      setTimeout(function(){$self.removeAttr('checked');},0);
	    };
	    var unbind = function(){
	      $self.unbind('mouseup',up);
	    };
	    var up = function(){
	      uncheck();
	      unbind();
	    };
	    $self.bind('mouseup',up);
	    $self.one('mouseout', unbind);
	  }
	});

	$( ".mc4wp-accordion" ).accordion({ 
		header: "h4", 
		collapsible: true,
		active: false

	});

	$iframe.load(function() {
		setElements();
		applyCSS();
	});

	$("#setting-error-mc4wp-cant-write-css a.mc4wp-show-css").click(function() {
		$generatedCss = $("#mc4wp_generated_css").toggle();
		if($generatedCss.is(":visible")) { $(this).text("Hide generated CSS"); } else { $(this).text("Show generated CSS"); }
	});


	// helper functions
	var poll = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout(timer);
			timer = setTimeout(callback, ms);
		};
	})();

})(jQuery);