<?php

/**
 * The Fullpage Slide Type Metabox Class
 */
class WP_Fullpage_Slide_Type_Metabox extends WP_Fullpage_Metabox_Base {

	/**
	 * Init Metaboxes Object
	 *
	 * @return  void
	 */
	public function init( $dir = __DIR__, $file = __FILE__ ) {

		parent::init( __DIR__, __FILE__ );

		$this->post_type = WPFP_FULLPAGE_SLIDE_PT;
		
		// Add actions and filters
		$this->actions_filters();

	} // END public function init

	/**
	 * Register Actions and Filters
	 *
	 * @return  void
	 */
	private function actions_filters() {

		// Add Some Metaboxes
		add_action( 'load-post.php', array( &$this, 'metaboxes_init' ) );
		add_action( 'load-post-new.php', array( &$this, 'metaboxes_init' ) );
		
		// Add some scripts		
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );

	} // END private function actions_filters

	/**
	 * Metaboxes Init
	 *
	 * @return  void
	 */
	public function metaboxes_init() {

		// Add metaboxes
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );

		// Save Datas
		add_action( 'save_post', array( &$this, 'save' ) );

	} // END public function metaboxes_init
	
	/**
	 * Add some metaboxes
	 *
	 * @param  string  $post_type  the post type
	 *
	 * @return void
	 */
	public function add_meta_box( $post_type ) {
		
		if ( $this->post_type == $post_type ) {
			
			// Slide Options
			$this->slide_meta_box();
			
		}

	} // END public function add_meta_box
	
	/**
	 * Add Slide Options Metabox
	 *
	 * @return void
	 */
	private function slide_meta_box() {
		
		// Slide Options Metabox
		add_meta_box(
			'wpfp_slide_options',
			__( 'WP Fullpage Options', WPFP_DOMAIN ),
			array( &$this, 'render_slide_options_content' ),
			$this->post_type,
			'advanced',
			'high'
		);

	} // END private function slide_meta_box
	
	/**
	 * Render Slide Options Meta Box content.
	 *
	 * @param  WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function render_slide_options_content( $post ) {
	
		// Slide Options
		$slide_options = $this->prepare_slide_options( $post );

		extract( $slide_options['options'] );
		extract( array_change_key_case( $slide_options['default'], CASE_UPPER ) );

		// Render metabox
		include( $this->rel_path . 'views/slide-options.php' );

	} // END public function render_slide_options_content
	
	/**
	 * Prepare Slide Options.
	 *
	 * @param  WP_Post $post The post object.
	 *
	 * @return array  an array of params
	 */
	private function prepare_slide_options( $post ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( WPFP_SLIDE_PT_SLIDE_OPTIONS, WPFP_SLIDE_PT_SLIDE_OPTIONS . '_nonce' );

		// Retrieving an existing value from the database.
		$slide_options = (array) get_post_meta( $post->ID, WPFP_SLIDE_PT_SLIDE_OPTIONS, true );
		
		// restrieving default settings
		$default_slides_options = get_option( WPFP_SETTINGS_SLIDES_OPTIONS, array() );
		
		// Parsing options with default from Settings
		$slide_options = wp_parse_args( $slide_options, $default_slides_options );

		return array(
			'options' => $slide_options,
			'default' => $default_slides_options,
		);

	} // END public function prepare_slide_options

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param  int  $post_id  The ID of the post being saved.
	 *
	 * @return void
	 */
	public function save( $post_id ) {
		
		// Save Slide Options
		$this->save_slide_options( $post_id );

	} // public function save

	/**
	 * Save Slide Options
	 *
	 * @param  int  $post_id  The ID of the post being saved.
	 *
	 * @return void
	 */
	private function save_slide_options( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		if( ! $this->is_it_safe( $post_id, WPFP_SLIDE_PT_SLIDE_OPTIONS, WPFP_SLIDE_PT_SLIDE_OPTIONS, WPFP_SLIDE_PT_SLIDE_OPTIONS . '_nonce' ) )
			return $post_id;

		// Sanitize the user inputs.
		$slide_options = $_POST[ WPFP_SLIDE_PT_SLIDE_OPTIONS ];

		// Update the meta field.
		update_post_meta( $post_id, WPFP_SLIDE_PT_SLIDE_OPTIONS, $slide_options );

	} // private function save_slide_options
	
	/**
	 * Add some scripts and styles to Slide Post Type metabox.
	 *
	 * @param string $hook the current admin page
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		
		if( 'post.php' != $hook && 'post-new.php' != $hook )
			return;
		
		$dependencies = array();
		$post         = get_post();
		$post_type    = get_post_type( $post );
		
		if( $this->post_type != $post_type )
			return;

		WPFP_JS_Handlers()->jquery_tooltip( $dependencies );

		WPFP_JS_Handlers()->reset_form( '#wpfp_slide_options', '#wpfp_reset', $dependencies );

		wp_enqueue_style( 'slide-options', $this->assets_url . 'css/slide-options.css', $dependencies['css'], WPFP_VERSION );

	} // END public function admin_enqueue_scripts

} // END class WP_Fullpage_Slide_Type_Metabox

// instantiate the Settings class
$WP_Fullpage_Slide_Type_Metabox = new WP_Fullpage_Slide_Type_Metabox();
