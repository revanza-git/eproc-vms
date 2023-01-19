<script type="text/javascript">
	$(function(){
		_division = $('#division');
		if($('#id_role').val()==9){
				_division.show();
				$('#divisionDD',_division).attr('disabled',false);
			}else{
				_division.hide();
				$('#divisionDD',_division).attr('disabled',true);
			}
		$('#id_role').on('change',function(){
			
			if($(this).val()==9){
				_division.show();
				$('#divisionDD',_division).attr('disabled',false);
			}else{
				_division.hide();
				$('#divisionDD',_division).attr('disabled',true);
			}
		});
	})
</script>