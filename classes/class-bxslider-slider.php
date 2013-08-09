<?php
if(!class_exists('Bxslider_Slider')):

    /**
    * Class that handles the front-end slider
    */
    class Bxslider_Slider {
        
        private $view; // Our view manager
        public $initialize_script_url; // Allow this to be overridden
        public $slider_view_path; // Allow this to be overridden
        
        /**
         * Initializes 
         */
        public function __construct( $view ) {
            
            $this->view = $view;
            
            $this->slider_view_path = BXSLIDER_PATH . 'views/slider.php';
            $this->initialize_script_url = BXSLIDER_URL.'js/initialize.min.js';
            
            // Register frontend styles and scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ), 100 );
            
            // Register our shortcode
            add_shortcode('bxslider', array( $this, 'bxslider_shortcode') );
            
        } // end constructor
        
        /**
         * Add front end scripts and styles  
         */
        public function register_plugin_scripts() {
            
            /*** We do some checking here so that we load only scripts when needed. ***/
            $is_using_jquery_easing = false;
            $has_video = false;
            if($sliders = BxSlider_Data::get_sliders()){
                foreach($sliders as $slider){
                    $options = BxSlider_Data::get_options($slider->ID);
                    if($options['use_css']=='false'){
                        $is_using_jquery_easing = true;
                    }
                    if($options['video']=='true'){
                        $has_video = true;
                    }
                }
            }
            
            /*** Styles ***/
            wp_enqueue_style( 'bxslider-styles', BXSLIDER_URL.'bxslider/jquery.bxslider.css', array(), BXSLIDER_VERSION );
            
            /*** Scripts ***/
            if($is_using_jquery_easing){ //If one of the sliders using jquery easing
                wp_enqueue_script( 'easing', BXSLIDER_URL.'bxslider/plugins/jquery.easing.1.3.js', array('jquery'), BXSLIDER_VERSION );
            }
            if($has_video){ //If one of the sliders has a video slide
                wp_enqueue_script( 'fitvids', BXSLIDER_URL.'bxslider/plugins/jquery.fitvids.js', array('jquery'), BXSLIDER_VERSION );
            }
            wp_enqueue_script( 'bxslider', BXSLIDER_URL.'bxslider/jquery.bxslider.min.js', array('jquery'), BXSLIDER_VERSION ); 
            wp_enqueue_script( 'bxslider-initialize', $this->initialize_script_url, array('bxslider'), BXSLIDER_VERSION );
            
            
            
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
                    $vars['slides'][$i] = wp_parse_args($slide, Bxslider_Data::get_slide_defaults()); //Apply defaults in case some keys are missing
                    $image_url = wp_get_attachment_image_src( $slide['id'], 'full' );
                    $image_url = (is_array($image_url)) ? $image_url[0] : '';
                    $vars['slides'][$i]['image_url'] = $image_url;
                }
                $view_path = apply_filters('bxslider_view_path', $this->slider_view_path, $slider->post_name, $options, $slides);
                $this->view->set_view_file( $view_path );
                $this->view->set_vars( $vars );
                $output = $this->view->get_render();
            } else {
                $output = sprintf(__('[Slider "%s" not found]', 'bxslider'), $name);
            }
            
            return $output;
        }
        
    }
    
endif;