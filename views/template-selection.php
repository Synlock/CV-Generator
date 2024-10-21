<?php

function cv_template_selection_page()
{
    if (!isset($_GET['id']) || !wp_verify_nonce($_GET['_wpnonce'], 'view_cv_' . $_GET['id'])) {
        return 'Invalid request.';
    }

    $cv_id = intval($_GET['id']);
    $template_dir = plugin_dir_path(__FILE__) . "../templates/";  // Server path for accessing files
    $template_uri = plugin_dir_url(__FILE__) . "../templates/";   // Browser-accessible path for CSS

    // Get all template PHP files
    $template_files = glob($template_dir . '*.php');

    if (empty($template_files)) {
        echo '<p>No templates found.</p>';
        return;
    }

    // Display available templates
    echo '<div id="template-selection">';

    foreach ($template_files as $template_file) {
        // Extract the template name from the filename
        $template_slug = basename($template_file, '.php');
        $template_name = ucwords(str_replace('-', ' ', $template_slug));

        // Create the preview URL
        $preview_url = esc_url(add_query_arg([
            'id' => $cv_id,
            'template' => $template_slug,
            '_wpnonce' => wp_create_nonce('view_cv_' . $cv_id),
        ], home_url('/cv-template-preview/')));

        // Load the template's CSS for display as a mini version
        $css_file_path = $template_dir . "css/{$template_slug}-styles.css";
        $css_file_url = $template_uri . "css/{$template_slug}-styles.css";

        $css_content = '';
        if (file_exists($css_file_path)) {
            // Get the CSS content
            $css_content = file_get_contents($css_file_path);
        }

        // Wrapper for the template preview box
        echo '<div class="template-preview-wrapper">';

        //Template stylesheet
        echo '<link rel="stylesheet" href="' . esc_url($css_file_url) . '">';

        //Template title
        echo '<a href="' . $preview_url . '"<h2>' . esc_html($template_name) . '</h2></a>';


        // The template content, positioned at the top-left corner
        echo '<div id="template-wrapper">';
        include $template_file;
        echo '</div>';

        // The anchor tag, positioned absolutely over the template preview
        echo '<a href="' . $preview_url . '" class="template-box-link"></a>';

        echo '</div>';  // End of template-preview-wrapper

    }

    echo '</div>';
}
add_shortcode('cv_template_selection', 'cv_template_selection_page');