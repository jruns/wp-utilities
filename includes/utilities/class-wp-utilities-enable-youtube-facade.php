<?php

class Wp_Utilities_Enable_Youtube_Facade {

	private $settings;

	public static $needs_html_buffer = true;

	public function __construct() {
	}

	public function process_youtube_iframes( $buffer ) {
		$youtube_iframe_count = 0;

		// Process all YouTube iframe tags
		$buffer = preg_replace_callback( 
			'/<iframe[^>]*?src=[\\\'\"]([^\\\'\"]*youtube\.com[^\\\'\"]*)[\\\'\"][^>]*?>[\s\S]*?<\/[^>]*iframe[^>]*>/im', 
			function( $matches ) {
				// Replace YouTube iframes with placeholder image (facade)
				$has_youtube_iframes = true;

				$original_iframe = $matches[0];

				$video_url = $matches[1];
				$video_url .= strpos( $video_url, '?' ) === false ? '?autoplay=1' : '&autoplay=1';

				preg_match( '/src=[\\\'\"][^\\\'\"]*youtube\.com\/embed\/([a-zA-Z0-9]+)[^\\\'\"]*[\\\'\"]/i', $matches[0], $video_id_matches );
				$video_id = $video_id_matches[1];

				preg_match( '/width=[\\\'\"]([^\\\'\"]*)[\\\'\"] height=[\\\'\"]([^\\\'\"]*)[\\\'\"]/i', $matches[0], $dimension_matches );
				$width = $dimension_matches[1];
				$height = $dimension_matches[2];

				$img_url = "https://img.youtube.com/vi/$video_id/hqdefault.jpg";

				return "<div class='wputil-youtube-embed wputil-youtube-embed-$video_id' data-src='$video_url' data-background-image='$img_url' data-video-id='$video_id' data-width='$width' data-height='$height' style=\"position: relative;cursor: pointer;max-width: ${width}px;height: ${height}px;background-image:url('$img_url');background-size: cover;background-position: center;\" title='Play'><div class='wputil-youtube-play'></div></div><noscript>$original_iframe</noscript>";
			},
			$buffer,
			-1,
			$youtube_iframe_count
		);

		if( $youtube_iframe_count > 0 ) {
			$buffer = str_replace( '</body>', $this->get_footer_code() . '</body>', $buffer );
		}

		return $buffer;
	}

	public function get_footer_code() {
		$footer_code = <<<END
<style id="wputil-youtube-facade-styles">
.wputil-youtube-play { 
	position: absolute;
	left: 50%;
	top: 50%;
	width: 62px;
	height: 48px;
	margin-left: -34px;
	margin-top: -24px;
	padding: 0;
	background: red;
	border-radius: 50% / 10%;
	color: #FFFFFF;
	text-align: center;
}

.wputil-youtube-play::before { 
	background: inherit;
	border-radius: 5% / 50%;
	bottom: 9%;
	content: "";
	left: -5%;
	position: absolute;
	right: -5%;
	top: 9%;
}

.wputil-youtube-play::after {
	border-style: solid;
	border-width: 1em 0 1em 1.732em;
	border-color: transparent transparent transparent rgba(255, 255, 255, 1);
	content: ' ';
	font-size: 0.64em;
	height: 0;
	margin: -1em 0 0 -0.75em;
	top: 50%;
	position: absolute;
	width: 0;
}
.wp-embed-responsive .wp-embed-aspect-16-9 .wp-block-embed__wrapper::before {
	padding-top: 0;
}
.wp-embed-responsive .wp-embed-aspect-16-9 .wp-block-embed__wrapper .wputil-youtube-embed {
  padding-top: 56.25%;
}

.wp-embed-responsive .wp-has-aspect-ratio .wputil-youtube-embed {
  bottom: 0;
  height: 100%;
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  width: 100% !important;
  max-width: 100% !important;
}
.wp-block-embed .wputil-youtube-embed {
  max-width: 100%;
}
</style>

<script id="wputil-youtube-facade-scripts">
document.addEventListener('DOMContentLoaded', function () {
    var youtubeFacades = document.querySelectorAll('.wputil-youtube-embed');
    youtubeFacades.forEach(function (facade) {
        facade.addEventListener('click', function () {
            var iframe = document.createElement('iframe');
            iframe.setAttribute('src', facade.dataset.src);
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('width', facade.dataset.width ?? 560);
            iframe.setAttribute('height', facade.dataset.height ?? 315);
            iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; web-share');
            facade.replaceChildren(iframe);
            iframe.focus();
        });
    });
});
</script>

END;
		return $footer_code;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.1.0
	 */
	public function run() {
		// Iterate over scripts and styles to remove
		add_filter( 'wp_utilities_modify_final_output', array( $this, 'process_youtube_iframes' ), 15 );
	}
}
