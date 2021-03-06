<html>
    <head>
        <title>SimplyRETS Map Search</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.2.3/leaflet.draw.css" />

        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.2.3/leaflet.draw.js"></script>

        <script language="javascript">
            $(document).ready(function () {
                var map = new L.Map('map');
                L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
                }).addTo(map);
                var houston = new L.LatLng(29.97, -95.35);
                map.setView(houston, 10);
                var drawnItems = new L.FeatureGroup();
                map.addLayer(drawnItems);
                var drawControl = new L.Control.Draw({
                    draw: {
                        circle: false,
                        marker: false,
                        polyline: false
                    },
                    edit: {
                        featureGroup: drawnItems
                    }
                });
                map.addControl(drawControl);
                var markers = [];
                var polygon = null;
                map.on('draw:created', function (e) {
                    // remove markers and polygon from the last run
                    $.each(markers, function (i) {
                        map.removeLayer(markers[i])
                    });
                    if (polygon != null)
                        map.removeLayer(polygon);
                    var latLngs = $.map(e.layer.getLatLngs(), function (o) {
                        return {name: "points", value: o.lat + "," + o.lng};
                    });
                    $.ajax({
                        url: 'https://api.simplyrets.com/properties',
                        data: latLngs,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader("Authorization", "Basic " + btoa("simplyrets:simplyrets"))
                        },
                        success: function (data) {
                            $.each(data, function (idx) {

                                var markerLocation = new L.LatLng(data[idx].geo.lat, data[idx].geo.lng);
                                var marker = new L.Marker(markerLocation);
                                
                 
                                var popupContent = data[idx].address.full;
                                //var img = data[idx].photos[0];
                                var img = "<img src='" + data[idx].photos[0] +"' style='width:120px;' />";
                                popupContent += '<br>' + img;
                                marker.bindPopup(popupContent).openPopup();
                                    
                               // marker.bindPopup(popup).openPopup();    
                                marker.on('mouseover', function () {
                                    popover(this)
                                });

                                map.addLayer(marker);
                                markers.push(marker);
                            });
                        }
                    });
                    polygon = e.layer;
                    map.addLayer(e.layer);

                });
            });


            function popover(element) {
                var html = '<div class="tooltip">Hover over me<span class="tooltiptext">Tooltip text</span></div>';
                element = element.innerHTML + html;
            }
        </script>


        <style>
            .tooltip {
                position: relative;
                display: inline-block;
                border-bottom: 1px dotted black;
            }

            .tooltip .tooltiptext {
                visibility: hidden;
                width: 120px;
                background-color: #555;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px 0;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                margin-left: -60px;
                opacity: 0;
                transition: opacity 1s;
            }

            .tooltip .tooltiptext::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: #555 transparent transparent transparent;
            }

            .tooltip:hover .tooltiptext {
                visibility: visible;
                opacity: 1;
            }
        </style>
    </head>
    <body>
        <div id="map" style="height: 100%"></div>
    </body>
</html>