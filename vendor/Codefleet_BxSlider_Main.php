<?php
/**
* Class that handles the front-end slider
*/
class Codefleet_BxSlider_Main {
	
	protected $version;
	protected $path;
	protected $url;
	protected $debug;
	protected $textdomain;
	protected $data;
	protected $view; // Our view manager
	
	protected $scripts; // Hold the scripts
	protected $styles; // Hold the CSS
	
	/**
	 * Constructor intentionally left empty 
	 */
	public function __construct() {}
	
	public static function get_instance(){
		
		static $instance = null;
        if (null === $instance) {
            $instance = new self;
        }
        return $instance;
	}
	
	public function init($version, $path, $url, $debug, $textdomain, Codefleet_Common_View $view, Codefleet_BxSlider_Data $data ) {
		
		$this->version = $version;
		$this->path = $path;
		$this->url = $url;
		$this->debug = $debug;
		$this->textdomain = $textdomain;
		$this->view = $view;
		$this->data = $data;
		
		$this->styles = array(
			'bxslider-styles' => array(
				'url' => $this->url.'bxslider/jquery.bxslider.css',
				'deps' => array(),
				'ver' => $this->version,
				'media' => 'all'
			)
		);
		
		$this->scripts = array(
			'easing' => array(
				'url' => $this->url.'bxslider/plugins/jquery.easing.1.3.js',
				'deps' => array('jquery'),
				'ver' => $this->version
			),
			'fitvids' => array(
				'url' => $this->url.'bxslider/plugins/jquery.fitvids.js',
				'deps' => array('jquery'),
				'ver' => $this->version
			),
			'bxslider' => array(
				'url' => $this->url.'bxslider/jquery.bxslider.min.js',
				'deps' => array('jquery'),
				'ver' => $this->version
			),
			'bxslider-initialize' => array(
				'url' => $this->url.'js/initialize.min.js',
				'deps' => array('bxslider'),
				'ver' => $this->version
			)
		);
		
		// Register frontend styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_callback' ), 100 );
		
		// Register our shortcode
		add_shortcode('bxslider', array( $this, 'bxslider_shortcode') );
		
	} // end constructor
	
	/**
	 * Add front end scripts and styles  
	 */
	public function wp_enqueue_scripts_callback() {
		
		/*** We do some checking here so that we load only scripts when needed. ***/
		$is_using_jquery_easing = false;
		$has_video = false;
		if($sliders = $this->data->get_sliders()){
			foreach($sliders as $slider){
				$options = $this->data->get_options($slider->ID);
				if($options['use_css']=='false'){
					$is_using_jquery_easing = true;
				}
				if($options['video']=='true'){
					$has_video = true;
				}
			}
		}
		
		$styles = $this->styles;
		$scripts = $this->scripts;
		
		/*** Scripts ***/
		if(!$is_using_jquery_easing){ //If none of the sliders using jquery easing
			unset($scripts['easing']);
		}
		if(!$has_video){ //If none of the sliders has a video slide
			unset($scripts['fitvids']);
		}
		
		$this->enqueue_styles($styles);
		$this->enqueue_scripts($scripts);
		
	}
	
	protected function enqueue_styles( $styles ){
		
		foreach($styles as $key=>$value){
			wp_enqueue_style( $key, $value['url'], $value['deps'], $value['ver'] );
		}
	}
	
	protected function enqueue_scripts( $scripts ){
		
		foreach($scripts as $key=>$value){
			wp_enqueue_script( $key, $value['url'], $value['deps'], $value['ver'] );
		}
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
		
		if( $slider = $this->data->get_slider_by_name( $name ) ){

			$slides = $this->data->get_slides( $slider->ID );
			$options = $this->data->get_options( $slider->ID );
			
			$vars = array();
			$vars['slides'] = $slides;
			$vars['options'] = $options;
			$vars['slider_id'] = $slider->ID;
			
			foreach($vars['slides'] as $i=>$slide){
				$vars['slides'][$i] = wp_parse_args($slide, $this->data->get_slide_defaults()); //Apply defaults in case some keys are missing
				$image_url = wp_get_attachment_image_src( $slide['id'], 'full' );
				$image_url = (is_array($image_url)) ? $image_url[0] : '';
				$vars['slides'][$i]['image_url'] = $image_url;
			}
			$view_path = apply_filters('bxslider_view_path', $this->path . 'views/slider.php', $slider->post_name, $options, $slides);
			$this->view->set_view_file( $view_path );
			$this->view->set_vars( $vars );
			$output = $this->view->get_render();
		} else {
			$output = sprintf(__('[Slider "%s" not found]', 'bxslider'), $name);
		}
		
		return $output;
	}
	
	
}