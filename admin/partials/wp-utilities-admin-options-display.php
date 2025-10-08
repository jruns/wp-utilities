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
<style>;
    }
    .form-table th, .form-table td {
        padding: 0 10px 0 0;
    }
    @media screen and (min-width: 783px) {
        .form-table th {
            width: 250px;
        }
    }

    .child-table {
        width: 100%;
        margin-top: 10px;
        margin-left: 40px;
    }
    .form-table .child-table th {
        padding: 0;
    }
    .form-table .child-table td {
        padding: 5px 0 10px;
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
    'utility_var'       => 'wp_utilities_remove_versions',
    'heading'           => 'Remove Versions from Scripts and Styles?',
    'description'       => 'Remove versions from the source urls of external scripts and styles on the frontend. This can improve browser and CDN caching.'
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
    'description'       => 'Implement the `wp_utilities_scripts_and_styles_to_remove` WordPress filter to selectively remove scripts and styles from the frontend.'
);
echo output_admin_option( $args );

$args = array(
    'utility_var'       => 'wp_utilities_delay_scripts_and_styles',
    'heading'           => 'Delay Scripts?',
    'description'       => 'Implement the `wp_utilities_scripts_and_styles_to_delay` WordPress filter to selectively delay javascript and stylesheets on the frontend.',
    'child_options'     => array(
        array(
            'utility_var'       => 'wp_utilities_delay_scripts_and_styles_autoload_delay',
            'type'              => 'number',
            'default'           => 15000,
            'heading'           => 'User interaction autoload delay',
            'description'       => 'Modify the autoload delay that will load a script when the user has not yet interacted with the page. Default is 15 seconds (in milliseconds).'
        )
    )
);
echo output_admin_option( $args );

$args = array(
    'utility_var'       => 'wp_utilities_preload_images',
    'heading'           => 'Preload Images?',
    'description'       => 'Implement the `wp_utilities_images_to_preload` WordPress filter to selectively preload images on the frontend to improve Largest Contentful Paint (LCP).'
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
    $utility_value = null;
    $placeholder = '';
    $after_label_msg = '';
    if( defined( $utility_constant ) ) {
        $utility_value = constant( $utility_constant );
        $after_label_msg = __( "<span class='tooltip'><span class='dashicons dashicons-warning'></span><span class='tooltip-text'>This setting is currently configured in your wp-config.php file and can only be enabled or disabled there.<br/><br/>Remove $utility_constant from wp-config.php in order to enable/disable this setting here.</span></span>" );
    } else {
        $utility_value = get_option( $utility_var );
    }

    $child_output = '';

    if ( ! empty( $child_options ) && is_array( $child_options ) ) {
        foreach( $child_options as $child ) {
            $child['is_child'] = true;
            $child_output .= output_admin_option( $child );
        }
        $child_output = "<table class='child-table'>" . $child_output . "</table>";
    }

    $input_output = "<input type='checkbox' id='$utility_var' name='$utility_var' value='1' " . ( $utility_value ? "checked='checked'" : '' ) . ( defined( $utility_constant ) ? ' disabled' : '' ) . "/>" . __( $description, 'wp-utilities' ) . "$after_label_msg";
    if ( ! empty( $type ) ) {
        if ( empty( $utility_value ) && ! empty( $default ) ) {
            $placeholder = "placeholder='$default'";
        }

        if ( 'number' === $type ) {
            $input_output = __( $description, 'wp-utilities' ) . "<br/><input type='number' id='$utility_var' name='$utility_var' value='$utility_value' $placeholder" . ( defined( $utility_constant ) ? ' disabled' : '' ) . "/>$after_label_msg";
        }
    }

    return "<tr valign='top'>
        <th scope='row'>" . __( $heading, 'wp-utilities' ) . "</th>" .
        ( ! empty( $is_child ) && $is_child ? "</tr><tr valign='top'>" : "" ) .
        "<td><label>$input_output</label>
        $child_output
        </td></tr>";
}