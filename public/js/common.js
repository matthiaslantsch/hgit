$.fn.serializeObject = function() {
   var o = {};
   var a = this.serializeArray();
   $.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   return o;
};

$(function(){
	$('#filterTf').keyup(function() {
		if($(this).val() != "") {
			$.each($(".filterable"), function() {
				if ($(this).find(".filter_crit").text().toLowerCase().indexOf($("#filterTf").val()) >= 0) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		} else {
			$(".filterable").show();
		}	
	});

	$(".copyButton").click(function() {
		$("#copyFrom").focus();
		$("#copyFrom").select();
	});

	$.each($(".simplemde"), function(ele) {
		var simplemde = new SimpleMDE({"element": ele, "spellChecker": false});
	});
});

function alert(msg, level) {
	$(".alertArea").html('<div class="alert alert-'+level+'">'+msg+'</div>');
	$(".alert").fadeOut(4000);
}