<?php
if(!class_exists('Bxslider_Data')):

    /**
     * Class for saving and getting data  
     */
    class Bxslider_Data {
        
        private $nonce_name;
        private $nonce_action;
        
        /**
         * Initializes the class
         */
        public function __construct(){
            
            // Intialize properties
            $this->nonce_name = 'bxslider_builder_nonce'; //Must match with the one in class-bxslider-builder.php
            $this->nonce_action = 'bxslider-save'; //Must match with the one in class-bxslider-builder.php
            
            // Save routine
            add_action( 'save_post', array( $this, 'save_post' ) );
        }
        
        /**
         * Save post hook
         */
        public function save_post($post_id){
            global $bxslider_saved_done;

            // Stop! We have already saved..
            if($bxslider_saved_done){
                return $post_id;
            }
            
            // Verify nonce
            if (!empty( $_POST[ $this->nonce_name ] )) {
                if (!wp_verify_nonce($_POST[ $this->nonce_name ], $this->nonce_action )) {
                    return $post_id;
                }
            } else {
                return $post_id; // Make sure we cancel on missing nonce!
            }
            
            // Check autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
            
            delete_post_meta($post_id, '_bxslider');
            update_post_meta($post_id, '_bxslider', $_POST['bxslider']);
        
        }
        
        
        /**
        * Gets all sliders
        *
        * @return array The array of slider
        */
        public static function get_sliders() {
            $args = array(
                'post_type' => 'bxslider',
                'order'=>'ASC',
                'numberposts' => -1
            );

            $slider_posts = get_posts($args); // Use get_posts to avoid filters
            $slider = '';
            
            if( !empty($slider_posts) ){
                return $slider_posts;
            } else {
                return false;
            }
        }
        
        /**
        * Get a slider
        *
        * @param string $name Post slug of the slider custom post.
        * @return array The array of slider
        */
        public static function get_slider_by_name( $name ) {
            // Get slideshow by id
            $args = array(
                'post_type' => 'bxslider',
                'order'=>'ASC',
                'numberposts' => 1,
                'name'=> $name
            );

            $slider_posts = get_posts( $args ); // Use get_posts to avoid filters

            if( !empty($slider_posts) and isset($slider_posts[0]) ){
                return $slider_posts[0];
            } else {
                return false;
            }
        }
        
        /**
        * Gets the slideshow settings. Defaults and filters are applied.
        *
        * @param int $slideshow_id Post ID of the slideshow custom post.
        * @return array The array of slideshow settings
        */
        public static function get_saved_data( $slideshow_id ) {
            $meta = get_post_custom($slideshow_id);
            $slideshow_settings = array();
            if(isset($meta['_bxslider'][0]) and !empty($meta['_bxslider'][0])){
                $slideshow_settings = maybe_unserialize($meta['_bxslider'][0]);
            }
            return $slideshow_settings;
        }
        
        /**
        * Gets the number of slides in a slideshow
        *
        * @param int Slideshow id
        * @return int Total slides
        */
        public static function get_slides_count($slideshow_id){
            $slides = self::get_slides( $slideshow_id );
            if(!empty($slides)){
                return count( $slides );
            }
            return 0;
        }
        
        /**
        * Get slides
        *
        * @param int Slideshow id
        * @return array Slide data
        */
        public static function get_slides( $slideshow_id ){
            $slides = array();
            $saved_data = self::get_saved_data( $slideshow_id );
            if(isset( $saved_data['slides'] ) and !empty( $saved_data['slides'] )){
                $slides = $saved_data['slides'];
            }
            return $slides;
        }
        
        /**
         * Get slide defaults  
         */
        public static function get_slide_defaults(){
            return array(
                'type' => 'image',
                'id' => '',
                'caption' => '',
                'custom' => ''
            );
        }
        
        /**
         * Get slider options
         *
         * @param int Slideshow id
         * @return array Slide options data
         */
        public static function get_options( $slideshow_id ){
            $options = array();
            $saved_data = self::get_saved_data( $slideshow_id );
            if(isset( $saved_data['options'] ) and !empty( $saved_data['options'] )){
                $options = $saved_data['options'];
            }
            $options = wp_parse_args($options, self::get_default_options() );
            return $options;
        }
        
        /**
         * Get default options
         *
         * @return array Default slide options data
         */
        public static function get_default_options(){
            return array(
                'mode' => 'horizontal',
                'speed' => 500,
                'slide_margin' => 0,
                'start_slide' => 0,
                'random_start' => 'false',
                'slide_selector' => '',
                'infinite_loop' => 'true',
                'hide_control_on_end' => 'false',
                'captions' => 'false',
                'ticker' => 'false',
                'ticker_hover' => 'false',
                'adaptive_height' => 'false',
                'adaptive_height_speed' => 500,
                'video' => 'false',
                'responsive' => 'true',
                'use_css' => 'true',
                'easing' => 'null',
                'preload_images' => 'visible',
                'touch_enabled' => 'true',
                'swipe_threshold' => 50,
                'one_to_one_touch' => 'true',
                'prevent_default_swipe_x' => 'true',
                'prevent_default_swipe_y' => 'false',
                
                'pager' => 'true',
                'pager_type' => 'full',
                'pager_short_separator' => ' / ',
                'pager_selector' => '',
                
                'controls' => 'true',
                'next_text' => 'Next',
                'prev_text' => 'Prev',
                'next_selector' => 'null',
                'prev_selector' => 'null',
                'auto_controls' => 'false',
                'start_text' => 'Start',
                'stop_text' => 'Stop',
                'auto_controls_combine' => 'false',
                'auto_controls_selector' => 'null',
                
                'auto' => 'false',
                'pause' => 4000,
                'auto_start' => 'true',
                'auto_direction' => 'next',
                'auto_hover' => 'false',
                'auto_delay' => 0,
                
                'min_slides' => 1,
                'max_slides' => 1,
                'move_slides' => 0,
                'slide_width' => 0,
            );
        }
        
        public static function get_jquery_easing_options(){
            return array(
                array(
                    'text' => 'default',
                    'value' => 'null'
                ),
                array(
                    'text' => 'swing',
                    'value' => 'swing'
                ),
                array(
                    'text' => 'easeInQuad',
                    'value' => 'easeInQuad'
                ),
                array(
                    'text' => 'easeOutQuad',
                    'value' => 'easeOutQuad'
                ),
                array(
                    'text' => 'easeInOutQuad',
                    'value' => 'easeInOutQuad'
                ),
                array(
                    'text' => 'easeInCubic',
                    'value' => 'easeInCubic'
                ),
                array(
                    'text' => 'easeOutCubic',
                    'value' => 'easeOutCubic'
                ),
                array(
                    'text' => 'easeInOutCubic',
                    'value' => 'easeInOutCubic'
                ),
                array(
                    'text' => 'easeInQuart',
                    'value' => 'easeInQuart'
                ),
                array(
                    'text' => 'easeOutQuart',
                    'value' => 'easeOutQuart'
                ),
                array(
                    'text' => 'easeInOutQuart',
                    'value' => 'easeInOutQuart'
                ),
                array(
                    'text' => 'easeInQuint',
                    'value' => 'easeInQuint'
                ),
                array(
                    'text' => 'easeOutQuint',
                    'value' => 'easeOutQuint'
                ),
                array(
                    'text' => 'easeInOutQuint',
                    'value' => 'easeInOutQuint'
                ),
                array(
                    'text' => 'easeInSine',
                    'value' => 'easeInSine'
                ),
                array(
                    'text' => 'easeOutSine',
                    'value' => 'easeOutSine'
                ),
                array(
                    'text' => 'easeInOutSine',
                    'value' => 'easeInOutSine'
                ),
                array(
                    'text' => 'easeInExpo',
                    'value' => 'easeInExpo'
                ),
                array(
                    'text' => 'easeOutExpo',
                    'value' => 'easeOutExpo'
                ),
                array(
                    'text' => 'easeInOutExpo',
                    'value' => 'easeInOutExpo'
                ),
                array(
                    'text' => 'easeInCirc',
                    'value' => 'easeInCirc'
                ),
                array(
                    'text' => 'easeOutCirc',
                    'value' => 'easeOutCirc'
                ),
                array(
                    'text' => 'easeInOutCirc',
                    'value' => 'easeInOutCirc'
                ),
                array(
                    'text' => 'easeInElastic',
                    'value' => 'easeInElastic'
                ),
                array(
                    'text' => 'easeOutElastic',
                    'value' => 'easeOutElastic'
                ),
                array(
                    'text' => 'easeInOutElastic',
                    'value' => 'easeInOutElastic'
                ),
                array(
                    'text' => 'easeInBack',
                    'value' => 'easeInBack'
                ),
                array(
                    'text' => 'easeOutBack',
                    'value' => 'easeOutBack'
                ),
                array(
                    'text' => 'easeInOutBack',
                    'value' => 'easeInOutBack'
                ),
                array(
                    'text' => 'easeInBounce',
                    'value' => 'easeInBounce'
                ),
                array(
                    'text' => 'easeOutBounce',
                    'value' => 'easeOutBounce'
                ),
                array(
                    'text' => 'easeInOutBounce',
                    'value' => 'easeInOutBounce'
                )
            );
        }
        
        public static function get_css_easing_options(){
            return array(
                array(
                    'text' => 'default',
                    'value' => 'null'
                ),
                array(
                    'text' => 'linear',
                    'value' => 'linear'
                ),
                array(
                    'text' => 'ease',
                    'value' => 'ease'
                ),
                array(
                    'text' => 'ease-in',
                    'value' => 'ease-in'
                ),
                array(
                    'text' => 'ease-out',
                    'value' => 'ease-out'
                ),
                array(
                    'text' => 'ease-in-out',
                    'value' => 'ease-in-out'
                )
            );
        }
    }
    
endif;
