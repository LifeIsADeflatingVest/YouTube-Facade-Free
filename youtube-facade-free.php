<?php
/**
 * Plugin Name: YouTube Video Facade: Free Version
 * Description: Replaces embedded YouTube videos with clickable facades using their thumbnails.
 * Version: 1.1
 * Author: Chris Angelis, <a href="https://homeforfiction.com">Home for Fiction</a>.
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Hook the content filter
add_filter('the_content', 'ytf_custom_youtube_thumbnail');

// Main function to replace YouTube iframes with thumbnails
function ytf_custom_youtube_thumbnail($content) {
    if (strpos($content, '<iframe') !== false && strpos($content, 'youtube.com') !== false) {
        // Replace the <iframe> src with the YouTube video thumbnail
        $content = preg_replace_callback('/<iframe(.*?)src="(.*?youtube\.com.*?)".*?<\/iframe>/', 'ytf_replace_with_thumbnail', $content);
    }
    return $content;
}

// Callback function to generate the facade
function ytf_replace_with_thumbnail($matches) {
    // Extract the YouTube video URL
    $video_url = esc_attr($matches[2]);
    // Get the YouTube video ID from the URL
    preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video_url, $video_id_matches);
    $video_id = $video_id_matches[1];

    // Define an array of possible thumbnail options (in preferred order)
    $thumbnail_options = array('maxresdefault', 'hqdefault', 'default');

    // Loop through thumbnail options to find the first available thumbnail
    $thumbnail_url = '';
    foreach ($thumbnail_options as $option) {
        $thumbnail_url = "https://img.youtube.com/vi/{$video_id}/{$option}.jpg";
        $headers = get_headers($thumbnail_url);
        if (strpos($headers[0], '200') !== false) {
            break;
        }
    }

    // Replace the <iframe> src with the YouTube video thumbnail
    return '<div data-src="' . $video_url . '" class="video-facade">
        <p class="clickNote">Click to display the embedded YouTube video</p>
        <div class="svgDiv">
            <svg viewBox="0 0 24 24" width="100" height="100" style="fill: red;">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
        </div>
        <img alt="facade placeholder" ' . $matches[1] . ' src="' . esc_url($thumbnail_url) . '">
    </div>';
}

// Enqueue the CSS and JS files for video facade functionality
function ytf_enqueue_assets() {
    wp_enqueue_style('ytf-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('ytf-script', plugin_dir_url(__FILE__) . 'js/script.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'ytf_enqueue_assets');
