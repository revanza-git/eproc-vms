function ajaxJsonFeedBack(toLoad, varData, callback){
	$.ajax({
		type : 'POST',
		url : toLoad,
		data : varData,
		dataType : 'json',
		success : function(data){
			// console.log(data);
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
		url	: base_url + "index.php/vms/utilities/get_sub_bidang/" + id + '/' + location + '/' + id_location,
		dataType : "json",
		success : function(data){
			toReturn = '<option value="">-- pilih --</option>';
			for(i=0;i<data.length;i++)
				toReturn += '<option value="' + data[i].value + '">' + data[i].label + '</option>';
			
			$("#" + subName).html(toReturn);
		}
	});
}

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