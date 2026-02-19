$(function(){
	$("body").append('<div id="jc-form-modal-platform"></div>');
	$("body").append('<div id="jc-form-save-loading"></div>');

	$(document).keyup(function(e){
		if(e.keyCode == 27) jcFormCloseForm();
	});
});

(function($){
	$.fn.jcForm = function(data){
		
		var $data = data; 
		$(this).append('<iframe name="jquery-ajax-form-iframe-' + $(this).attr('id') + '" id="jquery-ajax-form-iframe-' + $(this).attr('id') + '" style="display : none"></iframe>');
		$(this).attr('target', 'jquery-ajax-form-iframe-' + $(this).attr('id'));
		$(this).removeAttr("method");
		$(this).attr("method", "POST");
		
		if(data.use_dialog == undefined) data.use_dialog = true;
		
		var useDialog = data.use_dialog;
		
		$(this).find('input.jc-form-button-close').click(function(){ jcFormCloseForm() });
		
		$(this).find('iframe#jquery-ajax-form-iframe-' + $(this).attr('id')).load(function(){
			var respond = $.parseJSON($(this).contents().text());
			$data.success(respond);
			
			$("#jc-form-save-loading").fadeOut();
		});
		
		$(this).submit(function(){ 
			$("#jc-form-save-loading").show();

			if(useDialog){
				var con = confirm("Simpan data ?");
				if(!con){ $("#jc-form-save-loading").fadeOut(); return false; }
			}
			
			var check = data.check;
			for(i=0;i<check.length;i++){
				if(check[i].type == "empty"){
					if($(this).find('#' + check[i].name).val() == ""){
						$(this).find('#' + check[i].name).focus();
						alert(check[i].message);
						$("#jc-form-save-loading").fadeOut();
						return false;
					}
				}
			}
			return true;
		});
	};
})(jQuery);

/* utilities */

function jcFormOpenForm(varUrl){
	$.ajax({
		url : varUrl,
		data : null,
		type : 'POST',
		success : function(data){
			$("#jc-form-modal-platform").html(data);
		},
		error : function() {
			$(".loading-content").fadeOut();
		}
	});
}

function jcFormCloseForm(){
	var con = confirm('Tutup form ?');
	if(!con) return false;
	
	$(".loading-content").fadeOut();
}