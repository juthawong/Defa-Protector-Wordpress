<?php
/*
Plugin Name: Defa Protector Feather
Plugin URI: http://www.ampareengine.com
Description: Protect Video From Save As From Browser and Some Video Grabber
Version: 6.7.2
Author: Juthawong Naisanguansee
Author URI: http://www.juthawong.com/
License: MIT
 */

function defadmin_actions()
{
    add_menu_page('Defa Protector Engine FAQ and Help', 'Defa Protector Engine Help'
        , 'manage_options', 'defaprotector-info',
        'defa_admin', 'dashicons-media-code', 4);
}

function defaprotectorinit()
{
    ob_start();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

}
function defaprotector_footer()
{
    $output = ob_get_contents();
    if ((strpos($output, "<video") > -1 || strpos($output, "<audio") > -1 || strpos($output, "<source") > -1) && (strpos($output, "<safe") == false)) {
        ob_end_clean();
        //Check If There is Video On The Page Then Load Defa Protector
        // Source Tag Validation isn't need but for safety
        //If HTML Contains Safe Tag, Then Not Load Defa Protector
        function getURL($matches)
        {
            $crc = substr(sha1($matches['2']), -8, -1);
            $_SESSION['defaprotect' . $crc] = $matches['2'];
            return $matches[1] . wp_make_link_relative(plugins_url("defavid.php", __FILE__)) . "?crc=" . $crc;
        }
        //Super Ugly But Works Better
        $output = preg_replace_callback("/(<video[^>]*src *= *[\"']?)([^\"']*)/i", getURL, $output);
        $output = preg_replace_callback("/(<source[^>]*src *= *[\"']?)([^\"']*)/i", getURL, $output);
        $output = preg_replace_callback("/(<audio[^>]*src *= *[\"']?)([^\"']*)/i", getURL, $output);
        echo $output;

    }

}
add_action('wp_print_scripts', 'no_mediaelement_scripts', 100);
add_filter('wp_video_shortcode_library', 'no_mediaelement');

function no_mediaelement_scripts()
{
    wp_dequeue_script('wp-mediaelement');
    wp_deregister_script('wp-mediaelement');
}

function no_mediaelement()
{
    return '';
}
function defa_admin()
{
    echo '
<meta http-equiv="refresh" content="0; url=https://sites.google.com/site/defaprotectorhelp/faq" />
<script> window.location.href = "https://sites.google.com/site/defaprotectorhelp/faq"; </script>
 You will Be <a href="https://sites.google.com/site/defaprotectorhelp/faq">Redirect</a> Soon
';
}
add_action('init', 'defaprotectorinit');
add_action('wp_footer', 'defaprotector_footer');
add_action('admin_menu', 'defadmin_actions');
add_filter('wp_mediaelement_fallback', create_function('$stopmediafallback', "return null;"));
