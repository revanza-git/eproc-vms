<script type="text/javascript">

$(function(){
    $.ajax({
        url : '<?php echo site_url('pemaketan/get_step/'.$id)?>',
        method: 'GET',
        dataType : 'json',
        success: function(xhr){
            xhr.onSuccess = function(data){
                alert('Sukses');
            }
            xhr.successMessage = 'Berhasil !!';
            $('#formStep .form').form(xhr);
        }
    });
});


</script>
