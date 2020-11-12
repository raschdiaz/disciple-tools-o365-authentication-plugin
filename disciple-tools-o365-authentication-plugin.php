<?php

/**
 * Plugin Name: Disciple Tools - 0365 Authentication
 * Plugin URI: https://github.com/HighDeveloper/disciple-tools-o365-authentication-plugin
 * Description: Disciple Tools - 0365 is a plugin created to allow the user to connect the Disciple Tools instance using
 * O365 credentials of their organization
 * Version:  0.1.0
 * Author URI: https://github.com/HighDeveloper
 * GitHub Plugin URI: https://github.com/HighDeveloper/disciple-tools-o365-authentication-plugin
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.4
 *
 * @package High_Developer
 * @link    https://github.com/HighDeveloper
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Refactoring (renaming) this plugin as your own:
 * 1. @todo Refactor all occurrences of the name DT_O365_Authentication, dt_o365_authentication, dt-o365-authentication, o365-authentication-plugin, o365-authentication-plugin, o365_post_type, and O365 Authentication Plugin
 * 2. @todo Rename the `disciple-tools-o365-authentication-plugin.php and menu-and-tabs.php files.
 * 3. @todo Update the README.md and LICENSE
 * 4. @todo Update the default.pot file if you intend to make your plugin multilingual. Use a tool like POEdit
 * 5. @todo Change the translation domain to in the phpcs.xml your plugin's domain: @todo
 * 6. @todo Replace the 'sample' namespace in this and the rest-api.php files
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$dt_o365_authentication_required_dt_theme_version = '0.28.0';

/**
 * Gets the instance of the `DT_O365_Authentication_Plugin` class.
 *
 * @since  0.1
 * @access public
 * @return object|bool
 */
function dt_o365_authentication_plugin()
{
    global $dt_o365_authentication_required_dt_theme_version;
    $wp_theme = wp_get_theme();
    $version = $wp_theme->version;

    /*
     * Check if the Disciple.Tools theme is loaded and is the latest required version
     */
    $is_theme_dt = strpos($wp_theme->get_template(), "disciple-tools-theme") !== false || $wp_theme->name === "Disciple Tools";
    if ($is_theme_dt && version_compare($version, $dt_o365_authentication_required_dt_theme_version, "<")) {
        add_action('admin_notices', 'dt_o365_authentication_plugin_hook_admin_notice');
        add_action('wp_ajax_dismissed_notice_handler', 'dt_hook_ajax_notice_handler');
        return false;
    }
    if (!$is_theme_dt) {
        return false;
    }
    /**
     * Load useful function from the theme
     */
    if (!defined('DT_FUNCTIONS_READY')) {
        require_once get_template_directory() . '/dt-core/global-functions.php';
    }
    /*
     * Don't load the plugin on every rest request. Only those with the 'sample' namespace
     */
    $is_rest = dt_is_rest();
    //@todo change 'sample' if you want the plugin to be set up when using rest api calls other than ones with the 'sample' namespace
    if (!$is_rest) {
        return DT_O365_Authentication_Plugin::get_instance();
    }
    // @todo remove this "else if", if not using rest-api.php
    else if (strpos(dt_get_url_path(), 'dt_o365_authentication_plugin') !== false) {
        return DT_O365_Authentication_Plugin::get_instance();
    }
    // @todo remove if not using a post type
    else if (strpos(dt_get_url_path(), 'o365_post_type') !== false) {
        return DT_O365_Authentication_Plugin::get_instance();
    }
}
add_action('after_setup_theme', 'dt_o365_authentication_plugin');

/**
 * Singleton class for setting up the plugin.
 *
 * @since  0.1
 * @access public
 */
class DT_O365_Authentication_Plugin
{

    /**
     * Declares public variables
     *
     * @since  0.1
     * @access public
     * @return object
     */
    public $token;
    public $version;
    public $dir_path = '';
    public $dir_uri = '';
    public $img_uri = '';
    public $includes_path;

