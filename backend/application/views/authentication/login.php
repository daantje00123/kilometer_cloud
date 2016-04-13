<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1>Inloggen</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <form action="<?php echo base_url('authentication'); ?>" method="post" id="inlog-form">
                <div class="row form-group<?php echo (form_error('username') ? ' has-danger' : ''); ?>">
                    <label class="col-xs-12 col-md-2 form-control-label" for="username">Gebruikersnaam</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="text" id="username" name="username" value="<?php echo set_value('username'); ?>" class="form-control<?php echo (form_error('username') ? ' form-control-danger' : ''); ?>" />
                        <?php echo form_error('username', '<div class="text-help">', '</div>'); ?>
                    </div>
                </div>
                <div class="row form-group<?php echo (form_error('password') ? ' has-danger' : ''); ?>">
                    <label class="col-xs-12 col-md-2 form-control-label" for="password">Wachtwoord</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="password" id="password" name="password" class="form-control<?php echo (form_error('password') ? ' form-control-danger' : ''); ?>" />
                        <?php echo form_error('password', '<div class="text-help">', '</div>'); ?>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-12 offset-md-2 col-md-5">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="remember" /> Onthoud mij
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-12 offset-md-2 col-md-5">
                        <button type="submit" class="btn btn-outline-primary">Inloggen</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        var user = localStorage.username;
        var pass = localStorage.password;

        $('#username').val(user);
        $('#password').val(pass);

        if (!user || !pass) {

        } else {
            $('#inlog-form').submit();
        }

        $('#inlog-form').on('submit', function() {
            var remember = $('#remember:checked').val()?true:false;
            var username = $('#username').val();
            var password = $('#password').val();

            if (!username || !password) {
                return;
            }

            if (remember === true) {
                localStorage.username = username;
                localStorage.password = password;
            }
        });
    });
</script>