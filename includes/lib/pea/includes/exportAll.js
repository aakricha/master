_Bbc(function($){
	var a = $(".roll-export");
	var b = $(".export_all", a).is(":checked");
	$(".fa-lg", a).on("click", function(e){
		e.preventDefault();
		var b = $(".export_all", $(this).closest(".roll-export"));
		var c = $(this).attr("rel");
		var d = "";
		if (b.is(":checked")) {
			var p = $(b).data("page");
			var n = $(b).data("name");
			var r = new RegExp("([\?&]"+p+"=[0-9]+)", "g");
			d = document.location.href;
			e = d;
			d = d.replace(r, "");
			d += d.match(/\?/) ? "&" : "?";
			d += n + "_export_all=1";
			d += "&" + n + "_export_type="+$(this).data("type");
			d += "&" + p + "=";
			f = '<div class="modal fade" tabindex="-1" role="dialog" id="export_'+n+'">\
  <div class="modal-dialog" role="document">\
    <div class="modal-content">\
      <div class="modal-header">\
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
        <h4 class="modal-title">Extracting Data..</h4>\
      </div>\
      <div class="modal-body"></div>\
      <div class="modal-footer">\
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>\
      </div>\
    </div>\
  </div>\
</div>';
			$(document.body).append(f);
			var g = $("#export_"+n);
			var h = $(".modal-body", g);
			g.on("show.bs.modal", function(e){
				window[$(this).prop("id")] = true;
			}).on("hide.bs.modal", function(e){
				window[$(this).prop("id")] = false;
				$(this).remove();
			});
			g.modal("show");
			peaExtract(d, 1, g, h, e);
		}else{
			document.location.href = c;
		}
	});
	var peaExtract = function(a, b, c, d, e) {
		$.ajax(a+b,{
			global: false,
			success: function(f) {
				if (window[$(c).prop("id")]) {
					if (typeof f.url == 'undefined') {
						b++;
						d.html(f);
						peaExtract(a, b, c, d, e);
					}else{
						c.modal("hide");
						if (f.msg) {
							if (confirm(f.msg)) {
								document.location.href=f.url
							}
						}else{
							document.location.href=f.url;
						}
					}
				}
			}
		});
	};
});