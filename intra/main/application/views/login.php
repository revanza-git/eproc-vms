<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Aplikasi Sistem Kelogistikan</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/normalize.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('assets/font/font-awesome/css/font-awesome.min.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/base.css'); ?>" type="text/css" media="screen" />
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="loginPanel block">
                <div class="panel">
                   
                    <div class="panel-content">
                        <div class="contentForm">
                            <div class="form" id="login-form"> </div>
                        </div>
                        <div class="form-group btn-group"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    var base_url = "<?php echo base_url()?>";
    var site_url = "<?php echo site_url()?>";
    var csrf_token_name = "<?php echo $this->security->get_csrf_token_name(); ?>";
    var csrf_cookie_name = "<?php echo $this->config->item('csrf_cookie_name'); ?>";
    var csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    }
</script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js');?>"></script>
<script type="text/javascript">
    if (window.jQuery) {
        $.ajaxPrefilter(function(options) {
            var method = (options.type || options.method || 'GET').toString().toUpperCase();
            if (method !== 'POST') return;
            var token = getCookie(csrf_cookie_name) || csrf_hash;
            if (!token) return;

            if (typeof FormData !== 'undefined' && options.data instanceof FormData) {
                if (typeof options.data.has === 'function') {
                    if (!options.data.has(csrf_token_name)) {
                        options.data.append(csrf_token_name, token);
                    }
                } else {
                    options.data.append(csrf_token_name, token);
                }
                return;
            }

            if (typeof options.data === 'string') {
                if (options.data.indexOf(csrf_token_name + '=') !== -1) return;
                options.data = options.data + (options.data.length ? '&' : '') +
                    encodeURIComponent(csrf_token_name) + '=' + encodeURIComponent(token);
                return;
            }

            if (options.data == null) {
                options.data = {};
            }

            if (typeof options.data === 'object' && !options.data[csrf_token_name]) {
                options.data[csrf_token_name] = token;
            }
        });

        $(document).ajaxComplete(function(event, xhr) {
            try {
                var nextToken = xhr.getResponseHeader('X-CSRF-Token');
                if (nextToken) csrf_hash = nextToken;
            } catch (e) {}
        });
    }
</script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.imask.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/form.js');?>"></script>
<script type="text/javascript">
    $(document).ready(function(e) {
        var data = $('.loginPanel .form').form({
            url: '<?php echo site_url('main/check'); ?>',
            form: [{
                field: 'username',
                type: 'text',
                icon: 'fa fa-user',
                placeholder: 'Username'
            }, {
                field: 'password',
                type: 'password',
                icon: 'fa fa-key',
                placeholder: 'Password'
            }],
            button: [{
                type: 'submit',
                label: 'LOGIN',
                field: 'submit',
                class: 'buttonBlock'
            }],
            onError: function(xhr) {
                this.errorMessage = xhr.message;
            },
            onSuccess: function(xhr) {
                this.successMessage = xhr.message;
                window.location = xhr.url;
            }
        });
    })
</script>

</html>
