<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h1>Route aanpassen</h1>
        </div>
    </div>

    <?php echo validation_errors('<div class="row"><div class="col-xs-12"><div class="alert alert-danger">', '</div></div></div>'); ?>

    <div class="row">
        <div class="col-xs-12">
            <form action="<?php echo current_url(); ?>" method="post">
                <div class="row form-group">
                    <label class="form-control-label col-xs-12 col-md-2" for="omschrijving">Omschrijving</label>
                    <div class="col-xs-12 col-md-5">
                        <input type="text" name="omschrijving" id="omschrijving" placeholder="Omschrijving" class="form-control" value="<?php echo set_value('omschrijving', $kilometer['omschrijving']); ?>" />
                    </div>
                </div>
                <div class="row form-group">
                    <label class="form-control-label col-xs-12 col-md-2">Betaald</label>
                    <div class="col-xs-12 col-md-5">
                        <div class="radio">
                            <label>
                                <input type="radio" name="betaald" value="0"<?php if($kilometer['betaald'] == 0) {echo ' checked';} ?> />
                                Nee
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="betaald" value="1"<?php if($kilometer['betaald'] == 1) {echo ' checked';} ?> />
                                Ja
                            </label>
                        </div>
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