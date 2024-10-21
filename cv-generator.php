<?php
/*
Plugin Name: CV Generator
Description: A plugin to generate a CV from inputted data.
Version: 1.0
Author: Roye Shomroni
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

include(plugin_dir_path(__FILE__) . 'includes/constants.php');

function enqueue_admin_scripts()
{
    wp_enqueue_style('admin-styles', plugin_dir_url(__FILE__) . 'assets/css/admin-styles.css');
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

// Enqueue styles
function cv_generator_enqueue_styles()
{
    //Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

    if (is_page('cv-template-selection')) {
        wp_enqueue_style('cv-template-selection', plugin_dir_url(__FILE__) . 'assets/css/cv-template-selection.css');
    }

    if (is_page('cv-template-preview')) {
        wp_enqueue_style('cv-template-preview', plugin_dir_url(__FILE__) . 'assets/css/cv-template-preview.css');
    }
}
add_action('wp_enqueue_scripts', 'cv_generator_enqueue_styles');

function cv_generator_enqueue_scripts()
{
    if (is_page('cv-generator-form')) {
        wp_enqueue_media();

        wp_enqueue_script(
            'cv-generator-js',
            plugins_url('assets/js/cv-generator.js', __FILE__),
            array('jquery'),
            CURRENT_PLUGIN_VERSION,
            true
        );
    }

    if (is_page('cv-template-preview')) {
        wp_enqueue_script(
            'cv-template-preview-js',
            plugins_url('assets/js/template-preview.js', __FILE__),
            array('jquery'),
            CURRENT_PLUGIN_VERSION,
            true
        );

        // Enqueue html2pdf from CDN
        wp_enqueue_script(
            'html2pdf',
            'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js',
            array(),
            null,
            true // Load in footer
        );

        // Enqueue html2canvas from CDN
        wp_enqueue_script(
            'html2canvas',
            'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js',
            array(),
            null,
            true // Load in footer
        );

        // Enqueue jsPDF from CDN
        wp_enqueue_script(
            'jspdf',
            'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
            array(),
            null,
            true // Load in footer
        );
    }
}
add_action('wp_enqueue_scripts', 'cv_generator_enqueue_scripts');

// Include the form and template
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/cv-form.php';
require_once plugin_dir_path(__FILE__) . 'views/template-preview.php';
require_once plugin_dir_path(__FILE__) . 'views/template-selection.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin.php';

//Create table that stores created CVs
add_action('init', 'cv_generator_create_table');

//Add admin menu to WordPress Dashboard
add_action('admin_menu', 'cv_generator_add_admin_menu');

//Add a delete cv functionality
add_action('admin_init', 'handle_cv_delete');

// Shortcode to display the CV form
function cv_generator_shortcode()
{
    ob_start();
    cv_generator_display_form();
    return ob_get_clean();
}
add_shortcode('cv_generator_form', 'cv_generator_shortcode');