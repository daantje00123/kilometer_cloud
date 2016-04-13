<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow" />
    <title><?php echo (isset($title) ? $title : 'Kilometer Meter Backend'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url('../bower_components/tether/dist/css/tether.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('../bower_components/bootstrap/dist/css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('../bower_components/font-awesome/css/font-awesome.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>" />
</head>
<body>
<?php if (db_user_loggedin()): ?>
    <nav class="navbar navbar-light bg-faded" id="main-menu">
        <div class="nav navbar-nav">
            <a class="nav-item nav-link<?php echo (empty(uri_string()) ? ' active' : ''); ?>" href="<?php echo base_url(); ?>"><span class="fa fa-home"></span> Home</a>
        </div>
        <div class="nav navbar-nav pull-right">
            <div class="dropdown">
                <a class="nav-item nav-link dropdown-toggle" href="#" data-toggle="dropdown"><span class="fa fa-user"></span> <?php echo db_get_user()->get_fullname(); ?></a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo base_url('settings'); ?>"><span class="fa fa-gears"></span> Instellingen</a>
                    <a class="dropdown-item" href="<?php echo base_url('authentication/logout'); ?>"><span class="fa fa-sign-out"></span> Uitloggen</a>
                </div>
            </div>
        </div>
    </nav>
<?php else: ?>
    <nav class="navbar navbar-light bg-faded" id="main-menu">
        <div class="nav navbar-nav">
            <a class="nav-item nav-link">&nbsp;</a>
        </div>
        <div class="nav navbar-nav pull-right">
            <a href="<?php echo base_url('authentication'); ?>" class="nav-link nav-item"><span class="fa fa-user"></span> Inloggen</a>
        </div>
    </nav>
<?php endif; ?>