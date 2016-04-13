<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h1>Instellingen</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Naam</th>
                    <th>Waarde</th>
                    <th>Omschrijving</th>
                    <th>Acties</th>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($settings) && !empty($settings)): ?>
                    <?php foreach($settings as $setting): ?>
                        <tr>
                            <td><?php echo $setting['se_human']; ?></td>
                            <td><?php echo $setting['se_value']; ?></td>
                            <td><?php echo $setting['se_desc']; ?></td>
                            <td><a href="<?php echo base_url('settings/edit/'.$setting['se_key']); ?>"><span class="fa fa-pencil"></span></a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Er zijn nog geen instellingen</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>