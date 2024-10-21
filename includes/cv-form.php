<?php

function cv_generator_display_form()
{
    global $wpdb;
    $table_name = $wpdb->prefix . CV_TABLE_NAME;
    $cv_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Get the CV ID if editing

    // Initialize variables for pre-population
    $cv_data = [
        'name' => '',
        'job_title' => '',
        'email' => '',
        'phone' => '',
        'location' => '',
        'linkedin' => '',
        'portfolio' => '',
        'professional_profile' => '',
        'education' => [],
        'skills' => [],
        'experience' => []
    ];

    // If editing, fetch the existing CV data
    if ($cv_id) {
        $cv = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $cv_id), ARRAY_A);
        error_log(print_r($cv, true));
        if ($cv) {
            // Prepopulate the data from the DB
            $cv_data['name'] = $cv['name'];
            $cv_data['job_title'] = $cv['job_title'];
            $cv_data['email'] = $cv['email'];
            $cv_data['phone'] = $cv['phone'];
            $cv_data['location'] = $cv['location'];
            $cv_data['linkedin'] = $cv['linkedin'];
            $cv_data['portfolio'] = $cv['portfolio'];
            $cv_data['professional_profile'] = $cv['professional_profile'];
            $cv_data['education'] = maybe_unserialize($cv['education']);
            $cv_data['skills'] = maybe_unserialize($cv['skills']);
            $cv_data['experience'] = maybe_unserialize($cv['career_summary']);
        }
    }

    ?>
