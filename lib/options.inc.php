<?php

/**
 * Tiny class for text field options
 */
class Settings {

	var $title = 'Adult Video oEmbed Options';
	var $menu_title = 'Adult Embed';
	var $page_slug = 'avo_options';
	var $capability = 'manage_options';
	var $options_name = 'avo_options';
	var $section_title = 'Configure your width and height for the video players';
	var $parent_slug = 'options-general.php';
	var $menu_hook = 'admin_menu';
	var $button_text = 'Save Embed Options';

	private $hook_suffix;

	var $fields = array();
	var $sections = array();
	var $saved_options;

	/**
	 * Only method called from outside.
	 */
	public function init() {

		$this->saved_options = get_option( $this->options_name );

		add_action( $this->menu_hook, array( $this, 'add_page' ), 11 );

		add_action( 'admin_init', array( $this, 'sections_init' ), 11 );
		add_action( 'admin_init', array( $this, 'options_init' ), 12 );
	}

	/**
	 * Register options page. Only for main site
	 */
	function add_page() {
		$this->hook_suffix = add_submenu_page(
			$this->parent_slug,
			$this->title,				// page title. for title bar
			$this->menu_title,			// menu title. for menu label
			$this->capability,			// capability
			$this->page_slug,			// slug. needed for sections, unique identifier - options.php?page=$page_slug
			array( $this, 'render_page' ) 				// page callback. renders the page itself
		);
	}

	/**
	 * Register extra sections on this option page.
	 */
	function sections_init() {
		add_settings_section(
			'general',		       // id
			$this->section_title,  // title
			'__return_false',	   // callback
			$this->page_slug	   // page slug
		);

		foreach ( $this->sections as $section ) {
			$section['callback'] =
				( isset( $section['callback'] ) && !empty( $section['callback'] ) ) ? $section['callback'] : '__return_false';

			add_settings_section(
				$section['name'],		// id
				$section['title'],		// title
				$section['callback'],	// callback
				$this->page_slug		// page slug
			);
		}
	}

	/**
	 * Generate settings and settings sections for our options page
	 */
	function options_init() {

		register_setting(
			$this->options_name,	// option group. used in render_page() -> settings_fields()
			$this->options_name		// option name. database option name

		);

		foreach ( $this->fields as $field ) {
			if ( $field ) {
				$section =
					isset( $field['section'] ) && $this->section_exists( $field['section'] ) ? $field['section'] : 'general';
				add_settings_field(
					$field['name'],						// field id (internal)
					$field['label'],					// field label
					array( $this, 'display_field' ),	// callback function
					$this->page_slug,					// page to add to
					$section,							// section to add to
					$field 								// extra args
				);
			}
		}
	}

	/**
	 * Add extra section for this option page.
	 *
	 * @param array $options
	 *
	 * @var string $options ['name']
	 * @var string $options ['title']
	 * @var string $options ['callback']
	 */
	function add_section( $options = array() ) {
		$this->sections[] = $options;
	}

	/**
	 *
	 * @param array $args
	 *
	 * @var string $args ['name'] coffee_check
	 * @var string $args ['label'] Want a coffee?
	 * @var string $args ['type'] checkbox
	 * @var string $args ['description'] If the user want coffee or not
	 *
	 */
	public function add_field( $args = array() ) {
		$this->fields[] = $args;
	}


	/**
	 * Check if the section exists on current options page.
	 *
	 * @param $section
	 *
	 * @return bool
	 */
	function section_exists( $section ) {
		global $wp_settings_sections;

		foreach ( $wp_settings_sections[$this->page_slug] as $section_name => $args ) {
			if ( $section === $section_name ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Callback for register_settings_field().
	 *
	 * @param array $field options passed by register_settings_field()
	 */
	function display_field( $field ) {
		$current_option_name = isset( $field['name'] ) ? $field['name'] : '';

		$field_callback = isset( $field['type'] ) ? 'display_' . $field['type'] : 'display_text';

		$field_name = "{$this->options_name}[{$current_option_name}]";
		$field_value =
			isset( $this->saved_options[$current_option_name] ) ? $this->saved_options[$current_option_name] : '';
		$extra = $field;

		$this->$field_callback( $field_name, $field_value, $extra );
		if ( isset( $field['description'] ) ) {
			$this->display_description( $field['description'] );
		}
	}

	/**
	 * @param string $text
	 */
	function display_description( $text = '' ) {
		if ( $text ) {
			?>
			<p><?php echo $text; ?></p>
		<?php
		}
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_text( $field_name, $field_value, $extra = array() ) {
		?>
		<input type="text" size="5" name="<?php echo $field_name; ?>" value="<?php echo esc_attr( $field_value ); ?>"/>
	<?php
	}

	/**
	 * Display the options page
	 */
	function render_page() {
		global $wp_version;
		?>
		<div class="wrap">
			<?php if ( version_compare( $wp_version, '3.8', '<' ) ) { screen_icon(); } ?>

			<h2><?php echo $this->title; ?></h2>

			<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">
				<?php
				settings_fields( $this->options_name );
				do_settings_sections( $this->page_slug );
				if ( $this->button_text !== false ) submit_button( $this->button_text );
				?>
			</form>
		</div><!-- .wrap -->
	<?php
	}

}

add_action( 'init', 'avo_add_options_page', 11 );

/**
 * Display the settings panel
 *
 */
function avo_add_options_page() {

	$options = new Settings();

	// width field
	$options->add_field(
		array(
			 'name'   => 'width',
			 'label'  => __( 'Embed Width', 'avo' ),
			 'type'   => 'text',
		)
	);

	// height field
	$options->add_field(
		array(
			 'name'   => 'height',
			 'label'  => __( 'Embed Height', 'avo' ),
			 'type'   => 'text',
		)
	);

	// Render the page 
	$options->init();
}

/**
 * get video width
 *
 * @return string
 */
function get_video_width() {
	$options = get_option( 'avo_options' );
	$width = $options['width'];

	return $width;
}

/**
 * get video height
 *
 * @return string
 */
function get_video_height() {
	$options = get_option( 'avo_options' );
	$height = $options['height'];
	return $height;
}
