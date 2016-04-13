<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h1>Instelling aanpassen</h1>
        </div>
    </div>

    <?php echo validation_errors('<div class="row"><div class="col-xs-12"><div class="alert alert-danger">', '</div></div></div>'); ?>

    <div class="row">
        <div class="col-xs-12">
            <form action="<?php echo current_url(); ?>" method="post">
                <div class="row form-group">
                    <label class="form-control-label col-xs-12 col-md-2" for="human">Naam</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="text" id="human" class="form-control" placeholder="Naam" value="<?php echo $setting['se_human']; ?>" disabled />
                    </div>
                </div>
                <div class="row form-group">
                    <label class="form-control-label col-xs-12 col-md-2" for="value">Waarde</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="text" name="value" id="value" class="form-control" placeholder="Waarde" value="<?php echo set_value('value', $setting['se_value']); ?>" />
                    </div>
                </div>
                <div class="row form-group">
                    <label class="form-control-label col-xs-12 col-md-2" for="desc">Omschrijving</label>
                    <div class="col-xs-12 col-md-5">
                        <textarea name="desc" id="desc" class="form-control" placeholder="Omschrijving"><?php echo set_value('desc', $setting['se_desc']); ?></textarea>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-xs-12 offset-md-2 col-md-5">
                        <button type="submit" class="btn btn-primary">Opslaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>