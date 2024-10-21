<?php
if (isset($_GET['id'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . CV_TABLE_NAME;
    $cv_id = intval($_GET['id']);

    // Fetch the CV details from the database
    $cv = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $cv_id));
    if ($cv) {
        echo '<div id="cv-container">';

        // Header Section (Positioned above everything)
        echo '<div class="cv-header">';
        echo '<h1>' . esc_html($cv->name) . '</h1>';
        echo '<h2>' . esc_html($cv->job_title) . '</h2>';
        echo '</div>';

        // Flexbox for Sidebar and Main Content
        echo '<div class="cv-content">';

        // Sidebar (Left Column)
        echo '<div class="cv-sidebar">';

        // Contact Details
        echo '<div class="cv-sidebar-content-wrapper">';
        echo '<div class="contact-details section">';
        echo '<h3>Contact Details</h3>';

        if (!empty($cv->location)) {
            echo '<p><i class="fas fa-map-marker-alt"></i> ' . esc_html($cv->location) . '</p>';
        }

        if (!empty($cv->phone)) {
            echo '<p><i class="fas fa-phone"></i> ' . esc_html($cv->phone) . '</p>';
        }

        if (!empty($cv->email)) {
            echo '<p><i class="fas fa-envelope"></i> <a href="mailto:' . esc_html($cv->email) . '">' . esc_html($cv->email) . '</a></p>';
        }

        if (!empty($cv->linkedin)) {
            $display_linkedin = clean_url_display($cv->linkedin);
            echo '<p><i class="fab fa-linkedin"></i> <a href="' . esc_url($cv->linkedin) . '" target="_blank">' . esc_html($display_linkedin) . '</a></p>';
        }

        if (!empty($cv->portfolio)) {
            $display_portfolio = clean_url_display($cv->portfolio);
            echo '<p><i class="fas fa-globe"></i> <a href="' . esc_url($cv->portfolio) . '" target="_blank">' . esc_html($display_portfolio) . '</a></p>';
        }

        echo '</div>';

        // Core Skills (Skill Groups and Icons)
        $skill_groups = maybe_unserialize($cv->skills); // Assuming serialized skill groups
        echo '<div class="core-skills section">';
        echo '<h3>Core Skills</h3>';
        if (!empty($skill_groups) && is_array($skill_groups)) {
            foreach ($skill_groups as $group_index => $group) {
                echo '<h4>' . esc_html($group['name']) . '</h4>';
                echo '<ul>';
                foreach ($group['skills'] as $skill_index => $skill) {
                    $icon_class = !empty($skill['icon']) && $skill['icon'] !== 'custom' ? esc_attr($skill['icon']) : '';
                    $custom_icon = !empty($skill['custom_icon']) ? esc_url($skill['custom_icon']) : '';

                    echo '<li>';
                    if (!empty($custom_icon)) {
                        // Display custom uploaded icon
                        echo '<img src="' . $custom_icon . '" alt="Custom Icon" style="width:24px; height:24px; margin-right:10px;">';
                    } elseif (!empty($icon_class)) {
                        // Display Font Awesome icon
                        echo '<i class="' . $icon_class . '" style="margin-right:10px;"></i>';
                    }
                    echo esc_html($skill['name']);
                    echo '</li>';
                }
                echo '</ul>';
            }
        } else {
            echo '<p>No skills provided.</p>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>'; // End Sidebar

        // Main Content (Right Column)
        echo '<div class="cv-main">';
        echo '<div class="cv-main-content-wrapper">';
        // Professional Profile
        echo '<div class="section">';
        echo '<h3>Professional Profile</h3>';
        echo '<p>' . nl2br(esc_html($cv->professional_profile)) . '</p>';
        echo '</div>';

        // Career Summary
        $career_summary = maybe_unserialize($cv->career_summary);
        echo '<div class="section career-summary">';
        echo '<h3>Career Summary</h3>';
        if (!empty($career_summary) && is_array($career_summary)) {
            $job_titles = $career_summary['job_title'] ?? [];
            $company_names = $career_summary['company_name'] ?? [];
            $experience_start_dates = $career_summary['experience_start_date'] ?? [];
            $experience_end_dates = $career_summary['experience_end_date'] ?? [];
            $experience_descriptions = $career_summary['experience_description'] ?? [];

            foreach ($job_titles as $key => $job_title) {
                $company = $company_names[$key] ?? '';
                $start_date = $experience_start_dates[$key] ?? '';
                $end_date = $experience_end_dates[$key] ?? '';
                $description = $experience_descriptions[$key] ?? '';

                echo '<p><strong>' . esc_html($company) . '</strong> | ' . esc_html($start_date) . ' - ' . esc_html($end_date) . '</p>';
                echo '<p>' . esc_html($job_title) . '</p>';
                echo '<p>' . nl2br(esc_html($description)) . '</p>';
            }
        }
        echo '</div>';

        // Education
        $education = maybe_unserialize($cv->education);
        echo '<div class="section education">';
        echo '<h3>Education</h3>';
        if (!empty($education) && is_array($education)) {
            $school_names = $education['school_name'] ?? [];
            $degrees = $education['degree'] ?? [];
            $education_start_dates = $education['education_start_date'] ?? [];
            $education_end_dates = $education['education_end_date'] ?? [];

            foreach ($school_names as $key => $school_name) {
                $degree = $degrees[$key] ?? '';
                $start_date = $education_start_dates[$key] ?? '';
                $end_date = $education_end_dates[$key] ?? '';

                echo '<p><strong>' . esc_html($school_name) . '</strong> | ' . esc_html($start_date) . ' - ' . esc_html($end_date) . '</p>';
                echo '<p>' . esc_html($degree) . '</p>';
            }
        }
        echo '</div>';
        echo '</div>';
        echo '</div>'; // End Main Content

        echo '</div>'; // End Content Section (Flexbox)
        echo '</div>'; // End Container
    } else {
        return '<p>CV not found.</p>';
    }
} else {
    return '<p>No CV ID provided.</p>';
}