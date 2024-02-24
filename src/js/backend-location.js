window.addEventListener('load', function(e) {

  // FUNCTIONS

  //set lat & lng input fields
  function setLocationLatLng(markerLatLng) {
    jQuery('#oum_location_lat').val(markerLatLng.lat);
    jQuery('#oum_location_lng').val(markerLatLng.lng);
  }

  //set address field
  function setAddress(label) {
    jQuery('#oum_location_address').val(label);
  }


  // VARIABLES

  const latLngInputs = jQuery('#latLngInputs');
  const showLatLngInputs = jQuery('#showLatLngInputs');
  let markerIsVisible = false;

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


  // SETUP MAP

  const map = L.map('mapGetLocation', {
      scrollWheelZoom: false,
      zoomSnap: 1,
      zoomDelta: 1,
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
      'draggable': true
  });
  
  // render map
  if(lat && lng) {
      //location has coordinates
      map.setView([lat, lng], zoom);
      locationMarker.addTo(map);
      markerIsVisible = true;
  }else{
      //location has NO coordinates yet
      map.setView([0, 0], 1);
  }

  // Control: search address
  const search = new GeoSearch.GeoSearchControl({
    style: 'bar',
    showMarker: false,
    provider: oum_geosearch_selected_provider,
    searchLabel: oum_searchaddress_label
  });
  map.addControl(search);

  // Control: get current location
  if(enableCurrentLocation) {
    L.control.locate({
      flyTo: true,
      initialZoomLevel: 12,
      drawCircle: false,
      drawMarker: false
    }).addTo(map);
  }


  // Trigger resize (sometimes necessary to render the map properly)
  setInterval(function () {
    map.invalidateSize();
  }, 1000)


  // EVENTS

  //Event: click on map to set marker
  map.on('click locationfound', function(e) {
    let coords = e.latlng;

    locationMarker.setLatLng(coords);

    if(!markerIsVisible) {
        locationMarker.addTo(map);
        markerIsVisible = true;
    }

    setLocationLatLng(coords);
  });

  //Event: geosearch success
  map.on('geosearch/showlocation', function(e) {
    let coords = e.marker._latlng;
    let label = e.location.label;

    locationMarker.setLatLng(coords);

    if (!markerIsVisible) {
      locationMarker.addTo(map);
      markerIsVisible = true;
    }

    setLocationLatLng(coords);
    
    //setAddress(label);
  });

  //Event: drag marker
  locationMarker.on('dragend', function(e) {
      setLocationLatLng(e.target.getLatLng());
  });

  //Event: click on "edit coordinates manually"
  showLatLngInputs.on('click', function(e) {
      e.preventDefault();
      jQuery(this).parent('.hint').hide();
      latLngInputs.fadeIn();
  });

}, false);