<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://jruns.github.io/
 * @since      1.0.0
 *
 * @package    Wp_Utilities
 * @subpackage Wp_Utilities/admin/partials
 */
?>

<div class="wrap">
<h1><?php esc_html_e( 'WP Performance Utilities', 'wp-utilities' ); ?></h1>

<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">
<?php settings_fields( 'wp-utilities' ); ?>

<ul>
<li class="itemDetail">
<h2 class="itemTitle"><?php _e( 'General Options', 'wp-utilities' ); ?></h2>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e( 'Disable jQuery Migrate?', 'wp-utilities' ); ?></th>
<?php 
$utility_var = 'wp_utilities_disable_jquery_migrate';
$utility_constant = strtoupper( $utility_var );
$utility_status = null;
$after_label_msg = '';
if( defined( $utility_constant ) ) {
    $utility_status = constant( $utility_constant );
    $after_label_msg = __( "<br/><br/>This setting is currently configured in your wp-config.php file and can only be edited there. Remove $utility_constant from wp-config.php in order to configure this setting here." );
} else {
    $utility_status = get_option( 'wp_utilities_disable_jquery_migrate' );
}
?>
<td><label><input type="checkbox" id="wp_utilities_disable_jquery_migrate" name="wp_utilities_disable_jquery_migrate" value="1" <?php echo $utility_status ? 'checked="checked"' : ''; ?> <?php echo defined( $utility_constant ) ? 'disabled' : ''; ?>/>
<?php _e( 'Disable jQuery migrate from the frontend.' ); ?></label>
<?php echo $after_label_msg; ?></td>
</tr>
</table>

</li>
</ul>

<p class="submit">
<input type="submit" class="button-secondary" value="<?php _e( 'Save Changes', 'eventcore' ); ?>" />
</p>

</form>
</div>