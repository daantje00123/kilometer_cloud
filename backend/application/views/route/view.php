<div class="container-fluid">
    <div class="row" id="title-row">
        <div class="col-xs-12">
            <h1>Route overzicht</h1>
        </div>
    </div>
    <div class="row" id="data-row">
        <div class="col-xs-12 col-md-3">
            <b>#:</b> <?php echo $kilometer['id_route']; ?>
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Omschrijving:</b> <?php echo $kilometer['omschrijving']; ?>
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Start datum:</b> <?php echo $kilometer['datum']['start']['day'].'-'.$kilometer['datum']['start']['month'].'-'.$kilometer['datum']['start']['year'].' '.$kilometer['tijd']['start']['hours'].':'.$kilometer['tijd']['start']['minutes']; ?>
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Stop datum:</b> <?php echo $kilometer['datum']['eind']['day'].'-'.$kilometer['datum']['eind']['month'].'-'.$kilometer['datum']['eind']['year'].' '.$kilometer['tijd']['eind']['hours'].':'.$kilometer['tijd']['eind']['minutes']; ?>
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Aantal kilometer:</b> <?php echo number_format($kilometer['kms'], 2,',','.'); ?> km
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Reistijd:</b> <?php echo $kilometer['tijd']['reis']; ?> uur
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Gemiddelde snelheid:</b> <?php echo number_format($kilometer['gemiddelde'], 2,',','.'); ?> km/h
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Betaald:</b> <?php echo ($kilometer['betaald'] == 1 ? 'Ja' : 'Nee'); ?>
        </div>
        <div class="col-xs-12 col-md-3">
            <b>Kosten:</b> &euro;<?php echo number_format($kilometer['kms'] * db_setting('prijs_per_kilometer'),2,',','.'); ?>
        </div>
    </div>
    <?php if (!empty($kilometer['route'])): ?>
        <div class="row" id="map-row">
            <div class="col-xs-12" style="height: 100%;">
                <div id="map" style="width:100%; height: 100%; text-align: center;">Google Maps wordt geladen... <span class="fa fa-spin fa-spinner"></span></div>
            </div>
        </div>

        <script>
            var map;
            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: <?php echo $kilometer['route'][0]->lat; ?>, lng: <?php echo $kilometer['route'][0]->lng; ?>},
                    zoom: 14
                });

                var marker = new google.maps.Marker({
                    position: {lat: <?php echo $kilometer['route'][0]->lat; ?>, lng: <?php echo $kilometer['route'][0]->lng; ?>},
                    map: map,
                    title: 'Start'
                });

                var route = new google.maps.Polyline({
                    path: <?php echo json_encode($kilometer['route']); ?>,
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    map: map
                });
            }

            window.addEventListener('DOMContentLoaded', function() {
                $('#map-row').height($(document).height() - $('#title-row').height() - $('#data-row').height() - $('#main-menu').height() - 30);
            });
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?signed_in=true&key=AIzaSyC83qOwsQ6dCRP9TlWoklYl2k63LN3zdLI&callback=initMap" async defer></script>

    <?php else: ?>
        <div class="row" id="map-row">
            <div class="col-xs-12">
                <p class="alert alert-info">Er is geen route beschikbaar, mogelijk is deze route handmatig ingevoerd.</p>
            </div>
        </div>
    <?php endif; ?>
</div>