    /**
     * Returns the instance.
     *
     * @since  0.1
     * @access public
     * @return object
     */
    public static function get_instance()
    {

        static $instance = null;

        if (is_null($instance)) {
            $instance = new dt_o365_authentication_plugin();
            $instance->setup();
            $instance->includes();
            $instance->setup_actions();
        }
        return $instance;
    }

    /**
     * Constructor method.
     *
     * @since  0.1
     * @access private
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Loads files needed by the plugin.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    private function includes()
    {
        if (is_admin()) {
            require_once 'includes/admin/admin-menu-and-tabs.php';
        }
    }

    /**
     * Sets up globals.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    private function setup()
    {

        // Main plugin directory path and URI.
        $this->dir_path = trailingslashit(plugin_dir_path(__FILE__));
        $this->dir_uri = trailingslashit(plugin_dir_url(__FILE__));

        // Plugin directory paths.
        $this->includes_path = trailingslashit($this->dir_path . 'includes');

        // Plugin directory URIs.
        $this->img_uri = trailingslashit($this->dir_uri . 'img');

        // Admin and settings variables
        $this->token = 'dt_o365_authentication_plugin';
        $this->version = '0.1';

        // sample rest api class
        require_once 'includes/rest-api.php';

        // sample post type class
        require_once 'includes/post-type.php';

        // custom site to site links
        require_once 'includes/custom-site-to-site-links.php';
    }

    /**
     * Sets up main plugin actions and filters.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    private function setup_actions()
    {

        if (is_admin()) {
            // Check for plugin updates
            if (!class_exists('Puc_v4_Factory')) {
                require get_template_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php';
            }
            /**
             * Below is the publicly hosted .json file that carries the version information. This file can be hosted
             * anywhere as long as it is publicly accessible. You can download the version file listed below and use it as
             * a template.
             * Also, see the instructions for version updating to understand the steps involved.
             * @see https://github.com/DiscipleTools/disciple-tools-version-control/wiki/How-to-Update-the-Starter-Plugin
             * @todo enable this section with your own hosted file
             * @todo An example of this file can be found in /includes/admin/disciple-tools-o365-authentication-plugin-version-control.json
             * @todo It is recommended to host this version control file outside the project itself. Github is a good option for delivering static json.
             */

