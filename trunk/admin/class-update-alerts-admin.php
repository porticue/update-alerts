<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/objects/Plugin.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://therevoltgroup.com
 * @since      1.0.0
 *
 * @package    Update_Alerts
 * @subpackage Update_Alerts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Update_Alerts
 * @subpackage Update_Alerts/admin
 * @author     Andrew Karetas <andrew@therevoltgroup.com>
 */
class Update_Alerts_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    private $option_name = 'update_alerts';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Update_Alerts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Update_Alerts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/update-alerts-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Update_Alerts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Update_Alerts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/update-alerts-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_options_page() {
        if ( is_multisite() ){
            $this->plugin_screen_hook_suffix = add_submenu_page(
                'settings.php',
                __( 'Update Alerts Settings', 'update-alerts' ),
                __( 'Update Alerts', 'update-alerts' ),
                'manage_network_options',
                $this->plugin_name,
                array( $this, 'display_options_page' )
            );
        }else{
            add_options_page(
                __( 'Update Alerts', 'update-alerts' ),
                __( 'Update Alerts', 'update-alerts' ),
                'manage_options',
                $this->plugin_name,
                array( $this, 'display_options_page' )
            );
        }
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_options_page() {

        include_once 'partials/update-alerts-admin-display.php';
        $this->update_ua_options();
    }

    /**
     * Register the settings section for the plugin
     *
     * @since 1.0.0
     */
    public function register_setting() {
        // Add a General section
        add_settings_section(
            $this->option_name . '_general',
            __( 'General', 'update-alerts' ),
            array( $this, $this->option_name . '_general_cb' ),
            $this->plugin_name
        );

        add_settings_field(
                $this->option_name . '_projectSlug',
                __( 'Site name identifier', 'update-alerts' ),
                array( $this, $this->option_name . '_projectSlug_cb' ),
                $this->plugin_name,
                $this->option_name . '_general',
                array( 'label_for' => $this->option_name . '_projectSlug' )
        );

        add_settings_field(
            $this->option_name . '_emailTo',
            __( 'Where to send alert', 'update-alerts' ),
            array( $this, $this->option_name . '_emailTo_cb' ),
            $this->plugin_name,
            $this->option_name . '_general',
            array( 'label_for' => $this->option_name . '_emailTo' )
        );

        add_settings_field(
                $this->option_name . '_secondEmail',
                __( 'Second email to send alert', 'update-alerts' ),
                array( $this, $this->option_name . '_secondEmail_cb' ),
                $this->plugin_name,
                $this->option_name . '_general',
                array( 'label_for' => $this->option_name . '_secondEmail' )
        );

        add_settings_field(
            $this->option_name . '_day',
            __( 'Add reminder to ticket after', 'update-alerts' ),
            array( $this, $this->option_name . '_day_cb' ),
            $this->plugin_name,
            $this->option_name . '_general',
            array( 'label_for' => $this->option_name . '_day' )
        );

    }

    public function update_ua_options(){

        if ( ! empty( $_POST ) && check_admin_referer( 'Update_UA_Options', 'update_alerts_nonce' ) ) {
            if(is_multisite()){
                if(!current_user_can('manage_network_options')) wp_die('FU');
            }else{
                if(!current_user_can('manage_options')) wp_die('FU');
            }

            update_site_option($this->option_name . '_projectSlug', sanitize_text_field( $_POST[$this->option_name . '_projectSlug']));
            update_site_option($this->option_name . '_emailTo', $this->update_alerts_sanitize_email($_POST[$this->option_name . '_emailTo']));
            update_site_option($this->option_name . '_secondEmail', $this->update_alerts_sanitize_email($_POST[$this->option_name . '_secondEmail']));
            update_site_option($this->option_name . '_day', intval($_POST[$this->option_name . '_day']));

            if(!is_multisite()){
                wp_redirect(add_query_arg(array('page' => 'update-alerts', 'updated' => 'true'), admin_url('options-general.php')));
            }
        }
        if( is_multisite() ){
            wp_redirect(add_query_arg(array('page' => 'update-alerts', 'updated' => 'true'), network_admin_url('settings.php')));
        }

        exit();
    }

    /**
     * Render the text for the general section
     *
     * @since  1.0.0
     */
    public function update_alerts_general_cb() {

        echo '<p>' . __( 'Required to have a valid email. Secondary email is optional. An email will be sent per plugin update. These emails can then be used to trigger ticket creations through 3rd party applications.', 'update-alerts' ) . '</p>';
    }

