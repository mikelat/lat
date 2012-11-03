$().ready(function(){

	if($('#captcha').length) {
		Recaptcha.create(
			lat['validate']['recaptcha_public'], "captcha",
			{
				theme: "red",
				callback: Recaptcha.focus_response_field
			}
		);
	}

	$('form[data-ajax]').submit(function() {
		var form_error = false;

		$('.error', this).removeClass('error');
		$('.msg', this).remove();

		$('input', this).each(function() {
			var msg = '';
			var error = false;

			// clear existing errors

			// match another field
			if($(this).data('match') !== undefined && $(this).val() !== $('#' + $(this).data('match')).val()) {
				error = true;
				msg = lat['validate']['error_validate_match'].replace('%s', $(this).data('match'));
			}

			// regex match
			if($(this).data('regex') !== undefined) {
				var v = $(this).val();
				$.each($(this).data('regex').split(','), function(index, value) {
					var r = lat['validate']['regex'][value].split('/');
					r = new RegExp(r[1], r[2]);
					if( ! r.test(v)) {
						error = true;
						msg = lat['validate']['error_regex_' + value];
					}
				});
			}

			// minimum characters
			if($(this).data('minimum') !== undefined && $(this).val().length < $(this).data('minimum')) {
				error = true;
				msg = $(this).data('minimum') == 1 ? lat['validate']['error_validate_required'] : lat['validate']['error_validate_minimum'].replace('%s', $(this).data('minimum'));
			}

			// maximum characters
			if($(this).data('maximum') !== undefined && $(this).val().length > $(this).data('maximum')) {
				error = true;
				msg = lat['validate']['error_validate_maximum'].replace('%s', $(this).data('maximum'));
			}

			if(error) {
				form_error = true;
				$(this).parent().addClass('error').find('label:eq(0)').after('<p class="msg">' + msg + '</p>');
			}

		});

		if(!form_error) {
			//$("button[type=submit]", this).prop('disabled', true);

			$.ajax({
				type: 'POST',
				url: $(this).attr('action'),
				data: $(this).serialize() + '&submit=1',
				success: function(json) {
					if(json.success == false) {
						Recaptcha.reload();

						var x;
						for(x in json.msg) {
							if(x == 'lockout') {
								popup({ content: json.msg[x], title: 'Locked Out', icon: 'caution' })
							} else {
								$('#' + x).parent().addClass('error').find('label:eq(0)').after('<p class="msg">' + json.msg[x] + '</p>');
							}
						}
					} else {
						load_page(json);
					}
					//$("button[type=submit]", this).prop('disabled', false);
				},
				dataType: 'json'
			});
			//alert('this form will submit');
		}

		return false;
	});
});