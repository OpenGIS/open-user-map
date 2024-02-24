<div class="form-field geo-coordinates-wrap">
        <?php
        // Set map style
        $map_style = get_option('oum_map_style') ? get_option('oum_map_style') : 'Esri.WorldStreetMap';
        $oum_tile_provider_mapbox_key = get_option('oum_tile_provider_mapbox_key', '');
        $start_lat = get_option('oum_start_lat');
        $start_lng = get_option('oum_start_lng');
        $start_zoom = get_option('oum_start_zoom');
        ?>

        <div class="map-wrap">
            <div id="mapGetRegion" class="leaflet-map map-style_<?php echo esc_attr($map_style); ?>"></div>
        </div>
        <div class="input-wrap">
            <div class="latlng-wrap">
                <div class="form-field lat-wrap">
                    <label class="meta-label" for="oum_lat">
                        <?php echo __('Lat', 'open-user-map'); ?>
                    </label>
                    <input type="text" readonly class="widefat" id="oum_lat" name="oum_lat" value="<?php echo esc_attr($start_lat); ?>"></input>
                </div>
                <div class="form-field lng-wrap">
                    <label class="meta-label" for="oum_lng">
                        <?php echo __('Lng', 'open-user-map'); ?>
                    </label>
                    <input type="text" readonly class="widefat" id="oum_lng" name="oum_lng" value="<?php echo esc_attr($start_lng); ?>"></input>
                </div>
                <div class="form-field zoom-wrap">
                    <label class="meta-label" for="oum_zoom">
                        <?php echo __('Zoom', 'open-user-map'); ?>
                    </label>
                    <input type="text" readonly class="widefat" id="oum_zoom" name="oum_zoom" value="<?php echo esc_attr($start_zoom); ?>"></input>
                </div>
            </div>

            <div class="geo-coordinates-hint">
                <strong><?php echo __('How to adjust the Region view:', 'open-user-map'); ?></strong>
                <ol>
                    <li><?php echo __('Use the map to find your area of interest', 'open-user-map'); ?></li>
                    <li><?php echo __('Zoom and pan the map to set the perfect initial view', 'open-user-map'); ?><br><br><strong><?php echo __('Tip:', 'open-user-map'); ?></strong> <?php echo __('Hold down the Shift key + mouse to zoom in on an area.', 'open-user-map'); ?></li>
                </ol>
            </div>
        </div>

        <script type="text/javascript" data-category="functional" class="cmplz-native" id="oum-inline-js">
        const lat = '<?php echo esc_attr($start_lat) ? esc_attr($start_lat) : '0'; ?>';
        const lng = '<?php echo esc_attr($start_lng) ? esc_attr($start_lng) : '0'; ?>';
        const zoom = '<?php echo esc_attr($start_zoom) ? esc_attr($start_zoom) : '1'; ?>';
        const mapStyle = '<?php echo $map_style; ?>';
        const oum_tile_provider_mapbox_key = `<?php echo esc_attr($oum_tile_provider_mapbox_key); ?>`;
        let oum_geosearch_selected_provider = ``; 
        const oum_geosearch_provider = `<?php echo get_option('oum_geosearch_provider') ? get_option('oum_geosearch_provider') : 'osm'; ?>`;
        const oum_geosearch_provider_geoapify_key = `<?php echo get_option('oum_geosearch_provider_geoapify_key', ''); ?>`;
        const oum_geosearch_provider_here_key = `<?php echo get_option('oum_geosearch_provider_here_key', ''); ?>`;
        const oum_geosearch_provider_mapbox_key = `<?php echo get_option('oum_geosearch_provider_mapbox_key', ''); ?>`;
        const oum_searchaddress_label = `<?php echo esc_attr(get_option('oum_searchaddress_label') ? get_option('oum_searchaddress_label') : $this->oum_searchaddress_label_default); ?>`;
        </script>

        <?php 
        // load map base scripts
        $this->include_map_scripts();
        
        wp_enqueue_script('oum_backend_region_js', $this->plugin_url . 'src/js/backend-region.js', array('wp-polyfill', 'oum_leaflet_providers_js', 'oum_leaflet_markercluster_js', 'oum_leaflet_subgroups_js', 'oum_leaflet_geosearch_js', 'oum_leaflet_locate_js', 'oum_leaflet_fullscreen_js', 'oum_leaflet_search_js', 'oum_leaflet_gesture_js'), $this->plugin_version); 
        ?>
        
    </div>