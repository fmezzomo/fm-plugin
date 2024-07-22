<?php

  final class fmPluginTranslations {

    static public function init() {
        add_action( 'plugins_loaded', __CLASS__ . '::load_fm_textdomain' );
    }

    static public function load_fm_textdomain() {

        load_plugin_textdomain(
            'fm-plugin',
            false,
            trailingslashit( FM_PLUGIN_DIR )  . 'languages/'
        );

        if ( function_exists( 'get_user_locale' ) ) {
			$locale = apply_filters( 'plugin_locale', get_user_locale(), 'fm-plugin' );
		} else {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'fm-plugin' );
		}

        load_textdomain(
            'fm-plugin',
            trailingslashit( FM_PLUGIN_DIR )  . 'languages/fm-plugin-' . $locale . '.mo'
        );
	}

    static public function getTranslation() {
        $translated_strings = array(
            'Emails' => __('Emails', 'fm-plugin'),
            'Table' => __('Table', 'fm-plugin'),
            'Graph' => __('Graph', 'fm-plugin'),
            'Settings' => __('Settings', 'fm-plugin'),
            'Top Pages' => __('Top Pages', 'fm-plugin'),
            'Loading graph data...' => __('Loading graph data...', 'fm-plugin'),
            'Graph Tab' => __('Graph Tab', 'fm-plugin'),
            'Settings Tab' => __('Settings Tab', 'fm-plugin'),
            'Number of Rows' => __('Number of Rows', 'fm-plugin'),
            'numberOfRowsAlert' => __('Number of Rows must be between 1 and 5', 'fm-plugin'),
            'Type the number of rows you want to show.' => __('Type the number of rows you want to show.', 'fm-plugin'),
            'Human date' => __('Human date', 'fm-plugin'),
            'Select to date column shows the date in human readable format than Unix timestamp.' => __('Select to date column shows the date in human readable format than Unix timestamp.', 'fm-plugin'),
            'Add Email' => __('Add Email', 'fm-plugin'),
            'Remove' => __('Remove', 'fm-plugin'),
            'Save Settings' => __('Save Settings', 'fm-plugin'),
            'Title' => __('Title', 'fm-plugin'),
            'Pageviews' => __('Pageviews', 'fm-plugin'),
            'Date' => __('Date', 'fm-plugin'),
            'insertValidEmail' => __('Insert valid email', 'fm-plugin'),
        );

        return $translated_strings;
    }
  }


fmPluginTranslations::init();