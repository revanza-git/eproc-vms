function ajaxJsonFeedBack(toLoad, varData, callback){
	$.ajax({
		type : 'POST',
		url : toLoad,
		data : varData,
		dataType : 'json',
		success : function(data){
			callback(data);
		},
		error : function(xhr, status, error){
			console.log(xhr);
		}
	});	
}

function ajaxHtmlFeedBack(toLoad, varData, callback){
	$.ajax({
		type : 'POST',
		url : toLoad,
		data : varData,
		dataType : 'html',
		success : function(data){
			callback(data);
		},
		error : function(error){
			console.log(error);
		}
	});	
}

function fill_sub_bidang(subName, id, location, id_location){	
	$.ajax({
		url	: base_url + "index.php/utilities/get_sub_bidang/" + id + '/' + location + '/' + id_location,
		dataType : "json",
		success : function(data){
			toReturn = '<option value="">-- pilih --</option>';
			for(i=0;i<data.length;i++)
				toReturn += '<option value="' + data[i].value + '">' + data[i].label + '</option>';
			
			$("#" + subName).html(toReturn);
		}
	});
}

// Auto-bind bidang dropdown onchange events
$(document).ready(function() {
	// For forms with id_bidang dropdown and id_sub_bidang dropdown
	$('select[name="id_bidang"]').on('change', function() {
		var bidangId = $(this).val();
		var subBidangSelect = $('select[name="id_sub_bidang"]');
		
		if (subBidangSelect.length > 0) {
			if (bidangId) {
				fill_sub_bidang('id_sub_bidang', bidangId, '', '');
			} else {
				subBidangSelect.html('<option value="">-- pilih --</option>');
			}
		}
	});
	
	// For other bidang/sub-bidang combinations that might exist
	$('select[id*="bidang"]').on('change', function() {
		var bidangId = $(this).val();
		var selectId = $(this).attr('id');
		var subSelectId = selectId.replace('bidang', 'sub_bidang');
		var subBidangSelect = $('#' + subSelectId);
		
		if (subBidangSelect.length > 0) {
			if (bidangId) {
				fill_sub_bidang(subSelectId, bidangId, '', '');
			} else {
				subBidangSelect.html('<option value="">-- pilih --</option>');
			}
		}
	});
});

function fill_option(subName, address, id){	
	$.ajax({
		url	: base_url + "index.php/utilities/" + address + "/" + id,
		dataType : "json",
		success : function(data){
			toReturn = '<option value="">-- choose one --</option>';
			for(i=0;i<data.length;i++)
				toReturn += '<option value="' + data[i].value + '">' + data[i].label + '</option>';
			
			$("#" + subName).html(toReturn);
		}
	});
}