            /***** @todo remove from here

        $hosted_json = "https://raw.githubusercontent.com/DiscipleTools/disciple-tools-o365-authentication-plugin/master/includes/admin/version-control.json"; // @todo change this url
        Puc_v4_Factory::buildUpdateChecker(
        $hosted_json,
        __FILE__,
        'disciple-tools-o365-authentication-plugin'
        );

         ********* @todo to here */
        }

        // Internationalize the text strings used.
        add_action('init', array($this, 'i18n'), 2);

        if (is_admin()) {
            // adds links to the plugin description area in the plugin admin list.
            add_filter('plugin_row_meta', [$this, 'plugin_description_links'], 10, 4);
        }
    }

    /**
     * Filters the array of row meta for each/specific plugin in the Plugins list table.
     * Appends additional links below each/specific plugin on the plugins page.
     *
     * @access  public
     * @param   array       $links_array            An array of the plugin's metadata
     * @param   string      $plugin_file_name       Path to the plugin file
     * @param   array       $plugin_data            An array of plugin data
     * @param   string      $status                 Status of the plugin
     * @return  array       $links_array
     */
    public function plugin_description_links($links_array, $plugin_file_name, $plugin_data, $status)
    {
        if (strpos($plugin_file_name, basename(__FILE__))) {
            // You can still use `array_unshift()` to add links at the beginning.

            $links_array[] = '<a href="https://disciple.tools">Disciple.Tools Community</a>'; // @todo replace with your links.

            // add other links here
        }

        return $links_array;
    }

    /**
     * Method that runs only when the plugin is activated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function activation()
    {

        // Confirm 'Administrator' has 'manage_dt' privilege. This is key in 'remote' configuration when
        // Disciple Tools theme is not installed, otherwise this will already have been installed by the Disciple Tools Theme
        $role = get_role('administrator');
        if (!empty($role)) {
            $role->add_cap('manage_dt'); // gives access to dt plugin options
        }

        $settings = [
            "client_id" => "",
			"client_secret" => "",
			"scopes" => "user.read"
        ];

        add_option('dt_o365_settings', json_encode($settings));
    }

    /**
     * Method that runs only when the plugin is deactivated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function deactivation()
    {
        delete_option('dismissed-dt-o365-authentication');
        delete_option('dt_o365_settings');
    }

    /**
     * Loads the translation files.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function i18n()
    {
        load_plugin_textdomain('dt_o365_authentication_plugin', false, trailingslashit(dirname(plugin_basename(__FILE__))) . 'languages');
    }

    /**
     * Magic method to output a string if trying to use the object as a string.
     *
     * @since  0.1
     * @access public
     * @return string
     */
    public function __toString()
    {
        return 'dt_o365_authentication_plugin';
    }

    /**
     * Magic method to keep the object from being cloned.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, 'Whoah, partner!', '0.1');
    }

    /**
     * Magic method to keep the object from being unserialized.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, 'Whoah, partner!', '0.1');
    }

    /**
     * Magic method to prevent a fatal error when calling a method that doesn't exist.
     *
     * @param string $method
     * @param array $args
     * @return null
     * @since  0.1
     * @access public
     */
    public function __call($method = '', $args = array())
    {
        _doing_it_wrong("dt_o365_authentication_plugin::" . esc_html($method), 'Method does not exist.', '0.1');
        unset($method, $args);
        return null;
    }
}
// end main plugin class

// Register activation hook.
register_activation_hook(__FILE__, ['DT_O365_Authentication_Plugin', 'activation']);
register_deactivation_hook(__FILE__, ['DT_O365_Authentication_Plugin', 'deactivation']);

function dt_o365_authentication_plugin_hook_admin_notice()
{
    global $dt_o365_authentication_required_dt_theme_version;
    $wp_theme = wp_get_theme();
    $current_version = $wp_theme->version;
    $message = __("'Disciple Tools - O365 Authentication Plugin' plugin requires 'Disciple Tools' theme to work. Please activate 'Disciple Tools' theme or make sure it is latest version.", "dt_o365_authentication_plugin");
    if ($wp_theme->get_template() === "disciple-tools-theme") {
        $message .= sprintf(esc_html__('Current Disciple Tools version: %1$s, required version: %2$s', 'dt_o365_authentication_plugin'), esc_html($current_version), esc_html($dt_o365_authentication_required_dt_theme_version));
    }
    // Check if it's been dismissed...
    if (!get_option('dismissed-dt-o365-authentication', false)) {?>
        <div class="notice notice-error notice-dt-o365-authentication is-dismissible" data-notice="dt-o365-authentication">
            <p><?php echo esc_html($message); ?></p>
        </div>
        <script>
            jQuery(function($) {
                $(document).on('click', '.notice-dt-o365-authentication .notice-dismiss', function() {
                    $.ajax(ajaxurl, {
                        type: 'POST',
                        data: {
                            action: 'dismissed_notice_handler',
                            type: 'dt-o365-authentication',
                            security: '<?php echo esc_html(wp_create_nonce('wp_rest_dismiss')) ?>'
                        }
                    })
                });
            });
        </script>
<?php }
}

/**
 * AJAX handler to store the state of dismissible notices.
 */
if (!function_exists("dt_hook_ajax_notice_handler")) {
    function dt_hook_ajax_notice_handler()
    {
        check_ajax_referer('wp_rest_dismiss', 'security');
        if (isset($_POST["type"])) {
            $type = sanitize_text_field(wp_unslash($_POST["type"]));
            update_option('dismissed-' . $type, true);
        }
    }
}
