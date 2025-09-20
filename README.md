# wp-utilities
WP Performance Utilities is a WordPress plugin to improve the performance of a WordPress website. It includes several utilities that can be activated and configured in order to improve various aspects of your website. Disable jQuery Migrate, replace YouTube iframes with a placeholder image (facade), remove scripts or styles from specific pages, move scripts or styles to the page footer, and delay execution of scripts until the page has loaded or the user has interacted with the page.

## Available Utilities

### Disable jQuery Migrate
This utility removes the jQuery Migrate javascript file from the frontend. It does not remove it from the admin section.  

Add this to your wp-config.php file to activate the utility:
```php
define( 'WP_UTILITIES_DISABLE_JQUERY_MIGRATE', true );
```

Or activate the utility on the WP Performance Utilities wp-admin options page.

### Delay Scripts
This utility allows you to selectively delay inline and external javascript on your site. You can choose from a default delay which will add `defer` to `<script>` tags, a page loaded delay that will delay a script from executing until the page has loaded, or a user interaction delay that will delay a script from executing until the user has interacted with the page. There is a default autoload delay for the user interaction delay that you can modify.  

If you have any script configured to use the user_interaction delay you can also take advantage of the `DOMUserInteraction` browser event that we attach to the browser document.  

Any script tag with one of the following attributes will be excluded from being delayed: `nodelay`, `nowprocket`, `data-pagespeed-no-defer`  

Add this to your wp-config.php file to enable the utility:  
```php
define( 'WP_UTILITIES_DELAY_SCRIPTS', true );
```
Or activate the utility on the WP Performance Utilities wp-admin options page.  

To modify the default user interaction autoload delay of 15000 milliseconds (15s), you can add this to your wp-config.php file:  
```php
define( 'WP_UTILITIES_DELAY_SCRIPTS_AUTOLOAD_DELAY', 15000 );
```
Or override the default value in the WP Performance Utilities wp-admin options page.  
The value entered should be in milliseconds. This is a failsafe that will ensure that a script will still load even if the user takes longer to interact with the page.  

Then specify the scripts to be delayed with the `wp_utilities_scripts_to_delay` filter by adding something like the following to your functions.php file:  
```php
function delay_scripts( $settings ) {
    $settings['scripts'][] = array( 
        'match'     => 'id',
        'find'      => array( 'wc-add-to', 'js-cookie' )
    );
    
    $settings['scripts'][] = array( 
        'match'     => 'src',
        'find'      => 'add-to-cart.min.js',
        'args'      => array(
            'operation'     => 'user_interaction'
        )
    );

    $settings['scripts'][] = array( 
        'match'     => 'code',
        'find'      => 'wpemojiSettings',
        'where'     => 'is_page',
        'args'      => array(
            'operation'     => 'page_loaded',
            'delay'         => '5000'
        )
    );

    return $settings;
}
add_filter( 'wp_utilities_scripts_to_delay', 'delay_scripts', 10, 1 );
```

#### Filter settings options for script tags:
Add individual configurations by adding a new array to the `scripts` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `src`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |
| args      | Optional | Array. Available keys: `operation` and `delay`. `delay` must be in milliseconds.<br/>Available operations: `page_loaded` and `user_interaction` |

#### How to use the new DOMUserInteraction event:
You must first have at least one script being delayed on a specified page with the `wp_utilities_scripts_to_delay` filter. Then the new event will be available on that page.  
```php
document.addEventListener( "DOMUserInteraction", () => {
  console.log( "User has interacted with the page" );
});
```

### Enable YouTube Facade
This utility replaces YouTube iframes with an image placeholder (facade) on any frontend posts or pages. This delays initializing the video until the user clicks on the placeholder image, at which time the video will load and autoplay. The image placeholder is lazy loaded by default. And if the user has javascript disabled, clicking on the image will open the video in a new tab.

Add this to your wp-config.php file to activate the utility:  
```php
define( 'WP_UTILITIES_ENABLE_YOUTUBE_FACADE', true );
```
Or activate the utility on the WP Performance Utilities wp-admin options page.  

### Move Scripts and Styles to Footer
This utility moves specified javascript scripts and css styles to the page footer on the frontend. It does not move them in the admin section.  

Add this to your wp-config.php file to enable the utility:  
```php
define( 'WP_UTILITIES_MOVE_SCRIPTS_AND_STYLES_TO_FOOTER', true );
```

