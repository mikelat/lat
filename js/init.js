var js = { loaded: 0, count: 0, code: [], html: null };

$().ready(function(){
	init();
	$("header form input:visible:first").focus();

	// delay popstate so it doesn't fire immediately
	window.setTimeout(function() {
		$(window).bind('popstate', function(){
			get_page(location.pathname);
		});
	}, 200);
});

function ajax_loading(act) {
	// Toggle
	if(typeof act == 'undefined') {
		if($('#loading').is(':visible')) {
			act = 'hide';
		} else {
			act = 'show';
		}
	}
	
	// Close
	if(act == 'hide') {
		$('#loading').fadeOut('fast', function(){
			clearInterval($('#loading').data('interval'));
			$('#loading').remove();
		});
	}
	// Open
	else if (act == 'show') {
		$('#content').after('<div id="loading"></div>');
		var obj_load = $('#loading');
		var loading_r = 0;
		obj_load.data('interval', setInterval(function(){
			loading_r = (loading_r + 1) % 360;
			obj_load.css({ WebkitTransform: 'rotate(' + loading_r + 'deg)', '-moz-transform': 'rotate(' + loading_r + 'deg)'});
		}, 5)).fadeIn('fast');

	}
}

function init(context) {
	if(typeof context == 'undefined') context = null;

	if(js['count'] != js['loaded']) {
		return false;
	}

	$('a[rel!=external][href^="'+lat.url+'"]', context).on('click', function(){
		if (typeof window.history.pushState == 'function') {
			get_page($(this).attr('href'));
			history.pushState({}, document.title, $(this).attr('href'));
			return false;
		}
		return true;
	});

	$('.close').on('click', function() {
		$(this).off('click');
		$(this).parent().slideUp('fast', function() {
			$(this).remove();
		});
	});

	if(js['code']) {
		$.each(js['code'], function(i, j) {
			eval(j);
		});

		js['code'] = [];
	}

	return true;
}

function get_page(url) {

	$('#content *').off();
	$('#content').fadeOut('fast', function() { $('#content').data('hidden', 1); load_page(); }).data('hidden', 0);;
	ajax_loading('show');

	$.ajax({
		type: 'POST',
		url: url,
		data: { json: 1 },
		success: load_page,
		dataType: 'json'
	});
}

function load_page(data) {
	
	if(js['html'] !== null) {
		console.log('grabbing data');
		data = js['html'];
		js['html'] = null;
	}

	if(typeof data == 'undefined') {
		return;
	}
	
	if($('#content').data('hidden') != 1) {
		js['html'] = data; 
		return;
	}
	
	lat['js_vars'] = data['js_vars'];

	if(data.js_files) {
		js['count'] = data['js_files'].length;
		js['loaded'] = 0;
		js['code'] = data['js_code'];

		$.each(data['js_files'], function(i, v) {
			$.ajax({
				url: v,
				dataType: "script",
				success: function() {
					js['loaded'] = js['loaded'] + 1;
					init('#content');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				}
			});
		});
	} else {
		js['count'] = 0;
		js['loaded'] = 0;
		js['code'] = data['js_code'];
	}

	if(data['url']) {
		history.pushState({}, document.title, data['url']);
	}

	$('#content').html(data['content']).data('hidden', 0).fadeIn('fast');
	
	ajax_loading('hide');
	init('#content');
}