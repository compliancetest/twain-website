(function($) {

	$('#mc4wp-admin input[name$="[double_optin]"]').change(function() {
		if($(this).val() == 0) {
			$("tr#mc4wp-send-welcome").removeClass('hidden').find(':input').removeAttr('disabled');
		} else {
			$("tr#mc4wp-send-welcome").addClass('hidden').find(':input').attr('disabled', 'disabled').attr('checked', false);
		}
	});

	$('#mc4wp-admin input[name="mc4wp_form[update_existing]"]').change(function() {
		if($(this).val() == 1) {
			$("tr#mc4wp-replace-interests").removeClass('hidden').find(':input').removeAttr('disabled');
		} else {
			$("tr#mc4wp-replace-interests").addClass('hidden').find(':input').attr('disabled', 'disabled').attr('checked', false);
		}
	})

	$("#mc4wp-admin select[name='mc4wp_form[css]']").change(function() {
		$("#mc4wp-custom-color").toggle(($(this).val() == 'custom-color'));
	});

	// init
	$('input.color-field').wpColorPicker();

})(jQuery)