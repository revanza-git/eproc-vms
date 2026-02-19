<script type="text/javascript">

$(function(){
    $.ajax({
        url : '<?php echo site_url('pemaketan/get_analisa/'.$id)?>',
        method: 'GET',
        dataType : 'json',
        success: function(xhr){
            xhr.onSuccess = function(data){
                alert('Sukses');
            }
            xhr.successMessage = 'Berhasil !!';

            $('#formStepAnalisa .form').form(xhr);
        }
    });
});


</script>
