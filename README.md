# wp-utilities
WP Performance Utilities plugin to improve the performance of a WordPress website.

## Available Utilities

### Disable jQuery Migrate
This utility will remove the jQuery Migrate javascript file from the frontend. It does not remove it from the admin section.  

Add this to your wp-config.php file:
```php
define( 'WP_UTILITIES_DISABLE_JQUERY_MIGRATE', true );
```

Or activate the utility on the WP Performance Utilities wp-admin options page.

## To Do
2. Remove script by handle or filename, and by conditional or page parameters (slug, root folder, child folder)
3. Remove stylesheet by handle or filename, and by conditional or page parameters (slug, root folder, child folder)
4. Delay script by handle or filename, and by conditional or page parameters (slug, root folder, child folder)
5. Delay stylesheet by handle or filename, and by conditional or page parameters (slug, root folder, child folder)