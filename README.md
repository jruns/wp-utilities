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
This utility will replace YouTube iframes with an image placeholder (facade) on any frontend posts or pages. This delays initializing the video until the user clicks on the placeholder image, at which time the video will load and autoplay. The image placeholder is lazy loaded by default. And if the user has javascript disabled, clicking on the image will open the video in a new tab.

Add this to your wp-config.php file:
```php
define( 'WP_UTILITIES_ENABLE_YOUTUBE_FACADE', true );
```

Or activate the utility on the WP Performance Utilities wp-admin options page.

### Move Scripts and Styles to Footer
This utility will move specified javascript scripts and css styles to the page footer on the frontend. It does not move them in the admin section.  

Add this to your wp-config.php file:  
```php
define( 'WP_UTILITIES_MOVE_SCRIPTS_AND_STYLES_TO_FOOTER', true );
```

Then specify scripts and styles to be moved with the `wp_utilities_scripts_and_styles_to_move_to_footer` filter by adding something like the following to your functions.php file:  
```php
function move_scripts_and_styles_to_footer( $settings ) {
    $settings['scripts'][] = array ( 
        'match'     => 'id',
        'find'      => 'ez-toc',
        'where'     => 'not_is_page'
    );
    $settings['scripts'][] = array ( 
        'match'     => 'src',
        'find'      => 'js/smooth_scroll.min.js',
        'where'     => 'path_sample-page'
    );
    $settings['scripts'][] = array ( 
        'match'     => 'code',
        'find'      => 'eztoc_smooth_local',
        'where'     => 'is_front_page'
    );

    $settings['styles'][] = array ( 
        'match'     => 'id',
        'find'      => array( 'kadence-header', 'wp-block-library' ),
        'where'     => 'is_page'
    );
    $settings['styles'][] = array ( 
        'match'     => 'href',
        'find'      => '/kadence/assets/css/content.min.css',
        'where'     => 'not_is_page'
    );
    $settings['styles'][] = array ( 
        'match'     => 'code',
        'find'      => '.tablepress thead',
        'where'     => 'not_is_page'
    );

    return $settings;
}
add_filter( 'wp_utilities_scripts_and_styles_to_move_to_footer', 'move_scripts_and_styles_to_footer', 10, 1 );
```

Available tag attributes/sections to search for matching scripts: `id`, `src`, `code`  
Available tag attributes/sections to search for matching styles (style and link tags): `id`, `href`, `code`  
Tag attributes/sections can be searched for a specific string or an array of strings that can match multiple tags. Ex: `'match' => 'id', 'find' => 'kadence-header'` or `'match' => 'id', 'find' => array( 'kadence-header', 'wp-block-library' )`

Available `where` options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified.   
Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in  

### Remove Scripts and Styles
This utility will remove specified javascript scripts and css styles from the frontend. It does not remove them from the admin section.  

Add this to your wp-config.php file:  
```php
define( 'WP_UTILITIES_REMOVE_SCRIPTS_AND_STYLES', true );
```

Then specify scripts and styles to be removed with the `wp_utilities_scripts_and_styles_to_remove` filter by adding something like the following to your functions.php file:  
```php
function remove_scripts_and_styles( $settings ) {
    $settings['scripts'][] = array ( 
        'match'     => 'id',
        'find'      => 'ez-toc',
        'where'     => 'not_is_page'
    );
    $settings['scripts'][] = array ( 
        'match'     => 'src',
        'find'      => 'js/smooth_scroll.min.js',
        'where'     => 'path_sample-page'
    );
    $settings['scripts'][] = array ( 
        'match'     => 'code',
        'find'      => 'eztoc_smooth_local',
        'where'     => 'is_front_page'
    );

    $settings['styles'][] = array ( 
        'match'     => 'id',
        'find'      => array( 'kadence-header', 'wp-block-library' ),
        'where'     => 'is_page'
    );
    $settings['styles'][] = array ( 
        'match'     => 'href',
        'find'      => '/kadence/assets/css/content.min.css',
        'where'     => 'not_is_page'
    );
    $settings['styles'][] = array ( 
        'match'     => 'code',
        'find'      => '.tablepress thead',
        'where'     => 'not_is_page'
    );

    return $settings;
}
add_filter( 'wp_utilities_scripts_and_styles_to_remove', 'remove_scripts_and_styles', 10, 1 );
```

Available tag attributes/sections to search for matching scripts: `id`, `src`, `code`  
Available tag attributes/sections to search for matching styles (style and link tags): `id`, `href`, `code`  
Tag attributes/sections can be searched for a specific string or an array of strings that can match multiple tags. Ex: `'match' => 'id', 'find' => 'kadence-header'` or `'match' => 'id', 'find' => array( 'kadence-header', 'wp-block-library' )`

Available `where` options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified.  
Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in  