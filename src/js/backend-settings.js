window.addEventListener('load', function(e) {
  const map = L.map('mapGetInitial', {
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

  // Tabs
  const tabs = document.querySelectorAll(".nav-tab-wrapper > .nav-tab");

  for(i = 0; i < tabs.length; i++) {
    tabs[i].addEventListener("click", switchTab);
  }

  function switchTab(event) {
    event.preventDefault();
    document.querySelector(".nav-tab-wrapper > .nav-tab.nav-tab-active").classList.remove("nav-tab-active");
    document.querySelector(".tab-pane.active").classList.remove("active");

    let clickedTab = event.currentTarget;
    let anchor = event.target;
    let activePaneID = anchor.getAttribute("href");

    clickedTab.classList.add("nav-tab-active");
    document.querySelector(activePaneID).classList.add("active");

    //reposition map
    map.invalidateSize();
  }

  // Map type selector
  jQuery('.map-types input[name=oum_map_type]').on('change', function() {
    if(this.value == 1) {
      jQuery('#oum_enable_add_location').prop('checked', true);
    }else{
      jQuery('#oum_enable_add_location').prop('checked', false);
    }
  });

  //Color Picker
  if ( jQuery.isFunction( jQuery.fn.wpColorPicker ) ) {
		jQuery( 'input.oum_colorpicker' ).wpColorPicker();
	}

  // map style selector
  jQuery('.map_styles input[type=radio]').on('change', function(e) {
    jQuery('.map_styles label').removeClass('checked');
    jQuery(this).parent('label').addClass('checked');
    toggleTileProviderApiKeySettings(e.target.value);
  });

  // api keys for commercial map styles
  if(jQuery('.map_styles input[type=radio]').length > 0) {
    toggleTileProviderApiKeySettings(jQuery('.map_styles input[type=radio]:checked').val());

    function toggleTileProviderApiKeySettings(val) {

      jQuery('.wrap-tile-provider-settings > div').hide();

      if(val.includes('MapBox')) {
        // show
        jQuery('.tile-provider-mapbox').show();

        // validate
        if(jQuery('#oum_tile_provider_mapbox_key').val() == '') {
          alert("Please enter a MapBox API Key");
          window.scrollTo({
            top: jQuery('#oum_tile_provider_mapbox_key').offset().top - 200, 
            behavior: 'smooth'
          });
        }
      }
    }
  }

  // marker icon selector
  jQuery('.marker_icons input[type=radio]').on('change', function(e) {
    jQuery('.marker_icons label').removeClass('checked');
    jQuery(this).parent('label').addClass('checked');
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
      searchLabel: oum_searchaddress_label,
  });
  map.addControl(search);

  map.setView([lat, lng], zoom);

  // set Initial view by move/zoom
  map.on('move', function(e) {
      setInitialLatLngZoom(map.getCenter(), map.getZoom());
  });

  //set lat & lng & zoom input fields
  function setInitialLatLngZoom(mapCenterLatLng, mapZoom) {
      jQuery('#oum_start_lat').val(mapCenterLatLng.lat);
      jQuery('#oum_start_lng').val(mapCenterLatLng.lng);
      jQuery('#oum_start_zoom').val(mapZoom);
  }

  //Custom Fields
  let maxField = 10; //Input fields increment limitation
  let addButton = jQuery('.oum_add_button'); //Add button selector
  let wrapper = jQuery('.oum_custom_fields_wrapper'); //Input field wrapper  
  let x = 1; //Initial field counter is 1
  
  //Once add button is clicked
  jQuery(addButton).click(function(e){
    e.preventDefault();
    
    //Check maximum number of input fields
    if(x < maxField){ 
        x++; //Increment field counter
        let index = Date.now();
        let fieldHTML = `
          <tr>
            <td>
              <input type="text" class="field-type-text field-type-link field-type-email field-type-checkbox field-type-radio field-type-select" name="oum_custom_fields[${index}][label]" placeholder="Enter label" value="" />
            </td>
            <td>
              <input class="oum-switch field-type-text field-type-link field-type-email field-type-checkbox field-type-radio field-type-select" id="oum_custom_fields_${index}_required" type="checkbox" name="oum_custom_fields[${index}][required]"><label class="field-type-text field-type-link field-type-email field-type-checkbox field-type-radio field-type-select" for="oum_custom_fields_${index}_required"></label>
            </td>
            <td>
              <input class="oum-switch field-type-text field-type-link field-type-email field-type-checkbox field-type-radio field-type-select" id="oum_custom_fields_${index}_private" type="checkbox" name="oum_custom_fields[${index}][private]"><label class="field-type-text field-type-link field-type-email field-type-checkbox field-type-radio field-type-select" for="oum_custom_fields_${index}_private"></label>
            </td>
            <td>
              <input class="small-text field-type-text field-type-link field-type-email" type="number" min="0" name="oum_custom_fields[${index}][maxlength]" />
            </td>
            <td>
              <select class="oum-custom-field-fieldtype" name="oum_custom_fields[${index}][fieldtype]">                         
                  <option value="text">Text</option>
        `;

        

        fieldHTML += `
              </select>
            </td>
            <td>
              <input type="text" class="regular-text field-type-checkbox field-type-radio field-type-select" name="oum_custom_fields[${index}][options]" placeholder="Red|Blue|Green" value="" style="display: none;" />
              <label class="field-type-select oum-custom-field-allow-empty" style="display: none;"><input class="field-type-select" type="checkbox" name="oum_custom_fields[${index}][emptyoption]" />add empty option</label>
              <textarea class="regular-text field-type-html" name="oum_custom_fields[${index}][html]" placeholder="Enter HTML here" style="display: none;"></textarea>
            </td>
            <td>
              <input type="text" class="field-type-text field-type-link field-type-email field-type-checkbox field-type-radio field-type-select" name="oum_custom_fields[${index}][description]" placeholder="Enter description (optional)" value="" />
            </td>
            <td class="actions">
              <a class="up" href="#"><span class="dashicons dashicons-arrow-up"></span></a>
              <a class="down" href="#"><span class="dashicons dashicons-arrow-down"></span></a>
              <a class="remove_button" href="#"><span class="dashicons dashicons-trash"></span></a>
            </td>
          </tr>
        `;
        jQuery(wrapper).find('tbody').append(fieldHTML); //Add field html
    }
  });

  jQuery(wrapper).on('change', '.oum-custom-field-fieldtype', function(e) {
    updateCustomFieldRow(this);
  });

  jQuery('.oum-custom-field-fieldtype').each(function() {
    updateCustomFieldRow(this);
  });

  function updateCustomFieldRow(el) {
    jQuery(el).closest('tr').find('[class*="field-type-"]').hide();

    if(jQuery(el).val() == 'text') {
      jQuery(el).closest('tr').find('.field-type-text').show();
      return;
    }

    if(jQuery(el).val() == 'link') {
      jQuery(el).closest('tr').find('.field-type-link').show();
      return;
    }

    if(jQuery(el).val() == 'email') {
      jQuery(el).closest('tr').find('.field-type-email').show();
      return;
    }

    if(jQuery(el).val() == 'checkbox') {
      jQuery(el).closest('tr').find('.field-type-checkbox').show();
      return;
    }

    if(jQuery(el).val() == 'radio') {
      jQuery(el).closest('tr').find('.field-type-radio').show();
      return;
    }

    if(jQuery(el).val() == 'select') {
      jQuery(el).closest('tr').find('.field-type-select').show();
      return;
    }

    if(jQuery(el).val() == 'html') {
      jQuery(el).closest('tr').find('.field-type-html').show();
      return;
    }
  }

  //up button is clicked
  jQuery(wrapper).on('click', '.up', function(e) {
    e.preventDefault();
    let item = jQuery(this).closest('tr');
    item.insertBefore(item.prev());
  });

  //down button is clicked
  jQuery(wrapper).on('click', '.down', function(e) {
    e.preventDefault();
    let item = jQuery(this).closest('tr');
    item.insertAfter(item.next());
  });
  
  //remove button is clicked
  jQuery(wrapper).on('click', '.remove_button', function(e){
      e.preventDefault();
      jQuery(this).closest('tr').remove(); //Remove field html
      x--; //Decrement field counter
  });


  //Setting: Action after submit
  actionAfterSubmit(jQuery('#oum_action_after_submit').val());

  jQuery('#oum_action_after_submit').on('change', function(e){
    actionAfterSubmit(this.value);
  });

  function actionAfterSubmit(val) {
    if(val == 'text') {
      jQuery('#oum_action_after_submit_text').show();
      jQuery('#oum_action_after_submit_redirect').hide();
    }else if(val == 'redirect') {
      jQuery('#oum_action_after_submit_text').hide();
      jQuery('#oum_action_after_submit_redirect').show();
    }else{
      jQuery('#oum_action_after_submit_text').hide();
      jQuery('#oum_action_after_submit_redirect').hide();
    }
  }

  //Setting: Redirect to registration
  if(jQuery('#oum_enable_user_restriction').length > 0) {
    
    redirectToRegistration(jQuery('#oum_enable_user_restriction').is(':checked'));

    jQuery('#oum_enable_user_restriction').on('click', function(e){
      redirectToRegistration(this.checked);
    });

    function redirectToRegistration(val) {
      if(val) {
        jQuery('#redirect_to_registration').show();
      }else{
        jQuery('#redirect_to_registration').hide();
      }
    }
  }

  //Setting: Enable Filterable Marker Categories
  if(jQuery('#oum_enable_marker_types').length > 0) {
    
    toggleMarkerCategoriesSettings(jQuery('#oum_enable_marker_types').is(':checked'));

    jQuery('#oum_enable_marker_types').on('click', function(e){
      toggleMarkerCategoriesSettings(this.checked);
    });

    function toggleMarkerCategoriesSettings(val) {
      if(val) {
        // show
        jQuery('.wrap-marker-categories-settings').show();
      }else{
        // hide
        jQuery('.wrap-marker-categories-settings').hide();
      }
    }
  }

  //Setting: Geoseach Provider
  if(jQuery('#oum_geosearch_provider').length > 0) {
    
    toggleApiKeySettings(jQuery('#oum_geosearch_provider').val());

    jQuery('#oum_geosearch_provider').on('change', function(e){
      toggleApiKeySettings(e.target.value);
    });

    function toggleApiKeySettings(val) {
      jQuery('.wrap-geosearch-provider-settings > div').hide();

      if(val == 'geoapify') {
        // show
        jQuery('.geosearch-provider-geoapify').show();
      }
      if(val == 'here') {
        // show
        jQuery('.geosearch-provider-here').show();
      }
      if(val == 'mapbox') {
        // show
        jQuery('.geosearch-provider-mapbox').show();
      }
    }
  }

  //Setting: Enable Searchbar
  if(jQuery('#oum_enable_searchbar').length > 0) {
    
    toggleSearchbarSettings(jQuery('#oum_enable_searchbar').is(':checked'));

    jQuery('#oum_enable_searchbar').on('click', function(e){
      toggleSearchbarSettings(this.checked);
    });

    function toggleSearchbarSettings(val) {
      if(val) {
        // show
        jQuery('.wrap-searchbar-settings').show();
      }else{
        // hide
        jQuery('.wrap-searchbar-settings').hide();
      }
    }
  }

}, false);