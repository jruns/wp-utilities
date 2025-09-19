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
    @media screen and (min-width: 783px) {
        .form-table th {
            width: 250px;
        }
    }

    .utility_notice {
        font-size: 0.9em;
        color: #666;
    }

    .dashicons-warning {
        line-height: 1.4;
        font-size: 14px;
        color: #F5B027;
        margin-left:4px;
    }

    .tooltip {
        position: relative;
        display: inline-block;	
    }
    .tooltip .tooltip-text {
        visibility: hidden;
        top: 20px;
        right: 0;
        min-width:280px;
        background-color: #E4E4E4;
        border: 2px solid #3D3D3D;
        border-radius: 5px;
        font-size: 0.9em;
        color: rgb(60, 67, 74);
        padding: 4px;
        position: absolute;
        z-index: 1;
    }
    .tooltip:hover .tooltip-text {
        visibility: visible;
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
    'utility_var'       => 'wp_utilities_move_scripts_and_styles_to_footer',
    'heading'           => 'Move Scripts and Styles to the footer?',
    'description'       => 'Enable the `wp_utilities_scripts_and_styles_to_move_to_footer` WordPress filter to selectively move scripts and styles to the page footer on the frontend.'
);
echo output_admin_option( $args );

$args = array(
    'utility_var'       => 'wp_utilities_remove_scripts_and_styles',
    'heading'           => 'Remove Scripts and Styles?',
    'description'       => 'Enable the `wp_utilities_scripts_and_styles_to_remove` WordPress filter to selectively remove scripts and styles from the frontend.'
);
echo output_admin_option( $args );

$args = array(
    'utility_var'       => 'wp_utilities_delay_scripts',
    'heading'           => 'Delay Scripts?',
    'description'       => 'Enable the `wp_utilities_scripts_to_delay` WordPress filter to selectively delay scripts on the frontend.'
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
        $after_label_msg = __( "<span class='tooltip'><span class='dashicons dashicons-warning'></span><span class='tooltip-text'>This setting is currently configured in your wp-config.php file and can only be enabled or disabled there.<br/><br/>Remove $utility_constant from wp-config.php in order to enable/disable this setting here.</span></span>" );
    } else {
        $utility_status = get_option( $utility_var );
    }

    return "<tr valign='top'>
        <th scope='row'>" . __( $heading, 'wp-utilities' ) . "</th>
        <td><label><input type='checkbox' id='$utility_var' name='$utility_var' value='1' " . ( $utility_status ? "checked='checked'" : '' ) . ( defined( $utility_constant ) ? ' disabled' : '' ) . "/> " .
        __( $description, 'wp-utilities' ) . "$after_label_msg</label>
        </td></tr>";
}