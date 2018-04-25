<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FWC Image lazyLoad.
 */
class FWClazyLoad
{
	/* constructor */
	function __construct() {
		$this->init();

		$this->includes();
	}

	public function init() {
		add_action( "wp_enqueue_scripts", array($this, "fwc_lazyLoadXT_scripts"), 9 );

		// add_filter( "post_thumbnail_size", array($this, "fwc_filtering_image_size"), 99, 1 );

		add_filter( "post_thumbnail_html", array($this, "modify_featured_image_src_attribute"), 10, 3 );
		add_filter( "the_content", array($this, "entry_content_image_src_filter") );

		add_filter( "post_thumbnail_html", array($this, "modify_featured_image_alt_attribute"), 10, 3 );
		add_filter( "the_content", array($this, "entry_content_image_alt_filter") );

		add_action( "wp_head", array($this, "print_inline_style_for_lazyLoadXT") );
		add_action( "wp_footer", array($this, "write_image_alt_attribute") );

		// add_filter( "instant_articles_transformer_rules_configuration_json_file_path", array($this, "load_custom_rules_configuration_json_file_path") );
	}

	/**
	 * Load assets
	 */
	public function fwc_lazyLoadXT_scripts() { 
		global $fwc_lazyLoadXT_plugin_uri;

		$plugin_dir = $fwc_lazyLoadXT_plugin_uri;

		wp_enqueue_script('jquery');

		wp_enqueue_style( 'lazyloadxt-min-spinner-min-css', $plugin_dir . 'assets/css/jquery.lazyloadxt.spinner.min.css', array(), false );

		wp_enqueue_script( 'lazyloadxt',  $plugin_dir . 'assets/js/jquery.lazyloadxt.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'lazyloadxt-extra',  $plugin_dir . 'assets/js/jquery.lazyloadxt.extra.js', array( 'jquery' ), '', true );

		wp_enqueue_style( 'fwc-lazyload', $plugin_dir . 'assets/css/style.css', array(), false );
		wp_enqueue_script( 'fwc-lazyload',  $plugin_dir . 'assets/js/scripts.js', array( 'jquery' ), '', true );
	}

	/**
	 * Filtering image size
	 */
	public function fwc_filtering_image_size($size) {
		if ( is_admin() ) 
			return;

		if ( is_feed() ) 
			return;
		
		$size = 'thumbnail';

		return $size;
	}

	/**
	 * Filter replace image 'src' Arrtibutes to 'data-src'
	 */
	public function modify_featured_image_src_attribute( $html, $post_id, $post_image_id ) {
		if ( is_admin() ) 
			return $html;

		if ( is_feed() ) 
			return $html;

		$html = preg_replace( '#<img([^>]+?)class=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} class=${2} lazy-hidden ${3}>', $html );

		$html = preg_replace( '#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-src="${2}"${3}><noscript><img${1}src="${2}"${3}></noscript>', $html );

		return $html;
	}

	public function entry_content_image_src_filter( $content ) {
		if ( is_admin() ) 
			return $content;

		if ( is_feed() ) 
			return $content;

		// do not save if this is an ajax routine
		if ( defined('DOING_AJAX') && DOING_AJAX ) 
			return $content;

		$content = preg_replace( '#<img([^>]+?)class=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} class=${2} lazy-hidden ${3}>', $content );

		$content = preg_replace( '#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-src="${2}"${3}>', $content );

		return $content;
	}

	/**
	 * Filter replace image 'alt' Arrtibutes to 'data-alt'
	 */
	public function modify_featured_image_alt_attribute( $html, $post_id, $post_image_id ) {
		if ( is_admin() ) 
			return $html;

		if ( is_feed() ) 
			return $html;

		$html = preg_replace( '#<img([^>]+?)alt=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-alt=${2}${3}>', $html );

		return $html;
	}

	public function entry_content_image_alt_filter( $content ) {
		if ( is_admin() ) 
			return $content;

		if ( is_feed() ) 
			return $content;

		// do not save if this is an ajax routine
		if ( defined('DOING_AJAX') && DOING_AJAX ) 
			return $content;

		$content = preg_replace( '#<img([^>]+?)alt=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-alt=${2}${3}>', $content );

		return $content;
	}

	public function print_inline_style_for_lazyLoadXT() { 
		if ( is_admin() ) 
			return;

		if ( is_feed() ) 
			return;

		?>
		<script>
			(function($){
				if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
					var css = 'img.lazy-hidden { opacity: 1 !important; }', 
						head = document.head || document.getElementsByTagName('head')[0], 
						style = document.createElement('style');

					style.type = 'text/css';
					if ( style.styleSheet ) {
						style.styleSheet.cssText = css;
					} else {
						style.appendChild(document.createTextNode(css));
					}

					head.appendChild(style);
				}
			}(jQuery));
		</script>
		<?php 
	}
	
	public function write_image_alt_attribute() { 
		if ( is_admin() ) 
			return;

		if ( is_feed() ) 
			return;
		
		?>
		<script>
			(function($){
				$(document).ready(function(){		
					$('img').each(function(){
						var alt = $(this).attr('data-alt');
						
						if ( alt ) {
							alt = alt;
						} else {
							alt = '';
						}

						$(this).attr( 'alt', alt );
					});
				});
			}(jQuery));
		</script>
		<?php 
	}

	public function load_custom_rules_configuration_json_file_path($file_path) {
		global $fwc_lazyLoadXT_plugin_uri;

		if ( is_admin() ) { 
			return $file_path;
		}

		if ( is_feed() ) { 
			return $file_path;
		}

		$plugin_dir = $fwc_lazyLoadXT_plugin_uri;

		$file_path = $fwc_lazyLoadXT_plugin_uri . 'inc/custom-ia-rules.configuration.json';

		return $file_path;
	}

	public function includes() {}
}

function FWClazyLoad_init() { 
	return new FWClazyLoad();
}

add_action( "init", "fwc_lazyloadxt_init" );
function fwc_lazyloadxt_init() { 
	$settings = get_option( 'fwc_lazyloadxt' );

	if ( wp_is_mobile() ) { 
		if ( ! isset($settings['disable_on_mobile']) || empty($settings['disable_on_mobile']) ) { 
			FWClazyLoad_init();
		}
	} else { 
		if ( ! isset($settings['disable_on_desktop']) || empty($settings['disable_on_desktop']) ) { 
			FWClazyLoad_init();
		}
	}
}
