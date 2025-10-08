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
		// Filter out settings that are not valid for the current page, based on conditional matches
		$this->settings['images'] = Wp_Utilities_Conditional_Checks::filter_matches( $this->settings['images'] );

		$preload_tags = '';

		// Process images to preload
		if ( ! empty( $this->settings['images'] ) ) {

			// Process specific urls to insert
			foreach( $this->settings['images'] as $image_setting ) {
				
				if ( array_key_exists( 'args', $image_setting ) && array_key_exists( 'operation', $image_setting['args'] ) ) {
					if ( 'insert_url' === $image_setting['args']['operation'] ) {
						$media_query = '';
						if ( array_key_exists( 'media', $image_setting['args'] ) && ! empty( $image_setting['args']['media'] ) ) {
							$media_query = "media=\"{$image_setting['args']['media']}\" ";
						}

						$preload_tags = "<link rel=\"preload\" href=\"{$image_setting['args']['url']}\" as=\"image\" fetchpriority=\"high\" {$media_query}/>" . PHP_EOL . $preload_tags;
					}
				}
			}
		}

		if ( ! empty( $preload_tags ) ) {
			$buffer = str_replace( '</head>', $preload_tags . '</head>', $buffer );
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