<form action="" method="POST" class="cv-form" id="cv-form">
    <!-- Add hidden field to store CV ID (if editing) -->
    <input type="hidden" name="cv_id" value="<?php echo esc_attr($cv_id); ?>">

    <h3>Personal Information</h3>
    <label for="cv_name">Name:</label>
    <input type="text" name="cv_name" id="cv_name" value="<?php echo esc_attr($cv_data['name']); ?>"
        placeholder="John Doe"><br>

    <label for="cv_job_title">Job Title:</label>
    <input type="text" name="cv_job_title" id="cv_job_title" value="<?php echo esc_attr($cv_data['job_title']); ?>"
        placeholder="Software Developer"><br>

    <label for="cv_email">Email:</label>
    <input type="email" name="cv_email" id="cv_email" value="<?php echo esc_attr($cv_data['email']); ?>"
        placeholder="john.doe@example.com"><br>

    <label for="cv_phone">Phone:</label>
    <input type="text" name="cv_phone" id="cv_phone" value="<?php echo esc_attr($cv_data['phone']); ?>"
        placeholder="(123) 456-7890"><br>

    <label for="cv_location">Location:</label>
    <input type="text" name="cv_location" id="cv_location" value="<?php echo esc_attr($cv_data['location']); ?>"
        placeholder="City, State"><br>

    <label for="cv_linkedin">LinkedIn Profile Link:</label>
    <input type="text" name="cv_linkedin" id="cv_linkedin" value="<?php echo esc_attr($cv_data['linkedin']); ?>"
        placeholder="linkedin.com/in/your-profile"><br>

    <label for="cv_portfolio">Website or Portfolio Link:</label>
    <input type="text" name="cv_portfolio" id="cv_portfolio" value="<?php echo esc_attr($cv_data['portfolio']); ?>"
        placeholder="example.com"><br>

    <label for="cv_professional_profile">Professional Profile:</label>
    <textarea name="cv_professional_profile" id="cv_professional_profile" rows="4"
        placeholder="A brief professional summary..."><?php echo esc_textarea($cv_data['professional_profile']); ?></textarea><br>

    <!-- Dynamic Skill Groups Section -->
    <h3>Skills</h3>
    <div id="skill-groups">
        <?php
            $skills = maybe_unserialize($cv['skills'] ?? '');
            if (!empty($skills) && is_array($skills)) {
                foreach ($skills as $group_index => $skill_group) {
                    $skill_group_name = $skill_group['name'] ?? '';
                    $skills_in_group = $skill_group['skills'] ?? [];

                    ?>
        <div class="skill-group" data-group-index="<?php echo $group_index; ?>">
            <h4>Skill Group: <input type="text" name="skill_groups[<?php echo $group_index; ?>][name]"
                    value="<?php echo esc_attr($skill_group_name); ?>" placeholder="e.g., Programming Languages"></h4>
            <ul id="skill-list-<?php echo $group_index; ?>">
                <?php
                            foreach ($skills_in_group as $skill_index => $skill) {
                                ?>
                <li>
                    <label for="skill_<?php echo $group_index; ?>_<?php echo $skill_index; ?>">Skill:</label>
                    <input type="text"
                        name="skill_groups[<?php echo $group_index; ?>][skills][<?php echo $skill_index; ?>][name]"
                        id="skill_<?php echo $group_index; ?>_<?php echo $skill_index; ?>"
                        value="<?php echo esc_attr($skill['name'] ?? ''); ?>" placeholder="Skill Name"><br>

                    <label for="icon_<?php echo $group_index; ?>_<?php echo $skill_index; ?>">Icon:</label>
                    <select name="skill_groups[<?php echo $group_index; ?>][skills][<?php echo $skill_index; ?>][icon]"
                        id="icon_<?php echo $group_index; ?>_<?php echo $skill_index; ?>" class="icon-select">
                        <option value="fas fa-code" <?php selected($skill['icon'] ?? '', 'fas fa-code'); ?>>Code Icon
                        </option>
                        <option value="fas fa-user" <?php selected($skill['icon'] ?? '', 'fas fa-user'); ?>>Person Icon
                        </option>
                        <option value="fas fa-laptop-code"
                            <?php selected($skill['icon'] ?? '', 'fas fa-laptop-code'); ?>>Laptop Code Icon</option>
                        <option value="custom" <?php selected($skill['icon'] ?? '', 'custom'); ?>>Upload Custom</option>
                    </select>
                    <i id="icon-preview_<?php echo $group_index; ?>_<?php echo $skill_index; ?>"
                        class="<?php echo esc_attr($skill['icon'] ?? 'fas fa-code'); ?> icon-preview"
                        style="font-size: 24px; margin-left: 10px;"></i>

                    <button type="button" class="upload-icon" data-group-index="<?php echo $group_index; ?>"
                        data-skill-index="<?php echo $skill_index; ?>" style="display:none;">Upload Custom Icon</button>
                    <input type="hidden"
                        name="skill_groups[<?php echo $group_index; ?>][skills][<?php echo $skill_index; ?>][custom_icon]"
                        id="custom_icon_<?php echo $group_index; ?>_<?php echo $skill_index; ?>"
                        value="<?php echo esc_attr($skill['custom_icon'] ?? ''); ?>">

                    <button type="button" class="remove-skill-item">Remove Skill</button>
                </li>
                <?php
                            }
                            ?>
            </ul>
            <button type="button" class="add-skill" data-group-index="<?php echo $group_index; ?>">Add Skill</button>
        </div>
        <?php
                }
            } else {
                // Default blank group if no skills exist
                ?>
        <div class="skill-group" data-group-index="0">
            <h4>Skill Group: <input type="text" name="skill_groups[0][name]" placeholder="e.g., Programming Languages">
            </h4>
            <ul id="skill-list-0">
                <li>
                    <label for="skill_0_0">Skill:</label>
                    <input type="text" name="skill_groups[0][skills][0][name]" id="skill_0_0"
                        placeholder="Skill Name"><br>

                    <label for="icon_0_0">Icon:</label>
                    <select name="skill_groups[0][skills][0][icon]" id="icon_0_0" class="icon-select">
                        <option value="fas fa-code">Code Icon</option>
                        <option value="fas fa-user">Person Icon</option>
                        <option value="fas fa-laptop-code">Laptop Code Icon</option>
                        <option value="custom">Upload Custom</option>
                    </select>
                    <i id="icon-preview_0_0" class="fas fa-code icon-preview"
                        style="font-size: 24px; margin-left: 10px;"></i>

                    <button type="button" class="upload-icon" data-group-index="0" data-skill-index="0"
                        style="display:none;">Upload Custom Icon</button>
                    <input type="hidden" name="skill_groups[0][skills][0][custom_icon]" id="custom_icon_0_0">

                    <button type="button" class="remove-skill-item">Remove Skill</button>
                </li>
            </ul>
            <button type="button" class="add-skill" data-group-index="0">Add Skill</button>
        </div>
        <?php
            }
            ?>
    </div>
    <button type="button" id="add-skill-group">Add Skill Group</button>

    <!-- Dynamic Experience Section -->
    <h3>Experience</h3>
    <ul id="experience-list">
        <?php
            $career_summary = maybe_unserialize($cv['career_summary'] ?? '');
            if (!empty($career_summary) && is_array($career_summary)) {
                $job_titles = $career_summary['job_title'] ?? [];
                $company_names = $career_summary['company_name'] ?? [];
                $experience_start_dates = $career_summary['experience_start_date'] ?? [];
                $experience_end_dates = $career_summary['experience_end_date'] ?? [];
                $experience_descriptions = $career_summary['experience_description'] ?? [];
                $experience_roles = $career_summary['experience_roles'] ?? [];

                foreach ($job_titles as $key => $job_title) {
                    ?>
        <li>
            <label for="job_title_<?php echo $key; ?>">Job Title:</label>
            <input type="text" name="job_title[]" id="job_title_<?php echo $key; ?>"
                value="<?php echo esc_attr($job_title); ?>" placeholder="Web Developer"><br>

            <label for="company_name_<?php echo $key; ?>">Company Name:</label>
            <input type="text" name="company_name[]" id="company_name_<?php echo $key; ?>"
                value="<?php echo esc_attr($company_names[$key] ?? ''); ?>" placeholder="ABC Tech Solutions"><br>

            <label for="experience_start_date_<?php echo $key; ?>">Start Date:</label>
            <input type="text" name="experience_start_date[]" id="experience_start_date_<?php echo $key; ?>"
                value="<?php echo esc_attr($experience_start_dates[$key] ?? ''); ?>"><br>

            <label for="experience_end_date_<?php echo $key; ?>">End Date:</label>
            <input type="text" name="experience_end_date[]" id="experience_end_date_<?php echo $key; ?>"
                value="<?php echo esc_attr($experience_end_dates[$key] ?? ''); ?>"><br>

            <label for="experience_description_<?php echo $key; ?>">Job Description:</label>
            <textarea name="experience_description[]" id="experience_description_<?php echo $key; ?>" rows="4"
                placeholder="Describe your role and key achievements..."><?php echo esc_textarea($experience_descriptions[$key] ?? ''); ?></textarea><br>

            <!-- Bullet Points Section for Roles -->
            <label>Experience Roles:</label>
            <ul id="achievement-list-<?php echo $key; ?>" class="achievement-list">
                <?php
                            if (!empty($experience_roles[$key]) && is_array($experience_roles[$key])) {
                                foreach ($experience_roles[$key] as $role_index => $role) {
                                    echo '<li><input type="text" name="experience_roles[' . $key . '][]" value="' . esc_attr($role) . '"><button type="button" class="remove-bullet">Remove</button></li>';
                                }
                            }
                            ?>
            </ul>
            <button type="button" class="add-bullet" data-index="<?php echo $key; ?>">Add Role</button><br><br>
        </li>
        <?php
                }
            } else {
                // Default blank fields for new entries
                ?>
        <li>
            <label for="job_title_0">Job Title:</label>
            <input type="text" name="job_title[]" id="job_title_0" placeholder="Web Developer"><br>

            <label for="company_name_0">Company Name:</label>
            <input type="text" name="company_name[]" id="company_name_0" placeholder="ABC Tech Solutions"><br>

            <label for="experience_start_date_0">Start Date:</label>
            <input type="text" name="experience_start_date[]" id="experience_start_date_0"><br>

            <label for="experience_end_date_0">End Date:</label>
            <input type="text" name="experience_end_date[]" id="experience_end_date_0"><br>

            <label for="experience_description_0">Job Description:</label>
            <textarea name="experience_description[]" id="experience_description_0" rows="4"
                placeholder="Describe your role and key achievements..."></textarea><br>

            <!-- Bullet Points Section for Achievements -->
            <label>Experience Roles:</label>
            <ul id="achievement-list-0" class="achievement-list"></ul>
            <button type="button" class="add-bullet" data-index="0">Add Role</button><br><br>
        </li>
        <?php
            }
            ?>
    </ul>
    <button type="button" id="add-experience">Add Another Experience</button>

    <!-- Dynamic Education Section -->
    <h3>Education</h3>
    <ul id="education-list">
        <?php
            $education = maybe_unserialize($cv['education'] ?? '');
            if (!empty($education) && is_array($education)) {
                $school_names = $education['school_name'] ?? [];
                $degrees = $education['degree'] ?? [];
                $education_start_dates = $education['education_start_date'] ?? [];
                $education_end_dates = $education['education_end_date'] ?? [];

                foreach ($school_names as $key => $school_name) {
                    ?>
        <li>
            <label for="school_name_<?php echo $key; ?>">School Name:</label>
            <input type="text" name="school_name[]" id="school_name_<?php echo $key; ?>"
                value="<?php echo esc_attr($school_name); ?>" placeholder="University XYZ"><br>

            <label for="degree_<?php echo $key; ?>">Degree:</label>
            <input type="text" name="degree[]" id="degree_<?php echo $key; ?>"
                value="<?php echo esc_attr($degrees[$key] ?? ''); ?>" placeholder="Bachelor of Science"><br>

            <label for="education_start_date_<?php echo $key; ?>">Start Date:</label>
            <input type="text" name="education_start_date[]" id="education_start_date_<?php echo $key; ?>"
                value="<?php echo esc_attr($education_start_dates[$key] ?? ''); ?>"><br>

            <label for="education_end_date_<?php echo $key; ?>">End Date:</label>
            <input type="text" name="education_end_date[]" id="education_end_date_<?php echo $key; ?>"
                value="<?php echo esc_attr($education_end_dates[$key] ?? ''); ?>"><br>

            <button type="button" class="remove-item">Remove</button>
        </li>
        <?php
                }
            } else {
                // Default blank fields for new entries
                ?>
        <li>
            <label for="school_name_0">School Name:</label>
            <input type="text" name="school_name[]" id="school_name_0" placeholder="University XYZ"><br>

            <label for="degree_0">Degree:</label>
            <input type="text" name="degree[]" id="degree_0" placeholder="Bachelor of Science"><br>

            <label for="education_start_date_0">Start Date:</label>
            <input type="text" name="education_start_date[]" id="education_start_date_0"><br>

            <label for="education_end_date_0">End Date:</label>
            <input type="text" name="education_end_date[]" id="education_end_date_0"><br>

            <button type="button" class="remove-item">Remove</button>
        </li>
        <?php
            }
            ?>
    </ul>
    <button type="button" id="add-education">Add Another Education</button>

    <input type="submit" name="submit_cv" value="<?php echo $cv_id ? 'Update CV' : 'Generate CV'; ?>">
</form>
<?php

    if (isset($_POST['submit_cv'])) {
        cv_generator_save_to_db(); // Ensure the save function handles both new submissions and updates
    }
}