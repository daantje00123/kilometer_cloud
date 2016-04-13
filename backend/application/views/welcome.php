<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h1>Gereden kilometers</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            Geselecteerde: 
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <form action="" method="post">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="select_all_routes" /></th>
                        <th>#</th>
                        <th>Omschrijving</th>
                        <th>Start datum</th>
                        <th>Stop datum</th>
                        <th>Aantal kilometers</th>
                        <th>Betaald</th>
                        <th>Kosten</th>
                        <th>Acties</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($kilometers) && !empty($kilometers)): ?>
                        <?php foreach($kilometers as $km): ?>
                            <tr>
                                <td><input type="checkbox" name="routes[]" /></td>
                                <td><?php echo $km['id_route']; ?></td>
                                <td><?php echo $km['omschrijving']; ?></td>
                                <td><?php echo $km['datum']['start']['day'].'-'.$km['datum']['start']['month'].'-'.$km['datum']['start']['year'].' '.$km['tijd']['start']['hours'].':'.$km['tijd']['start']['minutes']; ?></td>
                                <td><?php echo $km['datum']['eind']['day'].'-'.$km['datum']['eind']['month'].'-'.$km['datum']['eind']['year'].' '.$km['tijd']['eind']['hours'].':'.$km['tijd']['eind']['minutes']; ?></td>
                                <td><?php echo number_format($km['kms'], 2,',','.'); ?> km</td>
                                <td><?php echo ($km['betaald'] == 1 ? 'Ja' : 'Nee'); ?></td>
                                <td class="<?php echo ($km['betaald'] == 1 ? 'paid' : 'not-paid'); ?>">&euro;<?php echo number_format($km['kms'] * db_setting('prijs_per_kilometer'),2,',','.'); ?></td>
                                <td>
                                    <a href="<?php echo base_url('route/view/'.$km['id_route']); ?>"><span class="fa fa-eye"></span></a>
                                    <a href="<?php echo base_url('route/edit/'.$km['id_route']); ?>"><span class="fa fa-pencil"></span></a>
                                    <a href="<?php echo base_url('route/delete/'.$km['id_route']); ?>"><span class="fa fa-trash"></span></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Er zijn nog geen kilometers gemaakt. <span class="fa fa-car"></span></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="4">Totaal aantal kilometers:</th>
                        <td><?php echo number_format($total, 2,',','.'); ?> km</td>
                        <th>Totaal kosten:</th>
                        <td colspan="2">&euro;<?php echo number_format($total*db_setting('prijs_per_kilometer'),2,',','.'); ?></td>
                    </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    </div>
</div>

<script>
    window.addEventListener("DOMContentLoaded", function() {
        // Check all the checkboxes in front of the routes
        $('#select_all_routes').on('click', function(){
            if($('#select_all_routes:checked').val() == "on") {
                $('input[name="routes[]"]').prop("checked", true);
            } else {
                $('input[name="routes[]"]').prop("checked", false);
            }
        });
    });
</script>