    /**
     * Render the text input for project slug to option
     *
     * @since  1.0.0
     */
    public function update_alerts_projectSlug_cb() {
        $projectSlug = get_site_option( $this->option_name . '_projectSlug' );
        echo '<input type="text" name="' . $this->option_name . '_projectSlug' . '" id="' . $this->option_name . '_projectSlug' . '" value="' . $projectSlug . '">';
        echo '<br/><span style="font-size: 8pt">*If using Flow to create Jira ticket this will be project repo name.</span>';
    }

    /**
     * Render the text input for email to option
     *
     * @since  1.0.0
     */
    public function update_alerts_emailTo_cb() {
        $emailTo = get_site_option( $this->option_name . '_emailTo' );
        echo '<input type="text" name="' . $this->option_name . '_emailTo' . '" id="' . $this->option_name . '_emailTo' . '" value="' . $emailTo . '">';
        echo '<br/><span style="font-size: 8pt">*Required.</span>';
    }

    /**
     * Render the text input for second email to option
     *
     * @since  1.0.0
     */
    public function update_alerts_secondEmail_cb() {
        $secondEmail = get_site_option( $this->option_name . '_secondEmail' );
        echo '<input type="text" name="' . $this->option_name . '_secondEmail' . '" id="' . $this->option_name . '_secondEmail' . '" value="' . $secondEmail . '">';
    }

    /**
     * Render the treshold day input for this plugin
     *
     * @since  1.0.0
     */
    public function update_alerts_day_cb() {
        $day = get_site_option( $this->option_name . '_day' );
        echo '<input type="text" name="' . $this->option_name . '_day' . '" id="' . $this->option_name . '_day' . '" value="' . $day . '"> ' . __( 'days', 'update-alerts' );
        echo '<br/><span style="font-size: 8pt">*If 0 days, reminder alerts will not be sent.</span>';
    }

    /**
     * Sanitize the email address before being saved to database
     *
     * @param  string $input $_POST value
     * @since  1.0.0
     * @return string           Sanitized value
     */
    public function update_alerts_sanitize_email( $input ) {
        $validated = sanitize_email( $input );
        return $validated;
    }

    /**
     * Called daily to check plugin status
     *
     * @since 1.0.0
     *
     */
    public function update_alerts_updatechecker() {
        global $wpdb;

        //Check for Wordpress Core Updates
        $update_core = get_site_transient( 'update_core' );
        if ( ! empty($update_core) && isset($update_core->updates) && is_array($update_core->updates)
            && isset($update_core->updates[0]->response) && 'upgrade' == $update_core->updates[0]->response) {
            $newversion = $update_core->updates[0]->current;
            $oldversion = $update_core->version_checked;
            $blogurl = esc_url( home_url() );
            $summary = 'Summary: Wordpress Core update available<br/>';
            $description = "It's time to update the version of WordPress running at $blogurl from version $oldversion to $newversion.<br/>";
            // don't let $wp_version mangling plugins mess this up
            if ( ! preg_match( '/^(\d+\.)?(\d+\.)?(\d+)$/', $oldversion ) ) {
                include( ABSPATH . WPINC . '/version.php' );
                $description = $wp_version == $newversion ? '' : "It's time to update the version of WordPress running at $blogurl from version $oldversion to $newversion.<br/>";
            }
            $data = array('plugin_name' => 'wordpress-core', 'current_version' => $oldversion, 'updated_version' => $newversion, 'date' => date("Y-m-d H:i:s"));
            $result = $this->getEntry('wordpress-core');
            $this->sendAlert($result, $data, $summary, $description);
        }
        //Check for Plugin Updates
        $update_plugins = get_site_transient( 'update_plugins' );
        if ( ! empty($update_plugins->response) ) {
            //
            $plugins_needupdate = $update_plugins->response;
            foreach ( $plugins_needupdate as $plugin ) {
                //get stored result for specific plugin
                //info included: plugin name, current installed version, available update version, date
                $result = $this->getEntry($plugin->slug);
                $summary = 'Summary: Plugin update available for ' . $plugin->slug . '<br />';
                $description = 'Description: ' . $plugin->slug . ' requires an update. the current installed version is ' . $update_plugins->checked[$plugin->plugin] . ', the new version is ' . $plugin->new_version . '. You can find more about this update at '. $plugin->url . '<br />';
                $data = array('plugin_name' => $plugin->slug, 'current_version' => $update_plugins->checked[$plugin->plugin], 'updated_version' => $plugin->new_version, 'date' => date("Y-m-d H:i:s"));
                $this->sendAlert($result, $data, $summary, $description);
            }
        }
        //Check for Theme Updates
        $update_themes = get_site_transient( 'update_themes' );
        if ( ! empty($update_themes->response) ) {
            $themes_needupdate = $update_themes->response;
            $active_themes = array();
            if(is_multisite()){
                $sites = wp_get_sites();
                foreach ($sites as $site) {
                    switch_to_blog($site['blog_id']);
                    array_push($active_themes, strtolower(get_option('current_theme')));
                    restore_current_blog();
                }
            }else{
                array_push($active_themes, strtolower(get_option('current_theme')));
            }
            foreach ( $themes_needupdate as $update_theme ) {
                //loop through each active theme (may be multiple if on multisite)
                //compare against themes that need updates
                foreach($active_themes as $theme){
                    $theme_name = $update_theme['theme'];
                    if (strpos($theme, $theme_name) !== false) {
                        //active theme needs an update. Run check if alert is needed
                        $result = $this->getEntry($theme);
                        $summary = 'Summary: Theme update available for ' . $theme . '<br />';
                        $description = 'Description: ' . $theme . ' requires an update. The new version is ' . $update_theme[''] . '. You can find more about this update at '. $update_theme['url'] . '<br />';
                        $data = array('plugin_name' => $theme_name, 'current_version' => $update_themes->checked[$theme_name], 'updated_version' => $update_theme['new_version'], 'date' => date("Y-m-d H:i:s"));
                        $this->sendAlert($result, $data, $summary, $description);
                    }
                }

            }
        }

    }

