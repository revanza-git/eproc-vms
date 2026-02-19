<script type="text/javascript">
	$(function() {
		var edit = $('.buttonView').modal({
            header: 'Info Data',
            render : function(el, data){
                _self = edit;

                data.onSuccess = function(){
                    // $(edit).data('modal').close();
                    // folder.data('plugin_folder').fetchData();
                    location.reload()
                };
                data.isReset = false;
                $(el).form(data).data('form');
            }
        });
	})
</script>