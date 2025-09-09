# wp-utilities
WP Performance Utilities is a WordPress plugin to improve the performance of a WordPress website.

## Available Utilities

### Disable jQuery Migrate
This utility will remove the jQuery Migrate javascript file from the frontend. It does not remove it from the admin section.  

Add this to your wp-config.php file:
```php
define( 'WP_UTILITIES_DISABLE_JQUERY_MIGRATE', true );
```

Or activate the utility on the WP Performance Utilities wp-admin options page.

### Enable YouTube Facade
This utility will replace YouTube iframes with an image placeholder (facade) on the frontend. This delays initializing the video until the user clicks on the placeholder image, at which time the video will load and autoplay. The image placeholder is lazy loaded by default. And if the user has javascript disabled, clicking on the image will open the video in a new tab.

Add this to your wp-config.php file:
```php
define( 'WP_UTILITIES_ENABLE_YOUTUBE_FACADE', true );
```

Or activate the utility on the WP Performance Utilities wp-admin options page.

### Remove Scripts and Styles
This utility will remove specified javascript scripts and css styles from the frontend. It does not remove them from the admin section.  

This is a work in progress.

Add this to your wp-config.php file:  
```php
define( 'WP_UTILITIES_REMOVE_SCRIPTS_AND_STYLES', true );
```

Then specify scripts and styles to be removed with the `wp_utilities_scripts_and_styles_to_remove` filter by adding something like the following to your functions.php file:  
```php
function remove_scripts_and_styles( $settings ) {
    $settings['scripts'][] = array ( 
        'id'        => 'ez-toc',
        'match'     => 'not_is_page'
    );
    $settings['scripts'][] = array ( 
        'src'       => 'js/smooth_scroll.min.js',
        'match'     => 'path_sample-page'
    );
    $settings['scripts'][] = array ( 
        'code'      => 'eztoc_smooth_local',
        'match'     => 'is_front_page'
    );

    $settings['styles'][] = array ( 
        'id'        => 'kadence-header',
        'match'     => 'is_page'
    );
    $settings['styles'][] = array ( 
        'href'      => '/kadence/assets/css/content.min.css',
        'match'     => 'not_is_page'
    );
    $settings['styles'][] = array ( 
        'code'      => '.tablepress thead',
        'match'     => 'not_is_page'
    );

    return $settings;
}
add_filter( 'wp_utilities_scripts_and_styles_to_remove', 'remove_scripts_and_styles', 10, 1 );
```

## To Do
1. Add support for matching conditionals when modifying a script or style
2. Simplify code that removes script or style tags when configured conditionals match. Use reusable functions for common commands.
3. Remove script by handle, source code, or filename, and by conditional or page parameters (slug, root folder, child folder)
4. Remove stylesheet by handle, inline code, or filename, and by conditional or page parameters (slug, root folder, child folder)
5. Move script or stylesheet to footer by handle, inline code, or filename.
6. Delay script by handle or filename, and by conditional or page parameters (slug, root folder, child folder)
7. Delay stylesheet by handle or filename, and by conditional or page parameters (slug, root folder, child folder)
8. Cleanup plugin code, restructure code, and remove unneeded code.