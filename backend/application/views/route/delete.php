<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h1>Route verwijderen</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <form action="<?php echo current_url(); ?>" method="post">
                <p class="alert alert-danger">Weet u zeker dat u de route wilt verwijderen?</p>
                <button type="submit" name="action" value="Ja" class="btn btn-danger">Ja</button>
                <button type="submit" name="action" value="Nee" class="btn btn-secondary">Nee</button>
            </form>
        </div>
    </div>
</div>