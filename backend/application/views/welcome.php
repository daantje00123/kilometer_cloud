<form action="<?php echo base_url("batch"); ?>" method="post">
    <input type="hidden" name="referer" value="<?php echo current_url(); ?>" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1>Gereden kilometers</h1>
            </div>
        </div>
        <div class="row" style="margin-top: 15px; margin-bottom: 15px;">
            <div class="col-xs-12">
                Met geselecteerde:
                <button type="submit" name="action" value="pay" class="btn btn-secondary action_btn">Betaald</button>
                <button type="submit" name="action" value="not_pay" class="btn btn-secondary action_btn">Niet betaald</button>
                <button type="submit" name="action" value="delete" class="btn btn-danger action_btn" id="delete_btn">Verwijderen</button>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="select_all_routes" /></th>
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
                                <td><input type="checkbox" class="route_check" name="routes[<?php echo $km['id_route']; ?>]" /></td>
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
                        <td colspan="2">&euro;<?php echo number_format($price,2,',','.'); ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <nav>
                    <ul class="pagination">
                        <?php echo $pagination; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</form>

<script>
    window.addEventListener("DOMContentLoaded", function() {
        // Check all the checkboxes in front of the routes
        $('#select_all_routes').on('click', function(){
            if($('#select_all_routes:checked').val() == "on") {
                $('.route_check').prop("checked", true);
            } else {
                $('.route_check').prop("checked", false);
            }
        });

        $('.action_btn').on('click', function(e) {
            if ($('.route_check:checked').length == 0) {
                e.preventDefault();
                alert("Selecteer ten minste 1 route!");
                return;
            }

            if ($(this).attr('id') == 'delete_btn') {
                if (!confirm("Weet u zeker dat u de routes wilt verwijderen?")) {
                    e.preventDefault();
                    $('.route_check').prop('checked', false);
                }
            }
        });
    });
</script>