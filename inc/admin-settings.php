<?php 
/**
 * FWC_LazyLoadXT_Settings Class.
 *
 * @class       FWC_LazyLoadXT_Settings
 * @version		1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'FWC_LAZYLOADXT_SLUG', 'laxyloadxt' ); 

class FWC_LazyLoadXT_Settings { 
	 /**
	 * Singleton method
	 *
	 * @return self
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new FWC_LazyLoadXT_Settings();
		}

		return $instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		global $fwc_lazyLoadXT_plugin_basename;

		add_filter( "plugin_action_links_$fwc_lazyLoadXT_plugin_basename", array($this, "plugin_setting_link") );

		add_action( "admin_menu", array($this, "admin_menu") );
		add_action( "admin_notices", array($this, "admin_notices") );
	}

	/**
	 * Add setting button on plugin actions
	 */
	public function plugin_setting_link($links) { 
		$settings_link = '<a href="options-general.php?page='.FWC_LAZYLOADXT_SLUG.'">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function admin_menu() { 
		add_options_page( __('LazyLoadXT'), __('LazyLoadXT'), 'manage_options', FWC_LAZYLOADXT_SLUG, array($this, 'admin_page_handler') );
	}

	public function admin_page_handler() { 
		if ( isset($_POST['save_settings']) ) 
			$this->update_settings();

		$settings 	= get_option( 'fwc_lazyloadxt' ); 

		$disable_on_desktop = ( isset($settings['disable_on_desktop']) && ! empty($settings['disable_on_desktop']) ) ? true : false;
		$disable_on_mobile = ( isset($settings['disable_on_mobile']) && ! empty($settings['disable_on_mobile']) ) ? true : false;

		?>
		<div class="wrap">
			<h1><?php _e( 'LazyLoadXT', 'fwc' ); ?></h1>

			<form method="post" role="form" action="" autocomplete="off">
				<?php wp_nonce_field( 'fwc-lazyload', 'nonce' ); ?>

				<table class="form-table">
					<tbody>
						<tr>
							<th><label for=""><?php _e( 'Disable on Desktop View', 'fwc' ); ?></label></th>
							<td>
								<input type="checkbox" name="fwc_lazyloadxt[disable_on_desktop]" class="" value="<?php echo $disable_on_desktop; ?>" <?php checked( true, $disable_on_desktop, true ); ?>>
							</td>
						</tr>
						<tr>
							<th><label for=""><?php _e( 'Disable on Mobile View', 'fwc' ); ?></label></th>
							<td>
								<input type="checkbox" name="fwc_lazyloadxt[disable_on_mobile]" class="" value="<?php echo $disable_on_mobile; ?>" <?php checked( true, $disable_on_mobile, true ); ?>>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Changes' ), 'primary left', 'save_settings', false ); ?>
			</form>
		</div>
		<?php 
	}

	public function update_settings() { 
		global $wpdb, $pagenow;

		if ( $pagenow != 'options-general.php' ) 
			return;

		if ( ! isset($_GET['page']) || FWC_LAZYLOADXT_SLUG != $_GET['page'] ) 
			return;

		if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'fwc-lazyload') ) 
			return;

		$settings = get_option( 'fwc_lazyloadxt' );

		$disable_on_desktop = isset($_POST['fwc_lazyloadxt']['disable_on_desktop']) ? true : false;
		$disable_on_mobile = isset($_POST['fwc_lazyloadxt']['disable_on_mobile']) ? true : false;

		$settings['disable_on_desktop'] = $disable_on_desktop;
		$settings['disable_on_mobile'] 	= $disable_on_mobile;

		update_option( 'fwc_lazyloadxt', $settings, 'no' );

		$redirect = add_query_arg( 
			array( 
				'page' 		=> FWC_LAZYLOADXT_SLUG, 
				'update' 	=> true 
			), admin_url( 'options-general.php' ) );

		wp_redirect( $redirect, 301 );
		exit();
	}

	public function admin_notices() { 
		global $pagenow;

		if ( $pagenow != 'options-general.php' ) 
			return;

		if ( ! isset($_GET['page']) || FWC_LAZYLOADXT_SLUG != $_GET['page'] ) 
			return;

		if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'fwc-lazyload') ) 
			return;

		if ( isset($_GET['update']) && 1 == intval($_GET['update']) ) { ?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'Settings Saved.', 'fwc' ); ?></p>
			</div>
			<?php 
		}
	}
}

FWC_LazyLoadXT_Settings::init();
