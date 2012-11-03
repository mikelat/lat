$().ready(function(){
	$("#agree button").on('click', function() {
		$("#agree").slideUp('fast');
		$("#register").slideDown('fast', function() {
			$("#register input:visible:first").focus();
		});
	});
});