    private function sendAlert($result, $data, $summary, $description){
        global $wpdb;
        PC::debug("SendAlert");
        if(! empty($result)){


            if($data['current_version'] != $result['current_version'])
            {
                //plugin was updated since last check, but new version is now available. update database entry
                $wpdb->update($wpdb->prefix . 'update_alerts', $data, array( 'plugin_name' => $data['plugin_name'] ) );
                //send new alert
                wp_mail(get_site_option( $this->option_name . '_emailTo' ), 'Plugin Update: ' . get_site_option( $this->option_name . '_projectSlug' ), $summary . $description . 'End');
                if(get_site_option( $this->option_name . '_secondEmail') != ''){
                    wp_mail(get_site_option( $this->option_name . '_secondEmail' ), 'Plugin Update: ' . get_site_option( $this->option_name . '_projectSlug' ), $summary . $description . 'End');
                }

            }elseif ($data['updated_version'] != $result['updated_version'])
            {
                //plugin hasn't been updated since last check but the available updated version of the plugin has increased
                //don't send alert
                //update db entry
                $wpdb->update($wpdb->prefix . 'update_alerts', $data, array( 'plugin_name' => $data['plugin_name'] ) );
                //todo: update jira ticket to include new updated version
                wp_mail(get_site_option( $this->option_name . '_emailTo' ), 'Plugin Reminder: ' . get_site_option( $this->option_name . '_projectSlug' ), $data['plugin_name'] . " hasn't been updated and the updated version has increased to " . $data['updated_version']);
                if(get_site_option( $this->option_name . '_secondEmail') != ''){
                    wp_mail(get_site_option( $this->option_name . '_secondEmail' ), 'Plugin Reminder: ' . get_site_option( $this->option_name . '_projectSlug' ), $data['plugin_name'] . " hasn't been updated and the updated version has increased to " . $data['updated_version']);
                }
            }else {
                //Alert was already sent
                //don't send alert
                //todo:may want to append a bump to ticket depending on lapsed time
                //PC::debug("No new updates available");
            }
        }else{
            //There is a new plugin update, add database entry
            $wpdb->insert($wpdb->prefix . 'update_alerts', $data);
            //send alert
            wp_mail(get_site_option( $this->option_name . '_emailTo' ), 'Plugin Update: ' . get_site_option( $this->option_name . '_projectSlug' ), $summary . $description . 'End');
            if(get_site_option( $this->option_name . '_secondEmail') != ''){
                wp_mail(get_site_option( $this->option_name . '_secondEmail' ), 'Plugin Update: ' . get_site_option( $this->option_name . '_projectSlug' ), $summary . $description . 'End');
            }
        }
    }

    private function getEntry( $plugin_name ){
        global $wpdb;
        $query = "SELECT * FROM {$wpdb->prefix}update_alerts WHERE plugin_name = '$plugin_name'";

        $results = $wpdb->get_results( $query, ARRAY_A);
        if(!empty($results)){
            return $results[0];
        }
        return null;
    }

}
