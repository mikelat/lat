var js = { loaded: 0, count: 0, code: [], data_buffer: null };

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
 * Controls the ajax indicator
 * 
 * @param act
 */
function ajax_loading(act) {
	// Toggle
	if(typeof act == 'undefined') {
		if($('#loading').is(':visible')) {
			act = 'hide';
		} else {
			act = 'show';
		}
	}
	console.log(act)
	
	// Remove the indicator
	if(act == 'hide') {
		clearInterval($('#loading').data('interval'));
		$('#loading').remove();
		console.log('test')
	}
	// Show the indicator
	else if (act == 'show') {
		$('#content').after('<div id="loading"></div>');
		var obj_load = $('#loading');
		var loading_r = 0;
		// interval for spinning the indicator
		obj_load.data('interval', setInterval(function(){
			loading_r = (loading_r + 1) % 360;
			obj_load.css({ WebkitTransform: 'rotate(' + loading_r + 'deg)', '-moz-transform': 'rotate(' + loading_r + 'deg)'});
		}, 5));

	}
}

/**
 * Loads a new page by ajax
 * 
 * @param url
 */
function get_page(url) {
	if (typeof window.history.pushState == 'function') {
		$('#content *').off();
		$("#content").css({ opacity: 0.5 });
		ajax_loading('show');
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
	$('#content').html(data['content']);//.data('hidden', 0).fadeIn('fast');
	$("#content").css({ opacity: 1 });
	// finish up and initalize the page
	ajax_loading('hide');
	init('#content');
}