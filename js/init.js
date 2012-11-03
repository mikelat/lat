var loading_r = 0;
var js = { loaded: 0, count: 0, code: [] };
var obj = { };

$().ready(function(){
	init();
	$("header form input:visible:first").focus();
	obj.loading = $("#loading");

	// delay popstate so it doesn't fire immediately
	window.setTimeout(function() {
		$(window).bind('popstate', function(){
			get_page(location.pathname);
		});
	}, 200);

	$("#search, #login").submit(submit_header);

});

function submit_header() {

	$('header').data('state', 1);

	$('header form').each(function() {
		var form = $(this);
		$('input:eq(0)', form).animate({ width: 'toggle' }, 100, function() {
			$('div', form).animate({ width: 'toggle' }, 100);
			$('input:eq(1)', form).animate({ width: 'toggle' }, 100, function() {
				form.toggleClass('close');
			});
		});
	});

	return false;

}

function ajax_loading(act) {
	// Toggle
	if(typeof act == undefined) {
		if(obj.loading.is(':visible')) {
			act = 'hide';
		} else {
			act = 'show';
		}
	}

	// Close
	if(act == 'hide') {
		obj.loading.fadeOut('fast', function(){
			clearInterval($("#loading").data('interval'));
		});
	}
	// Open
	else if (act == 'show') {
		obj.loading.data('interval', setInterval(function(){
			loading_r = (loading_r + 1) % 360;
			obj.loading.css({ WebkitTransform: 'rotate(' + loading_r + 'deg)', '-moz-transform': 'rotate(' + loading_r + 'deg)'});
		}, 5));
		obj.loading.fadeIn('fast');
	}
}

function init(context) {
	if(typeof context == undefined) context = null;

	if(js['count'] != js['loaded']) {
		return false;
	}

	if($('#content').is(':hidden')) {
		ajax_loading('hide');
		$('#content').fadeIn('fast');
	}

	$('a[rel!=internal][href^="'+base_url+'"]', context).on('click', function(){
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
	$('#content').fadeOut('fast').data('hidden', 1);
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
	if($('#content').data('hidden') != 1) {
		$('#content').data('hidden', 1).fadeOut('fast', function(){
			load_page(data);
		});
		return;
	}

	lat = data['js_vars'];

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

	$('#content').html(data.output).data('hidden', 0);
	init('#content');
}

function toggle_fade(arg) {
	if(arg.length != 2) {
		return false;
	}

	if($(arg[1]).is(':visible'))
	{
		arg.reverse();
	}

	$(arg[0]).fadeOut('fast', function(){
		$(arg[1]).fadeIn('fast');
	});

	return false;
}

function popup_close() {
	$('#popup, #overlay').fadeOut('fast', function() { $(this).remove(); });
}

function popup(arg) {

	var b, c = 0;
	if(!arg.button) {
		arg.button = { 'Close': function() { popup_close() } }
	}

	var popup = '<div id="popup"><h2>'+ arg.title +'</h2><section>';

	if(arg.icon) {
		popup += '<span class="icon '+ arg.icon +'"></span>';
	}

	popup += '<p>'+ arg.content +'</p><footer>';

	for (b in arg.button)
	{
		popup += '<button>'+ b +'</button>';
	}

	popup += '</footer></section></div><div id="overlay"></div>';

	$('#content').append(popup);

	for (b in arg.button)
	{
		$('#popup:eq('+ c +')').on('click', arg.button[b])
	}

	$('#popup').css('top', (($(window).height() - $('#popup').outerHeight()) / 2) + $(window).scrollTop() + "px");
	$('#popup, #overlay').fadeIn('fast');

	//i am popup</h2><section><footer><button>close</button></footer>
}