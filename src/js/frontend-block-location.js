document.addEventListener('DOMContentLoaded', function(e) {
  (function(){

    const $el = jQuery('#mapRenderLocation');
    const lat = $el.data('lat');
    const lng = $el.data('lng');
    const zoom = $el.data('zoom');
    const mapStyle = $el.data('mapstyle');
    const oum_tile_provider_mapbox_key = $el.data('tile_provider_mapbox_key');
    const marker_icon_url = $el.data('marker_icon_url');
    const marker_shadow_url = $el.data('marker_shadow_url');


    const map = L.map('mapRenderLocation', {
        scrollWheelZoom: false,
        attributionControl: false,
    });

    // Set map style
    if (mapStyle == 'Custom1') {

      L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png').addTo(map);
      L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
        tileSize: 512,
        zoomOffset: -1
      }).addTo(map);

    } else if (mapStyle == 'Custom2') {

      L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}.png').addTo(map);
      L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
        tileSize: 512,
        zoomOffset: -1
      }).addTo(map);

    } else if (mapStyle == 'Custom3') {

      L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}.png').addTo(map);
      L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
        tileSize: 512,
        zoomOffset: -1
      }).addTo(map);

    } else if (mapStyle == 'MapBox.streets') {

      L.tileLayer.provider('MapBox', {
        id: 'mapbox/streets-v12',
        accessToken: oum_tile_provider_mapbox_key
      }).addTo(map);

    } else if (mapStyle == 'MapBox.outdoors') {

      L.tileLayer.provider('MapBox', {
        id: 'mapbox/outdoors-v12',
        accessToken: oum_tile_provider_mapbox_key
      }).addTo(map);

    } else if (mapStyle == 'MapBox.light') {

      L.tileLayer.provider('MapBox', {
        id: 'mapbox/light-v11',
        accessToken: oum_tile_provider_mapbox_key
      }).addTo(map);

    } else if (mapStyle == 'MapBox.dark') {

      L.tileLayer.provider('MapBox', {
        id: 'mapbox/dark-v11',
        accessToken: oum_tile_provider_mapbox_key
      }).addTo(map);

    } else if (mapStyle == 'MapBox.satellite') {

      L.tileLayer.provider('MapBox', {
        id: 'mapbox/satellite-v9',
        accessToken: oum_tile_provider_mapbox_key
      }).addTo(map);

    } else if (mapStyle == 'MapBox.satellite-streets') {

      L.tileLayer.provider('MapBox', {
        id: 'mapbox/satellite-streets-v12',
        accessToken: oum_tile_provider_mapbox_key
      }).addTo(map);

    } else {
      // Default
      L.tileLayer.provider(mapStyle).addTo(map);
    }

    //define marker

    // Marker Icon
    let markerIcon = L.icon({
      iconUrl: marker_icon_url,
      iconSize: [26, 41],
      iconAnchor: [13, 41],
      popupAnchor: [0, -25],
      shadowUrl: marker_shadow_url,
      shadowSize: [41, 41],
      shadowAnchor: [13, 41]
    });

    let locationMarker = L.marker([lat, lng], {icon: markerIcon}, {
        'draggable': false
    });
    
    if(lat && lng) {
        //location has coordinates
        map.setView([lat, lng], zoom);
        locationMarker.addTo(map);
        markerIsVisible = true;
    }else{
        //location has NO coordinates yet
        map.setView([0, 0], 1);
    }

  })();
});