Then specify scripts and styles to be moved with the `wp_utilities_scripts_and_styles_to_move_to_footer` filter by adding something like the following to your functions.php file:  
```php
function move_scripts_and_styles_to_footer( $settings ) {
    $settings['scripts'][] = array( 
        'match'     => 'id',
        'find'      => 'ez-toc',
        'where'     => 'not_is_page'
    );
    $settings['scripts'][] = array( 
        'match'     => 'src',
        'find'      => 'js/smooth_scroll.min.js',
        'where'     => 'path_sample-page'
    );
    $settings['scripts'][] = array( 
        'match'     => 'code',
        'find'      => 'eztoc_smooth_local',
        'where'     => 'is_front_page'
    );

    $settings['styles'][] = array( 
        'match'     => 'id',
        'find'      => array( 'kadence-header', 'wp-block-library' ),
        'where'     => 'is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'href',
        'find'      => '/kadence/assets/css/content.min.css',
        'where'     => 'not_is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'code',
        'find'      => '.tablepress thead',
        'where'     => 'not_is_page'
    );

    return $settings;
}
add_filter( 'wp_utilities_scripts_and_styles_to_move_to_footer', 'move_scripts_and_styles_to_footer', 10, 1 );
```

#### Filter settings options for script tags:
Add individual configurations by adding a new array to the `scripts` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `src`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

#### Filter settings options for style and link tags:
Add individual configurations by adding a new array to the `styles` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `href`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

### Remove Scripts and Styles
This utility removes specified javascript scripts and css styles from the frontend. It does not remove them from the admin section.  

Add this to your wp-config.php file to enable the utility:  
```php
define( 'WP_UTILITIES_REMOVE_SCRIPTS_AND_STYLES', true );
```

Then specify scripts and styles to be removed with the `wp_utilities_scripts_and_styles_to_remove` filter by adding something like the following to your functions.php file:  
```php
function remove_scripts_and_styles( $settings ) {
    $settings['scripts'][] = array( 
        'match'     => 'id',
        'find'      => 'ez-toc',
        'where'     => 'not_is_page'
    );
    $settings['scripts'][] = array( 
        'match'     => 'src',
        'find'      => 'js/smooth_scroll.min.js',
        'where'     => array( 'path_sample-page', 'is_page' )
    );
    $settings['scripts'][] = array( 
        'match'     => 'code',
        'find'      => 'eztoc_smooth_local',
        'where'     => 'is_front_page'
    );

    $settings['styles'][] = array( 
        'match'     => 'id',
        'find'      => array( 'kadence-header', 'wp-block-library' ),
        'where'     => 'is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'href',
        'find'      => '/kadence/assets/css/content.min.css',
        'where'     => 'not_is_page'
    );
    $settings['styles'][] = array( 
        'match'     => 'code',
        'find'      => '.tablepress thead',
        'where'     => 'not_is_page'
    );

    return $settings;
}
add_filter( 'wp_utilities_scripts_and_styles_to_remove', 'remove_scripts_and_styles', 10, 1 );
```

#### Filter settings options for script tags:
Add individual configurations by adding a new array to the `scripts` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `src`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String or Array. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |

#### Filter settings options for style and link tags:
Add individual configurations by adding a new array to the `styles` key in the variable passed to your function by the filter.  
| Key   |     | Allowed Values |
| ----------- | --- | -------------- |
| match     | Required | String. `id`, `href`, `code` |
| find      | Required | String or Array. Text to search within the `match` attribute/section. Arrays allow you to match multiple tags on a page with an OR-like search. |
| where     | Optional | String. Available options: `all` for matching all posts/pages, select WP conditionals, and `path_` or `not_path_` for matching url path. Defaults to `all` if `where` is not specified. Arrays allow you to combine multiple conditions with an AND-like search (only pages containing all conditions will be matched).<br/><br/>Available WP conditionals: is_home, is_front_page, is_single, is_page, is_author, is_archive, has_excerpt, is_search, is_404, is_paged, is_attachment, is_singular, is_user_logged_in, not_is_home, not_is_front_page, not_is_single, not_is_page, not_is_author, not_is_archive, not_has_excerpt, not_is_search, not_is_404, not_is_paged, not_is_attachment, not_is_singular, not_is_user_logged_in |
