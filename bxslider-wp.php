<?php
/*
Plugin Name: BxSlider WP
Plugin URI: http://www.codefleet.net/bxslider-wp/
Description: Provides an easy to use interface for BxSlider that blends seamlessly with your WordPress workflow.
Version: 1.3.3
Author: Nico Amarilla
Author URI: http://www.codefleet.net/
License:

  Copyright 2013 (kosinix@codefleet.net)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/
if(!defined('BXSLIDER_VERSION')){
    define('BXSLIDER_VERSION', '1.3.3' );
}
if(!defined('BXSLIDER_PATH')){
    define('BXSLIDER_PATH', realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR );
}
if(!defined('BXSLIDER_URL')){
    define('BXSLIDER_URL', plugin_dir_url(__FILE__) );
}

// Include common classes
require_once(BXSLIDER_PATH.'classes/codefleet/class-codefleet-view.php');

// Include this plugin's classes
require_once(BXSLIDER_PATH.'classes/class-bxslider-data.php');
require_once(BXSLIDER_PATH.'classes/class-bxslider-admin.php');
require_once(BXSLIDER_PATH.'classes/class-bxslider-slider.php');

$bxslider_saved_done = false; //Global variable to limit save_post execution to only once

// Load domain in this hook to work with WPML
add_action('plugins_loaded', 'bxslider_plugin_init');
function bxslider_plugin_init() {
    // Load language files
    load_plugin_textdomain( 'bxslider', false, basename(dirname(__FILE__)).'/lang' );
}

// Store the plugins instance to a global objects so that filter and actions can be overidden.
$bxslider_data_instance = new Bxslider_Data();
$bxslider_admin_instance = new Bxslider_Admin( new Codefleet_View( '' ) ); // Inject our view manager 
$bxslider_slider_instance = new Bxslider_Slider( new Codefleet_View( '' ) );

// Prevent conflicts in global space
if(!function_exists('bxslider_options')):
    /**
     * Function that adds data attributes to the slider to be used by the initialization script
     *
     * @param int $slider_id - Slider custom post ID
     */
    function bxslider_options($slider_id){
        $options = Bxslider_Data::get_options( $slider_id );
        $out = ' ';
        foreach($options as $name=>$option){
            $out .= 'data-bxslider-'.str_replace('_', '-', $name).'="'.$option.'"';
        }
        $out.=' ';
        echo $out;
    }
endif;

if(!function_exists('bxslider')):
    /**
    * BxSlider
    *
    * Displays the slider on template files.
    *
    * @param string $slider_slug The slug of the slider.
    */
    function bxslider( $slider_slug ){
        global $bxslider_slider_instance;
        if(isset($bxslider_slider_instance)){
            echo $bxslider_slider_instance->bxslider_shortcode( array('id'=>$slider_slug) );
        }
    }
endif;