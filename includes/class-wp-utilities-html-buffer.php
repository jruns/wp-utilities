<?php

/**
 * HTML Buffer for modifying html before its output
 *
 * @link       https://jruns.github.io/
 * @since      0.1.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/includes
 */

class Wp_Utilities_Html_Buffer {

	private $buffer;

	public function __construct() {
		add_action( 'init', array( $this, 'start_buffer' ) );
		add_action( 'shutdown', array( $this, 'end_buffer' ), 9 );
	}

	static function filter_buffer( $buffer ) {
		$buffer = apply_filters( 'wp_utilities_modify_final_output', $buffer );
		return $buffer;
	}

	public function start_buffer() {
		ob_start( array( 'self', 'filter_buffer' ) );
	}

	public function end_buffer() {
		$this->buffer = ob_get_clean();
	}

	/**
	 * Process buffer tag replacements
	 *
	 * @since    0.3.0
	 */
	public static function process_buffer_replacements( $buffer, $args ) {
		extract( $args );

		$moves_queue = array();
		$insert_delay_script = false;

		if ( ! empty( $exclusions ) ) {
			$exclusions = join( '|', $exclusions );
		} else {
			$exclusions = null;
		}

		foreach ( $match_settings as $ele ) {
			// skip element if it is missing required keys or has unallowed values
			if ( ! array_key_exists( 'match', $ele ) || ! array_key_exists( 'find', $ele ) || ! in_array( $ele['match'], $match_types ) ) {
				continue;
			}

			// Turn array search options into an OR string
			if ( is_array( $ele['find'] ) ) {
				$ele['find'] = join( "|", $ele['find'] );
			}
			$search_string = addcslashes( $ele['find'], '/()[]{}.,+*^$' );

			if ( 'code' === $ele['match'] ) {
				if ( 'script' === $tag_type ) {
					$regex_string = '/<script[^>]*?>(?!<\/[^>]*script[^>]*?)[\s\S]+?<\/[^>]*script[^>]*?>\n?/im';
				} else {
					$regex_string = '/<style[^>]*?>(?!<\/[^>]*script[^>]*?)[\s\S]+?<\/[^>]*style[^>]*?>\n?/im';
				}
			} else {
				$regex_base = $ele['match'] . '=[\\\'\"][^\\\'\"]*(' . $search_string . ')[^\\\'\"]*[\\\'\"]';
				
				if ( 'script' === $tag_type ) {
					$regex_string = '/<script[^>]*?' . $regex_base . '[^>]*?>[\s\S]*?<\/[^>]*script[^>]*>\n?/im';
				} else {
					$regex_string = '/<link[^>]*?' . $regex_base . '[^>]*?rel=[\\\'\"]stylesheet[\\\'\"][^>]*?>\n?|<link[^>]*?rel=[\\\'\"]stylesheet[\\\'\"][^>]*' . $regex_base . '[^>]*?>\n?|<style[^>]*?' . $regex_base . '[^>]*?>[\s\S]*?<\/[^>]*style[^>]*>\n?/im';
				}
			}

			$buffer = preg_replace_callback( 
				$regex_string, 
				function( $matches ) use( $operation, $tag_type, $search_string, $ele, $exclusions, &$moves_queue, &$insert_delay_script )  {
					$tag_contents = $matches[0];

					if ( 'code' === $ele['match'] ) {
						// Check if code section matches search settings. Skip tag if it doesn't.
						if ( 0 === preg_match( '/(' . $search_string . ')/im', $tag_contents ) ) {
							return $tag_contents;
						}
					}

					if ( $operation === 'delay' ) {
						if ( empty( $exclusions ) || 1 !== preg_match( '/<script[^>]*' . $exclusions . '[^>]*>/im', $tag_contents ) ) {
							if ( 0 === preg_match( '/<script[^>]* defer[^>]*>/im', $tag_contents ) ) {
								$tag_contents = str_replace( '<script', '<script defer', $tag_contents );
							}

							if ( array_key_exists( 'args', $ele ) && ! empty( $ele['args'] ) ) {
								if ( array_key_exists( 'operation', $ele['args'] ) && 'user_interaction' === $ele['args']['operation'] ) {
									// delay until user interaction
									if ( 'script' === $tag_type ) {
										$tag_contents = str_replace( 'src=', 'data-type="lazy" data-src=', $tag_contents );
										$insert_delay_script = true;
									}
								}
							}
						}
					} else {
						if ( $operation === 'move_to_footer' ) {
							$moves_queue[] = $tag_contents;
						}

						// Remove existing tag
						$tag_contents = '';
					}
					
					return $tag_contents;
				},
				$buffer
			);
		}

		// Add tags queued for movement to the footer.
		foreach( $moves_queue as $tag_to_move ) {
			$buffer = str_replace( '</body>', $tag_to_move . '</body>', $buffer );
		}

		// Add user interaction delay script if needed
		if ( $insert_delay_script ) {
			$autoLoadTimeout = 15000;
			$delay_script = '<script>
{
    const load = () => document.querySelectorAll("script[data-type=\'lazy\']").forEach(el => el.src = el.dataset.src);
    const timer = setTimeout(load, ' . $autoLoadTimeout . ');
    const trigger = () => {
        load();
        clearTimeout(timer);
    };
    ["mouseover","keydown","touchmove","touchstart"].forEach(e => window.addEventListener(e, trigger, {passive: true, once: true}));
}
</script>';

			$buffer = str_replace( '</body>', $delay_script . '</body>', $buffer );
		}

		return $buffer;
	}
}
