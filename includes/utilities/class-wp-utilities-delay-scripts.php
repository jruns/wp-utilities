<?php

class Wp_Utilities_Delay_Scripts {

	private $settings;

	public static $needs_html_buffer = true;

	public const EXCLUSIONS = array( 'nodelay', 'nowprocket', 'data-pagespeed-no-defer' );

	public function __construct() {
		$this->settings = array(
			'scripts'	=> array()
		);

		$this->settings = apply_filters( 'wp_utilities_scripts_to_delay', $this->settings );
	}

	public function process_delays( $buffer ) {
		// Filter out delays that are not valid for the current page, based on conditional matches
		$this->settings['scripts'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['scripts'] );

		// Process delays
		if ( ! empty( $this->settings['scripts'] ) ) {
			// Process all script tags
			$match_args = array(
				'tag_type'			=> 'script',
				'match_settings'	=> $this->settings['scripts'],
				'match_types'		=> array( 'id', 'src', 'code' ),
				'operation'			=> 'delay'
			);

			$buffer = Wp_Utilities_Html_Buffer::process_buffer_replacements( $buffer, $match_args );
		}

		return $buffer;
	}

	/**
	 * Process tag replacements for HTML Buffer class
	 *
	 * @since    0.4.0
	 */
	public static function process_tag( $args, &$insert_delay_scripts ) {
		extract( $args );

		if ( empty( self::EXCLUSIONS ) || 1 !== preg_match( '/<script[^>]*' . join( '|', self::EXCLUSIONS ) . '[^>]*>/im', $tag_contents ) ) {
			if ( 0 === preg_match( '/<script[^>]* defer[^>]*>/im', $tag_contents ) ) {
				$tag_contents = str_replace( '<script', '<script defer', $tag_contents );
			}

			if ( array_key_exists( 'args', $ele ) && ! empty( $ele['args'] ) ) {
				if ( array_key_exists( 'operation', $ele['args'] ) ) {
					if ( 'user_interaction' === $ele['args']['operation'] ) {
						// delay until user interaction
						if ( 'script' === $tag_type ) {
							$tag_contents = str_replace( 'src=', 'data-type="lazy" data-src=', $tag_contents );
							$insert_delay_scripts['user_interaction'] = true;
						}
					} elseif ( 'page_loaded' === $ele['args']['operation'] ) {
						// delay until page loaded
						if ( 'script' === $tag_type ) {
							$delay_timeout = 0;
							if ( array_key_exists( 'delay', $ele['args'] ) && is_numeric( $ele['args']['delay'] ) ) {
								$delay_timeout = intval( sanitize_text_field( $ele['args']['delay'] ) );
							}

							if ( 1 === preg_match( '/<script[^>]*?src=[\\\'\"][^\\\'\"]*[\\\'\"][^>]*?>/im', $tag_contents ) ) {
								$tag_contents = str_replace( 'src=', 'data-type="page_loaded_delay" data-delay="' . $delay_timeout . '" data-src=', $tag_contents );
								$insert_delay_scripts['page_loaded'] = true;
							} else {
								$code_replacement = 'document.addEventListener(\'DOMContentLoaded\', () => { setTimeout(function () { ${2} }, ' . $delay_timeout . '); });';
								$tag_contents = preg_replace( '/(<script[^>]*?[^>]*?>)([\s\S]*?)(<\/[^>]*script[^>]*?>)/im', '${1}' . $code_replacement . '${3}', $tag_contents );
							}
						}
					}
				}
			}
		}

		return $tag_contents;
	}

	/**
	 * Return the necessary delay scripts
	 *
	 * @since    0.4.0
	 */
	public static function get_delay_scripts( $args ) {
		$output = '';

		if ( array_key_exists( 'user_interaction', $args ) && $args['user_interaction'] ) {
			$output .= self::get_user_interaction_delay_script() . PHP_EOL;
		}

		if ( array_key_exists( 'page_loaded', $args ) && $args['page_loaded'] ) {
			$output .= self::get_page_loaded_delay_script() . PHP_EOL;
		}

		return $output;
	}

	/**
	 * Return the user interaction delay script
	 *
	 * @since    0.4.0
	 */
	public static function get_user_interaction_delay_script() {
		$delay_var = 'wp_utilities_delay_scripts_autoload_delay';
		$delay_constant = strtoupper( $delay_var );

		if ( defined( $delay_constant ) && is_numeric( constant( $delay_constant ) ) ) {
			$autoLoadDelay = intval( constant( $delay_constant ) );
		} else {
			// get option, default to 15000 milliseconds if not set
			$autoLoadDelay = get_option( $delay_var );
			if ( empty( $autoLoadDelay ) ) {
				$autoLoadDelay = 15000;
			}
		}

		return '<script>const wputilAutoLoadDelay = ' . $autoLoadDelay . ';</script>' . PHP_EOL . 
			'<script defer>{const e=wputilAutoLoadDelay,t=["mouseover","keydown","touchmove","touchstart"],o=()=>{const e=new Event("DOMUserInteraction");document.dispatchEvent(e),console.log("interacted"),document.querySelectorAll("script[data-type=lazy]").forEach((e=>e.src=e.dataset.src)),t.forEach((e=>window.removeEventListener(e,n,{passive:!0,once:!0})))},c=setTimeout(o,e),n=()=>{o(),clearTimeout(c)};t.forEach((e=>window.addEventListener(e,n,{passive:!0,once:!0})))}</script>';
	}

	/**
	 * Return the page loaded delay script
	 *
	 * @since    0.4.0
	 */
	public static function get_page_loaded_delay_script() {
		return '<script defer>document.addEventListener("DOMContentLoaded",(()=>{document.querySelectorAll("script[data-type=page_loaded_delay]").forEach((e=>{setTimeout((function(){e.src=e.dataset.src}),e.dataset.delay)}))}));</script>';
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.4.0
	 */
	public function run() {
		// Iterate over scripts to delay
		add_filter( 'wp_utilities_modify_final_output', array( $this, 'process_delays' ), 9 );
	}
}
