(function($) {
	var console = window.console || { log: function(t){ } };

	/* Ajax Forms */
	var $context;

	/* add style to head*/
	$("head").prepend('<style type="text/css">.mc4wp-ajax-loader{ vertical-align: middle; height: 16px; width:16px; border:0; background: url(\''+ mc4wp_vars.ajax_loader_url +'\')}</style>');

	$(".mc4wp-ajax form").ajaxForm({
		data: { action: 'mc4wp_submit_form' },
		dataType: 'json',
		url: mc4wp_vars.ajaxurl,
		delegation: true,
		success: function(response, status) {

			$ajaxLoader = $context.find('.mc4wp-ajax-loader');
			$ajaxLoader.hide();
			
			if(response.success) {
				$message = $context.find('div.mc4wp-success-message').show();
				$context.find('form').trigger('reset');

				// Redirect to the specified location
				if(response.redirect) {
					window.setTimeout(function() {
						window.location.replace(response.redirect);
					}, 2500);
					
				}

				if(response.hide_form) {
					$context.find('form').hide();
				}

			} else {
				var e = (response.error == '') ? 'error' : response.error;
				$message = $context.find('.mc4wp-' + e + '-message').show();
			}

		},
		error: function(response) {
			console.log(response);
		},
		beforeSubmit: function(data, $form) {
			var $ajaxLoader, $submitButton;

			$context = $form.parent('div.mc4wp-form');
			$context.find('.mc4wp-alert').hide();

			$ajaxLoader = $context.find('.mc4wp-ajax-loader');
			$submitButton = $form.find('input[type=submit]');

			$ajaxLoader.insertAfter($submitButton).css('display', 'inline-block'); 
			
			if(parseInt($ajaxLoader.css('margin-left')) < 5) { $ajaxLoader.css('margin-left', '5px'); }

			return true;
		}
	});

})(jQuery);