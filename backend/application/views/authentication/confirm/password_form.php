<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1>Wachtwoord aanmaken</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <form action="<?php echo base_url('authentication/confirm/'.$token); ?>" method="post">
                <div class="row form-group">
                    <label class="col-xs-12 col-md-2 form-control-label" for="username">Gebruikersnaam</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="text" id="username" value="<?php echo $user->get_username(); ?>" class="form-control" disabled />
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-xs-12 col-md-2 form-control-label" for="name">Naam</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="text" id="name" value="<?php echo $user->get_fullname(); ?>" class="form-control" disabled />
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-xs-12 col-md-2 form-control-label" for="email">E-mailadres</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="text" id="email" value="<?php echo $user->get_email(); ?>" class="form-control" disabled />
                    </div>
                </div>
                <div class="row form-group<?php echo (form_error('password[1]') ? ' has-danger' : ''); ?>">
                    <label class="col-xs-12 col-md-2 form-control-label" for="password[1]">Wachtwoord</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="password" id="password[1]" name="password[1]" class="form-control<?php echo (form_error('password[1]') ? ' form-control-danger' : ''); ?>" />
                        <?php echo form_error('password[2]', '<div class="text-help">', '</div>'); ?>
                    </div>
                </div>
                <div class="row form-group<?php echo (form_error('password[2]') ? ' has-danger' : ''); ?>">
                    <label class="col-xs-12 col-md-2 form-control-label" for="password[2]">Wachtwoord controle</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="password" id="password[2]" name="password[2]" class="form-control<?php echo (form_error('password[2]') ? ' form-control-danger' : ''); ?>" />
                        <?php echo form_error('password[2]', '<div class="text-help">', '</div>'); ?>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-12 offset-md-2 col-md-5">
                        <button type="submit" class="btn btn-outline-primary">Account activeren</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>