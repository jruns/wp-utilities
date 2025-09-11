<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://jruns.github.io/
 * @since      0.1.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/admin/partials
 */
?>
<style>
    .utility_notice {
        font-size: 0.9em;
        color: #666;
    }
</style>

<div class="wrap">
<h1><?php esc_html_e( 'WP Performance Utilities', 'wp-utilities' ); ?></h1>

<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">
<?php settings_fields( 'wp-utilities' ); ?>

<ul>
<li class="itemDetail">
<h2 class="itemTitle"><?php _e( 'General Options', 'wp-utilities' ); ?></h2>

<table class="form-table">
<?php
$args = array(
    'utility_var'       => 'wp_utilities_disable_jquery_migrate',
    'heading'           => 'Disable jQuery Migrate?',
    'description'       => 'Disable jQuery migrate script from the frontend.'
);
echo output_admin_option( $args );

$args = array(
    'utility_var'       => 'wp_utilities_remove_scripts_and_styles',
    'heading'           => 'Remove Scripts and Styles?',
    'description'       => 'Enable the `wp_utilities_scripts_and_styles_to_remove` WordPress filter to selectively remove scripts and styles from the frontend.'
);
echo output_admin_option( $args );

$args = array(
    'utility_var'       => 'wp_utilities_enable_youtube_facade',
    'heading'           => 'Enable YouTube Facade?',
    'description'       => 'Enable YouTube facade for videos on the frontend, and delay loading videos until the user clicks the placeholder image.'
);
echo output_admin_option( $args );
?>
</table>

</li>
</ul>

<p class="submit">
<input type="submit" class="button-secondary" value="<?php _e( 'Save Changes', 'eventcore' ); ?>" />
</p>

</form>
</div>

<?php

function output_admin_option( $args ) {
    extract( $args );

    $utility_constant = strtoupper( $utility_var );
    $utility_status = null;
    $after_label_msg = '';
    if( defined( $utility_constant ) ) {
        $utility_status = constant( $utility_constant );
        $after_label_msg = __( "<br/><br/><span class='utility_notice'>This setting is currently configured in your wp-config.php file and can only be edited there. Remove $utility_constant from wp-config.php in order to configure this setting here.</span>" );
    } else {
        $utility_status = get_option( $utility_var );
    }

    return "<tr valign='top'>
        <th scope='row'>" . __( $heading, 'wp-utilities' ) . "</th>
        <td><label><input type='checkbox' id='$utility_var' name='$utility_var' value='1' " . ( $utility_status ? "checked='checked'" : '' ) . ( defined( $utility_constant ) ? ' disabled' : '' ) . "/> " .
        __( $description, 'wp-utilities' ) . "</label>
        $after_label_msg
        </td></tr>";
}