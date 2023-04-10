<?php

class Google_Analytics_Tag_Manager {

	private $plugin_name;
	private $version;

	public function __construct() {
		$this->plugin_name = 'google-analytics-tag-manager';
		$this->version     = '1.0';
	}

    public function run() {
        add_action( 'wp_head', array( $this, 'insert_tracking_code' ), 9999 );
        add_action( 'wp_body_open', array( $this, 'insert_gtm_body_code' ), 9999 );
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }
    

	public function enqueue_scripts() {
		// Add your front-end scripts here if needed.
	}

	public function enqueue_admin_scripts() {
        // For future updates if needed
		// wp_enqueue_style( $this->plugin_name, GATM_PLUGIN_URL . 'admin/css/google-analytics-tag-manager-admin.css', array(), $this->version, 'all' );
		// wp_enqueue_script( $this->plugin_name, GATM_PLUGIN_URL . 'admin/js/google-analytics-tag-manager-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function add_plugin_admin_menu() {
		add_options_page(
			'GA and GTM',
			'GA and GTM',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	public function register_plugin_settings() {
        register_setting( $this->plugin_name, 'gatm_google_analytics_code', 'string' );
        register_setting( $this->plugin_name, 'gatm_google_tag_manager_code', 'string' );

        add_settings_section(
            'gatm_general_settings',
            __( 'General Settings', 'google-analytics-tag-manager' ),
            null,
            $this->plugin_name
        );
        
        add_settings_field(
            'gatm_tracking_type',
            __( 'Tracking Type', 'google-analytics-tag-manager' ),
            array( $this, 'gatm_tracking_type_callback' ),
            $this->plugin_name,
            'gatm_general_settings'
        );
        
        add_settings_field(
            'gatm_tracking_code',
            __( 'Tracking Code', 'google-analytics-tag-manager' ),
            array( $this, 'gatm_tracking_code_callback' ),
            $this->plugin_name,
            'gatm_general_settings'
        );
        
	}

	public function display_plugin_admin_page() {
		require_once GATM_PLUGIN_DIR . 'admin/partials/google-analytics-tag-manager-admin-display.php';
	}

    public function insert_tracking_code() {
        $google_analytics_code = get_option( 'gatm_google_analytics_code' );
        $google_tag_manager_code = get_option( 'gatm_google_tag_manager_code' );
    
        if ( ! empty( $google_analytics_code ) ) {
            $google_analytics_code = preg_replace( '/<!--.*?-->\s*/s', '', $google_analytics_code );

            echo '<!-- Google Analytics added by Adjective Digital - Google Analytics and Google Tag Manager Plugin -->' . PHP_EOL;
            echo $google_analytics_code . PHP_EOL;
            echo '<!-- End Google Analytics code -->' . PHP_EOL;
        }
    
        if ( ! empty( $google_tag_manager_code ) ) {
            // Extract the <noscript> part of the Google Tag Manager code
            if ( preg_match( '/<noscript>.*?<\/noscript>/s', $google_tag_manager_code, $matches ) ) {
                $gtm_noscript_code = $matches[0];
                $google_tag_manager_code = str_replace( $gtm_noscript_code, '', $google_tag_manager_code );
            } else {
                $gtm_noscript_code = '';
            }

            $google_tag_manager_code = preg_replace( '/<!--.*?-->\s*/s', '', $google_analytics_code );

    
            echo '<!-- Google Tag Manager (head) added by Adjective Digital - Google Analytics and Google Tag Manager Plugin -->' . PHP_EOL;
            echo $google_tag_manager_code . PHP_EOL;
            echo '<!-- End Google Tag Manager (head) code -->' . PHP_EOL;
        }
    }

    public function insert_gtm_body_code() {
        $google_tag_manager_code = get_option( 'gatm_google_tag_manager_code' );
    
        if ( ! empty( $google_tag_manager_code ) ) {
            if ( preg_match( '/<noscript>.*?<\/noscript>/s', $google_tag_manager_code, $matches ) ) {
                $gtm_noscript_code = $matches[0];
                $gtm_noscript_code = preg_replace( '/<!--.*?-->\s*/s', '', $gtm_noscript_code );
                echo '<!-- Google Tag Manager (body) added by Adjective Digital - Google Analytics and Google Tag Manager Plugin -->' . PHP_EOL;
                echo $gtm_noscript_code . PHP_EOL;
                echo '<!-- End Google Tag Manager (body) code -->' . PHP_EOL;
            }
        }
    }

    public function detect_existing_tracking_codes() {
        $existing_tracking_codes = false;
    
        $google_analytics_pattern = '/(UA|G)-\d{4,10}-\d{1,4}/';
        $google_tag_manager_pattern = '/GTM-\w{4,10}/';
    
        // Check active plugins and theme files
        $existing_tracking_codes = $this->check_plugins_and_theme_files( $google_analytics_pattern, $google_tag_manager_pattern );
    
        // Check output of wp_head and wp_footer
        if ( ! $existing_tracking_codes ) {
            $existing_tracking_codes = $this->check_wp_head_and_footer( $google_analytics_pattern, $google_tag_manager_pattern );
        }
    
        return $existing_tracking_codes;
    }
    
    private function check_plugins_and_theme_files( $google_analytics_pattern, $google_tag_manager_pattern ) {
        // Check active plugins
        $active_plugins = get_option( 'active_plugins' );
        foreach ( $active_plugins as $plugin ) {
            if ( $plugin !== plugin_basename( __FILE__ ) ) {
                $plugin_directory = WP_PLUGIN_DIR . '/' . dirname( $plugin );
                $plugin_files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $plugin_directory ) );
                foreach ( $plugin_files as $file ) {
                    if ( $file->isFile() && $file->getExtension() == 'php' ) {
                        $file_content = file_get_contents( $file->getPathname() );
                        if ( preg_match( $google_analytics_pattern, $file_content ) || preg_match( $google_tag_manager_pattern, $file_content ) ) {
                            return true;
                        }
                    }
                }
            }
        }
    
        // Check theme files
        $theme_files = array( 'header.php', 'footer.php' );
        foreach ( $theme_files as $file ) {
            $file_path = get_template_directory() . '/' . $file;
    
            if ( file_exists( $file_path ) ) {
                $file_content = file_get_contents( $file_path );
    
                if ( preg_match( $google_analytics_pattern, $file_content ) || preg_match( $google_tag_manager_pattern, $file_content ) ) {
                    return true;
                }
            }
        }
    
        return false;
    }
    
    
    private function check_wp_head_and_footer( $google_analytics_pattern, $google_tag_manager_pattern ) {
        // Capture output of wp_head and wp_footer
        ob_start();
        do_action( 'wp_head' );
        $wp_head_output = ob_get_clean();
    
        ob_start();
        do_action( 'wp_footer' );
        $wp_footer_output = ob_get_clean();
    
        // Check for tracking codes in the captured output
        if ( preg_match( $google_analytics_pattern, $wp_head_output ) || preg_match( $google_tag_manager_pattern, $wp_head_output )
            || preg_match( $google_analytics_pattern, $wp_footer_output ) || preg_match( $google_tag_manager_pattern, $wp_footer_output ) ) {
            return true;
        }
    
        return false;
    }
    
    
    
}

