<?php

if ( ! class_exists( 'FmPlugin' ) ) {

  final class FmPlugin {

    static private $translated_strings = [];

		/**
		 * Load the Plugin
		 *
		 * @return void
		 */
		static public function init() {
      self::define_constants();
      self::load_classes();
      
      add_action( 'init', __CLASS__ . '::init_hooks');

      add_action('rest_api_init', function () {
        // Get Data endpoint
        register_rest_route('fm-plugin/v1', '/data', array(
          'methods' => 'GET',
          'callback' => __CLASS__ . '::fm_plugin_get_data',
          'permission_callback' => '__return_true'
        ));

        // Update Settings endpoint
        register_rest_route('fm-plugin/v1', '/settings', array(
          'methods' => 'POST',
          'callback' => __CLASS__ . '::fm_plugin_update_settings',
          'permission_callback' => '__return_true',
        ));
    
        // Get All Settings endpoint
        register_rest_route('fm-plugin/v1', '/all-settings', array(
          'methods' => 'GET',
          'callback' => __CLASS__ . '::fm_plugin_get_all_settings',
          'permission_callback' => '__return_true',
        ));
      });

      add_action( 'plugins_loaded', __CLASS__ . '::load_fm_textdomain' );
    }

    static public function init_hooks() {
      if ( ! is_admin() ) {
        return;
      }
  
      add_action( 'admin_menu', __CLASS__ . '::fm_plugin_add_menu' );
      add_action( 'admin_enqueue_scripts', __CLASS__ . '::fm_plugin_enqueue_scripts' );
    }

    // Callback function for the data REST route
    static public function fm_plugin_get_data() {
      // Check if the data is cached
      $cached_data = get_transient('fm_plugin_data');

      if (false === $cached_data) {
          // Fetch data from the external API
          $response = wp_safe_remote_get('https://miusage.com/v1/challenge/2/static/');
          $body = wp_remote_retrieve_body($response);
          $data = json_decode($body, true);

          if (is_array($data) && isset($data['graph']) && isset($data['table']['data']['rows'])) {
              // Cache the data for one hour
              set_transient('fm_plugin_data', $data, HOUR_IN_SECONDS);
          } else {
              // Handle API error
              return new WP_Error('api_error', 'Failed to fetch data from API', array('status' => 500));
          }
      } else {
          $data = $cached_data;
      }

      // Prepare the response data
    // $table_data = isset($data['table']['data']['rows']) ? $data['table']['data']['rows'] : array();
      $table_data = isset($data['table']) ? $data['table'] : array();
      $graph_data = isset($data['graph']) ? $data['graph'] : array();

      // Return the data in the REST response
      return array(
          'table' => $table_data,
          'graph' => $graph_data,
          'admin_email' => $graph_data,
      );
    }


    // Callback function for the Update Settings endpoint
    static public function fm_plugin_update_settings($request) {

      // Verify that the user is an administrator
      if ( current_user_can( 'activate_plugins' ) ) {
        return rest_ensure_response(array('error' => 'Permission denied'));
      }

      // Check if the request data is valid
      $data = $request->get_json_params();
      if (!$data || !is_array($data)) {
          return new WP_Error('invalid_data', 'Invalid data.', array('status' => 400));
      }

      // Sanitize and validate the data
      $numRows = absint($data['numRows']);
      $showHumanDate = isset($data['showHumanDate']) && $data['showHumanDate'] == true ? true : false;
      $emails = isset($data['emails']) && is_array($data['emails']) ? array_map('sanitize_email', $data['emails']) : array();

      $dataSubmit = [
        'numRows' => $numRows,
        'showHumanDate' => $showHumanDate,
        'emails' => $emails
      ];
      
      if(update_option('fm_plugin_settings', $dataSubmit)) {
        return array(
                    'message' => __('Settings updated successfully.', 'fm-plugin'),
                    'success' => true
                );
      }
      
      return array(
                  'message' => __('Failed to save!', 'fm-plugin'),
                  'success' => false
              );
    }


    // Callback function for the Get All Settings endpoint
    static public function fm_plugin_get_all_settings() {
      // Get the current user ID
      $user_id = get_current_user_id();

      // Verify that the user is an administrator
      //if (!current_user_can('activate_plugins', $user_id)) {
      if(!is_admin()) {
        //return rest_ensure_response(array('error' => 'Permission denied'));
      }

      // Get the saved settings from the database
      $settings = get_option('fm_plugin_settings');

      // If settings are not found or empty, set default values
      if (empty($settings)) {
        $settings = array(
          'numRows' => 5,
          'showHumanDate' => true,
          'emails' => array(get_option('admin_email')),
        );
      }

      return rest_ensure_response($settings);
    }

    static public function fm_plugin_enqueue_scripts() {
      if (is_admin()) {
        
        wp_enqueue_script('fm-plugin-app', plugins_url('../dist/app.js', __FILE__), array(), '1.0.0', true);

        // Pass data from PHP to the Vue app
        $data_to_pass = array(
          'api_url' => esc_url_raw(rest_url('fm-plugin/v1')),
          'admin_email' => get_option("admin_email"),
          'fmPluginTranslations' => self::$translated_strings,
        );
        
        wp_localize_script('fm-plugin-app', 'fmPluginData', $data_to_pass);
      }
    }
    
    static public function fm_plugin_add_menu() {
        // Parameters for add_menu_page function:
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        // $page_title: The text to be displayed in the title tags of the page when the menu is selected.
        // $menu_title: The text to be used for the menu.
        // $capability: The capability required to access this menu.
        // $menu_slug: The slug name to refer to this menu.
        // $function: The function to be called to output the content for this page.
        // $icon_url: The URL to the icon to be used for this menu.
        // $position: The position in the menu order this menu should appear.

        if (is_admin()) {
            add_menu_page(
                'Fabio Mezzomo Plugin',
                'Fabio Mezzomo Plugin',
                'manage_options',
                'fm-plugin-settings',
                __CLASS__ . '::fm_plugin_settings_page',
                'dashicons-admin-generic',
                99
            );
        }
    }

    static private function define_constants() {

      define( 'FM_PLUGIN_FILE', trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'fm-plugin.php' );

      define( 'FM_PLUGIN_VERSION', '1.0.0' );

      define( 'FM_PLUGIN_DIR', plugin_dir_path( FM_PLUGIN_FILE ) );
      define( 'FM_PLUGIN_URL', plugins_url( '/', FM_PLUGIN_FILE ) );
    }

    static private function load_classes() {
      /* Classes */
        require_once FM_PLUGIN_DIR . 'classes/class-fm-plugin-translation.php';
    }

    static public function load_fm_textdomain() {
        $fmTranslation = new fmPluginTranslations();
        self::$translated_strings = $fmTranslation::getTranslation();
    }

    // The function to output content for the plugin settings page
    static public function fm_plugin_settings_page() {
      include plugin_dir_path(__FILE__) . '../main/index.html';
    }
  }
}

FmPlugin::init();