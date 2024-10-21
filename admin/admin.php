<?php
// Add the menu item to the admin dashboard
function cv_generator_add_admin_menu()
{
    add_menu_page(
        'Generated CVs', // Page title
        'Generated CVs', // Menu title
        'manage_options', // Capability
        'cv-generator-admin', // Menu slug
        'cv_generator_admin_page', // Function to display the page content
        'dashicons-media-text', // Icon
    );
}

// Admin page to display the generated CVs
function cv_generator_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . CV_TABLE_NAME;

    // Fetch all CVs from the database
    $cvs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    // Display the CVs in a table format
    echo '<div class="wrap">';
    echo '<h1>Generated CVs</h1>';
    echo '<table class="wp-list-table widefat fixed striped table-view-list">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Name</th>';
    echo '<th>Email</th>';
    echo '<th>Phone</th>';
    echo '<th>Date Created</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if (!empty($cvs)) {
        foreach ($cvs as $cv) {
            // Generate nonces for view and edit actions
            $view_nonce = wp_create_nonce('view_cv_' . $cv->id);
            $edit_nonce = wp_create_nonce('edit_cv_' . $cv->id);
            $delete_nonce = wp_create_nonce('delete_cv_' . $cv->id);

            // Create URLs with nonces
            $view_url = esc_url(add_query_arg(array(
                'id' => $cv->id,
                '_wpnonce' => $view_nonce,
            ), home_url('/cv-template-selection/')));

            $edit_url = esc_url(add_query_arg(array(
                'id' => $cv->id,
                '_wpnonce' => $edit_nonce,
            ), home_url('/cv-generator-form/')));

            $delete_url = esc_url(add_query_arg(array(
                'id' => $cv->id,
                '_wpnonce' => $delete_nonce,
                'action' => 'delete_cv'
            ), admin_url('admin.php?page=cv-generator-admin')));

            echo '<tr>';
            echo '<td>' . esc_html($cv->name) . '</td>';
            echo '<td>' . esc_html($cv->email) . '</td>';
            echo '<td>' . esc_html($cv->phone) . '</td>';
            echo '<td>' . esc_html(date('Y-m-d H:i:s', strtotime($cv->date_generated))) . '</td>';
            echo '<td>';
            echo '<a href="' . $view_url . '" class="button">View</a> ';
            echo '<a href="' . $edit_url . '" class="button button-primary">Edit</a>';
            echo '<a href="' . $delete_url . '" id="cvg-admin-delete-button" class="button button-primary">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">No CVs found.</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}