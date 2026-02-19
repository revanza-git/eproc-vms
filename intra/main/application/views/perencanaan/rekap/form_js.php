<?php 
    if ($action == 'Tambah') {
        $url = 'insert';
    } else {
        $url = 'edit';
    }
?>
<script>
     $.ajax({
        url : '<?php echo site_url('perencanaan/rekap/approve/'.$id)?>',
        method: 'POST',
        async : false,
        dataType : 'json',
        success: function(xhr){
            // console.log(xhr)
            xhr.onSuccess = function(data){
                alert('Sukses');
            }
            xhr.successMessage = 'Berhasil !!';
            xhr.formWrap = false;

            $('#tableGenerator').form(xhr);

            $('div#mceu_13.mce-tinymce.mce-container.mce-panel').attr('style','margin-left : 280px');

            tinymce.init({
                selector: '.tinymce',
                branding: false
            });

            $('.btn-cancel').on('click',function() {
               window.location.href = '<?php echo site_url('perencanaan/rekap/year/'.$id) ?>'; 
            });
        }
    });
</script>