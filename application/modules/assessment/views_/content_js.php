<script>
$(function(){

	$('.addFilter').on('click',function(e){
		e.preventDefault();
		var prnt = $(this).parent();
		var kloning = prnt.clone(true,true);
		var filterForm = $(prnt).parent();
		
		$('.filterForm').append(kloning);
		$(this).remove();
	});
});

/*$( document ).ready(function() {
    // console.log( "ready!" );
    var pw 	= "11nov2011";
	do{
    	input = prompt("Give input");
	}while(input == null || input == "" || input != pw);

});*/

/*var bla = $('#txt_name').val();

//Set
$('#txt_name').val(bla);*/
</script>