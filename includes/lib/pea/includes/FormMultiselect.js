_Bbc(function($) {
	function formMultiSelect(a, b, c, d, e) {
		// a:inputname, b:values, c:options, d:parentvalue, e:contextobject
		var f = document.createElement('select');
		var g="",h;
		$(f).attr("name", a).attr("class", "form-control");
		for (var i = 0; i < c.length; i++) {
			if (c[i]["par_id"]==d) {
				h = ($.inArray(c[i]["id"],b)!=-1) ? " selected" : "";
				g += '<option value="'+c[i]["id"]+'"'+h+'>'+c[i]["title"]+'</option>';
			};
		};
		if (g != "") {
			$(f).html(g);
			$(e).append(f);
			$(e).append(" ");
			$(f).on("change", function(){
				$(this).nextAll('select').remove();
				formMultiSelect(a, b, c, $(this).val(), e);
			}).trigger("change");
		};
	}
	$('.FormMultiselect_single_select').each(function(a){
		var a = $('select', $(this)).attr('name');
		var b = [];
		var c = eval('('+$('.referenceArray', $(this)).html()+')'); // object current options
		$('select :selected', $(this)).each(function(i, j){b[i]=$(j).val();});
		$(this).html("");
		formMultiSelect(a, b, c, "0", $(this));
	});
});
