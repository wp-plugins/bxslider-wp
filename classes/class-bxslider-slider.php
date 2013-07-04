<?php
if(!class_exists('Bxslider_Slider')):

    /**
    * Class that handles the front-end slider
    */
    class Bxslider_Slider {
        
        public $slider_view_path;
        
        /**
         * Initializes 
         */
        public function __construct() {
            
            $this->slider_view_path = BXSLIDER_PATH . 'views/slider.php';
            
            // Register frontend styles and scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ), 100 );
            
            // Register our shortcode
            add_shortcode('bxslider', array( $this, 'bxslider_shortcode') );
            
        } // end constructor
        
        /**
         * Add front end scripts and styles  
         */
        public function register_plugin_scripts() {

            /*** Styles ***/
            wp_enqueue_style( 'bxslider-styles', BXSLIDER_URL.'bxslider/jquery.bxslider.css', array(), BXSLIDER_VERSION );
            
            /*** Scripts ***/
            wp_enqueue_script( 'easing', BXSLIDER_URL.'bxslider/plugins/jquery.easing.1.3.js', array('jquery'), BXSLIDER_VERSION );
            wp_enqueue_script( 'fitvids', BXSLIDER_URL.'bxslider/plugins/jquery.fitvids.js', array('jquery'), BXSLIDER_VERSION );
            wp_enqueue_script( 'bxslider', BXSLIDER_URL.'bxslider/jquery.bxslider.min.js', array('jquery'), BXSLIDER_VERSION ); 
            wp_enqueue_script( 'bxslider-initialize', BXSLIDER_URL.'js/initialize.js', array('bxslider'), BXSLIDER_VERSION );

        
        }
        
        /**
         * Our shortcode function
         *
         * @param array $shortcode_settings - The array of args passed from a shortcode
         */
        public function bxslider_shortcode($shortcode_settings) {
            // Allow only certain args
            $shortcode_settings = shortcode_atts(
                array(
                    'id' => 0
                ),
                $shortcode_settings
            );
            $name = esc_attr($shortcode_settings['id']);// Slideshow slug or ID
    
            $output = '';
            
            if( $slider = Bxslider_Data::get_slider_by_name( $name ) ){

                $slides = Bxslider_Data::get_slides( $slider->ID );
                $options = Bxslider_Data::get_options( $slider->ID );
                
                $vars = array();
                $vars['slides'] = $slides;
                $vars['options'] = $options;
                $vars['slider_id'] = $slider->ID;
                
                foreach($vars['slides'] as $i=>$slide){
                    $image_url = wp_get_attachment_image_src( $slide['id'], 'full' );
                    $image_url = (is_array($image_url)) ? $image_url[0] : '';
                    $vars['slides'][$i]['image_url'] = $image_url;
                }
                $view = new Bxslider_View( $this->slider_view_path );
                $view->set_vars( $vars );
                $output = $view->get_render();
            } else {
                $output = sprintf(__('[Slider "%s" not found]', 'bxslider'), $name);
            }
            
            return $output;
        }
        
    }
    
endif;