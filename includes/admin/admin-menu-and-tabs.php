<?php

/**
 * DT_Third_Party_Authentication_Plugin_Menu class for the admin page
 *
 * @class       DT_Third_Party_Authentication_Plugin_Menu
 * @version     0.1.0
 * @since       0.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Initialize menu class
 */
DT_Third_Party_Authentication_Plugin_Menu::instance();

/**
 * Class DT_Third_Party_Authentication_Plugin_Menu
 */
class DT_Third_Party_Authentication_Plugin_Menu
{

    public $token = 'dt_third_party_authentication_plugin';

    private static $_instance = null;

    /**
     * DT_Third_Party_Authentication_Plugin_Menu Instance
     *
     * Ensures only one instance of DT_Third_Party_Authentication_Plugin_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_Third_Party_Authentication_Plugin_Menu instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct()
    {

        add_action("admin_menu", array($this, "register_menu"));
    } // End __construct()

    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu()
    {
        add_menu_page(__('Extensions (DT)', 'disciple_tools'), __('Extensions (DT)', 'disciple_tools'), 'manage_dt', 'dt_extensions', [$this, 'extensions_menu'], 'dashicons-admin-generic', 59);
        add_submenu_page('dt_extensions', __('Third Party Authentication Plugin', 'dt_third_party_authentication_plugin'), __('Third Party Authentication Plugin', 'dt_third_party_authentication_plugin'), 'manage_dt', $this->token, [$this, 'content']);
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu()
    {
    }

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content()
    {

        if (!current_user_can('manage_dt')) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die(esc_attr__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($_GET["tab"])) {
            $tab = sanitize_key(wp_unslash($_GET["tab"]));
        } else {
            $tab = 'microsoft-azure';
        }

        $link = 'admin.php?page=' . $this->token . '&tab=';

        ?>
		<div class="wrap">
			<h2><?php esc_attr_e('Third Party Authentication Plugin', 'dt_third_party_authentication_plugin')?></h2>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_attr($link) . 'microsoft-azure' ?>" class="nav-tab <?php echo esc_html(($tab == 'microsoft-azure' || !isset($tab)) ? 'nav-tab-active' : ''); ?>"><?php esc_attr_e('Microsoft Azure', 'dt_third_party_authentication_plugin')?></a>
				<!--a href="<?php //echo esc_attr( $link ) . 'second'
        ?>" class="nav-tab <?php //echo esc_html( ( $tab == 'second' || !isset( $tab ) ) ? 'nav-tab-active' : '' );
        ?>"><?php //esc_attr_e( 'Second', 'dt_third_party_authentication_plugin' )
        ?></a-->
			</h2>

			<?php
switch ($tab) {
            case "microsoft-azure":
                $object = new DT_O365_Authentication_Tab_Microsoft_Azure();
                $object->content();
                break;
            //case "second":
            //    $object = new DT_O365_Authentication_Tab_Second();
            //    $object->content();
            //    break;
            default:
                break;
        }
        ?>

		</div><!-- End wrap -->

	<?php
}
}

/**
 * Class DT_O365_Authentication_Tab_Microsoft_Azure
 */
class DT_O365_Authentication_Tab_Microsoft_Azure
{
    public function content()
    {

        // FORM SUBMIT -> UPDATE OPTIONS IN DATA BASE (BEFORE GET HIM IN VIEW)
        if (isset($_POST['submit'])) {

			$settings = json_decode(get_option('dt_o365_settings'));

			$settings->client_id = sanitize_text_field($_POST['clientId']);
            $settings->client_secret = sanitize_text_field($_POST['clientSecret']);
            $settings->logout_microsoft = sanitize_text_field($_POST['logoutMicrosoft']);
			
			update_option('dt_o365_settings', json_encode($settings));

		}
		
		// GET OPTIONS SAVED IN DATABASE
		$dtTpSettings = json_decode(get_option('dt_o365_settings'));

        ?>
		<div class="wrap">
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<form action="" method="post">
							<table class="widefat striped" style="width: 80%;">
								<thead>
									<tr>
										<th colspan="1">Settings</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<form action="" method="post">
												<table style="width: 100%">
													<tbody>
														<tr>
															<td style="vertical-align: middle; width: 25%;">
																<label for="tile-select">Application (client) ID (*)</label>
															</td>
															<td>
																<input type="text" name="clientId" required value="<?php echo ($dtTpSettings->client_id) ? $dtTpSettings->client_id : '' ?>" style="width: 100%">
															</td>
														</tr>
														<tr>
															<td style="vertical-align: middle; width: 25%;">
																<label for="tile-select">Client Secret Key (*)</label>
															</td>
															<td>
																<input type="text" name="clientSecret" required value="<?php echo ($dtTpSettings->client_secret) ? $dtTpSettings->client_secret : '' ?>" style="width: 100%">
															</td>
														</tr>
                                                        <tr>
															<td style="vertical-align: middle; width: 25%;">
																<label for="tile-select">Disconnect user's microsoft account on disconnect:</label>
															</td>
															<td>
                                                                <input type="radio" name="logoutMicrosoft" value="1" id="logoutMicrosoft1" <?php echo ($dtTpSettings->logout_microsoft == "1") ? 'checked="checked"' : '' ?>> <label for="logoutMicrosoft1">Yes</label></br>
                                                                <input type="radio" name="logoutMicrosoft" value="0" id="logoutMicrosoft0" <?php echo ($dtTpSettings->logout_microsoft == "0") ? 'checked="checked"' : '' ?>> <label for="logoutMicrosoft0">No</label>
															</td>
														</tr>
													</tbody>
												</table>
												<hr>
												<span style="float:right;">
													<button type="submit" name="submit" class="button float-right">Save</button>
												</span>
											</form>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>
				</div>
			</div>
		</div>
<?php
}
}