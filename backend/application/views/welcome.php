<form action="<?php echo base_url("batch"); ?>" method="post">
    <input type="hidden" name="referer" value="<?php echo current_url().'?'.http_build_query($_GET); ?>" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1>Gereden kilometers</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p>
                    Met geselecteerde:
                    <button type="submit" name="action" value="pay" class="btn btn-secondary action_btn">Betaald</button>
                    <button type="submit" name="action" value="not_pay" class="btn btn-secondary action_btn">Niet betaald</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger action_btn" id="delete_btn">Verwijderen</button>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p>
                    Aantal items:
                    <select name="per_page" id="select_per_page" data-toggle="tooltip" data-placement="top" title="Het aantal items dat per pagina wordt weergegeven">
                        <option value="5"<?php echo ($per_page == 5 ? ' selected' : ''); ?>>5</option>
                        <option value="10"<?php echo ($per_page == 10 ? ' selected' : ''); ?>>10</option>
                        <option value="15"<?php echo ($per_page == 15 ? ' selected' : ''); ?>>15</option>
                        <option value="20"<?php echo ($per_page == 20 ? ' selected' : ''); ?>>20</option>
                    </select>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th width="2%"><input type="checkbox" id="select_all_routes" /></th>
                        <th>Omschrijving</th>
                        <?php $_GET['order'] = ($order == 'desc' ? 'asc' : 'desc'); ?>
                        <th width="10%">Start datum <a href="<?php echo base_url(uri_string().'?'.http_build_query($_GET)); ?>"><span class="fa fa-sort-<?php echo $order; ?>"></span></a></th>
                        <th width="10%">Stop datum</th>
                        <th width="7%">Aantal kilometers</th>
                        <th width="5%">Betaald</th>
                        <th width="5%">Kosten</th>
                        <th width="5%">Acties</th>
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
                        <th colspan="2">Totalen:</th>
                        <td></td>
                        <td></td>
                        <td><?php echo number_format($total, 2,',','.'); ?> km</td>
                        <td></td>
                        <td>&euro;<?php echo number_format($price,2,',','.'); ?></td>
                        <td></td>
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
        $('[data-toggle="tooltip"]').tooltip();

        $('#select_per_page').on('change', function() {
            var per_page = $(this).val();

            window.location.href = "<?php echo base_url('welcome/page'); ?>?per_page="+per_page+'&order=<?php echo $order; ?>';
        });

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

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }
</script>