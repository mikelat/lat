$().ready(function(){

	$('form:not([data-no-validate])').submit(function() {
		var form_success = true;

		$('li.error', this).removeClass('error');
		$('p.error-msg', this).html('');
		$('div.error-msg', this).remove();

		$('input', this).each(function() {
			var vfrm = validate_field(this);

			if(vfrm['success'] == false) {
				form_success = false;
			}
		});
		
		if(form_success) {
			$("button[type=submit]", this).prop('disabled', true);

			$.ajax({
				type: 'POST'
			,	url: $(this).attr('action')
			,	data: $(this).serialize() + '&submit=1'
			,	dataType: 'json'
			,	success: function(json) {
					if(json.success == false) {
						for(var x in json.msg) {
							if(x == '_msg') {
								$('footer', this).before('<div class="error-msg">' + json.msg[x] + '</div>');
							} else {
								$('#' + x).parent().addClass('error').find('label:eq(0)').after('<p class="msg">' + json.msg[x] + '</p>');
							}
						}
						$("button[type=submit]", this).prop('disabled', false);
					} else {
						load_page(json);
					}
				}
			});
		}
		else {
			$('footer', this).before('<div class="error-msg">' + language_error('_errors') + '</div>');
		}

		return false;
	});
	
	$('form:not([data-no-validate]) input').keyup(validate_field).blur(validate_field);
});


function validate_field(field) {
	
	var e = null;
	
	// we got passed an input, probably from an event
	if($(this).is('input')) {
		e = field;
		field = $(this);
	}		

	if(!(field instanceof jQuery)) {
		field = $(field);
	}
	
	var r = { 'success': true, 'msg': '' };
	
	// match another field
	if(field.data('validate-match') !== undefined && field.val() !== $('#' + field.data('validate-match')).val()) {
		r = { 'success': false, 'msg': language_error('match', field.data('validate-match')) };
	}

	// regex match
	if(field.data('validate-regex') !== undefined) {
		var regex = lat['regex'][field.data('validate-regex')].split('/');
		regex = new RegExp(regex[1], regex[2]);

		if( ! regex.test(field.val())) {
			r = { 'success': false, 'msg': language_error('regex-' + field.data('validate-regex')) };
		}
	}

	// minimum characters
	if(field.data('validate-minlength') !== undefined && field.val().length < field.data('validate-minlength')) {
		r = { 'success': false, 'msg': language_error('minlength', field.data('validate-minlength')) };
	}

	// maximum characters
	if(field.data('validate-maxlength') !== undefined && field.val().length > field.data('validate-maxlength')) {
		r = { 'success': false, 'msg': language_error('maxlength', field.data('validate-maxlength')) };
	}
		
	if(r['success'] == false) {
		clearTimeout(field.data('validate-ajax-timeout'));
		field.parents('li').removeClass('ajaxing').addClass('error').find('p.error-msg').html(r['msg']);
	} 
	else {
		// ajax check field
		if(field.data('validate-ajax') !== undefined) {
			if(field.data('validate-ajax-value') !== field.val()) {
				field.data('validate-ajax-value', field.val()).parents('li').removeClass('error').addClass('ajaxing').find('p.error-msg').html(language_error('_ajax'));
				clearTimeout(field.data('validate-ajax-timeout'));
				field.data('validate-ajax-timeout', setTimeout(function(f) {
					var post_data = { validate: 1 };
					post_data[f.attr('id')] = f.val();
					$.ajax({
						type: 'POST'
					,	url: f.parents('form').attr('action')
					,	data: post_data
					,	dataType: 'json'
					,	success: function(json) {
							if(json['success']) {
								f.parents('li').removeClass('ajaxing error').find('p.error-msg').html(json['error'][field.attr('id')]);
							}
							else {
								f.parents('li').removeClass('ajaxing').addClass('error').find('p.error-msg').html(json['error'][field.attr('id')]);
							}
						}
					});
				}, 1000, field));
				return r;
			}			
		}
		else {
			field.parents('li').removeClass('error').find('p.error-msg').html('');
		}
	}
	
	return r;
}

function language_error(name, value) {
	var str = "";

	if(typeof lat['form_language'][name] !== 'undefined') {
		str = lat['form_language'][name];
	}
	
	if(typeof value != 'undefined' && typeof lat['form_language'][name + '-' + value] !== 'undefined') {
		str = lat['form_language'][name + '-' + value];
	}
	else if(value != 'undefined') {
		str = str.replace('%s', value);
	}
	
	return str;
}