$(function(){
	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() == $(document).height()) {
			loadActivity();
		}
	});
	loadActivity();
});

function loadActivity() {
	$.ajax({
		type: 'GET',
		url: $("#activity").data("backend"),
		data: {offset: parseInt($("#activity").data("offset"))},
		success: function(res) {
			$("#activity").append(res);
			$("#activity").data("offset", (parseInt($("#activity").data("offset")) + 20));
		},
		error: function() {
			alert('Cannot connect to the server at this time :\'(', "danger");
		},
		cache: true
	});
}