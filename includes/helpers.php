<?php

function cv_generator_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . CV_TABLE_NAME;

    $charset_collate = $wpdb->get_charset_collate();

    // Create the table for storing CV data, including serialized fields
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        name text NOT NULL,
        job_title text NOT NULL,
        email text,
        phone text,
        location text,
        linkedin text,
        portfolio text,
        professional_profile longtext,
        skills longtext,
        career_summary longtext,
        education longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function cv_generator_save_to_db()
{
    global $wpdb;
    $table_name = $wpdb->prefix . CV_TABLE_NAME;

    // Check if required fields exist
    if (!isset($_POST['cv_name'], $_POST['cv_email'])) {
        // Handle missing required fields (you can log an error or return a response)
        return;
    }

    // Sanitize individual fields
    $name = sanitize_text_field($_POST['cv_name']);
    $job_title = isset($_POST['cv_job_title']) ? sanitize_text_field($_POST['cv_job_title']) : '';
    $email = sanitize_email($_POST['cv_email']);
    $phone = isset($_POST['cv_phone']) ? sanitize_text_field($_POST['cv_phone']) : '';
    $location = isset($_POST['cv_location']) ? sanitize_text_field($_POST['cv_location']) : '';
    $linkedin = isset($_POST['cv_linkedin']) ? sanitize_url($_POST['cv_linkedin']) : '';
    $portfolio = isset($_POST['cv_portfolio']) ? sanitize_url($_POST['cv_portfolio']) : '';
    $professional_profile = isset($_POST['cv_professional_profile']) ? sanitize_textarea_field(wp_unslash($_POST['cv_professional_profile'])) : '';

    // Sanitize and serialize education fields, if they exist
    $education = '';
    $education_data = [];

    // Process school names
    if (isset($_POST['school_name']) && is_array($_POST['school_name'])) {
        $school_names = array_map('sanitize_text_field', $_POST['school_name']);
        $education_data['school_name'] = $school_names;
    }

    // Process degrees
    if (isset($_POST['degree']) && is_array($_POST['degree'])) {
        $degrees = array_map('sanitize_text_field', $_POST['degree']);
        $education_data['degree'] = $degrees;
    }

    // Process education start dates
    if (isset($_POST['education_start_date']) && is_array($_POST['education_start_date'])) {
        $education_start_dates = array_map('sanitize_text_field', $_POST['education_start_date']);

        // Parse and modify the dates
        foreach ($education_start_dates as $key => $start_date) {
            $education_start_dates[$key] = parse_date_input($start_date) ?: $start_date; // Keep original if invalid
        }
        $education_data['education_start_date'] = $education_start_dates;
    }

    // Process education end dates
    if (isset($_POST['education_end_date']) && is_array($_POST['education_end_date'])) {
        $education_end_dates = array_map('sanitize_text_field', $_POST['education_end_date']);

        // Parse and modify the dates
        foreach ($education_end_dates as $key => $end_date) {
            $education_end_dates[$key] = parse_date_input($end_date) ?: $end_date; // Keep original if invalid
        }
        $education_data['education_end_date'] = $education_end_dates;
    }

    // Serialize education data
    $education = maybe_serialize($education_data);


    // Sanitize and serialize skill groups with icons
    $skill_groups = [];
    if (isset($_POST['skill_groups'])) {
        foreach ($_POST['skill_groups'] as $group_index => $group) {
            $sanitized_group = [];
            $sanitized_group['name'] = sanitize_text_field($group['name']); // Skill group name

            $sanitized_skills = [];
            if (isset($group['skills'])) {
                foreach ($group['skills'] as $skill_index => $skill) {
                    $sanitized_skills[] = [
                        'name' => sanitize_text_field($skill['name']),
                        'icon' => sanitize_text_field($skill['icon']),
                    ];
                }
            }

            $sanitized_group['skills'] = $sanitized_skills;
            $skill_groups[] = $sanitized_group;
        }
    }

    // Serialize skills and skill groups
    $skills = maybe_serialize($skill_groups);

    // Sanitize and serialize experience fields, if they exist
    $career_summary = '';
    // Sanitize and check each individual field
    if (isset($_POST['job_title']) && is_array($_POST['job_title'])) {
        $job_titles = array_map('sanitize_text_field', $_POST['job_title']);
    }

    if (isset($_POST['company_name']) && is_array($_POST['company_name'])) {
        $company_names = array_map('sanitize_text_field', $_POST['company_name']);
    }

    if (isset($_POST['experience_start_date']) && is_array($_POST['experience_start_date'])) {
        // Sanitize the input
        $experience_start_dates = array_map('sanitize_text_field', $_POST['experience_start_date']);

        // Parse the dates
        foreach ($experience_start_dates as $key => $start_date) {
            $experience_start_dates[$key] = parse_date_input($start_date) ?: $start_date;
        }
    }

    if (isset($_POST['experience_end_date']) && is_array($_POST['experience_end_date'])) {
        // Sanitize the input
        $experience_end_dates = array_map('sanitize_text_field', $_POST['experience_end_date']);

        // Parse the dates
        foreach ($experience_end_dates as $key => $end_date) {
            $experience_end_dates[$key] = parse_date_input($end_date) ?: $end_date;
        }
    }


    if (isset($_POST['experience_description']) && is_array($_POST['experience_description'])) {
        $experience_descriptions = array_map('sanitize_textarea_field', $_POST['experience_description']);
    }

    if (isset($_POST['experience_roles']) && is_array($_POST['experience_roles'])) {
        $experience_roles = array_map('sanitize_text_field', $_POST['experience_roles']);
    }

    // Serialize experience data only if at least one of the fields has data
    if (!empty($job_titles) || !empty($company_names) || !empty($experience_start_dates) || !empty($experience_end_dates) || !empty($experience_descriptions) || !empty($experience_roles)) {
        $career_summary = maybe_serialize(array(
            'job_title' => $job_titles,
            'company_name' => $company_names,
            'experience_start_date' => $experience_start_dates,
            'experience_end_date' => $experience_end_dates,
            'experience_description' => $experience_descriptions,
            'experience_roles' => $experience_roles,
        ));
    }

    // Insert data into the database
    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'job_title' => $job_title,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'linkedin' => $linkedin,
            'portfolio' => $portfolio,
            'professional_profile' => $professional_profile,
            'education' => $education,
            'skills' => $skills,
            'career_summary' => $career_summary,
            'created_at' => current_time('mysql')
        )
    );

    // Check for success or failure
    if (false === $result) {
        // Handle the error (e.g., log it or notify the user)
        error_log('Error saving CV to the database.');
    } else {
        // Successfully saved
        // Optional: redirect or show success message
    }
}

