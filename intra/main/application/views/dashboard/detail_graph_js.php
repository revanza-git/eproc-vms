<script type="text/javascript">
    $(function() {
        dataPost = {
            // order: 'id',
            // sort: 'desc'
        };

        var folder = $('#folderGenerator').folder({
            url: '<?php echo site_url('dashboard/getDetailGraph/' . $method . '/' . $year); ?>',
            data: dataPost,
            dataRightClick: function(key, btn, value) {
                _id = value[key][3].value;

                btn = [{
                    icon: 'edit',
                    label: 'Ubah Tanggal',
                    class: 'buttonEdit',
                    href: site_url + "dashboard/form_edit_date/" + _id
                }];
                return btn;
            },
            callbackFunctionRightClick: function() {
                var edit = $('.buttonEdit').modal({
                    header: "Edit Data",
                    render: function(el, data) {
                        _self = edit;

                        data.onSuccess = function() {
                            $(edit).data('modal').close();
                            folder.data('plugin_folder').fetchData();

                        };
                        data.isReset = false;

                        $(el).form(data).data('form');

                    }
                });
            },

            renderContent: function(el, value, key) {
                html = '';
                switch (value[2].value) {
                    case 0:
                        status = 'FPPBJ';
                        break;
                    case 1:
                        status = 'FP3';
                        break;
                    default:
                        status = 'FKPBJ';
                        break;
                }

                html += '<div class="caption"><p>' + value[0].value + '</p><p>' + value[1].value + '</p><p>' + value[2].value + '</p></div>';
                // console.log(folder);
                return html;
            },
            additionFeature: function(el) {

            },
            finish: function() {

            }

        });
    });
</script>