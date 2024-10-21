<?php

function cv_template_preview_page()
{
    if (!isset($_GET['id'], $_GET['template']) || !wp_verify_nonce($_GET['_wpnonce'], 'view_cv_' . $_GET['id'])) {
        return 'Invalid request.';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'cv_data';
    $cv_id = intval($_GET['id']);
    $template = sanitize_text_field($_GET['template']);

    // Fetch the CV details
    $cv = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $cv_id));

    if (!$cv) {
        return 'CV not found.';
    }

    // Load the template and style
    $template_file = plugin_dir_path(__FILE__) . "../templates/{$template}.php";
    $css_file = plugin_dir_url(__FILE__) . "../templates/css/{$template}-styles.css";

    if (!file_exists($template_file)) {
        return 'Template not found.';
    }

    // Add the PDF generation button
    $generate_pdf_url = esc_url(add_query_arg([
        'id' => $cv_id,
        'template' => $template,
        'action' => 'generate_pdf',
        '_wpnonce' => wp_create_nonce('generate_pdf_' . $cv_id),
    ], admin_url('admin-post.php')));

    echo '<link rel="stylesheet" href="' . esc_url($css_file) . '">';
    echo '<div id="template-wrapper">';
    include $template_file;
    echo '</div>';

    // Button to generate PDF
    echo '<button id="generate-pdf-button" class="button button-primary">Generate PDF</button>';
}

add_shortcode('cv_template_preview', 'cv_template_preview_page');