<?php

class Wp_Utilities_Preload_Images {

	private $settings;

	public static $needs_html_buffer = true;

	public function __construct() {
		$this->settings = array(
			'images'	=> array()
		);

		$this->settings = apply_filters( 'wp_utilities_images_to_preload', $this->settings ) ?? $this->settings;
	}

	public function process_images( $buffer ) {
		// Filter out settomgs that are not valid for the current page, based on conditional matches
		$this->settings['images'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['images'] );

		// Add to img tag to not lazy load image
		// data-no-lazy="1"

		// Process images to preload
		if ( ! empty( $this->settings['images'] ) ) {

			// Process specific urls to insert
			foreach( $this->settings['images'] as $image_setting ) {
				$preload_tag = '';

				if ( array_key_exists( 'args', $image_setting ) && array_key_exists( 'operation', $image_setting['args'] ) ) {
					if ( 'insert_url' === $image_setting['args']['operation'] ) {
						$preload_tag = "<link rel=\"preload\" href=\"{$image_setting['args']['url']}\" as=\"image\" fetchpriority=\"high\" />";
					}
				}

				if ( ! empty( $preload_tag ) ) {
					//$buffer = str_replace( '</head>', $preload_tag . PHP_EOL . '</head>', $buffer );
				}
			}

			// Process other matches
			/*$match_args = array(
				'tag_type'			=> 'script',
				'match_settings'	=> $this->settings['scripts'],
				'match_types'		=> array( 'id', 'src', 'code' ),
				'operation'			=> 'remove'
			);
			$buffer = Wp_Utilities_Html_Buffer::process_buffer_replacements( $buffer, $match_args );*/
		}

		return $buffer;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.1.0
	 */
	public function run() {
		// Iterate over scripts and styles to remove
		add_filter( 'wp_utilities_modify_final_output', array( $this, 'process_images' ), 9 );
	}
}
