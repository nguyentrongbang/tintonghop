<?php
$default_dark = get_theme_mod( 'jnews_dark_mode_global_set', false );
$user_dark    = get_theme_mod( 'jnews_dark_mode_user_set', true );

if ( (( isset( $_COOKIE['darkmode'] ) && $_COOKIE['darkmode'] === 'true') && !$default_dark && $user_dark) || ( $default_dark && ! $user_dark ) ) {
    $script = "<script>$('body').addClass('jnews-dark-mode');</script>";
} elseif ( $default_dark ) {
    if ( isset( $_COOKIE['darkmode'] ) &&  $_COOKIE['darkmode'] == 'false' ) {
        $script = "<script>$('body').removeClass('jnews-dark-mode');</script>";
    } else {
        $script = "<script>$('body').addClass('jnews-dark-mode'); $( document ).ready(function() { $('.jeg_dark_mode_toggle').prop('checked',true).trigger('change'); });</script>";
    }
}
else {
    $script = "<script>$('body').removeClass('jnews-dark-mode');</script>";
}

$div_dark = "<div class=\"jeg_nav_item jeg_dark_mode\">";
if ( $user_dark ) {
    if ( ( isset( $_GET['vc_editable'] ) && $_GET['vc_editable'] !== null ) || ( isset( $_GET['elementor-preview'] ) && $_GET['elementor-preview'] !== null ) ) {
        $script = '';
    }
    $div_dark = $div_dark . "<label class=\"dark_mode_switch\">
                                <input type=\"checkbox\" class=\"jeg_dark_mode_toggle\">
                                <span class=\"slider round\"></span>
                            </label>";
}
$div_dark = $div_dark . $script . "</div>";
echo jnews_sanitize_by_pass( $div_dark );