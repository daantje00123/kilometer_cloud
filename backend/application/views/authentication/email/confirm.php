Beste heer/mevrouw <?php echo $user->get_fullname(); ?>,

Klik op de onderstaande link om een wachtwoord aan te maken en uw account te activeren:

{unwrap}<?php echo base_url('authentication/confirm/'.$user->get_confirm_token()); ?>{/unwrap}

Met vriendelijke groet,

Daan van Berkel