function clean_url_display($url)
{
    return str_replace(array('https://www.', 'http://www.', 'https://', 'http://'), '', $url);
}

function parse_date_input($input_date)
{
    // Define an array of date formats you expect
    $formats = array(
        'Y-m-d',
        'm-d-Y',
        'd-m-Y',
        'm/Y',
        'Y',
        'F Y',  // For formats like "Feb 2020"
        'Y-m',
        'd-m-y',
        'm-d-y'
    );

    // Loop through each format and try to create a DateTime object
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $input_date);

        // Check if the date is valid
        if ($date && $date->format($format) == $input_date) {
            return $date->format('Y-m-d');  // Always store in 'Y-m-d' format
        }
    }

    // If no format matches, return the original input or handle the error
    return null;
}

// Handle the deletion of a CV
function handle_cv_delete()
{
    if (isset($_GET['action']) && $_GET['action'] === 'delete_cv') {
        // Verify nonce
        $cv_id = intval($_GET['id']);
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_cv_' . $cv_id)) {
            wp_die(__('Nonce verification failed.'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . CV_TABLE_NAME;

        // Perform deletion
        $wpdb->delete($table_name, array('id' => $cv_id));

        // Redirect back to the admin page
        wp_redirect(admin_url('admin.php?page=cv-generator-admin'));
        exit;
    }
}