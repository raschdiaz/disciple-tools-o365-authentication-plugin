<?php

/**
 * DT_O365_Authentication_Plugin_Menu class for the admin page
 *
 * @class       DT_O365_Authentication_Plugin_Menu
 * @version     0.1.0
 * @since       0.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Initialize menu class
 */
DT_O365_Authentication_Plugin_Menu::instance();

/**
 * Class DT_O365_Authentication_Plugin_Menu
 */
class DT_O365_Authentication_Plugin_Menu
{

    public $token = 'dt_o365_authentication_plugin';

    private static $_instance = null;

    /**
     * DT_O365_Authentication_Plugin_Menu Instance
     *
     * Ensures only one instance of DT_O365_Authentication_Plugin_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return DT_O365_Authentication_Plugin_Menu instance
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
        add_submenu_page('dt_extensions', __('O365 Authentication Plugin', 'dt_o365_authentication_plugin'), __('O365 Authentication Plugin', 'dt_o365_authentication_plugin'), 'manage_dt', $this->token, [$this, 'content']);
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
            $tab = 'general';
        }

        $link = 'admin.php?page=' . $this->token . '&tab=';

        ?>
		<div class="wrap">
			<h2><?php esc_attr_e('O365 Authentication Plugin', 'dt_o365_authentication_plugin')?></h2>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_attr($link) . 'general' ?>" class="nav-tab <?php echo esc_html(($tab == 'general' || !isset($tab)) ? 'nav-tab-active' : ''); ?>"><?php esc_attr_e('General', 'dt_o365_authentication_plugin')?></a>
				<!--a href="<?php //echo esc_attr( $link ) . 'second'
        ?>" class="nav-tab <?php //echo esc_html( ( $tab == 'second' || !isset( $tab ) ) ? 'nav-tab-active' : '' );
        ?>"><?php //esc_attr_e( 'Second', 'dt_o365_authentication_plugin' )
        ?></a-->
			</h2>

			<?php
switch ($tab) {
            case "general":
                $object = new DT_O365_Authentication_Tab_General();
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
 * Class DT_O365_Authentication_Tab_General
 */
class DT_O365_Authentication_Tab_General
{
    public function content()
    {

        // FORM SUBMIT -> UPDATE OPTIONS IN DATA BASE (BEFORE GET HIM IN VIEW)
        if (isset($_POST['submit'])) {

			$settings = json_decode(get_option('dt_o365_settings'));

			$settings->client_id = sanitize_text_field($_POST['clientId']);
			$settings->client_secret = sanitize_text_field($_POST['clientSecret']);
			
			update_option('dt_o365_settings', json_encode($settings));

		}
		
		// GET OPTIONS SAVED IN DATABASE
		$dtO365Settings = json_decode(get_option('dt_o365_settings'));

        ?>
		<div class="wrap">
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<form action="" method="post">
							<table class="widefat striped" style="width: 80%;">
								<thead>
									<tr>
										<th colspan="1">Microsoft Azure API Credentials</th>
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
																<input type="text" name="clientId" required value="<?php echo ($dtO365Settings->client_id) ? $dtO365Settings->client_id : '' ?>" style="width: 100%">
															</td>
														</tr>
														<tr>
															<td style="vertical-align: middle; width: 25%;">
																<label for="tile-select">Client Secret Key (*)</label>
															</td>
															<td>
																<input type="text" name="clientSecret" required value="<?php echo ($dtO365Settings->client_secret) ? $dtO365Settings->client_secret : '' ?>" style="width: 100%">
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

/**
 * Class DT_O365_Authentication_Tab_Second
 */
/*class DT_O365_Authentication_Tab_Second {
public function content() {
?>
<div class="wrap">
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content">
<!-- Main Column -->

<?php $this->main_column() ?>

<!-- End Main Column -->
</div><!-- end post-body-content -->
<div id="postbox-container-1" class="postbox-container">
<!-- Right Column -->

<?php $this->right_column() ?>

<!-- End Right Column -->
</div><!-- postbox-container 1 -->
<div id="postbox-container-2" class="postbox-container">
</div><!-- postbox-container 2 -->
</div><!-- post-body meta box container -->
</div><!--poststuff end -->
</div><!-- wrap end -->
<?php
}

public function main_column() {
?>
<!-- Box -->
<table class="widefat striped">
<thead>
<tr>
<th>Header</th>
</tr>
</thead>
<tbody>
<tr>
<td>
Content
</td>
</tr>
</tbody>
</table>
<br>
<!-- End Box -->
<?php
}

public function right_column() {
?>
<!-- Box -->
<table class="widefat striped">
<thead>
<tr>
<th>Information</th>
</tr>
</thead>
<tbody>
<tr>
<td>
Content
</td>
</tr>
</tbody>
</table>
<br>
<!-- End Box -->
<?php
}
}*/