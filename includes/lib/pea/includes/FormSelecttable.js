_Bbc(function($){if($("select.allow_new").length){var r=$("select.allow_new");var f;for(var i=0;i<r.length;i++){$(r[i]).after('<div class="modal fade" tabindex="-1" role="dialog"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title">'+$(r[i]).data('newlabel')+'</h4></div><div class="modal-body"><div class="form-group"><input type="text" class="form-control allow_new_input" placeholder="insert new" /><div class="help-block">Press ENTER to submit new data, this data will only be saved if you select it as its option</div></div></div></div></div></div>')};$('.allow_new_input').on('keydown keypress',function(e){if(e.which==13){e.preventDefault();var a=$(this).val().trim();var b=$(this).closest('.modal');$(this).val('');if(a!=""){var c=$(b).prev().get(0);var d=$('.modal-title',b).text();var f=c.options;var g="";for(var i=0;i<f.length;i++){if(f[i].value!=""&&f[i].text!=""&&f[i].text!=d){if(!/^new\|/.test(f[i].value)){g+='<option value="'+f[i].value+'">'+f[i].text+'</option>'}}};g+='<option value="new|'+a+'" selected>'+a+'</option>';g+='<option value="'+d+'">'+d+'</option>';$(c).html(g)};$(b).modal("hide")}});$("select.allow_new").focus(function(){f=this.value}).change(function(){if($(this).data("newlabel")==$(this).val()){$(this).val(f);$($(this).next()).modal("show").on('shown.bs.modal',function(){$('.allow_new_input',this).focus()}).on('hidden.bs.modal',function(){$(this).prev().focus()})}else{f=this.value}})}});