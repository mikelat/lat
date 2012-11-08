var js = { loaded: 0, count: 0, code: [], data_buffer: null };

$.ajaxSetup({cache: false});

$().ready(function(){
	init();
	
	// delay popstate so it doesn't fire immediately
	window.setTimeout(function() {
		$(window).bind('popstate', function(){
			get_page(location.pathname);
		});
	}, 200);
});

/**
 * Initalizes loaded page
 * 
 * @param context
 * @returns {Boolean}
 */
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

	return true;
}

/**
 * Loads a new page by ajax
 * 
 * @param url
 */
function get_page(url) {
	if (typeof window.history.pushState == 'function') {
		$("#content").css({ opacity: 0.5 });
		$.ajax({
			type: 'POST',
			url: url,
			cache: false,
			data: { json: 1 },
			success: load_page,
			error: function(jqXHR, textStatus, errorThrown) {
				$('html').html(jqXHR.responseText);
				console.log(jqXHR);
			},
			dataType: 'json'
		});
	}
	else {
		window.location = url;
	}
}

/**
 * Load page contents from ajax response onto the page
 * 
 * @param data
 */
function load_page(data) {

	// refresh variable array
	lat = data['jsv'];

	if(data.js_files) {
		js['count'] = data['jsf'].length;
		js['loaded'] = 0;

		// loads the new javascript files
		$.each(data['jsf'], function(i, v) {
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
	}

	// if a new url was provided (possible redirect?) then use that instead
	if(data['url']) {
		history.replaceState({}, document.title, data['url']);
	}

	// load content onto the page
	$('html').attr('class', data['classes']);
	$('#content *').off();
	$('#content').html(data['content']); //.data('hidden', 0).fadeIn('fast');
	$("#content").css({ opacity: 1 });
	// finish up and initalize the page
	init('#content');
}