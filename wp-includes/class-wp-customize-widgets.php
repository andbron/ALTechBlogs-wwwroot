<?php
/**
 * WordPress Customize Widgets classes
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.9.0
 */

/**
 * Customize Widgets class.
 *
 * Implements widget management in the Customizer.
 *
 * @since 3.9.0
 *
 * @see WP_Customize_Manager
 */
final class WP_Customize_Widgets {

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 3.9.0
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * All id_bases for widgets defined in core.
	 *
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $core_widget_id_bases = array(
		'archives', 'calendar', 'categories', 'links', 'meta',
		'nav_menu', 'pages', 'recent-comments', 'recent-posts',
		'rss', 'search', 'tag_cloud', 'text',
	);

	/**
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $rendered_sidebars = array();

	/**
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $rendered_widgets = array();

	/**
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $old_sidebars_widgets = array();

	/**
	 * Mapping of setting type to setting ID pattern.
	 *
	 * @since 4.2.0
	 * @access protected
	 * @var array
	 */
	protected $setting_id_patterns = array(
		'widget_instance' => '/^(widget_.+?)(?:\[(\d+)\])?$/',
		'sidebar_widgets' => '/^sidebars_widgets\[(.+?)\]$/',
	);

	/**
	 * Initial loader.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param WP_Customize_Manager $manager Customize manager bootstrap instance.
	 */
	public function __construct( $manager ) {
		$this->manager = $manager;

		add_filter( 'customize_dynamic_setting_args',          array( $this, 'filter_customize_dynamic_setting_args' ), 10, 2 );
		add_action( 'after_setup_theme',                       array( $this, 'register_settings' ) );
		add_action( 'wp_loaded',                               array( $this, 'override_sidebars_widgets_for_theme_switch' ) );
		add_action( 'customize_controls_init',                 array( $this, 'customize_controls_init' ) );
		add_action( 'customize_register',                      array( $this, 'schedule_customize_register' ), 1 );
		add_action( 'customize_controls_enqueue_scripts',      array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_print_styles',         array( $this, 'print_styles' ) );
		add_action( 'customize_controls_print_scripts',        array( $this, 'print_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_footer_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'output_widget_control_templates' ) );
		add_action( 'customize_preview_init',                  array( $this, 'customize_preview_init' ) );
		add_filter( 'customize_refresh_nonces',                array( $this, 'refresh_nonces' ) );

		add_action( 'dynamic_sidebar',                         array( $this, 'tally_rendered_widgets' ) );
		add_filter( 'is_active_sidebar',                       array( $this, 'tally_sidebars_via_is_active_sidebar_calls' ), 10, 2 );
		add_filter( 'dynamic_sidebar_has_widgets',             array( $this, 'tally_sidebars_via_dynamic_sidebar_calls' ), 10, 2 );
	}

	/**
	 * Get the widget setting type given a setting ID.
	 *
	 * @since 4.2.0
	 * @access protected
	 *
	 * @param $setting_id Setting ID.
	 * @return string|null Setting type. Null otherwise.
	 */
	protected function get_setting_type( $setting_id ) {
		static $cache = array();
		if ( isset( $cache[ $setting_id ] ) ) {
			return $cache[ $setting_id ];
		}
		foreach ( $this->setting_id_patterns as $type => $pattern ) {
			if ( preg_match( $pattern, $setting_id ) ) {
				$cache[ $setting_id ] = $type;
				return $type;
			}
		}
		return null;
	}

	/**
	 * Inspect the incoming customized data for any widget settings, and dynamically add them up-front so widgets will be initialized properly.
	 *
	 * @since 4.2.0
	 * @access public
	 */
	public function register_settings() {
		$widget_setting_ids = array();
		$incoming_setting_ids = array_keys( $this->manager->unsanitized_post_values() );
		foreach ( $incoming_setting_ids as $setting_id ) {
			if ( ! is_null( $this->get_setting_type( $setting_id ) ) ) {
				$widget_setting_ids[] = $setting_id;
			}
		}
		if ( $this->manager->doing_ajax( 'update-widget' ) && isset( $_REQUEST['widget-id'] ) ) {
			$widget_setting_ids[] = $this->get_setting_id( wp_unslash( $_REQUEST['widget-id'] ) );
		}

		$settings = $this->manager->add_dynamic_settings( array_unique( $widget_setting_ids ) );

		/*
		 * Preview settings right away so that widgets and sidebars will get registered properly.
		 * But don't do this if a customize_save because this will cause WP to think there is nothing
		 * changed that needs to be saved.
		 */
		if ( ! $this->manager->doing_ajax( 'customize_save' ) ) {
			foreach ( $settings as $setting ) {
				$setting->preview();
			}
		}
	}

	/**
	 * Determine the arguments for a dynamically-created setting.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @param false|array $setting_args The arguments to the WP_Customize_Setting constructor.
	 * @param string      $setting_id   ID for dynamic setting, usually coming from `$_POST['customized']`.
	 * @return false|array Setting arguments, false otherwise.
	 */
	public function filter_customize_dynamic_setting_args( $args, $setting_id ) {
		if ( $this->get_setting_type( $setting_id ) ) {
			$args = $this->get_setting_args( $setting_id );
		}
		return $args;
	}

	/**
	 * Get an unslashed post value or return a default.
	 *
	 * @since 3.9.0
	 *
	 * @access protected
	 *
	 * @param string $name    Post value.
	 * @param mixed  $default Default post value.
	 * @return mixed Unslashed post value or default value.
	 */
	protected function get_post_value( $name, $default = null ) {
		if ( ! isset( $_POST[ $name ] ) ) {
			return $default;
		}

		return wp_unslash( $_POST[ $name ] );
	}

	/**
	 * Override sidebars_widgets for theme switch.
	 *
	 * When switching a theme via the Customizer, supply any previously-configured
	 * sidebars_widgets from the target theme as the initial sidebars_widgets
	 * setting. Also store the old theme's existing settings so that they can
	 * be passed along for storing in the sidebars_widgets theme_mod when the
	 * theme gets switched.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function override_sidebars_widgets_for_theme_switch() {
		global $sidebars_widgets;

		if ( $this->manager->doing_ajax() || $this->manager->is_theme_active() ) {
			return;
		}

		$this->old_sidebars_widgets = wp_get_sidebars_widgets();
		add_filter( 'customize_value_old_sidebars_widgets_data', array( $this, 'filter_customize_value_old_sidebars_widgets_data' ) );

		// retrieve_widgets() looks at the global $sidebars_widgets
		$sidebars_widgets = $this->old_sidebars_widgets;
		$sidebars_widgets = retrieve_widgets( 'customize' );
		add_filter( 'option_sidebars_widgets', array( $this, 'filter_option_sidebars_widgets_for_theme_switch' ), 1 );
		unset( $GLOBALS['_wp_sidebars_widgets'] ); // reset global cache var used by wp_get_sidebars_widgets()
	}

	/**
	 * Filter old_sidebars_widgets_data Customizer setting.
	 *
	 * When switching themes, filter the Customizer setting
	 * old_sidebars_widgets_data to supply initial $sidebars_widgets before they
	 * were overridden by retrieve_widgets(). The value for
	 * old_sidebars_widgets_data gets set in the old theme's sidebars_widgets
	 * theme_mod.
	 *
	 * @see WP_Customize_Widgets::handle_theme_switch()
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $old_sidebars_widgets
	 */
	public function filter_customize_value_old_sidebars_widgets_data( $old_sidebars_widgets ) {
		return $this->old_sidebars_widgets;
	}

	/**
	 * Filter sidebars_widgets option for theme switch.
	 *
	 * When switching themes, the retrieve_widgets() function is run when the
	 * Customizer initializes, and then the new sidebars_widgets here get
	 * supplied as the default value for the sidebars_widgets option.
	 *
	 * @see WP_Customize_Widgets::handle_theme_switch()
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $sidebars_widgets
	 */
	public function filter_option_sidebars_widgets_for_theme_switch( $sidebars_widgets ) {
		$sidebars_widgets = $GLOBALS['sidebars_widgets'];
		$sidebars_widgets['array_version'] = 3;
		return $sidebars_widgets;
	}

	/**
	 * Make sure all widgets get loaded into the Customizer.
	 *
	 * Note: these actions are also fired in wp_ajax_update_widget().
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function customize_controls_init() {
		/** This action is documented in wp-admin/includes/ajax-actions.php */
		do_action( 'load-widgets.php' );

		/** This action is documented in wp-admin/includes/ajax-actions.php */
		do_action( 'widgets.php' );

		/** This action is documented in wp-admin/widgets.php */
		do_action( 'sidebar_admin_setup' );
	}

	/**
	 * Ensure widgets are available for all types of previews.
	 *
	 * When in preview, hook to 'customize_register' for settings
	 * after WordPress is loaded so that all filters have been
	 * initialized (e.g. Widget Visibility).
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function schedule_customize_register() {
		if ( is_admin() ) {
			$this->customize_register();
		} else {
			add_action( 'wp', array( $this, 'customize_register' ) );
		}
	}

	/**
	 * Register Customizer settings and controls for all sidebars and widgets.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function customize_register() {
		global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_sidebars;

		$sidebars_widgets = array_merge(
			array( 'wp_inactive_widgets' => array() ),
			array_fill_keys( array_keys( $GLOBALS['wp_registered_sidebars'] ), array() ),
			wp_get_sidebars_widgets()
		);

		$new_setting_ids = array();

		/*
		 * Register a setting for all widgets, including those which are active,
		 * inactive, and orphaned since a widget may get suppressed from a sidebar
		 * via a plugin (like Widget Visibility).
		 */
		foreach ( array_keys( $wp_registered_widgets ) as $widget_id ) {
			$setting_id   = $this->get_setting_id( $widget_id );
			$setting_args = $this->get_setting_args( $setting_id );
			if ( ! $this->manager->get_setting( $setting_id ) ) {
				$this->manager->add_setting( $setting_id, $setting_args );
			}
			$new_setting_ids[] = $setting_id;
		}

		/*
		 * Add a setting which will be supplied for the theme's sidebars_widgets
		 * theme_mod when the the theme is switched.
		 */
		if ( ! $this->manager->is_theme_active() ) {
			$setting_id = 'old_sidebars_widgets_data';
			$setting_args = $this->get_setting_args( $setting_id, array(
				'type' => 'global_variable',
				'dirty' => true,
			) );
			$this->manager->add_setting( $setting_id, $setting_args );
		}

		$this->manager->add_panel( 'widgets', array(
			'title'       => __( 'Widgets' ),
			'description' => __( 'Widgets are independent sections of content that can be placed into widgetized areas provided by your theme (commonly called sidebars).' ),
			'priority'    => 110,
		) );

		foreach ( $sidebars_widgets as $sidebar_id => $sidebar_widget_ids ) {
			if ( empty( $sidebar_widget_ids ) ) {
				$sidebar_widget_ids = array();
			}

			$is_registered_sidebar = isset( $GLOBALS['wp_registered_sidebars'][$sidebar_id] );
			$is_inactive_widgets   = ( 'wp_inactive_widgets' === $sidebar_id );
			$is_active_sidebar     = ( $is_registered_sidebar && ! $is_inactive_widgets );

			// Add setting for managing the sidebar's widgets.
			if ( $is_registered_sidebar || $is_inactive_widgets ) {
				$setting_id   = sprintf( 'sidebars_widgets[%s]', $sidebar_id );
				$setting_args = $this->get_setting_args( $setting_id );
				if ( ! $this->manager->get_setting( $setting_id ) ) {
					if ( ! $this->manager->is_theme_active() ) {
						$setting_args['dirty'] = true;
					}
					$this->manager->add_setting( $setting_id, $setting_args );
				}
				$new_setting_ids[] = $setting_id;

				// Add section to contain controls.
				$section_id = sprintf( 'sidebar-widgets-%s', $sidebar_id );
				if ( $is_active_sidebar ) {

					$section_args = array(
						'title' => $GLOBALS['wp_registered_sidebars'][ $sidebar_id ]['name'],
						'description' => $GLOBALS['wp_registered_sidebars'][ $sidebar_id ]['description'],
						'priority' => array_search( $sidebar_id, array_keys( $wp_registered_sidebars ) ),
						'panel' => 'widgets',
						'sidebar_id' => $sidebar_id,
					);

					/**
					 * Filter Customizer widget section arguments for a given sidebar.
					 *
					 * @since 3.9.0
					 *
					 * @param array      $section_args Array of Customizer widget section arguments.
					 * @param string     $section_id   Customizer section ID.
					 * @param int|string $sidebar_id   Sidebar ID.
					 */
					$section_args = apply_filters( 'customizer_widgets_section_args', $section_args, $section_id, $sidebar_id );

					$section = new WP_Customize_Sidebar_Section( $this->manager, $section_id, $section_args );
					$this->manager->add_section( $section );

					$control = new WP_Widget_Area_Customize_Control( $this->manager, $setting_id, array(
						'section'    => $section_id,
						'sidebar_id' => $sidebar_id,
						'priority'   => count( $sidebar_widget_ids ), // place 'Add Widget' and 'Reorder' buttons at end.
					) );
					$new_setting_ids[] = $setting_id;

					$this->manager->add_control( $control );
				}
			}

			// Add a control for each active widget (located in a sidebar).
			foreach ( $sidebar_widget_ids as $i => $widget_id ) {

				// Skip widgets that may have gone away due to a plugin being deactivated.
				if ( ! $is_active_sidebar || ! isset( $GLOBALS['wp_registered_widgets'][$widget_id] ) ) {
					continue;
				}

				$registered_widget = $GLOBALS['wp_registered_widgets'][$widget_id];
				$setting_id        = $this->get_setting_id( $widget_id );
				$id_base           = $GLOBALS['wp_registered_widget_controls'][$widget_id]['id_base'];

				$control = new WP_Widget_Form_Customize_Control( $this->manager, $setting_id, array(
					'label'          => $registered_widget['name'],
					'section'        => $section_id,
					'sidebar_id'     => $sidebar_id,
					'widget_id'      => $widget_id,
					'widget_id_base' => $id_base,
					'priority'       => $i,
					'width'          => $wp_registered_widget_controls[$widget_id]['width'],
					'height'         => $wp_registered_widget_controls[$widget_id]['height'],
					'is_wide'        => $this->is_wide_widget( $widget_id ),
				) );
				$this->manager->add_control( $control );
			}
		}

		if ( ! $this->manager->doing_ajax( 'customize_save' ) ) {
			foreach ( $new_setting_ids as $new_setting_id ) {
				$this->manager->get_setting( $new_setting_id )->preview();
			}
		}

		add_filter( 'sidebars_widgets', array( $this, 'preview_sidebars_widgets' ), 1 );
	}

	/**
	 * Covert a widget_id into its corresponding Customizer setting ID (option name).
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $widget_id Widget ID.
	 * @return string Maybe-parsed widget ID.
	 */
	public function get_setting_id( $widget_id ) {
		$parsed_widget_id = $this->parse_widget_id( $widget_id );
		$setting_id       = sprintf( 'widget_%s', $parsed_widget_id['id_base'] );

		if ( ! is_null( $parsed_widget_id['number'] ) ) {
			$setting_id .= sprintf( '[%d]', $parsed_widget_id['number'] );
		}
		return $setting_id;
	}

	/**
	 * Determine whether the widget is considered "wide".
	 *
	 * Core widgets which may have controls wider than 250, but can
	 * still be shown in the narrow Customizer panel. The RSS and Text
	 * widgets in Core, for example, have widths of 400 and yet they
	 * still render fine in the Customizer panel. This method will
	 * return all Core widgets as being not wide, but this can be
	 * overridden with the is_wide_widget_in_customizer filter.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $widge