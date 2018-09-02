$(function(){
	$(".formChecker").submit(function(e) {
		var form = $(this);
		e.preventDefault();

		$.ajax({
			type: 'POST',
			data: form.serializeObject(),
			url: form.get(0).action,
			dataType: "json", //does not work in IE??
			accepts: {
				text: "application/json"
			},
			success: function(res) {
				form.find(".form-group").removeClass("has-danger");
				form.find(".form-control-feedback").text("");
				if(res.errors == false) {
					console.log(res);

					alert("<strong>Success!</strong> Changes to project '"+$("#inputName").val()+"' saved.", "success");

					if(typeof(res.redirect) !== "undefined") {
						location.href = res.redirect;
					}
				} else {
					$.each(res.errors, function(key, err) {
						var formGroup = form.find("input[name='"+key+"']").addClass("form-control-danger").parents(".form-group");
						formGroup.find(".form-control-feedback").text(err.join("\n"));
						formGroup.addClass("has-danger");
					});
				}
			},
			error: function() {
				form.find(".form-group").removeClass("has-danger");
				form.find(".form-control-feedback").text("");
				alert('Cannot connect to the server at this time :\'(', "danger");
			}
		});
	});
});
