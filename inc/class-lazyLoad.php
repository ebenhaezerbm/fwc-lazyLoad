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

		add_action( "wp_footer", array($this, "remove_image_data_alt_attribute") );
	}

	/*
	 * Load assets
	 */
	public function fwc_lazyLoadXT_scripts() { 
		global $fwc_lazyLoadXT_plugin_uri;

		$plugin_dir = $fwc_lazyLoadXT_plugin_uri;

		wp_enqueue_script('jquery');
		
		wp_enqueue_script( 'lazyloadxt',  $plugin_dir . 'assets/js/jquery.lazyloadxt.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'lazyloadxt-extra',  $plugin_dir . 'assets/js/jquery.lazyloadxt.extra.js', array( 'jquery' ), '', true );
	}

	/*
	 * Filtering image size
	 */
	public function fwc_filtering_image_size($size) {
		$size = 'thumbnail';

		return $size;
	}

	/**
	 * Filter replace image 'src' Arrtibutes to 'data-src'
	 */
	public function modify_featured_image_src_attribute( $html, $post_id, $post_image_id ) {
		if ( is_feed() ) 
			return $content;

		$html = preg_replace( '#<img([^>]+?)class=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} class=${2} lazy-hidden ${3}>', $html );

		$html = preg_replace( '#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-src="${2}"${3}><noscript><img${1}src="${2}"${3}></noscript>', $html );

		return $html;
	}

	public function entry_content_image_src_filter( $content ) {
		if ( is_feed() ) 
			return $content;

		$content = preg_replace( '#<img([^>]+?)class=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} class=${2} lazy-hidden ${3}>', $content );

		$content = preg_replace( '#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-src="${2}"${3}>', $content );

		return $content;
	}

	/**
	 * Filter replace image 'alt' Arrtibutes to 'data-alt'
	 */
	public function modify_featured_image_alt_attribute( $html, $post_id, $post_image_id ) {
		if ( is_feed() ) 
			return $content;

		$html = preg_replace( '#<img([^>]+?)alt=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-alt=${2}${3}>', $html );

		return $html;
	}

	public function entry_content_image_alt_filter( $content ) {
		if ( is_feed() ) 
			return $content;

		$content = preg_replace( '#<img([^>]+?)alt=([\'")?([^\'"\s>]+)[\'"]?([^>]*)>#', '<img${1} data-alt=${2}${3}>', $content );

		return $content;
	}

	public function remove_image_data_alt_attribute() { ?>
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

	public function includes() {}
}

return new FWClazyLoad();

