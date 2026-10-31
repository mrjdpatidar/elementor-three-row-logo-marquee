<?php
/**
 * Plugin Name: Three Row Logo Marquee for Elementor
 * Description: Responsive three-row logo slider with smooth marquee and stepped slide animations.
 * Version: 2.5.0
 * Requires Plugins: elementor
 * Text Domain: three-row-logo-marquee
 * Author: Drunall Technologies
 */
if (!defined('ABSPATH')) exit;
add_action('plugins_loaded', function () {
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', function () { echo '<div class="notice notice-warning"><p>Three Row Logo Marquee requires Elementor to be active.</p></div>'; });
        return;
    }
    add_action('elementor/widgets/register', function ($manager) {
        require_once __DIR__ . '/widget.php';
        $manager->register(new \TRLM_Logo_Marquee_Widget());
    });
});
add_action('wp_enqueue_scripts', function () {
    wp_register_style('trlm-style', plugins_url('assets/style.css', __FILE__), [], '2.5.0');
    wp_register_script('trlm-marquee', plugins_url('assets/marquee.js', __FILE__), [], '2.5.0', true);
});
