<?php
/**
* Class for updating plugin
*/
class BxSliderWp_Updater extends BxSliderWp_Base {
    
    private $license_id;
    private $license_key;
    
    public function bootstrap(){
        $settings_data = $this->plugin['settings_page']->get_settings_data();
        $this->license_id = $settings_data['license_id'];
        $this->license_key = $settings_data['license_key'];
        $this->check_updates();
    }
    
    /**
    * Check updates
    */
    public function check_updates($force_check=false){
        
        if($force_check){
            delete_site_transient('update_plugins'); // Force check
        }
        
        // Insert custom plugins info to 'update_plugins" site transient
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_plugin'));
        
        // Define the alternative response for information checking
        add_filter('plugins_api', array($this, 'check_info'), 10, 3);
    }
    
    /**
    * Check update for each plugin in the list
    *
    * @param object $transient - The site transient containing plugin info
    */
    public function check_plugin($transient) {
        
        // Return we already checked
        if (empty($transient->checked)) {
            return $transient;
        }
        
        // Get the remote plugin info
        $latest_plugin = $this->get_latest_plugin_info($this->plugin['updater.info_url']);
        
        if( $latest_plugin ) {
            
            // If a newer version is available, add the update info
            if ( version_compare($this->plugin['version'], $latest_plugin->version, '<') ) {
                
                // Prepare needed info for transient using objects
                $obj = new stdClass();
                
                $obj->slug = $latest_plugin->slug;
                $obj->new_version = $latest_plugin->version;
                $obj->url = $latest_plugin->url;
                
                
                $api_url = $this->plugin['updater.download_url'];
                $client_time = time();
                $license_id = $this->license_id;
                $license_key = $this->license_key;
        
                $obj->package = $this->generate_package_url($api_url, $client_time, $license_id, $license_key);
                
                $transient->response[ $obj->slug ] = $obj;
            }
            
        }
        
        return $transient;
    }

    /**
    * Add our self-hosted description to the filter
    */
    public function check_info($false, $action, $arg) {
        
        if ( isset($arg->slug) and $arg->slug === $this->plugin['slug'] ) { // Plugin slug format: {folder-name}/{main-file.php}
            
            // Get the remote version
            if( $latest_plugin = $this->get_latest_plugin_info( $this->plugin['updater.info_url'] ) ) {
                
                // Build needed info
                $information = new stdClass();
                $information->name = $this->plugin['name'];
                $information->slug = $this->plugin['slug'];
                $information->version = $latest_plugin->version;
                $information->author = $latest_plugin->author;
                $information->homepage = $latest_plugin->homepage;
                $information->requires = $latest_plugin->requires;  
                $information->tested = $latest_plugin->tested;  
                $information->downloaded = $latest_plugin->downloaded;  
                $information->last_updated = $latest_plugin->last_updated;  
                $information->sections = $this->format_sections((array) $latest_plugin->sections);
                
                $api_url = $this->plugin['updater.download_url'];
                $client_time = time();
                $license_id = $this->license_id;
                $license_key = $this->license_key;
                
                $information->download_link = $this->generate_package_url($api_url, $client_time, $license_id, $license_key);
    
                return $information;
            }
        }
            
        return $false;
    }
    
    
    
    /**
    * Fetch plugin info from remote url
    *
    * @param string $info_url - URL to API endpoint that returns latest plugin version + plugin info
    * @return object $response
    */
    public function get_latest_plugin_info( $info_url ) {
        
        $raw_response = wp_remote_get( $info_url );
        
        if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
            return false;
        
        $response = json_decode( wp_remote_retrieve_body( $raw_response ) );
        
        if(!isset($response->data)){
            return false;
        }
        return $response->data;
    }
    
    
    protected function format_sections( $sections ) {
        return array(
            'description' => $sections['description'],
            'installation' => $sections['installation'],
            'changelog' => $this->format_changelog( $sections['changelog'] )
        );
    }
    
    protected function format_changelog( $changelog ) {
        $html = '<ul>';
        foreach($changelog as $log){
            $html .= '<li>'.$log.'</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
    protected function generate_package_url($api_url, $client_time, $license_id, $license_key){
        
        $query_string = http_build_query(array(
            't' => $client_time,//uses time(),
            'lid' => $license_id
        ));
        
        $digest = $api_url.' '.$query_string.' '.$license_key;
        
        $signature = hash( 'sha256', $digest );
        
        $query_array = array(
            't' => $client_time,
            'lid' => $license_id,
            'signature' => $signature
        );
        $query_string = http_build_query($query_array);
        return $this->plugin['updater.download_url'].'?'.$query_string;
    
    }
} // end class
