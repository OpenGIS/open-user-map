(function() {
  const map = L.map('mapGetRegion', {
    scrollWheelZoom: false,
    zoomSnap: 0.5,
    zoomDelta: 0.5,
  });

  // prevent moving/zoom outside main world bounds
  let world_bounds = L.latLngBounds(L.latLng(-60, -190), L.latLng(80, 190));
  let world_min_zoom = map.getBoundsZoom(world_bounds);
  map.setMaxBounds(world_bounds);
  map.setMinZoom(Math.ceil(world_min_zoom));
  map.on('drag', function() {
    map.panInsideBounds(world_bounds, { animate: false });
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

  // Geosearch Provider
  switch (oum_geosearch_provider) {
    case 'osm':
      oum_geosearch_selected_provider = new GeoSearch.OpenStreetMapProvider();
      break;
    case 'geoapify':
      oum_geosearch_selected_provider = new GeoSearch.GeoapifyProvider({
        params: {
          apiKey: oum_geosearch_provider_geoapify_key
        }
      });
      break;
    case 'here':
      oum_geosearch_selected_provider = new GeoSearch.HereProvider({
        params: {
          apiKey: oum_geosearch_provider_here_key
        }
      });
      break;
    case 'mapbox':
      oum_geosearch_selected_provider = new GeoSearch.MapBoxProvider({
        params: {
          access_token: oum_geosearch_provider_mapbox_key
        }
      });
      break;
    default:
      oum_geosearch_selected_provider = new GeoSearch.OpenStreetMapProvider();
      break;
  }

  const search = new GeoSearch.GeoSearchControl({
      style: 'bar',
      showMarker: false,
      provider: oum_geosearch_selected_provider,
      searchLabel: oum_searchaddress_label
  });
  map.addControl(search);

  map.setView([lat, lng], zoom);

  // set Initial view by move/zoom
  map.on('move', function(e) {
      setInitialLatLngZoom(map.getCenter(), map.getZoom());
  });

  //set lat & lng & zoom input fields
  function setInitialLatLngZoom(mapCenterLatLng, mapZoom) {
      jQuery('#oum_lat').val(mapCenterLatLng.lat);
      jQuery('#oum_lng').val(mapCenterLatLng.lng);
      jQuery('#oum_zoom').val(mapZoom);
  }

})();
